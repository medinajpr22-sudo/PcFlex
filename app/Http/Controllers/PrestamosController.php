<?php
namespace App\Http\Controllers;

use App\Models\Borrower_users;
use App\Models\Disabilities;
use App\Models\Equipment;
use App\Models\Services;
use App\Traits\ValidatesEquipmentAndUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrestamosController extends Controller
{
    use ValidatesEquipmentAndUser;
    public function store(Request $request)
    {
        // Validación inicial
        request()->validate(Services::$rules);

        // Obtener equipo y usuario usando el método centralizado
        $equipment = $this->findOrFailWithMessage(
            'equipment_id',
            Equipment::class,
            'serie_equi',
            $request->equipment_id
        );
        
        $user = $this->findOrFailWithMessage(
            'user_borrower_id',
            Borrower_users::class,
            'number_identification',
            $request->user_borrower_id
        );

        // Validar disponibilidad de equipo y usuario usando trait
        $this->validateEquipmentAvailability($equipment);
        $this->validateUserCanBorrow($user);

        // Validación para evitar múltiples préstamos de equipos similares
        $prestamoMismoTipo = Services::where('user_borrower_id', $user->id)
            ->whereIn('equipment_id', function ($query) use ($equipment) {
                $query->select('id')->from('equipment')->where('type_equi', $equipment->type_equi);
            })
            ->where('status', 'pendiente')
            ->exists();

        if ($prestamoMismoTipo) {
            return redirect()->back()->withErrors(['error' => 'No se puede prestar dos veces un equipo con características similares.']);
        }

        // Comprobar que el usuario no tenga demasiados préstamos pendientes
        $cantidadPrestamos = Services::where('user_borrower_id', $user->id)
            ->where('status', 'pendiente')
            ->count();

        if ($cantidadPrestamos >= 3) {
            return redirect()->back()->withErrors(['error' => 'El usuario ya tiene el número máximo de equipos permitidos en préstamo (3).']);
        }

        // Determinar duración del préstamo según el rol del usuario
        $loanDuration = $this->getLoanDurationByRole($user);
        $expectedReturnDate = Carbon::now()->addHours($loanDuration);

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Crear el registro del préstamo
            $newService = Services::create([
                'user_borrower_id' => $user->id,
                'equipment_id' => $equipment->id,
                'librarian_borrower_id' => Auth::id(),
                'date_ser' => Carbon::now(),
                'status' => 'pendiente',
                'environment_id' => $request->environment_id,
                'loan_duration_hours' => $loanDuration,
                'expected_return_date' => $expectedReturnDate,
            ]);

            // Actualizar el estado del equipo y del usuario dentro de la transacción
            $equipment->update(['status' => 'prestado']);
            $user->update(['status' => 'conEquipo']);

            // Confirmar transacción
            DB::commit();

            return redirect()->back()->with(['success' => 'Préstamo registrado con éxito.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al registrar el préstamo: ' . $e->getMessage()]);
        }
    }

    /**
     * Determinar la duración del préstamo según el rol del usuario
     */
    private function getLoanDurationByRole($user)
    {
        // Aquí defines las reglas de duración según el rol
        // Puedes personalizar esto según tus necesidades
        
        $roll = strtolower($user->roll);
        
        switch ($roll) {
            case 'estudiante':
                return 6; // 6 horas
            case 'profesor':
            case 'docente':
                return 24; // 24 horas (1 día)
            case 'administrativo':
                return 48; // 48 horas (2 días)
            case 'investigador':
                return 72; // 72 horas (3 días)
            default:
                return 6; // Por defecto 6 horas
        }
    }

}
