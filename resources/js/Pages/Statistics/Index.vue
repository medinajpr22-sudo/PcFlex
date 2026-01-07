<template>
    <Head title="Estadísticas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <i class="fas fa-chart-line text-2xl text-purple-500"></i>
                <div>
                    <div class="page-pretitle text-gray-500">Análisis</div>
                    <h2 class="page-title text-gray-800">
                        Estadísticas del Sistema
                    </h2>
                </div>
            </div>
        </template>

        <!-- Cards de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card: Total Equipos -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80 uppercase tracking-wide">
                            Total Equipos
                        </p>
                        <h3 class="text-4xl font-bold mt-2">
                            {{ summary.equipments.total }}
                        </h3>
                        <div class="mt-3 space-y-1 text-sm">
                            <p>
                                <i class="fas fa-check-circle mr-2"></i
                                >{{ summary.equipments.available }} Disponibles
                            </p>
                            <p>
                                <i class="fas fa-hand-holding mr-2"></i
                                >{{ summary.equipments.onLoan }} Prestados
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-laptop text-6xl opacity-20"></i>
                </div>
            </div>

            <!-- Card: Préstamos -->
            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80 uppercase tracking-wide">
                            Préstamos
                        </p>
                        <h3 class="text-4xl font-bold mt-2">
                            {{ summary.loans.total }}
                        </h3>
                        <div class="mt-3 space-y-1 text-sm">
                            <p>
                                <i class="fas fa-clock mr-2"></i
                                >{{ summary.loans.active }} Activos
                            </p>
                            <p>
                                <i class="fas fa-calendar-day mr-2"></i
                                >{{ summary.loans.today }} Hoy
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-exchange-alt text-6xl opacity-20"></i>
                </div>
            </div>

            <!-- Card: Usuarios -->
            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80 uppercase tracking-wide">
                            Usuarios
                        </p>
                        <h3 class="text-4xl font-bold mt-2">
                            {{ summary.users.total }}
                        </h3>
                        <div class="mt-3 space-y-1 text-sm">
                            <p>
                                <i class="fas fa-user-check mr-2"></i
                                >{{ summary.users.active }} Activos
                            </p>
                            <p>
                                <i class="fas fa-user-slash mr-2"></i
                                >{{ summary.users.reported }} Sancionados
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-users text-6xl opacity-20"></i>
                </div>
            </div>

            <!-- Card: Reportes -->
            <div
                class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80 uppercase tracking-wide">
                            Reportes
                        </p>
                        <h3 class="text-4xl font-bold mt-2">
                            {{ summary.reports.total }}
                        </h3>
                        <div class="mt-3 space-y-1 text-sm">
                            <p>
                                <i class="fas fa-exclamation-triangle mr-2"></i
                                >{{ summary.reports.active }} Activos
                            </p>
                            <p>
                                <i class="fas fa-check mr-2"></i
                                >{{ summary.reports.inactive }} Resueltos
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-file-alt text-6xl opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Métricas Adicionales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Tasa de Devolución a Tiempo -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        <i class="fas fa-award text-yellow-500 mr-2"></i>
                        Tasa de Devolución a Tiempo
                    </h3>
                    <span
                        class="text-3xl font-bold"
                        :class="getOnTimeRateClass(summary.metrics.onTimeRate)"
                    >
                        {{ summary.metrics.onTimeRate }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div
                        class="h-4 rounded-full transition-all duration-500"
                        :class="getOnTimeRateColor(summary.metrics.onTimeRate)"
                        :style="{ width: summary.metrics.onTimeRate + '%' }"
                    ></div>
                </div>
            </div>

            <!-- Duración Promedio -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">
                            <i
                                class="fas fa-stopwatch text-indigo-500 mr-2"
                            ></i>
                            Duración Promedio de Préstamos
                        </h3>
                        <p class="text-sm text-gray-500">
                            Tiempo promedio que los equipos permanecen prestados
                        </p>
                    </div>
                    <div class="text-center">
                        <span class="text-4xl font-bold text-indigo-600">{{
                            summary.metrics.avgDuration
                        }}</span>
                        <p class="text-sm text-gray-500">horas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Préstamos por Mes -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                    Préstamos por Mes (Últimos 6 meses)
                </h3>
                <Bar :data="loansByMonthData" :options="barChartOptions" />
            </div>

            <!-- Distribución de Equipos -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                    Estado de Equipos
                </h3>
                <Doughnut
                    :data="equipmentStatusData"
                    :options="doughnutChartOptions"
                />
            </div>
        </div>

        <!-- Top 10 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Equipos Más Prestados -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Top 10 Equipos Más Prestados
                </h3>
                <div class="space-y-3">
                    <div
                        v-for="(
                            equipment, index
                        ) in charts.mostLoanedEquipments"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                    >
                        <div class="flex items-center space-x-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm"
                            >
                                {{ index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ equipment.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ equipment.serie }} - {{ equipment.type }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold"
                        >
                            {{ equipment.loans }} préstamos
                        </span>
                    </div>
                </div>
            </div>

            <!-- Usuarios Más Activos -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-star text-purple-500 mr-2"></i>
                    Top 10 Usuarios Más Activos
                </h3>
                <div class="space-y-3">
                    <div
                        v-for="(user, index) in charts.mostActiveUsers"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                    >
                        <div class="flex items-center space-x-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold text-sm"
                            >
                                {{ index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ user.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ user.identification }} - {{ user.role }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold"
                        >
                            {{ user.loans }} préstamos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Adicionales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Préstamos por Tipo de Equipo -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-laptop-code text-indigo-500 mr-2"></i>
                    Préstamos por Tipo de Equipo
                </h3>
                <Pie
                    :data="loansByEquipmentTypeData"
                    :options="pieChartOptions"
                />
            </div>

            <!-- Préstamos por Rol de Usuario -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    <i class="fas fa-user-tag text-teal-500 mr-2"></i>
                    Préstamos por Rol de Usuario
                </h3>
                <Pie :data="loansByUserRoleData" :options="pieChartOptions" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Bar, Doughnut, Pie } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
} from "chart.js";

// Registrar componentes de Chart.js
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement
);

const props = defineProps({
    summary: Object,
    charts: Object,
});

// Función para obtener clase de color según tasa
const getOnTimeRateClass = (rate) => {
    if (rate >= 80) return "text-green-600";
    if (rate >= 60) return "text-yellow-600";
    return "text-red-600";
};

const getOnTimeRateColor = (rate) => {
    if (rate >= 80) return "bg-green-500";
    if (rate >= 60) return "bg-yellow-500";
    return "bg-red-500";
};

// Datos para gráfico de préstamos por mes
const loansByMonthData = computed(() => ({
    labels: props.charts.loansByMonth.map((item) => item.month),
    datasets: [
        {
            label: "Préstamos",
            data: props.charts.loansByMonth.map((item) => item.count),
            backgroundColor: "rgba(59, 130, 246, 0.8)",
            borderColor: "rgba(59, 130, 246, 1)",
            borderWidth: 2,
        },
    ],
}));

// Datos para gráfico de estado de equipos
const equipmentStatusData = computed(() => ({
    labels: ["Disponibles", "Prestados", "En Reparación", "Inactivos"],
    datasets: [
        {
            data: [
                props.summary.equipments.available,
                props.summary.equipments.onLoan,
                props.summary.equipments.inRepair,
                props.summary.equipments.inactive,
            ],
            backgroundColor: [
                "rgba(34, 197, 94, 0.8)",
                "rgba(59, 130, 246, 0.8)",
                "rgba(251, 191, 36, 0.8)",
                "rgba(156, 163, 175, 0.8)",
            ],
            borderColor: [
                "rgba(34, 197, 94, 1)",
                "rgba(59, 130, 246, 1)",
                "rgba(251, 191, 36, 1)",
                "rgba(156, 163, 175, 1)",
            ],
            borderWidth: 2,
        },
    ],
}));

// Datos para préstamos por tipo de equipo
const loansByEquipmentTypeData = computed(() => ({
    labels: props.charts.loansByEquipmentType.map((item) => item.type),
    datasets: [
        {
            data: props.charts.loansByEquipmentType.map((item) => item.total),
            backgroundColor: [
                "rgba(99, 102, 241, 0.8)",
                "rgba(236, 72, 153, 0.8)",
                "rgba(14, 165, 233, 0.8)",
                "rgba(168, 85, 247, 0.8)",
                "rgba(34, 197, 94, 0.8)",
            ],
            borderWidth: 2,
        },
    ],
}));

// Datos para préstamos por rol
const loansByUserRoleData = computed(() => ({
    labels: props.charts.loansByUserRole.map((item) => item.role),
    datasets: [
        {
            data: props.charts.loansByUserRole.map((item) => item.total),
            backgroundColor: [
                "rgba(20, 184, 166, 0.8)",
                "rgba(249, 115, 22, 0.8)",
                "rgba(139, 92, 246, 0.8)",
                "rgba(236, 72, 153, 0.8)",
            ],
            borderWidth: 2,
        },
    ],
}));

// Opciones de gráficos
const barChartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0,
            },
        },
    },
};

const doughnutChartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            position: "bottom",
        },
    },
};

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            position: "bottom",
        },
    },
};
</script>
