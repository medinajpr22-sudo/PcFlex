<?php

namespace App\Http\Controllers;

use App\Models\Borrower_users;
use App\Models\Disability;
use App\Models\Equipment;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalEquipments = Equipment::count();
        $equipmentsAvailable = Equipment::where('status', 'disponible')->count();
        $equipmentsOnLoan = Equipment::where('status', 'prestado')->count();
        $equipmentsInRepair = Equipment::where('status', 'reparacion')->count();
        $equipmentsInactive = Equipment::where('status', 'inactivo')->count();

        // Préstamos
        $totalLoans = Services::count();
        $activeLoans = Services::where('status', 'pendiente')->count();
        $completedLoans = Services::where('status', 'devuelto')->count();
        
        // Préstamos del último mes
        $loansThisMonth = Services::whereMonth('date_ser', Carbon::now()->month)
            ->whereYear('date_ser', Carbon::now()->year)
            ->count();
        
        // Préstamos de hoy
        $loansToday = Services::whereDate('date_ser', Carbon::today())->count();

        // Usuarios
        $totalUsers = Borrower_users::count();
        $activeUsers = Borrower_users::where('status', 'activo')->count();
        $usersWithEquipment = Borrower_users::where('status', 'conEquipo')->count();
        $reportedUsers = Borrower_users::where('status', 'reportado')->count();

        // Reportes y sanciones
        $totalReports = Disability::count();
        $activeReports = Disability::where('status', 'activo')->count();
        $inactiveReports = Disability::where('status', 'inactivo')->count();

        // Equipos más prestados (top 10)
        $mostLoanedEquipments = Equipment::withCount('services')
            ->orderBy('services_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($equipment) {
                return [
                    'name' => $equipment->name_equi,
                    'serie' => $equipment->serie_equi,
                    'loans' => $equipment->services_count,
                    'type' => $equipment->type_equi,
                ];
            });

        // Usuarios más activos (top 10)
        $mostActiveUsers = Borrower_users::withCount('services')
            ->orderBy('services_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name_user . ' ' . $user->lastname_user,
                    'identification' => $user->number_identification,
                    'loans' => $user->services_count,
                    'role' => $user->roll,
                ];
            });

        // Préstamos por mes (últimos 6 meses)
        $loansByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Services::whereMonth('date_ser', $date->month)
                ->whereYear('date_ser', $date->year)
                ->count();
            
            $loansByMonth[] = [
                'month' => $date->translatedFormat('F'),
                'count' => $count,
            ];
        }

        // Préstamos por tipo de equipo
        $loansByEquipmentType = Equipment::select('type_equi', DB::raw('count(*) as total'))
            ->join('services', 'equipment.id', '=', 'services.equipment_id')
            ->groupBy('type_equi')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type_equi,
                    'total' => $item->total,
                ];
            });

        // Préstamos por rol de usuario
        $loansByUserRole = Borrower_users::select('roll', DB::raw('count(services.id) as total'))
            ->join('services', 'borrower_users.id', '=', 'services.user_borrower_id')
            ->groupBy('roll')
            ->get()
            ->map(function ($item) {
                return [
                    'role' => ucfirst($item->roll),
                    'total' => $item->total,
                ];
            });

        // Tasa de devolución a tiempo
        $totalCompletedLoans = Services::where('status', 'devuelto')
            ->whereNotNull('expected_return_date')
            ->count();
        
        $onTimeReturns = Services::where('status', 'devuelto')
            ->whereNotNull('expected_return_date')
            ->whereNotNull('return_ser')
            ->whereRaw('return_ser <= expected_return_date')
            ->count();
        
        $onTimeRate = $totalCompletedLoans > 0 
            ? round(($onTimeReturns / $totalCompletedLoans) * 100, 2) 
            : 0;

        // Promedio de duración de préstamos (en horas)
        $avgLoanDuration = Services::where('status', 'devuelto')
            ->whereNotNull('date_ser')
            ->whereNotNull('return_ser')
            ->get()
            ->avg(function ($service) {
                return Carbon::parse($service->date_ser)->diffInHours(Carbon::parse($service->return_ser));
            });

        return Inertia::render('Statistics/Index', [
            'summary' => [
                'equipments' => [
                    'total' => $totalEquipments,
                    'available' => $equipmentsAvailable,
                    'onLoan' => $equipmentsOnLoan,
                    'inRepair' => $equipmentsInRepair,
                    'inactive' => $equipmentsInactive,
                ],
                'loans' => [
                    'total' => $totalLoans,
                    'active' => $activeLoans,
                    'completed' => $completedLoans,
                    'thisMonth' => $loansThisMonth,
                    'today' => $loansToday,
                ],
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'withEquipment' => $usersWithEquipment,
                    'reported' => $reportedUsers,
                ],
                'reports' => [
                    'total' => $totalReports,
                    'active' => $activeReports,
                    'inactive' => $inactiveReports,
                ],
                'metrics' => [
                    'onTimeRate' => $onTimeRate,
                    'avgDuration' => round($avgLoanDuration, 1),
                ],
            ],
            'charts' => [
                'mostLoanedEquipments' => $mostLoanedEquipments,
                'mostActiveUsers' => $mostActiveUsers,
                'loansByMonth' => $loansByMonth,
                'loansByEquipmentType' => $loansByEquipmentType,
                'loansByUserRole' => $loansByUserRole,
            ],
        ]);
    }
}
