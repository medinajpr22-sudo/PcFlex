<?php

namespace App\Http\Controllers;

use App\Events\NewReservationCreated;
use App\Events\ReservationApproved;
use App\Models\Borrower_users;
use App\Models\Equipment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Mostrar lista de reservas
     */
    public function index()
    {
        $reservations = Reservation::with(['user', 'equipment', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_document' => 'required',
            'equipment_type' => 'required|string',
            'desired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Buscar usuario por documento
        $user = Borrower_users::where('number_identification', $request->user_document)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Usuario no encontrado. Debe estar registrado en el sistema.']);
        }

        // Verificar que el usuario no tenga sanciones
        if ($user->status === 'reportado') {
            return redirect()->back()->withErrors(['error' => 'El usuario tiene sanciones activas y no puede hacer reservas.']);
        }

        // Verificar que el usuario no tenga demasiadas reservas pendientes
        $reservasPendientes = Reservation::where('user_borrower_id', $user->id)
            ->where('status', 'pendiente')
            ->count();

        if ($reservasPendientes >= 2) {
            return redirect()->back()->withErrors(['error' => 'El usuario ya tiene el máximo de reservas pendientes permitidas (2).']);
        }

        try {
            $reservation = Reservation::create([
                'user_borrower_id' => $user->id,
                'equipment_type' => $request->equipment_type,
                'reservation_date' => Carbon::now(),
                'desired_date' => $request->desired_date ? Carbon::parse($request->desired_date) : null,
                'notes' => $request->notes,
                'status' => 'pendiente',
            ]);

            // Disparar evento de notificación en tiempo real
            broadcast(new NewReservationCreated($reservation->load('user')))->toOthers();

            return redirect()->back()->with('success', '¡Reserva creada exitosamente! Recibirás una notificación cuando sea aprobada.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear la reserva: ' . $e->getMessage()]);
        }
    }

    /**
     * Aprobar una reserva y asignar equipo
     */
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'equipment_serie' => 'required|string',
        ]);

        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pendiente') {
            return redirect()->back()->withErrors(['error' => 'Esta reserva ya fue procesada.']);
        }

        // Buscar equipo por serie
        $equipment = Equipment::where('serie_equi', $request->equipment_serie)->first();

        if (!$equipment) {
            return redirect()->back()->withErrors(['error' => 'Equipo no encontrado.']);
        }

        if ($equipment->status !== 'disponible') {
            return redirect()->back()->withErrors(['error' => 'El equipo no está disponible.']);
        }

        try {
            DB::beginTransaction();

            $reservation->status = 'aprobada';
            $reservation->equipment_id = $equipment->id;
            $reservation->approved_by = Auth::id();
            $reservation->approved_at = Carbon::now();
            $reservation->save();

            // Disparar evento de notificación en tiempo real al usuario
            broadcast(new ReservationApproved($reservation->load(['user', 'equipment'])))->toOthers();

            // Opcional: Enviar notificación por email
            // Mail::to($reservation->user->contacts->email_con)->send(new ReservaAprobada($reservation));

            DB::commit();

            return redirect()->back()->with('success', 'Reserva aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al aprobar la reserva: ' . $e->getMessage()]);
        }
    }

    /**
     * Rechazar una reserva
     */
    public function reject($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pendiente') {
            return redirect()->back()->withErrors(['error' => 'Esta reserva ya fue procesada.']);
        }

        $reservation->status = 'rechazada';
        $reservation->approved_by = Auth::id();
        $reservation->approved_at = Carbon::now();
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva rechazada.');
    }

    /**
     * Cancelar una reserva (por el usuario)
     */
    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pendiente') {
            return redirect()->back()->withErrors(['error' => 'Solo se pueden cancelar reservas pendientes.']);
        }

        $reservation->status = 'cancelada';
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva cancelada exitosamente.');
    }

    /**
     * Marcar reserva como completada (cuando el usuario recoge el equipo)
     */
    public function complete($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'aprobada') {
            return redirect()->back()->withErrors(['error' => 'Solo se pueden completar reservas aprobadas.']);
        }

        $reservation->status = 'completada';
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva completada. El usuario ha recogido el equipo.');
    }
}
