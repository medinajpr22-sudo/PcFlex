# 📸 Sistema de Fotografías - Documentación Optimizada

## ✅ Decisión de Diseño: Fotos en Reportes, NO en Devoluciones

### Por qué este enfoque es mejor:

1. **⚡ Eficiencia Operativa**

    - La mayoría de devoluciones son normales (sin daños)
    - No obligar a tomar foto en CADA devolución
    - Proceso de devolución más rápido

2. **📊 Contexto Relevante**

    - La foto tiene significado: muestra el daño específico
    - Se vincula directamente con la descripción del problema
    - Evidencia clara para futuros seguimientos

3. **💾 Optimización de Almacenamiento**
    - Solo se almacenan fotos cuando realmente se necesitan
    - Menos espacio en disco
    - Base de datos más limpia

## 🔄 Flujo de Trabajo

### Escenario A: Devolución Normal (Sin Problemas)

```
Usuario devuelve equipo
    ↓
Bibliotecario verifica
    ↓
Equipo está bien
    ↓
✅ Devolución registrada (SIN FOTO)
    ↓
Proceso terminado - ¡Rápido y simple!
```

### Escenario B: Devolución con Daños/Novedades

```
Usuario devuelve equipo
    ↓
Bibliotecario verifica
    ↓
⚠️ Encuentra un problema (pantalla rota, rayón, etc.)
    ↓
Sistema redirige a "Crear Reporte"
    ↓
Bibliotecario:
  1. Describe el daño
  2. 📸 Toma foto del problema
  3. Define fecha de sanción
    ↓
✅ Reporte guardado CON EVIDENCIA FOTOGRÁFICA
    ↓
Usuario queda sancionado hasta que se repare
```

## 📁 Estructura de Base de Datos

### Tabla: `disabilities` (Reportes/Novedades)

| Campo              | Tipo         | Descripción                   |
| ------------------ | ------------ | ----------------------------- |
| id                 | BIGINT       | ID único del reporte          |
| description        | VARCHAR(255) | Descripción del daño/problema |
| **photo_evidence** | VARCHAR(255) | 📸 Ruta de la foto (NULLABLE) |
| punishment_date    | TIMESTAMP    | Fecha de inicio de sanción    |
| end_date           | TIMESTAMP    | Fecha fin de sanción          |
| service_id         | BIGINT       | Préstamo relacionado          |
| status             | ENUM         | activo/inactivo               |
| created_at         | TIMESTAMP    | Fecha de creación             |
| updated_at         | TIMESTAMP    | Última actualización          |

**Importante:** El campo `photo_evidence` es **NULLABLE** (opcional).

### ❌ NO en la tabla `services`

La tabla `services` NO tiene campo de foto porque:

-   Representa el préstamo/devolución general
-   No todos los préstamos tienen problemas
-   La foto pertenece al **reporte de daño**, no al servicio en sí

## 🛠️ Implementación Técnica

### 1. Migración

```php
// database/migrations/2026_01_06_000005_add_photo_evidence_to_disabilities_table.php

Schema::table('disabilities', function (Blueprint $table) {
    $table->string('photo_evidence')->nullable()->after('description');
});
```

### 2. Modelo

```php
// app/Models/Disability.php

protected $fillable = [
    'description',
    'punishment_date',
    'end_date',
    'service_id',
    'photo_evidence'  // ← Nuevo campo
];
```

### 3. Controlador

```php
// app/Http/Controllers/reportsController.php

public function store(Request $request)
{
    $request->validate([
        'description' => 'required|string',
        'end_date' => 'required|date',
        'service_id' => 'required',
        'photo_evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Máx 5MB
    ]);

    // Guardar foto si existe
    $photoPath = null;
    if ($request->hasFile('photo_evidence')) {
        $photoPath = $request->file('photo_evidence')->store('disability_photos', 'public');
    }

    Disability::create([
        'description' => $request->input('description'),
        'end_date' => $request->input('end_date'),
        'service_id' => $request->input('service_id'),
        'photo_evidence' => $photoPath,
    ]);

    return redirect()->route('repors.index')->with('success', 'Reporte creado con éxito');
}
```

### 4. Formulario Vue (Create.vue)

```vue
<template>
    <!-- Campo de foto -->
    <div class="mb-6">
        <InputLabel
            for="photo_evidence"
            value="Fotografía del Equipo (Evidencia)"
        />
        <input
            type="file"
            @change="handleFileUpload"
            accept="image/*"
            class="w-full border rounded p-2"
        />
        <p class="text-xs text-gray-500 mt-1">
            📸 Sube una foto que muestre el daño o problema del equipo
        </p>

        <!-- Preview -->
        <div v-if="previewUrl" class="mt-3">
            <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
            <img
                :src="previewUrl"
                alt="Preview"
                class="max-w-xs rounded shadow"
            />
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const previewUrl = ref(null);

const form = useForm({
    description: "",
    service_id: service.id,
    end_date: "",
    photo_evidence: null,
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo_evidence = file;

        // Crear preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route("reports.store"), {
        forceFormData: true, // ← Importante para archivos
        onSuccess: () => {
            form.reset();
            previewUrl.value = null;
        },
    });
};
</script>
```

## 📊 Visualización de las Fotos

### En el Listado de Reportes (Index.vue)

```vue
<template>
    <div v-for="report in reports" :key="report.id" class="border rounded p-4">
        <h3 class="font-bold">{{ report.description }}</h3>
        <p class="text-sm text-gray-600">
            Sanción: {{ report.punishment_date }} - {{ report.end_date }}
        </p>

        <!-- Mostrar foto si existe -->
        <div v-if="report.photo_evidence" class="mt-3">
            <p class="text-sm font-medium text-gray-700 mb-2">
                Evidencia fotográfica:
            </p>
            <img
                :src="`/storage/${report.photo_evidence}`"
                alt="Evidencia del daño"
                class="w-48 h-48 object-cover rounded shadow cursor-pointer hover:scale-105 transition"
                @click="openLightbox(report.photo_evidence)"
            />
        </div>
    </div>
</template>
```

### En PDFs de Sanciones

```blade
<!-- resources/views/pdf/sanction-report.blade.php -->

<h2>Reporte de Sanción #{{ $disability->id }}</h2>

<p><strong>Descripción:</strong> {{ $disability->description }}</p>
<p><strong>Fecha de Sanción:</strong> {{ $disability->punishment_date }}</p>
<p><strong>Fecha de Finalización:</strong> {{ $disability->end_date }}</p>

@if($disability->photo_evidence)
    <div style="margin-top: 20px;">
        <strong>Evidencia Fotográfica:</strong><br>
        <img
            src="{{ public_path('storage/' . $disability->photo_evidence) }}"
            style="max-width: 500px; border: 2px solid #ddd; border-radius: 8px; margin-top: 10px;"
        />
    </div>
@endif
```

## 🗂️ Almacenamiento de Archivos

### Ubicación en el Servidor

```
storage/
  └── app/
      └── public/
          └── disability_photos/
              ├── abc123def456.jpg
              ├── xyz789ghi012.png
              └── ...
```

### Configuración

1. **Crear el enlace simbólico:**

```bash
php artisan storage:link
```

2. **Verificar permisos:**

```bash
chmod -R 755 storage/app/public
```

3. **Configuración en `config/filesystems.php`:**

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

## 📈 Estadísticas y Análisis

### Consultas Útiles

**Reportes con foto vs sin foto:**

```php
$withPhoto = Disability::whereNotNull('photo_evidence')->count();
$withoutPhoto = Disability::whereNull('photo_evidence')->count();
```

**Tipos de daños más comunes:**

```php
$damages = Disability::select('description')
    ->whereNotNull('photo_evidence')
    ->groupBy('description')
    ->orderByDesc(DB::raw('COUNT(*)'))
    ->get();
```

**Espacio usado por fotos:**

```php
$totalSize = Disability::whereNotNull('photo_evidence')
    ->get()
    ->sum(function($disability) {
        $path = storage_path('app/public/' . $disability->photo_evidence);
        return file_exists($path) ? filesize($path) : 0;
    });

echo "Espacio total: " . round($totalSize / 1024 / 1024, 2) . " MB";
```

## ✅ Ventajas de Este Enfoque

| Aspecto            | Beneficio                                          |
| ------------------ | -------------------------------------------------- |
| **Velocidad**      | Devoluciones normales son instantáneas             |
| **UX**             | No molesta al bibliotecario con fotos innecesarias |
| **Almacenamiento** | Solo guarda fotos relevantes                       |
| **Contexto**       | Foto tiene significado (muestra el daño)           |
| **Trazabilidad**   | Evidencia clara para disputas                      |
| **Escalabilidad**  | Menos datos = mejor rendimiento                    |

## 🚀 Mejoras Futuras

1. **Múltiples Fotos por Reporte**

    - Permitir subir hasta 3 fotos
    - Tabla pivot: `disability_photos`

2. **Compresión Automática**

    - Reducir tamaño de imágenes al subirlas
    - Usar intervention/image

3. **OCR en Fotos**

    - Extraer texto de las fotos (serie del equipo, etc.)
    - Usar Google Cloud Vision API

4. **Galería de Daños Comunes**
    - Catálogo visual de tipos de daños
    - Ayuda a bibliotecarios a identificar problemas

---

**Conclusión:** Este diseño optimizado hace que el sistema sea más rápido, eficiente y fácil de usar, mientras mantiene evidencia fotográfica donde realmente importa. 🎯
