<script setup>
import BorrowerLayout from "@/Layouts/BorrowerLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    history: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        devuelto: "bg-green-100 text-green-800",
        pendiente: "bg-yellow-100 text-yellow-800",
        vencido: "bg-red-100 text-red-800",
    };
    return badges[status] || "bg-gray-100 text-gray-800";
};

const getStatusText = (status) => {
    const texts = {
        devuelto: "Devuelto",
        pendiente: "Activo",
        vencido: "Vencido",
    };
    return texts[status] || status;
};
</script>

<template>
    <Head title="Historial de Préstamos" />

    <BorrowerLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📖 Historial de Préstamos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            v-if="history.data.length === 0"
                            class="text-center py-12 text-gray-500"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
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
                            <p class="mt-4">
                                No tienes préstamos en tu historial.
                            </p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            ID
                                        </th>
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
                                            Fecha Devolución
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Estado
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
                                        v-for="item in history.data"
                                        :key="item.id"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            #{{ item.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ item.equipment.serie_equ }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ item.equipment.brand_equ }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{ item.environment.name_env }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                new Date(
                                                    item.date_ser
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                item.return_date
                                                    ? new Date(
                                                          item.return_date
                                                      ).toLocaleDateString(
                                                          "es-ES"
                                                      )
                                                    : "-"
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="
                                                    getStatusBadge(item.status)
                                                "
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusText(item.status) }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <Link
                                                v-if="
                                                    item.status === 'devuelto'
                                                "
                                                :href="
                                                    route(
                                                        'borrower.download-receipt',
                                                        item.id
                                                    )
                                                "
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                📄 Descargar Comprobante
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{
                                        history.from
                                    }}</span>
                                    a
                                    <span class="font-medium">{{
                                        history.to
                                    }}</span>
                                    de
                                    <span class="font-medium">{{
                                        history.total
                                    }}</span>
                                    resultados
                                </div>
                                <div class="flex space-x-2">
                                    <Link
                                        v-for="link in history.links"
                                        :key="link.label"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="
                                            link.active
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-white text-gray-700 hover:bg-gray-50'
                                        "
                                        class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md"
                                        :disabled="!link.url"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BorrowerLayout>
</template>
