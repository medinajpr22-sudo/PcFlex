<?php

namespace App\Traits;

use App\Models\Borrower_users;
use App\Models\Disability;
use App\Models\Equipment;
use Illuminate\Validation\ValidationException;

trait ValidatesEquipmentAndUser
{
    /**
     * Valida que un equipo esté disponible para préstamo
     *
     * @param Equipment $equipment
     * @throws ValidationException
     */
    protected function validateEquipmentAvailability(Equipment $equipment)
    {
        switch ($equipment->status) {
            case 'prestado':
                throw ValidationException::withMessages([
                    'equipment_id' => 'El equipo ya está prestado.'
                ]);
            case 'inactivo':
                throw ValidationException::withMessages([
                    'equipment_id' => 'El equipo está inactivo y no puede ser prestado.'
                ]);
            case 'reparacion':
                throw ValidationException::withMessages([
                    'equipment_id' => 'El equipo está en reparación y no puede ser prestado.'
                ]);
        }
    }

    /**
     * Valida que un equipo esté prestado (para devoluciones)
     *
     * @param Equipment $equipment
     * @throws ValidationException
     */
    protected function validateEquipmentForReturn(Equipment $equipment)
    {
        switch ($equipment->status) {
            case 'inactivo':
                throw ValidationException::withMessages([
                    'equipment_id' => 'Este equipo está marcado como inactivo.'
                ]);
            case 'reparacion':
                throw ValidationException::withMessages([
                    'equipment_id' => 'Este equipo está en reparación.'
                ]);
            case 'disponible':
                throw ValidationException::withMessages([
                    'equipment_id' => 'Este equipo no está marcado como prestado.'
                ]);
        }
    }

    /**
     * Valida que un usuario pueda realizar préstamos
     *
     * @param Borrower_users $user
     * @throws ValidationException
     */
    protected function validateUserCanBorrow(Borrower_users $user)
    {
        switch ($user->status) {
            case 'inactivo':
                throw ValidationException::withMessages([
                    'user_borrower_id' => 'El usuario está inactivo y no puede solicitar préstamos.'
                ]);
            case 'reportado':
                throw ValidationException::withMessages([
                    'user_borrower_id' => 'El usuario está sancionado y no puede solicitar préstamos.'
                ]);
        }

        // Verificar sanciones activas
        $tieneSancion = Disability::whereHas('service', function ($query) use ($user) {
            $query->where('user_borrower_id', $user->id);
        })->where('status', 'activo')
          ->where('end_date', '>=', now())
          ->exists();

        if ($tieneSancion) {
            throw ValidationException::withMessages([
                'user_borrower_id' => 'El usuario tiene una sanción activa vigente.'
            ]);
        }
    }

    /**
     * Verifica si existe una identificación
     *
     * @param string $fieldName
     * @param string $modelClass
     * @param string $column
     * @param mixed $value
     * @return mixed
     * @throws ValidationException
     */
    protected function findOrFailWithMessage($fieldName, $modelClass, $column, $value)
    {
        $record = $modelClass::where($column, $value)->first();
        
        if (!$record) {
            $modelName = class_basename($modelClass);
            $messages = [
                'Equipment' => 'El equipo no existe.',
                'Borrower_users' => 'El usuario no existe.',
                'Services' => 'El servicio no existe.',
            ];
            
            throw ValidationException::withMessages([
                $fieldName => $messages[$modelName] ?? 'El registro no existe.'
            ]);
        }
        
        return $record;
    }
}
