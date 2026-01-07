<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <!-- Título con icono -->
                <div class="flex items-center space-x-3">
                    <i
                        class="fas fa-exclamation-triangle text-2xl text-yellow-500"
                    ></i>
                    <!-- Icono de alerta -->
                    <div>
                        <div class="page-pretitle text-gray-500">Reportes</div>
                        <h2 class="page-title text-gray-800">Crear Reporte</h2>
                    </div>
                </div>
            </div>
        </template>

        <!-- Información del servicio -->
        <div
            class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 mb-6 rounded-md shadow-sm"
        >
            <p class="font-bold mb-2">📋 Información del Préstamo</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="font-medium">Equipo:</span>
                    <span class="ml-2">{{
                        service.equipment?.name_equi || "N/A"
                    }}</span>
                </div>
                <div>
                    <span class="font-medium">Serie:</span>
                    <span class="ml-2">{{
                        service.equipment?.serie_equi || "N/A"
                    }}</span>
                </div>
                <div>
                    <span class="font-medium">Usuario:</span>
                    <span class="ml-2">{{
                        service.user?.name_user || "N/A"
                    }}</span>
                </div>
                <div>
                    <span class="font-medium">Fecha préstamo:</span>
                    <span class="ml-2">{{ formatDate(service.date_ser) }}</span>
                </div>
            </div>
            <div
                v-if="
                    service.user_returner_id &&
                    service.user_returner_id !== service.user_borrower_id
                "
                class="mt-3 bg-yellow-100 p-2 rounded border border-yellow-300"
            >
                <p class="text-yellow-800 text-sm">
                    ⚠️ <strong>Inconsistencia:</strong> El equipo fue devuelto
                    por una persona diferente al prestamista original.
                </p>
            </div>
        </div>

        <!-- Formulario de creación de reporte -->
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <!-- Campo: Descripción -->
            <div class="mb-6">
                <InputLabel
                    for="description"
                    value="Descripción del Problema"
                    class="font-medium text-gray-700"
                />
                <div class="relative">
                    <i
                        class="fas fa-align-left absolute left-3 top-3 text-gray-400"
                    ></i>
                    <!-- Icono de descripción -->
                    <textarea
                        v-model="form.description"
                        id="description"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.description }"
                        placeholder="Describe el daño o novedad encontrada..."
                        rows="4"
                    ></textarea>
                </div>
                <InputError
                    class="mt-1 text-sm text-red-600"
                    :message="form.errors.description"
                />
            </div>

            <!-- Campo: Fotografía del Daño -->
            <div class="mb-6">
                <InputLabel
                    for="photo_evidence"
                    value="Fotografía del Equipo (Evidencia)"
                    class="font-medium text-gray-700"
                />
                <div class="relative">
                    <i
                        class="fas fa-camera absolute left-3 top-3 text-gray-400"
                    ></i>
                    <!-- Icono de cámara -->
                    <input
                        type="file"
                        @change="handleFileUpload"
                        id="photo_evidence"
                        accept="image/*"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{
                            'border-red-500': form.errors.photo_evidence,
                        }"
                    />
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    📸 Sube una foto que muestre el daño o problema del equipo
                </p>
                <InputError
                    class="mt-1 text-sm text-red-600"
                    :message="form.errors.photo_evidence"
                />

                <!-- Preview de la imagen -->
                <div v-if="previewUrl" class="mt-3">
                    <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                    <img
                        :src="previewUrl"
                        alt="Preview"
                        class="max-w-xs rounded-lg shadow border border-gray-300"
                    />
                </div>
            </div>

            <!-- Campo oculto para el service_id -->
            <input type="hidden" v-model="form.service_id" />

            <!-- Campo: Fecha de Finalización -->
            <div class="mb-6">
                <InputLabel
                    for="end_date"
                    value="Fecha de Finalización"
                    class="font-medium text-gray-700"
                />
                <div class="relative">
                    <i
                        class="fas fa-calendar-alt absolute left-3 top-3 text-gray-400"
                    ></i>
                    <!-- Icono de calendario -->
                    <input
                        type="date"
                        v-model="form.end_date"
                        id="end_date"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.end_date }"
                    />
                </div>
                <InputError
                    class="mt-1 text-sm text-red-600"
                    :message="form.errors.end_date"
                />
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <SecondaryButton
                    @click="cancel"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700"
                >
                    <i class="fas fa-times mr-2"></i> Cancelar
                </SecondaryButton>
                <PrimaryButton
                    @click="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white"
                >
                    <i class="fas fa-save mr-2"></i> Guardar Reporte
                </PrimaryButton>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Accedemos a los props
const { service } = defineProps({
    service: Object, // Objeto del servicio con relaciones equipment y user
});

const previewUrl = ref(null);

// Función para formatear fechas
const formatDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleString("es-CO", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Inicializamos el formulario
const form = useForm({
    description: "",
    service_id: service.id, // Usamos el ID del servicio pasado desde el controlador
    end_date: "",
    photo_evidence: null,
});

// Función para manejar el upload de la foto
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

// Función para enviar el formulario
const submit = () => {
    form.post(route("reports.store"), {
        forceFormData: true, // Importante para enviar archivos
        onSuccess: () => {
            form.reset();
            previewUrl.value = null;
            showSuccessAlert("Reporte registrado con éxito");
        },
        onError: (errors) => {
            showErrorAlert("Hubo un error al registrar el reporte");
        },
    });
};

// Función para cancelar
const cancel = () => {
    form.reset();
};

// Función para mostrar alerta de éxito
const showSuccessAlert = (message) => {
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: message,
        showConfirmButton: false,
        timer: 1500,
        toast: true,
    });
};

// Función para mostrar alerta de error
const showErrorAlert = (message) => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message,
    });
};
</script>
