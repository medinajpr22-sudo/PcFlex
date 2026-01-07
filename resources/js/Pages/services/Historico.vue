<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import NavLink from "@/Components/NavLink.vue";
import CreateButton from "@/Components/CreateButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import EditButton from "@/Components/EditButton.vue";
import ShowButton from "@/Components/ShowButton.vue";
import ReactivateButton from "@/Components/ReactivateButton.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { useForm } from "@inertiajs/vue3";
import SearchHistorico from "@/Components/SearchHistorico.vue";

const props = defineProps({
    services: Object,
});

const showModalDel = ref(false);
const userToDelete = ref(null);

const openModalDel = (user) => {
    showModalDel.value = true;
    userToDelete.value = user;
};

const closeModalDel = () => {
    console.log("Cerrando modal");
    showModalDel.value = false;
    userToDelete.value = null;
};

const downloadPdf = () => {
    window.location.href = route("pdfhistorico");
};

const searchTerm = ref("");
const filteredservices = computed(() => {
    if (!searchTerm.value) {
        return props.services.data;
    }
    const lowerCaseSearchTerm = searchTerm.value.toLowerCase();
    return props.services.data.filter((service) => {
        return (
            service.equipment.serie_equi
                .toLowerCase()
                .includes(lowerCaseSearchTerm) ||
            service.users.number_identification
                .toLowerCase()
                .includes(lowerCaseSearchTerm)
        );
    });
});
</script>

<template>
    <Head title="Historial" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6"
            >
                <h2 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-history mr-3"></i>Historial de Préstamos
                </h2>
                <p class="text-indigo-100 text-sm">
                    Registro completo de préstamos de equipos
                </p>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6"
            >
                <div class="flex flex-wrap gap-3">
                    <button
                        @click="downloadPdf"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200"
                    >
                        <i class="fas fa-file-pdf mr-2"></i>
                        Exportar PDF
                    </button>
                    <NavLink :href="route('dashboard')">
                        <button
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-lg shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-200"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver
                        </button>
                    </NavLink>
                </div>
                <div class="w-full sm:w-auto">
                    <SearchHistorico
                        v-model:search="searchTerm"
                        @search="handleSearch"
                    />
                </div>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white"
                        >
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-calendar-alt mr-2"></i>Fecha
                                Préstamo
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-calendar-check mr-2"></i>Fecha
                                Devolución
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-barcode mr-2"></i>Serie Equipo
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-id-card mr-2"></i>Documento
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-info-circle mr-2"></i>Estado
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                            >
                                <i class="fas fa-cog mr-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="service in filteredservices"
                            :key="service.id"
                            class="hover:bg-indigo-50 transition-colors duration-150"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <i
                                    class="fas fa-calendar text-indigo-400 mr-2"
                                ></i>
                                {{ service.date_ser }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <i
                                    class="fas fa-calendar text-purple-400 mr-2"
                                ></i>
                                {{ service.return_ser }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                            >
                                {{ service.equipment.serie_equi }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ service.users.number_identification }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    v-if="service.status === 'devuelto'"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-md"
                                >
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Devuelto
                                </span>
                                <span
                                    v-else-if="service.status === 'pendiente'"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-md"
                                >
                                    <i class="fas fa-clock mr-1"></i>
                                    Pendiente
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md"
                                >
                                    {{ service.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <NavLink
                                    :href="route('info.details', service.id)"
                                >
                                    <ShowButton> Ver Detalles </ShowButton>
                                </NavLink>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-t border-indigo-100"
            >
                <Pagination :links="services.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
