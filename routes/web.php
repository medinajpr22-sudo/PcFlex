<?php

use App\Http\Controllers\Borrower_usersController;
use App\Http\Controllers\BorrowerAuth\BorrowerAuthController;
use App\Http\Controllers\BorrowerAuth\BorrowerDashboardController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\IndexCardController;
use App\Http\Controllers\PanelPrincipalController;
use App\Http\Controllers\PrestamosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\reportsController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
 
        
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PanelPrincipalController::class, 'index'])->name('dashboard');
    
    //ruta estadísticas
    Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics.index');

    //rutas reportes
    Route::resource('/reports', reportsController::class)->middleware('can:gestionar.recursos');
    Route::put('/reports/{report}/activate', [reportsController::class, 'activate'])->name('reports.activate');
    Route::get('/reports/create-from-service/{service_id}', [reportsController::class, 'create'])->name('reports.create-from-service');
    //rutas de servicio 
    Route::get('/info/{id}', [ServiceController::class, 'details'])->name('info.details');
    Route::get('historial', [ServiceController::class, 'historico'])->name('historial.historico');
    Route::get('/detalles/{id}', [ServiceController::class, 'show'])->name('detalles.show');
    Route::put('/devolucion', [DevolucionController::class, 'update'])->name('devolucion.resivir')->middleware('can:gestionar.recursos');
    Route::get('resivir', [DevolucionController::class, 'resivir'])->name('resivir')->middleware('can:gestionar.recursos');
    //  Route::resource('/devolucion', DevolucionController::class);
    Route::resource('/prestamos', PrestamosController::class)->middleware('can:gestionar.recursos');
    //ambientes 

    Route::put('/environments/{id}', [EnvironmentController::class, 'update'])->name('environments.update')->middleware('can:gestionar.recursos');
    Route::put('/environments/activate/{id}', [EnvironmentController::class, 'active'])->name('environments.activate')->middleware('can:gestionar.recursos');

    Route::resource('/environments', EnvironmentController::class)->middleware('can:gestionar.recursos');

    //rutas equipos 

    Route::put('/equipments/{id}/reparation', [EquipmentController::class, 'reparation'])->name('equipments.reparation')->middleware('can:gestionar.recursos');
    Route::put('/equipments/{id}/activate', [EquipmentController::class, 'active'])->name('equipments.activate')->middleware('can:gestionar.recursos');
    Route::resource('/equipments', EquipmentController::class)->middleware('can:gestionar.recursos');




    //rutas admins 
    Route::resource('/users', UserController::class)->middleware('can:crud.bibliotecario');
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    // Route::post('/users', [Borrower_users::class, 'update'])->name('users.update');


    //rutas fichas 
    Route::put('indexCard/{id}/activate', [IndexCardController::class, 'active'])->name('indexCard.activate')->middleware('can:gestionar.recursos');
    Route::resource('indexCard', IndexCardController::class)->middleware('can:gestionar.recursos');

    //rutas aprendices 

    Route::put('Borrower_users/{id}/activate', [Borrower_usersController::class, 'activate'])->name('Borrower_users.activate')->middleware('can:gestionar.recursos');
    Route::resource('Borrower_users', Borrower_usersController::class)->middleware('can:gestionar.recursos');

    //rutas programas 
    Route::put('/programs/{id}/activate', [ProgramController::class, 'activate'])->name('programs.activate')->middleware('can:gestionar.recursos');
    Route::put('/programs/{id}', [ProgramController::class, 'update'])->name('programs.update')->middleware('can:gestionar.recursos');
    Route::resource('programs', ProgramController::class)->middleware('can:gestionar.recursos');

    //rutas perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    //rutas pdf

    Route::get('/pdfequipos', [EquipmentController::class, 'pdfequipos'])->name('pdfequipos');
    Route::get('/pdfservices', [PanelPrincipalController::class, 'pdfservices'])->name('pdfservices');
    Route::get('/pdfhistorico', [ServiceController::class, 'pdfhistorico'])->name('pdfhistorico');
    Route::get('/pdfPrograms', [ProgramController::class, 'pdfPrograms'])->name('pdfPrograms');
    Route::get('/pdfIndexCard', [IndexCardController::class, 'pdfIndexCard'])->name('pdfIndexCard');
    Route::get('/pdfUsuarios', [Borrower_usersController::class, 'pdfUsuarios'])->name('pdfUsuarios');
    //ruta prestamos


});

// ========================================
// RUTAS DEL PORTAL DE USUARIOS (BORROWERS)
// ========================================

// Rutas públicas de autenticación para usuarios
Route::prefix('borrower')->name('borrower.')->group(function () {
    Route::get('/login', [BorrowerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [BorrowerAuthController::class, 'login']);
    Route::get('/register', [BorrowerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [BorrowerAuthController::class, 'register']);
});

// Rutas protegidas del portal de usuarios
Route::middleware('auth:borrower')->prefix('borrower')->name('borrower.')->group(function () {
    Route::post('/logout', [BorrowerAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [BorrowerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [BorrowerDashboardController::class, 'history'])->name('history');
    Route::get('/sanctions', [BorrowerDashboardController::class, 'sanctions'])->name('sanctions');
    Route::post('/renew-loan/{service}', [BorrowerDashboardController::class, 'renewLoan'])->name('renew-loan');
    Route::get('/download-receipt/{service}', [BorrowerDashboardController::class, 'downloadReceipt'])->name('download-receipt');
});

require __DIR__ . '/auth.php';
