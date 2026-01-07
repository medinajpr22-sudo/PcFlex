<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
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
import Swal from "sweetalert2";

const props = defineProps({
    users: Object,
});

const showModalDel = ref(false);
const userToDelete = ref(null);
const form = useForm({
    name: "",
    last_name: "",
    type_identification: "",
    number_identification: "",
    sexo: "",
    telefono: "",
    direccion: "",
    email: "",
    errors: {},
});

// Observar los mensajes flash
const page = usePage();
watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash.success) {
            showSuccessAlert(newFlash.success);
        }
        if (newFlash.error) {
            showErrorAlert(newFlash.error);
        }
    },
    { deep: true }
);

const openModalDel = (user) => {
    showModalDel.value = true;
    userToDelete.value = user;
};

const closeModalDel = () => {
    console.log("Cerrando modal");
    showModalDel.value = false;
    userToDelete.value = null;
};

const deleteUser = () => {
    console.log("Intentando eliminar usuario:", userToDelete.value);
    form.delete(route("users.destroy", userToDelete.value.id), {
        onSuccess: () => {
            closeModalDel();
        },
        onError: (errors) => {
            console.error("Error al eliminar usuario:", errors);
        },
    });
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
                                Usuarios del Sistema
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Administra los usuarios con acceso al sistema
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor de botones con diseño mejorado -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6"
            >
                <div class="flex gap-3">
                    <NavLink :href="route('users.create')">
                        <CreateButton
                            class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            <i
                                class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"
                            ></i>
                            Nuevo Usuario
                        </CreateButton>
                    </NavLink>
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
                                        <i class="fas fa-phone mr-2"></i>
                                        Teléfono
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
                                v-for="user in users.data"
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
                                        {{ user.sexo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ user.telefono }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <NavLink
                                            :href="route('users.show', user.id)"
                                        >
                                            <ShowButton
                                                class="inline-flex items-center px-3 py-2 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200"
                                            >
                                                <i class="fas fa-eye mr-1"></i>
                                                Info
                                            </ShowButton>
                                        </NavLink>
                                        <NavLink
                                            :href="route('users.edit', user.id)"
                                        >
                                            <EditButton
                                                class="inline-flex items-center px-3 py-2 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 transition-colors duration-200"
                                            >
                                                <i class="fas fa-edit mr-1"></i>
                                                Editar
                                            </EditButton>
                                        </NavLink>
                                        <DeleteButton
                                            @click="openModalDel(user)"
                                            class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors duration-200"
                                        >
                                            <i class="fas fa-trash mr-1"></i>
                                            Eliminar
                                        </DeleteButton>
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
            <!-- Contenedor principal del modal -->
            <div
                class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md mx-auto"
            >
                <!-- Icono de advertencia -->
                <div class="flex justify-center p-6 bg-red-50">
                    <i
                        class="fas fa-exclamation-triangle text-6xl text-red-600"
                    ></i>
                </div>

                <!-- Contenido del modal -->
                <div class="p-6">
                    <h1 class="text-xl font-bold text-gray-800 mb-2">
                        ¿Estás seguro de realizar esta acción?
                    </h1>
                    <p class="text-gray-600">
                        Esta acción no se puede deshacer.
                    </p>
                </div>

                <!-- Botones de acción -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-4">
                    <SecondaryButton
                        @click="closeModalDel"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteUser"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Sí, seguro
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
