<?php

namespace App\Http\Controllers;

use App\Models\Borrower_users;
use App\Models\Disability;
use App\Models\Equipment;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class reportsController extends Controller
{
    public function index()
    {
        return Inertia::render('reports/Index', [
            'repors' => Disability::with(['service.equipment', 'service.users'])
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
    public function create($service_id)
    {
       $service = Services::with(['equipment', 'users'])
           ->find($service_id);
           
       if (!$service) {
           return redirect()->back()->withErrors(['error' => 'Servicio no encontrado']);
       }

       return Inertia::render('reports/Create', [
           'service' => $service,
       ]);
    }
    
    public function store(Request $request)
    { 
        $currentDate = Carbon::now()->startOfDay();
       
        // Validación de fecha
        if (Carbon::parse($request->input('end_date'))->startOfDay()->lessThan($currentDate)) {
            return redirect()->back()->withErrors(['end_date' => 'La fecha debe ser posterior a la actual']);
        }
        
        // Validar datos
        $validated = $request->validate([
            'description' => 'required|string|min:10|max:500',
            'end_date' => 'required|date|after:today',
            'service_id' => 'required|exists:services,id',
            'photo_evidence' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB máximo
        ], [
            'description.required' => 'La descripción del problema es obligatoria',
            'description.min' => 'La descripción debe tener al menos 10 caracteres',
            'end_date.required' => 'La fecha de finalización es obligatoria',
            'end_date.after' => 'La fecha debe ser posterior a hoy',
            'photo_evidence.image' => 'El archivo debe ser una imagen',
            'photo_evidence.max' => 'La imagen no debe superar 5MB',
        ]);

        $service = Services::find($validated['service_id']);

        if (!$service) {
            return redirect()->back()->withErrors(['error' => 'Servicio no encontrado']);
        }
        
        // Manejar upload de foto si existe
        $photoPath = null;
        if ($request->hasFile('photo_evidence')) {
            $photoPath = $request->file('photo_evidence')->store('disability_photos', 'public');
        }
        
        // Crear reporte
        $disability = Disability::create([
            'description' => $validated['description'],
            'end_date' => $validated['end_date'],
            'service_id' => $validated['service_id'],
            'photo_evidence' => $photoPath,
            'punishment_date' => Carbon::now(),
        ]);

        // Actualizar estado del usuario a 'reportado'
        $borrower = Borrower_users::find($service->user_borrower_id);
        if ($borrower) {
            $borrower->status = 'reportado';
            $borrower->save();
        }

        return redirect()->route('reports.index')->with(['success' => 'Reporte creado con éxito. El usuario ha sido sancionado.']);
    }
    
    public function destroy(string $id) {

        $report = Disability::findOrFail($id);

        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Reporte no encontrado']);
        }
        $report->status = 'inactivo';
        $report->save();
        return redirect('repors');

    }
    public function activate(string $id)
    {
        $report = Disability::find($id);

        if (!$report) {
            return redirect()->back()->withErrors(['error' => 'Reporte no encontrado']);
        }
        $report->status = 'activo';
        $report->save();
        return redirect('repors');
    }


}
