<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    services: {
        type: Object,
    },
});
</script>

<template>
    <Head title="+info" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6"
            >
                <h2 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-info-circle mr-3"></i>Detalles del Préstamo
                </h2>
                <p class="text-indigo-100 text-sm">
                    Información completa del servicio de préstamo
                </p>
            </div>

            <div class="mb-6">
                <NavLink :href="route('historial.historico')">
                    <button
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-lg shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-200"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Historial
                    </button>
                </NavLink>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Información del Servicio -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4"
                >
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i>Información del
                        Servicio
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i
                                class="fas fa-calendar-plus text-indigo-500 mr-2"
                            ></i
                            >Fecha de Préstamo
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.date_ser }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i
                                class="fas fa-calendar-check text-purple-500 mr-2"
                            ></i
                            >Fecha de Devolución
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.return_ser }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-user-tie text-indigo-500 mr-2"></i
                            >Bibliotecario (Préstamo)
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.librarian.name }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i
                                class="fas fa-user-check text-purple-500 mr-2"
                            ></i
                            >Bibliotecario (Devolución)
                        </label>
                        <p
                            class="text-gray-800 font-medium"
                            v-if="services.librarianreturn"
                        >
                            {{ services.librarianreturn.name }}
                        </p>
                        <p class="text-gray-800 font-medium" v-else>
                            {{ services.librarian.name }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Información del Prestatario -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4"
                >
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user mr-3"></i>Información del
                        Prestatario
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-user text-indigo-500 mr-2"></i
                            >Nombre
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.name }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i class="fas fa-user text-purple-500 mr-2"></i
                            >Apellido
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.last_name }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-id-card text-indigo-500 mr-2"></i
                            >Identificación
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.number_identification }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i
                                class="fas fa-venus-mars text-purple-500 mr-2"
                            ></i
                            >Sexo
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.sex_user }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-envelope text-indigo-500 mr-2"></i
                            >Email
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.contacts.email_con }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i class="fas fa-phone text-purple-500 mr-2"></i
                            >Teléfono
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.contacts.telephone_con }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i
                                class="fas fa-map-marker-alt text-indigo-500 mr-2"
                            ></i
                            >Dirección
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.users.address.addres_add }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i class="fas fa-toggle-on text-purple-500 mr-2"></i
                            >Estado
                        </label>
                        <span
                            v-if="services.users.status === 'activo'"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-md"
                        >
                            <i class="fas fa-check-circle mr-1"></i>
                            Activo
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-red-400 to-red-500 text-white shadow-md"
                        >
                            <i class="fas fa-times-circle mr-1"></i>
                            Inactivo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información del Equipo -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4"
                >
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-laptop mr-3"></i>Información del Equipo
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-desktop text-indigo-500 mr-2"></i
                            >Tipo de Equipo
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.equipment.type_equi }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i class="fas fa-barcode text-purple-500 mr-2"></i
                            >Serie/Identificación
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.equipment.serie_equi }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500 md:col-span-2"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-list-ul text-indigo-500 mr-2"></i
                            >Características
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.equipment.characteristics }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información del Lugar -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4"
                >
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-map-marker-alt mr-3"></i>Lugar de
                        Traslado
                    </h3>
                </div>
                <div class="p-6">
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-door-open text-indigo-500 mr-2"></i
                            >Ambiente
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.environment.names }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información del Bibliotecario -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4"
                >
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-tie mr-3"></i>Bibliotecario
                        Encargado
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500"
                    >
                        <label
                            class="block text-sm font-bold text-indigo-900 mb-2"
                        >
                            <i class="fas fa-user text-indigo-500 mr-2"></i
                            >Nombre
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.librarian.name }}
                        </p>
                    </div>
                    <div
                        class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                    >
                        <label
                            class="block text-sm font-bold text-purple-900 mb-2"
                        >
                            <i class="fas fa-user text-purple-500 mr-2"></i
                            >Apellido
                        </label>
                        <p class="text-gray-800 font-medium">
                            {{ services.librarian.last_name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
