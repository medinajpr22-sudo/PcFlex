<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, usePage } from "@inertiajs/vue3";

import { ref, computed, onMounted } from "vue";
import NavLink from "@/Components/NavLink.vue";
import CreateButton from "@/Components/CreateButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import EditButton from "@/Components/EditButton.vue";
import ShowButton from "@/Components/ShowButton.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

import { useForm } from "@inertiajs/vue3";
import SearchUser from "@/Components/SearchUser.vue";
import GreenButton from "@/Components/GreenButton.vue";

const props = defineProps({
    success: String,
    users: Object,
});

const page = usePage();

const successMessage = computed(() => page.props.flash.success);

onMounted(() => {
    if (successMessage.value) {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: successMessage.value,
            showConfirmButton: false,
            timer: 7000,
            toast: true,
        });
    }
});

const searchTerm = ref("");
const filteredUsers = computed(() => {
    if (!searchTerm.value) {
        return props.users.data;
    }
    return props.users.data.filter((user) => {
        return (
            user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            user.last_name
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase()) ||
            user.number_identification.includes(searchTerm.value)
        );
    });
});

const showModalDel = ref(false);
const userToDelete = ref(null);
const form = useForm({
    names: "",
    last_name: "",
    type_identification: "",
    number_identification: "",
});

const openModalDel = (user) => {
    showModalDel.value = true;
    userToDelete.value = user;
};

const closeModalDel = () => {
    showModalDel.value = false;
    userToDelete.value = null;
};

const deleteUser = async () => {
    try {
        await form.delete(
            route("Borrower_users.destroy", userToDelete.value.id)
        );
        showSuccessAlert("Usuario inactivado con éxito");
        closeModalDel();
    } catch (errors) {
        console.error("Error al inactivar usuario:", errors);
    }
};

const activateUser = async (user) => {
    try {
        await form.put(route("Borrower_users.activate", user.id));
        showSuccessAlert("Usuario activado con éxito");
    } catch (errors) {
        console.error("Error al activar usuario:", errors);
    }
};

const showSuccessAlert = (message) => {
    closeModalDel();
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

const downloadPdf = () => {
    window.location.href = route("pdfUsuarios");
};
</script>
<template>
    <Head title="Usuarios" />

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
                            <i class="fas fa-users text-3xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Prestamistas
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Gestión de usuarios prestamistas del sistema
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor de botones y búsqueda con diseño mejorado -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6"
            >
                <!-- Botones con nuevo diseño -->
                <div class="flex gap-3">
                    <!-- Botón PDF mejorado -->
                    <button
                        @click="downloadPdf"
                        class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-red-600 hover:to-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <i
                            class="fa fa-file-pdf mr-2 group-hover:scale-110 transition-transform"
                            aria-hidden="true"
                        ></i>
                        Exportar PDF
                    </button>

                    <!-- Botón Crear mejorado -->
                    <NavLink :href="route('Borrower_users.create')">
                        <CreateButton
                            class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            <i
                                class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"
                            ></i>
                            Nuevo Prestamista
                        </CreateButton>
                    </NavLink>
                </div>

                <!-- Barra de búsqueda mejorada -->
                <div class="w-full sm:w-auto">
                    <SearchUser
                        v-model:search="searchTerm"
                        @search="handleSearch"
                        class="w-full sm:w-64"
                    />
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
                                        Nombre
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tag mr-2"></i>
                                        Apellido
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-id-card mr-2"></i>
                                        Número de Identificación
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-venus-mars mr-2"></i>
                                        Sexo
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-user-shield mr-2"></i>
                                        Roll
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
                                v-for="user in filteredUsers"
                                :key="user.id"
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
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{ user.name }}</span
                                        >
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ user.last_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ user.number_identification }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                    >
                                        {{ user.sex_user }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ user.roll }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="user.status === 'activo'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Activo
                                    </span>
                                    <span
                                        v-else-if="user.status === 'inactivo'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-400 to-red-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactivo
                                    </span>
                                    <span
                                        v-else-if="user.status === 'conEquipo'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-orange-400 to-orange-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-laptop mr-1"></i>
                                        Con Equipo
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <!-- Si el estado es "activo", muestra todos los botones -->
                                    <div
                                        v-if="user.status === 'activo'"
                                        class="flex justify-center gap-2"
                                    >
                                        <NavLink
                                            :href="
                                                route(
                                                    'Borrower_users.show',
                                                    user.id
                                                )
                                            "
                                        >
                                            <ShowButton
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            >
                                                <i class="fas fa-eye mr-1"></i>
                                                Info
                                            </ShowButton>
                                        </NavLink>
                                        <NavLink
                                            :href="
                                                route(
                                                    'Borrower_users.edit',
                                                    user.id
                                                )
                                            "
                                        >
                                            <EditButton
                                                class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            >
                                                <i class="fas fa-edit mr-1"></i>
                                                Editar
                                            </EditButton>
                                        </NavLink>
                                        <DeleteButton
                                            @click="openModalDel(user)"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                        >
                                            <i
                                                class="fas fa-user-slash mr-1"
                                            ></i>
                                            Inactivar
                                        </DeleteButton>
                                    </div>

                                    <!-- Si el estado es "conEquipo", solo muestra el botón de Info -->
                                    <div
                                        v-else-if="user.status === 'conEquipo'"
                                        class="flex justify-center gap-2"
                                    >
                                        <NavLink
                                            :href="
                                                route(
                                                    'Borrower_users.show',
                                                    user.id
                                                )
                                            "
                                        >
                                            <ShowButton
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            >
                                                <i class="fas fa-eye mr-1"></i>
                                                Info
                                            </ShowButton>
                                        </NavLink>
                                    </div>

                                    <!-- Si el estado es "inactivo", solo muestra el botón de Reactivar -->
                                    <div
                                        v-else-if="user.status === 'inactivo'"
                                        class="flex justify-center gap-2"
                                    >
                                        <GreenButton
                                            @click="activateUser(user)"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                        >
                                            <i
                                                class="fas fa-user-check mr-1"
                                            ></i>
                                            Reactivar
                                        </GreenButton>
                                    </div>

                                    <!-- Opcional: Manejo de estados no esperados -->
                                    <div v-else class="text-gray-500">
                                        Estado no reconocido
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
                >
                    <Pagination :links="users.links" />
                </div>
            </div>
        </div>
        <Modal :show="showModalDel" @close="closeModalDel">
            <div class="p-6 text-center">
                <!-- Icono de advertencia -->
                <div class="flex justify-center mb-4">
                    <i
                        class="fas fa-exclamation-triangle text-4xl text-red-600"
                    ></i>
                </div>

                <!-- Título del modal -->
                <h1 class="text-lg font-semibold mb-2">
                    ¿Estás seguro de realizar esta acción?
                </h1>

                <!-- Mensaje de advertencia -->
                <p class="text-gray-600 mb-6">
                    Esta acción no se puede deshacer.
                </p>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-4">
                    <SecondaryButton @click="closeModalDel">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton @click="deleteUser">
                        Sí, seguro
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
