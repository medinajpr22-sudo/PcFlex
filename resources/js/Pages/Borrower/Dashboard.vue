<script setup>
import BorrowerLayout from "@/Layouts/BorrowerLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    user: Object,
    activeLoans: Array,
    activeSanctions: Array,
    pendingReservations: Array,
});

const renewForm = useForm({});

const renewLoan = (serviceId) => {
    renewForm.post(route("borrower.renew-loan", serviceId), {
        preserveScroll: true,
    });
};

const getTimeRemaining = (expectedReturnDate) => {
    if (!expectedReturnDate) return "N/A";

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHours = Math.floor(diffMs / 1000 / 60 / 60);
    const diffMins = Math.floor((diffMs / 1000 / 60) % 60);

    if (diffMs < 0) {
        const absDiffHours = Math.abs(diffHours);
        return `Vencido hace ${absDiffHours}h`;
    }

    if (diffHours < 1) {
        return `${diffMins} min`;
    }

    return `${diffHours}h ${diffMins}m`;
};

const getTimeRemainingClass = (expectedReturnDate) => {
    if (!expectedReturnDate) return "";

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHours = Math.floor(diffMs / 1000 / 60 / 60);

    if (diffMs < 0) return "bg-red-100 text-red-800 border-red-200";
    if (diffHours <= 1) return "bg-red-100 text-red-800 border-red-200";
    if (diffHours <= 3)
        return "bg-yellow-100 text-yellow-800 border-yellow-200";
    return "bg-green-100 text-green-800 border-green-200";
};

const canRenew = (expectedReturnDate) => {
    if (!expectedReturnDate) return false;

    const now = new Date();
    const returnDate = new Date(expectedReturnDate);
    const diffMs = returnDate - now;
    const diffHours = Math.floor(diffMs / 1000 / 60 / 60);

    return diffHours >= 0 && diffHours <= 2;
};

const hasActiveSanctions = computed(() => props.activeSanctions.length > 0);
</script>

<template>
    <Head title="Mi Dashboard" />

    <BorrowerLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bienvenido, {{ user.name_user }} {{ user.lastname_user }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alert si tiene sanciones -->
                <div
                    v-if="hasActiveSanctions"
                    class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded"
                    role="alert"
                >
                    <p class="font-bold">⚠️ Tienes sanciones activas</p>
                    <p>
                        Tienes {{ activeSanctions.length }} sanción(es)
                        activa(s). No podrás realizar nuevos préstamos hasta
                        resolverlas.
                    </p>
                    <Link
                        :href="route('borrower.sanctions')"
                        class="underline font-semibold"
                        >Ver mis sanciones</Link
                    >
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 bg-blue-500 rounded-md p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <div
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Préstamos Activos
                                    </div>
                                    <div
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ activeLoans.length }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 bg-yellow-500 rounded-md p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <div
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Reservas Pendientes
                                    </div>
                                    <div
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ pendingReservations.length }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div
                                    :class="
                                        hasActiveSanctions
                                            ? 'bg-red-500'
                                            : 'bg-green-500'
                                    "
                                    class="flex-shrink-0 rounded-md p-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <div
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Sanciones Activas
                                    </div>
                                    <div
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ activeSanctions.length }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Préstamos Activos -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            📚 Mis Préstamos Activos
                        </h3>

                        <div
                            v-if="activeLoans.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            No tienes préstamos activos en este momento.
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Equipo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Ambiente
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Fecha Préstamo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Tiempo Restante
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="loan in activeLoans"
                                        :key="loan.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ loan.equipment.serie_equ }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ loan.equipment.brand_equ }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{ loan.environment.name_env }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                new Date(
                                                    loan.date_ser
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="
                                                    getTimeRemainingClass(
                                                        loan.expected_return_date
                                                    )
                                                "
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border"
                                            >
                                                {{
                                                    getTimeRemaining(
                                                        loan.expected_return_date
                                                    )
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <button
                                                v-if="
                                                    canRenew(
                                                        loan.expected_return_date
                                                    )
                                                "
                                                @click="renewLoan(loan.id)"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium"
                                                :disabled="renewForm.processing"
                                            >
                                                Renovar
                                            </button>
                                            <span v-else class="text-gray-400">
                                                {{
                                                    new Date(
                                                        loan.expected_return_date
                                                    ) < new Date()
                                                        ? "Vencido"
                                                        : "No disponible"
                                                }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reservas Pendientes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            🔖 Mis Reservas
                        </h3>

                        <div
                            v-if="pendingReservations.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            No tienes reservas pendientes.
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="reservation in pendingReservations"
                                :key="reservation.id"
                                class="border rounded-lg p-4"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{
                                                reservation.equipment.serie_equ
                                            }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{
                                                reservation.equipment.brand_equ
                                            }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Reservado:
                                            {{
                                                new Date(
                                                    reservation.created_at
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </p>
                                    </div>
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800':
                                                reservation.status ===
                                                'pendiente',
                                            'bg-green-100 text-green-800':
                                                reservation.status ===
                                                'aprobada',
                                            'bg-red-100 text-red-800':
                                                reservation.status ===
                                                'rechazada',
                                        }"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{
                                            reservation.status === "pendiente"
                                                ? "Pendiente"
                                                : reservation.status ===
                                                  "aprobada"
                                                ? "Aprobada"
                                                : "Rechazada"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BorrowerLayout>
</template>
