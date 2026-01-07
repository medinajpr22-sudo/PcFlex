<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <!-- Título con icono (centro) -->
                <div class="flex items-center space-x-3">
                    <i class="fas fa-tachometer-alt text-2xl text-blue-500"></i>
                    <!-- Icono de dashboard -->
                    <div>
                        <div class="page-pretitle text-gray-500">
                            Panel Principal
                        </div>
                        <h2 class="page-title text-gray-800">PcFlex</h2>
                    </div>
                </div>

                <!-- Botones de acciones -->
                <div class="flex items-center space-x-4">
                    <GreenButton @click="openModalForm(1)">
                        <i class="fas fa-hand-holding mr-2"></i>
                        Registrar Préstamo
                    </GreenButton>
                    <DangerButton @click="openModalForm(2)">
                        <i class="fas fa-undo-alt mr-2"></i>
                        Registrar Devolución
                    </DangerButton>
                    <button
                        @click="downloadPdf"
                        class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <i class="fas fa-file-pdf mr-2"></i>
                        PDF
                    </button>
                    <SearchService
                        v-model:search="searchTerm"
                        @search="handleSearch"
                    />
                </div>
            </div>
        </template>

        <!-- Cards de Resumen Rápido -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard
                title="Préstamos Activos"
                :value="stats ? stats.activos : 0"
                icon="fas fa-laptop"
                color="blue"
                subtitle="En este momento"
            >
                <template #footer>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                        Hoy: <strong>{{ stats ? stats.hoy : 0 }}</strong>
                    </div>
                </template>
            </StatCard>

            <StatCard
                title="Próximos a Vencer"
                :value="stats ? stats.proximosVencer : 0"
                icon="fas fa-clock"
                color="yellow"
                subtitle="Menos de 2 horas"
            />

            <StatCard
                title="Vencidos"
                :value="stats ? stats.vencidos : 0"
                icon="fas fa-exclamation-triangle"
                color="red"
                subtitle="Requieren atención"
            />

            <StatCard
                title="Este Mes"
                :value="stats ? stats.esteMes : 0"
                icon="fas fa-chart-line"
                color="green"
                subtitle="Total de préstamos"
            />
        </div>

        <!-- Alertas de préstamos vencidos y próximos a vencer -->
        <div
            v-if="stats && (stats.vencidos > 0 || stats.proximosVencer > 0)"
            class="mb-6 space-y-3"
        >
            <!-- Alerta de vencidos -->
            <div
                v-if="stats.vencidos > 0"
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md flex items-center shadow-md"
            >
                <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-bold">
                        ¡Atención! {{ stats.vencidos }} préstamo{{
                            stats.vencidos > 1 ? "s" : ""
                        }}
                        vencido{{ stats.vencidos > 1 ? "s" : "" }}
                    </p>
                    <p class="text-sm">
                        Hay equipos que no han sido devueltos a tiempo.
                    </p>
                </div>
            </div>

            <!-- Alerta de próximos a vencer -->
            <div
                v-if="stats.proximosVencer > 0"
                class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md flex items-center shadow-md"
            >
                <i class="fas fa-clock text-2xl mr-3"></i>
                <div>
                    <p class="font-bold">
                        {{ stats.proximosVencer }} préstamo{{
                            stats.proximosVencer > 1 ? "s" : ""
                        }}
                        próximo{{ stats.proximosVencer > 1 ? "s" : "" }} a
                        vencer
                    </p>
                    <p class="text-sm">
                        Recuerda contactar a los usuarios para recordarles la
                        devolución.
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
            <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b"
                        >
                            <th class="px-4 py-3">Fecha prestamo</th>
                            <th class="px-4 py-3">Numero documento</th>
                            <th class="px-4 py-3">Numero de Serie</th>
                            <th class="px-4 py-3">Ambientes</th>
                            <th class="px-4 py-3">Tiempo Restante</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <tr
                            v-for="service in filteredservices"
                            :key="service.id"
                            class="text-gray-700"
                        >
                            <td class="px-4 py-3 text-sm">
                                {{ service.date_ser }}
                            </td>
                            <th class="px-4 py-3 text-sm">
                                {{ service.users.number_identification }}
                            </th>
                            <td class="px-4 py-3 text-sm">
                                {{ service.equipment.serie_equi }}
                            </td>
                            <th class="px-4 py-3 text-sm">
                                {{ service.environment.names }}
                            </th>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    v-if="service.expected_return_date"
                                    :class="
                                        getTimeRemainingClass(
                                            service.expected_return_date
                                        )
                                    "
                                    class="px-2 py-1 rounded-full text-xs font-semibold"
                                >
                                    <i
                                        :class="
                                            getTimeRemainingIcon(
                                                service.expected_return_date
                                            )
                                        "
                                        class="mr-1"
                                    ></i>
                                    {{
                                        getTimeRemaining(
                                            service.expected_return_date
                                        )
                                    }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    :class="{
                                        'text-red-500':
                                            service.status === 'pendiente',
                                    }"
                                >
                                    {{ service.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <NavLink
                                    :href="route('detalles.show', service.id)"
                                >
                                    <ShowButton>Info</ShowButton>
                                </NavLink>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
            >
                <Pagination :links="services.links" />
            </div>
        </div>

        <Modal :show="showModalForm" @close="closeModalForm">
            <div class="p-8 space-y-6 bg-white rounded-lg">
                <!-- Encabezado del modal con icono -->
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exchange-alt text-2xl text-blue-600"></i>
                    <!-- Icono de intercambio -->
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ title }}
                    </h2>
                </div>

                <!-- Campo: Número de Documento -->
                <div class="space-y-2">
                    <InputLabel
                        for="number"
                        value="Número de Documento"
                        class="font-medium text-gray-700"
                    />
                    <div class="relative">
                        <i
                            class="fas fa-id-card absolute left-3 top-3 text-gray-400"
                        ></i>
                        <!-- Icono de documento -->
                        <TextInput
                            v-model="form.user_borrower_id"
                            id="number"
                            required
                            placeholder="Ingrese el número de documento"
                            class="w-full pl-10"
                        />
                    </div>
                    <InputError
                        class="mt-1"
                        :message="form.errors.user_borrower_id"
                    />
                </div>

                <!-- Campo: Número de Serie -->
                <div class="space-y-2">
                    <InputLabel
                        for="serie"
                        value="Número de Serie"
                        class="font-medium text-gray-700"
                    />
                    <div class="relative">
                        <i
                            class="fas fa-barcode absolute left-3 top-3 text-gray-400"
                        ></i>
                        <!-- Icono de código de barras -->
                        <TextInput
                            v-model="form.equipment_id"
                            id="serie"
                            required
                            placeholder="Ingrese el número de serie"
                            class="w-full pl-10"
                        />
                    </div>
                    <InputError
                        class="mt-1"
                        :message="form.errors.equipment_id"
                    />
                </div>

                <!-- Campo: Ambiente a Trasladar -->
                <div class="space-y-2">
                    <InputLabel
                        for="environment_id"
                        value="Ambiente a Trasladar"
                        class="font-medium text-gray-700"
                    />
                    <div class="relative">
                        <i
                            class="fas fa-door-open absolute left-3 top-3 text-gray-400"
                        ></i>
                        <!-- Icono de puerta -->
                        <select
                            v-model="form.environment_id"
                            id="environment_id"
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Seleccione un ambiente</option>
                            <option
                                v-for="environment in environments"
                                :key="environment.id"
                                :value="environment.id"
                            >
                                {{ environment.names }}
                            </option>
                        </select>
                    </div>
                    <InputError
                        class="mt-1"
                        :message="form.errors.environment_id"
                    />
                </div>

                <!-- Botones de acción -->
                <div
                    class="flex justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <SecondaryButton
                        @click="closeModalForm"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700"
                    >
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </SecondaryButton>
                    <EditButton
                        @click="save"
                        class="bg-blue-600 hover:bg-blue-700 text-white"
                    >
                        <i class="fas fa-save mr-2"></i> Guardar
                    </EditButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showModaldevolution" @close="closeModalForm">
            <div class="p-6 space-y-4 bg-white rounded-lg shadow-lg">
                <!-- Título con icono -->
                <div class="flex items-center space-x-2">
                    <i class="fas fa-undo-alt text-blue-500 text-xl"></i>
                    <!-- Icono de devolución -->
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ title }}
                    </h2>
                </div>

                <!-- Campo para el número de documento -->
                <div class="space-y-2">
                    <InputLabel
                        for="number"
                        value="Número de Documento"
                        class="text-gray-700"
                    />
                    <div class="relative">
                        <i
                            class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                        ></i>
                        <!-- Icono de documento -->
                        <TextInput
                            v-model="form.user_returner_id"
                            required
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <InputError
                        class="mt-1 text-sm text-red-600"
                        :message="form.errors.user_returner_id"
                    />
                </div>

                <!-- Campo para el número de serie -->
                <div class="space-y-2">
                    <InputLabel
                        for="serial"
                        value="Número de Serie"
                        class="text-gray-700"
                    />
                    <div class="relative">
                        <i
                            class="fas fa-barcode absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                        ></i>
                        <!-- Icono de serie -->
                        <TextInput
                            v-model="form.equipment_id"
                            required
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <InputError
                        class="mt-1 text-sm text-red-600"
                        :message="form.errors.equipment_id"
                    />
                </div>

                <!-- Botones de acción con iconos -->
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton
                        @click="closeModalForm"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center space-x-2"
                    >
                        <i class="fas fa-times"></i>
                        <!-- Icono de cancelar -->
                        <span>Cancelar</span>
                    </SecondaryButton>
                    <EditButton @click="save">
                        <span>Guardar</span>
                    </EditButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { defineProps } from "vue";
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatCard from "@/Components/StatCard.vue";
import GreenButton from "@/Components/GreenButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Pagination from "@/Components/Pagination.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NavLink from "@/Components/NavLink.vue";
import ShowButton from "@/Components/ShowButton.vue";
import CreateButton from "@/Components/CreateButton.vue";
import EditButton from "@/Components/EditButton.vue";
import SearchService from "@/Components/SearchService.vue";

const showModalForm = ref(false);
const showModaldevolution = ref(false);
const form = useForm({
    equipment_id: "",
    user_borrower_id: "",
    user_returner_id: "",
    environment_id: "",
});

const props = defineProps({
    services: {
        type: Object,
        required: true,
    },
    environments: Array,
    stats: Object,
});

const title = ref("");
const operation = ref(1);
const downloadPdf = () => {
    window.location.href = route("pdfservices");
};

// Función para calcular el tiempo restante
const getTimeRemaining = (expectedReturnDate) => {
    if (!expectedReturnDate) return "-";

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    if (diffHrs < 0) {
        const overdueHrs = Math.abs(diffHrs);
        return `Vencido ${overdueHrs}h`;
    } else if (diffHrs === 0) {
        return `${diffMins}m`;
    } else {
        return `${diffHrs}h ${diffMins}m`;
    }
};

// Función para obtener la clase CSS según el tiempo restante
const getTimeRemainingClass = (expectedReturnDate) => {
    if (!expectedReturnDate) return "bg-gray-200 text-gray-600";

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHrs = diffMs / (1000 * 60 * 60);

    if (diffHrs < 0) {
        // Vencido
        return "bg-red-100 text-red-800";
    } else if (diffHrs <= 1) {
        // Menos de 1 hora
        return "bg-orange-100 text-orange-800";
    } else if (diffHrs <= 3) {
        // Entre 1 y 3 horas
        return "bg-yellow-100 text-yellow-800";
    } else {
        // Más de 3 horas
        return "bg-green-100 text-green-800";
    }
};

// Función para obtener el icono según el tiempo restante
const getTimeRemainingIcon = (expectedReturnDate) => {
    if (!expectedReturnDate) return "fas fa-clock";

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHrs = diffMs / (1000 * 60 * 60);

    if (diffHrs < 0) {
        return "fas fa-exclamation-triangle";
    } else if (diffHrs <= 1) {
        return "fas fa-exclamation-circle";
    } else {
        return "fas fa-clock";
    }
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
                .includes(lowerCaseSearchTerm) ||
            service.environment.names
                .toLowerCase()
                .includes(lowerCaseSearchTerm)
        );
    });
});

const openModalForm = (op) => {
    operation.value = op;
    if (op === 1) {
        showModalForm.value = true;
        title.value = "Registrar Prestamo";
    } else {
        showModaldevolution.value = true;
        title.value = "Registrar Devolucion";
    }
};

const closeModalForm = () => {
    showModalForm.value = false;
    showModaldevolution.value = false;
    form.reset();
    form.clearErrors();
};

const save = () => {
    if (operation.value === 1) {
        form.post(route("prestamos.store"), {
            onSuccess: () => {
                showSuccessAlert("Préstamo registrado con éxito");
                closeModalForm();
            },
            onError: (errors) => showErrorAlert(errors.error),
        });
    } else {
        form.put(route("devolucion.resivir"), {
            onSuccess: () => {
                showSuccessAlert("Devolución registrada con éxito");
                closeModalForm();
            },
            onError: (errors) => showErrorAlert(errors.error),
        });
    }
};

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

const showErrorAlert = (message) => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message,
    });
};
</script>
