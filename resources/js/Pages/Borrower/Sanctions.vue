<script setup>
import BorrowerLayout from "@/Layouts/BorrowerLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    sanctions: Object,
});

const getSanctionDuration = (punishmentDate, endDate) => {
    const start = new Date(punishmentDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};
</script>

<template>
    <Head title="Mis Sanciones" />

    <BorrowerLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ⚠️ Mis Sanciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            v-if="sanctions.data.length === 0"
                            class="text-center py-12"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <p class="mt-4 text-gray-500">
                                ¡Excelente! No tienes sanciones.
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="sanction in sanctions.data"
                                :key="sanction.id"
                                class="border rounded-lg p-6"
                            >
                                <div
                                    class="flex justify-between items-start mb-4"
                                >
                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            {{ sanction.concept_dis }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Equipo:
                                            <span class="font-medium">{{
                                                sanction.service?.equipment
                                                    ?.serie_equ
                                            }}</span>
                                        </p>
                                    </div>
                                    <span
                                        :class="{
                                            'bg-red-100 text-red-800':
                                                sanction.status === 'activo',
                                            'bg-gray-100 text-gray-800':
                                                sanction.status ===
                                                'finalizada',
                                        }"
                                        class="px-3 py-1 text-sm font-semibold rounded-full"
                                    >
                                        {{
                                            sanction.status === "activo"
                                                ? "Activa"
                                                : "Finalizada"
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm"
                                >
                                    <div>
                                        <p class="text-gray-500">
                                            Fecha de Sanción
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{
                                                new Date(
                                                    sanction.punishment_date
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">
                                            Fecha de Finalización
                                        </p>
                                        <p class="font-medium text-gray-900">
                                            {{
                                                new Date(
                                                    sanction.end_date
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Duración</p>
                                        <p class="font-medium text-gray-900">
                                            {{
                                                getSanctionDuration(
                                                    sanction.punishment_date,
                                                    sanction.end_date
                                                )
                                            }}
                                            días
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="sanction.observation_dis"
                                    class="mt-4 p-3 bg-gray-50 rounded"
                                >
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold"
                                            >Observaciones:</span
                                        >
                                        {{ sanction.observation_dis }}
                                    </p>
                                </div>

                                <div
                                    v-if="sanction.status === 'activo'"
                                    class="mt-4"
                                >
                                    <div
                                        class="bg-yellow-50 border-l-4 border-yellow-400 p-3"
                                    >
                                        <p class="text-sm text-yellow-700">
                                            ⏳ No podrás realizar nuevos
                                            préstamos hasta que finalice esta
                                            sanción.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{
                                        sanctions.from
                                    }}</span>
                                    a
                                    <span class="font-medium">{{
                                        sanctions.to
                                    }}</span>
                                    de
                                    <span class="font-medium">{{
                                        sanctions.total
                                    }}</span>
                                    sanciones
                                </div>
                                <div class="flex space-x-2">
                                    <Link
                                        v-for="link in sanctions.links"
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
