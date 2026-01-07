<?php

namespace App\Http\Controllers\BorrowerAuth;

use App\Http\Controllers\Controller;
use App\Models\Disability;
use App\Models\Reservation;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BorrowerDashboardController extends Controller
{
    /**
     * Dashboard principal del usuario
     */
    public function index()
    {
        $user = Auth::guard('borrower')->user();
        
        // Préstamos activos
        $activeLoans = Services::with(['equipment', 'environment'])
            ->where('user_borrower_id', $user->id)
            ->where('status', 'pendiente')
            ->get();
        
        // Sanciones activas
        $activeSanctions = Disability::whereIn('service_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('services')
                    ->where('user_borrower_id', $user->id);
            })
            ->where('status', 'activo')
            ->get();
        
        // Reservas pendientes
        $pendingReservations = Reservation::with('equipment')
            ->where('user_borrower_id', $user->id)
            ->whereIn('status', ['pendiente', 'aprobada'])
            ->get();
        
        return Inertia::render('Borrower/Dashboard', [
            'user' => $user,
            'activeLoans' => $activeLoans,
            'activeSanctions' => $activeSanctions,
            'pendingReservations' => $pendingReservations,
        ]);
    }

    /**
     * Historial completo de préstamos
     */
    public function history(Request $request)
    {
        $user = Auth::guard('borrower')->user();
        
        $history = Services::with(['equipment', 'environment'])
            ->where('user_borrower_id', $user->id)
            ->orderBy('date_ser', 'desc')
            ->paginate(15);
        
        return Inertia::render('Borrower/History', [
            'history' => $history,
        ]);
    }

    /**
     * Ver sanciones
     */
    public function sanctions()
    {
        $user = Auth::guard('borrower')->user();
        
        $sanctions = Disability::with('service.equipment')
            ->whereIn('service_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('services')
                    ->where('user_borrower_id', $user->id);
            })
            ->orderBy('punishment_date', 'desc')
            ->paginate(10);
        
        return Inertia::render('Borrower/Sanctions', [
            'sanctions' => $sanctions,
        ]);
    }

    /**
     * Renovar préstamo (extender tiempo)
     */
    public function renewLoan(Request $request, $serviceId)
    {
        $user = Auth::guard('borrower')->user();
        
        $service = Services::where('id', $serviceId)
            ->where('user_borrower_id', $user->id)
            ->where('status', 'pendiente')
            ->first();
        
        if (!$service) {
            return redirect()->back()->withErrors(['error' => 'Préstamo no encontrado o ya devuelto.']);
        }

        // Verificar que esté cerca de vencer (dentro de las últimas 2 horas)
        $now = Carbon::now();
        $expectedReturn = Carbon::parse($service->expected_return_date);
        $hoursRemaining = $now->diffInHours($expectedReturn, false);
        
        if ($hoursRemaining > 2) {
            return redirect()->back()->withErrors(['error' => 'Solo puedes renovar cuando falten menos de 2 horas para la devolución.']);
        }
        
        if ($hoursRemaining < 0) {
            return redirect()->back()->withErrors(['error' => 'No puedes renovar un préstamo vencido. Debes devolverlo primero.']);
        }

        // Extender el préstamo por la mitad del tiempo original
        $extensionHours = $service->loan_duration_hours / 2;
        $newExpectedReturn = $expectedReturn->addHours($extensionHours);
        
        $service->expected_return_date = $newExpectedReturn;
        $service->loan_duration_hours += $extensionHours;
        $service->save();
        
        return redirect()->back()->with('success', "Préstamo renovado exitosamente. Nueva fecha límite: {$newExpectedReturn->format('d/m/Y H:i')}");
    }

    /**
     * Descargar comprobante de devolución
     */
    public function downloadReceipt($serviceId)
    {
        $user = Auth::guard('borrower')->user();
        
        $service = Services::with(['equipment', 'environment', 'users'])
            ->where('id', $serviceId)
            ->where('user_borrower_id', $user->id)
            ->where('status', 'devuelto')
            ->first();
        
        if (!$service) {
            return redirect()->back()->withErrors(['error' => 'Comprobante no encontrado.']);
        }

        $pdf = \PDF::loadView('receipts.return-receipt', [
            'service' => $service,
            'usuario' => $user,
        ]);
        
        return $pdf->download("comprobante-devolucion-{$service->id}.pdf");
    }
}
