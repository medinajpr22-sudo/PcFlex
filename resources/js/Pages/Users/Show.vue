<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import NavLink from "@/Components/NavLink.vue";
const props = defineProps({
    user: Object,
});
</script>

<template>
    <Head title="Bibliotecario" />

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
                            <i
                                class="fas fa-user-circle text-3xl text-white"
                            ></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Detalles del Usuario
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Información completa del usuario
                            </p>
                        </div>
                    </div>
                    <NavLink :href="route('users.index')">
                        <SecondaryButton
                            class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                        >
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </SecondaryButton>
                    </NavLink>
                </div>
            </div>
        </template>

        <div
            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-xl p-6"
        >
            <div class="bg-white rounded-xl shadow-lg p-6 mb-4">
                <h3
                    class="text-xl font-bold mb-6 text-gray-800 flex items-center"
                >
                    <i class="fas fa-user mr-3 text-indigo-600"></i>
                    Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i class="fas fa-user mr-2 text-indigo-500"></i>
                            Nombre
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.name }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i class="fas fa-user-tag mr-2 text-indigo-500"></i>
                            Apellido
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.last_name }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i class="fas fa-id-card mr-2 text-indigo-500"></i>
                            Identificación
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.number_identification }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i
                                class="fas fa-venus-mars mr-2 text-indigo-500"
                            ></i>
                            Sexo
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                {{ user.sexo }}
                            </span>
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                            Email
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.email }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i class="fas fa-phone mr-2 text-indigo-500"></i>
                            Teléfono
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.telefono }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-50 rounded-lg p-4 border border-gray-200 md:col-span-2"
                    >
                        <label
                            class="flex items-center text-sm font-semibold text-gray-600 mb-2"
                        >
                            <i
                                class="fas fa-map-marker-alt mr-2 text-indigo-500"
                            ></i>
                            Dirección
                        </label>
                        <p class="text-base font-medium text-gray-900">
                            {{ user.direccion }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
