<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import NavLink from "@/Components/NavLink.vue";
import CreateButton from "@/Components/CreateButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import EditButton from "@/Components/EditButton.vue";
import ShowButton from "@/Components/ShowButton.vue";
import GreenButton from "@/Components/GreenButton.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const selectedImage = ref(null);
const showImageModal = ref(false);

const openImageModal = (imagePath) => {
    selectedImage.value = imagePath;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    selectedImage.value = null;
};

const props = defineProps({
    repors: Object,
});

const v = ref({ id: "" });
const showModalDel = ref(false);

const form = useForm({
    names: "",
});

const openModalDel = (repor) => {
    showModalDel.value = true;
    v.value = { ...repor };
};

const closeModalDel = () => {
    showModalDel.value = false;
};

const deleteReport = () => {
    form.delete(route("reports.destroy", v.value.id), {
        onSuccess: () => {
            closeModalDel();
            showSuccessAlert("Reporte Inactivado con éxito");
        },
        onError: (errors) => {
            closeModalDel();
            showErrorAlert(errors.error);
        },
    });
};

const activateRepor = (repor) => {
    form.put(route("reports.activate", repor.id), {
        onSuccess: () => {
            showSuccessAlert("Reporte Activado con éxito");
        },
        onError: (errors) => {
            showErrorAlert(errors.error);
        },
    });
};

const showSuccessAlert = (message) => {
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: message,
        showConfirmButton: false,
        timer: 8000,
        toast: true,
    });
};

const showErrorAlert = (message) => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message,
    });
};
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <!-- Header con gradiente -->
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-3 mr-4"
                        >
                            <i class="fas fa-flag text-3xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Reportes de Incidencias
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Gestión y seguimiento de reportes del sistema
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div
            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-xl p-6"
        >
            <div
                class="overflow-hidden rounded-xl border border-gray-200 shadow-lg bg-white"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white"
                            >
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        Usuario
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-laptop mr-2"></i>
                                        Equipo
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Descripción
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-camera mr-2"></i>
                                        Evidencia
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Fecha Inicio
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i
                                            class="fas fa-calendar-check mr-2"
                                        ></i>
                                        Fecha Fin
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-toggle-on mr-2"></i>
                                        Estado
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="repor in repors.data"
                                :key="repor.id"
                                class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3"
                                        >
                                            <i
                                                class="fas fa-user text-white"
                                            ></i>
                                        </div>
                                        <div class="text-sm">
                                            <div
                                                class="font-semibold text-gray-900"
                                            >
                                                {{
                                                    repor.service?.users
                                                        ?.name_user
                                                }}
                                                {{
                                                    repor.service?.users
                                                        ?.lastname_user
                                                }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{
                                                    repor.service?.users
                                                        ?.number_identification
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div
                                            class="font-semibold text-gray-900"
                                        >
                                            {{
                                                repor.service?.equipment
                                                    ?.serie_equ
                                            }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                repor.service?.equipment
                                                    ?.brand_equ
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm max-w-xs">
                                    <div
                                        class="truncate"
                                        :title="repor.description"
                                    >
                                        {{ repor.description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        v-if="repor.photo_evidence"
                                        class="flex items-center justify-center"
                                    >
                                        <img
                                            :src="`/storage/${repor.photo_evidence}`"
                                            alt="Evidencia"
                                            class="w-16 h-16 object-cover rounded border border-gray-300 cursor-pointer hover:scale-110 transition-transform shadow-sm"
                                            @click="
                                                openImageModal(
                                                    repor.photo_evidence
                                                )
                                            "
                                        />
                                    </div>
                                    <div
                                        v-else
                                        class="text-center text-xs text-gray-400"
                                    >
                                        Sin foto
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{
                                        new Date(
                                            repor.punishment_date
                                        ).toLocaleDateString("es-ES")
                                    }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{
                                        new Date(
                                            repor.end_date
                                        ).toLocaleDateString("es-ES")
                                    }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800':
                                                repor.status === 'activo',
                                            'bg-gray-100 text-gray-800':
                                                repor.status === 'inactivo',
                                        }"
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{
                                            repor.status === "activo"
                                                ? "✓ Activo"
                                                : "○ Inactivo"
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <template v-if="repor.status === 'activo'">
                                        <DeleteButton
                                            @click="openModalDel(repor)"
                                            >Inactivar</DeleteButton
                                        >
                                    </template>
                                    <template v-else>
                                        <GreenButton
                                            @click="activateRepor(repor)"
                                            >Reactivar</GreenButton
                                        >
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
                >
                    <Pagination :links="repors.links" />
                </div>
            </div>
        </div>

        <Modal :show="showModalDel" @close="closeModalDel">
            <div class="p-6 bg-white rounded-lg shadow-lg">
                <!-- Encabezado del modal con icono -->
                <div class="flex items-center space-x-3 mb-4">
                    <i
                        class="fas fa-exclamation-circle text-2xl text-yellow-500"
                    ></i>
                    <!-- Icono de advertencia -->
                    <h1 class="text-lg font-semibold text-gray-800">
                        ¿Estás seguro de realizar esta acción?
                    </h1>
                </div>

                <!-- Mensaje del modal -->
                <p class="text-gray-600 mb-6">
                    Esta acción no se puede deshacer. ¿Deseas continuar?
                </p>

                <!-- Botones de acción -->
                <div
                    class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4"
                >
                    <SecondaryButton
                        @click="closeModalDel"
                        class="w-full sm:w-auto"
                    >
                        <i class="fas fa-times mr-2"></i>
                        <!-- Icono de cancelar -->
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteReport"
                        class="w-full sm:w-auto"
                    >
                        <i class="fas fa-check mr-2"></i>
                        <!-- Icono de confirmar -->
                        Sí, seguro
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Modal para ver imagen en tamaño completo -->
        <Modal :show="showImageModal" @close="closeImageModal" maxWidth="4xl">
            <div class="p-4 bg-white rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        📸 Evidencia Fotográfica
                    </h3>
                    <button
                        @click="closeImageModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex justify-center">
                    <img
                        v-if="selectedImage"
                        :src="`/storage/${selectedImage}`"
                        alt="Evidencia completa"
                        class="max-w-full max-h-[80vh] rounded shadow-lg"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
