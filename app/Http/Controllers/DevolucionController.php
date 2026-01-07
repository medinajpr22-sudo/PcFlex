<?php

namespace App\Http\Controllers;

use App\Models\Borrower_users;
use App\Models\Equipment;
use App\Models\Services;
use App\Mail\DevolucionComprobante;
use App\Traits\ValidatesEquipmentAndUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DevolucionController extends Controller
{
    use ValidatesEquipmentAndUser;
    public function update(Request $request)
    {
        request()->validate(Services::$Rules);
    
        // Usar métodos centralizados del trait
        $equipment = $this->findOrFailWithMessage(
            'equipment_id',
            Equipment::class,
            'serie_equi',
            $request->equipment_id
        );
        
        $user = $this->findOrFailWithMessage(
            'user_returner_id',
            Borrower_users::class,
            'number_identification',
            $request->user_returner_id
        );
    
        // Validar que el equipo esté en condiciones de ser devuelto
        $this->validateEquipmentForReturn($equipment);
    
        $service = Services::where('equipment_id', $equipment->id)
            ->where('status', 'pendiente')
            ->first();
    
        if (!$service) {
            return redirect()->back()->withErrors(['error' => 'No hay préstamos pendientes para este equipo.']);
        }
    
        $usuarioPrestatario = Borrower_users::find($service->user_borrower_id);

        if (!$usuarioPrestatario) {
            return redirect()->back()->withErrors(['error' => 'Usuario prestatario no encontrado.']);
        }
    
        // Se inicia la transacción en la base de datos
        DB::beginTransaction();
        try {
            // Actualizar el estado del equipo
            $equipment->status = 'disponible';
            $equipment->save();
    
            // Actualizar el servicio
            $returnDate = Carbon::now();
            $service->status = 'devuelto';
            $service->return_ser = $returnDate;
            $service->environment_id = 4;
    
            // Asignar el bibliotecario que realiza la devolución
            $bibliotecario = Auth::id();
            $service->librarian_borrower_id = $bibliotecario;
    
            // Si el usuario que devuelve es diferente al que prestó
            if ($usuarioPrestatario->id !== $user->id) {
                $service->user_returner_id = $user->id;
                $service->save();
                DB::commit();
                
                // Redirigir a crear reporte por inconsistencia
                return redirect()->route('reports.create-from-service', ['service_id' => $service->id])
                    ->with('warning', 'El equipo fue devuelto por una persona diferente. Por favor, complete el reporte.');
            }
    
            // Si el usuario que devuelve es el mismo que prestó
            $service->save();
            
            // Actualizar estado del usuario
            $usuarioPrestatario->status = 'activo';
            $usuarioPrestatario->save();
            
            DB::commit();

            // Enviar correo de comprobante de devolución
            try {
                // Cargar relaciones necesarias para el correo
                $service->load('environment');
                $usuarioPrestatario->load('contacts');
                
                // Intentar enviar el correo si el usuario tiene email
                if ($usuarioPrestatario->contacts && $usuarioPrestatario->contacts->email_con) {
                    Mail::to($usuarioPrestatario->contacts->email_con)
                        ->send(new DevolucionComprobante($service, $usuarioPrestatario, $equipment));
                }
            } catch (\Exception $e) {
                // Si falla el envío del correo, registrar el error pero no fallar la devolución
                \Log::error('Error al enviar correo de devolución: ' . $e->getMessage());
            }

            return redirect()->back()->with(['success' => 'Devolución exitosa.']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}