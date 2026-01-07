<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import GreenButton from "@/Components/GreenButton.vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import EditButton from "@/Components/EditButton.vue";
import CreateButton from "@/Components/CreateButton.vue";
import SearchProgram from "@/Components/SearchProgram.vue";
const showModalvue = ref(false);
const showModalForm = ref(false);
const showModalDel = ref(false);

const form = useForm({
    names_pro: "",
    code_pro: "",
    version: "",
});

const v = ref({ id: "", names_pro: "", code_pro: "", version: "" });

const title = ref("");
const operation = ref(1);
const msj = ref("");
const classMsj = ref("hidden");

const searchTerm = ref("");
const filteredProgram = computed(() => {
    if (!searchTerm.value) {
        return props.programs.data;
    }
    const search = searchTerm.value.toLowerCase();
    return props.programs.data.filter((program) => {
        return (
            program.names_pro.toLowerCase().includes(search) ||
            program.code_pro.toLowerCase().includes(search)
        );
    });
});

const openModalvue = (program) => {
    showModalvue.value = true;
    v.value = { ...program };
};

const downloadPdf = () => {
    window.location.href = route("pdfPrograms");
};

const openModalForm = (op, program) => {
    showModalForm.value = true;
    operation.value = op;
    if (op === 1) {
        title.value = "Crear Programa";
        form.reset();
    } else {
        title.value = "Editar Programa";
        v.value = { ...program };
        form.names_pro = program.names_pro;
        form.code_pro = program.code_pro;
        form.version = program.version;
    }
};

const openModalDel = (program) => {
    showModalDel.value = true;
    v.value = { ...program };
};

const closeModalvue = () => {
    showModalvue.value = false;
};

const closeModalForm = () => {
    showModalForm.value = false;
    form.reset();
    form.clearErrors();
};

const closeModaldel = () => {
    showModalDel.value = false;
};

const props = defineProps({
    programs: {
        type: Object,
        required: true,
    },
    programs: Array,
});

const showSuccessAlert = (message) => {
    closeModalForm();
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

const save = () => {
    if (operation.value === 1) {
        form.post(route("programs.store"), {
            onSuccess: () => {
                showSuccessAlert("Programa creado con éxito");
            },
            onError: (errors) => {
                showErrorAlert(errors.error);
            },
        });
    } else {
        form.put(route("programs.update", v.value.id), {
            onSuccess: () => {
                showSuccessAlert("Programa Editado con éxito");
            },
            onError: (errors) => {
                showErrorAlert(errors.error);
            },
        });
    }
};

const deleteprogram = () => {
    form.delete(route("programs.destroy", v.value.id), {
        onSuccess: () => {
            closeModaldel();
            showSuccessAlert("Programa Inactivado con éxito");
        },
        onError: (errors) => {
            closeModaldel();
            showErrorAlert(errors.error);
        },
    });
};

const activateProgram = (program) => {
    form.put(route("programs.activate", program.id), {
        onSuccess: () => {
            showSuccessAlert("Programa Activado con éxito");
        },
        onError: (errors) => {
            showErrorAlert(errors.error);
        },
    });
};
</script>

<template>
    <Head title="Programas" />
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
                                class="fas fa-laptop-code text-3xl text-white"
                            ></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Gestión de Programas
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Administra los programas académicos del sistema
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
                    <CreateButton
                        @click="openModalForm(1)"
                        class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <i
                            class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"
                        ></i>
                        Nuevo Programa
                    </CreateButton>
                </div>

                <!-- Barra de búsqueda mejorada -->
                <div class="w-full sm:w-auto">
                    <SearchProgram
                        v-model:search="searchTerm"
                        @search="handleSearch"
                        class="w-full sm:w-64"
                    />
                </div>
            </div>
        </template>

        <!-- Contenedor principal con fondo degradado -->
        <div
            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-xl p-6"
        >
            <!-- Alerta de éxito mejorada -->
            <div
                class="inline-flex overflow-hidden mb-6 w-full bg-white rounded-lg shadow-lg border-l-4 border-blue-500"
                :class="classMsj"
            >
                <div
                    class="flex justify-center items-center w-16 bg-gradient-to-br from-blue-500 to-blue-600"
                >
                    <svg
                        class="w-8 h-8 text-white fill-current"
                        viewBox="0 0 40 40"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"
                        ></path>
                    </svg>
                </div>
                <div class="px-4 py-3">
                    <div class="mx-3">
                        <span class="font-bold text-blue-600 text-lg"
                            >¡Éxito!</span
                        >
                        <p class="text-sm text-gray-700 mt-1">{{ msj }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla moderna con diseño mejorado -->
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
                                        <i class="fas fa-book mr-2"></i>
                                        Nombre del Programa
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag mr-2"></i>
                                        Código
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-code-branch mr-2"></i>
                                        Versión
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
                                    class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider"
                                >
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="program in filteredProgram"
                                :key="program.id"
                                class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3"
                                        >
                                            <i
                                                class="fas fa-graduation-cap text-white"
                                            ></i>
                                        </div>
                                        <div
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ program.names_pro }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        <i class="fas fa-barcode mr-1"></i>
                                        {{ program.code_pro }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                    >
                                        <i class="fas fa-tag mr-1"></i>
                                        v{{ program.version }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="program.status === 'activo'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Activo
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-400 to-red-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Inactivo
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <template
                                            v-if="program.status === 'activo'"
                                        >
                                            <EditButton
                                                @click="
                                                    openModalForm(2, program)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                            >
                                                Editar
                                            </EditButton>
                                            <DangerButton
                                                @click="openModalDel(program)"
                                                class="transform hover:scale-105 transition-transform"
                                            >
                                                <i class="fas fa-ban mr-1"></i>
                                                Inactivar
                                            </DangerButton>
                                        </template>
                                        <template v-else>
                                            <GreenButton
                                                @click="
                                                    activateProgram(program)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                            >
                                                <i
                                                    class="fas fa-check-circle mr-1"
                                                ></i>
                                                Reactivar
                                            </GreenButton>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación mejorada -->
                <div
                    class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200"
                >
                    <Pagination :links="programs.links" />
                </div>
            </div>
        </div>

        <!-- Modal para el formulario con diseño mejorado -->
        <Modal :show="showModalForm" @close="closeModalForm">
            <div class="p-8 w-full max-w-lg mx-auto">
                <!-- Header del modal con gradiente -->
                <div class="flex flex-col items-center mb-6">
                    <div
                        class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full p-4 mb-4 shadow-lg"
                    >
                        <i class="fas fa-laptop-code text-4xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 text-center">
                        {{ title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Complete la información del programa
                    </p>
                </div>

                <!-- Campos del formulario con diseño mejorado -->
                <div class="space-y-5">
                    <!-- Nombre del Programa -->
                    <div class="relative">
                        <InputLabel
                            for="names_pro"
                            value="Nombre del Programa"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-book"></i>
                            </span>
                            <TextInput
                                v-model="form.names_pro"
                                required
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Ej: Tecnología en Sistemas"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.names_pro"
                        />
                    </div>

                    <!-- Código del Programa -->
                    <div class="relative">
                        <InputLabel
                            for="code_pro"
                            value="Código del Programa"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <TextInput
                                v-model="form.code_pro"
                                required
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Ej: TEC-SIS-001"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.code_pro"
                        />
                    </div>

                    <!-- Versión -->
                    <div class="relative">
                        <InputLabel
                            for="version"
                            value="Versión"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-code-branch"></i>
                            </span>
                            <TextInput
                                v-model="form.version"
                                required
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Ej: 1.0"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.version"
                        />
                    </div>
                </div>

                <!-- Botones mejorados -->
                <div class="mt-8 flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModalForm"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </SecondaryButton>
                    <EditButton
                        @click="save"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </EditButton>
                </div>
            </div>
        </Modal>

        <!-- Modal para eliminación con diseño mejorado -->
        <Modal :show="showModalDel" @close="closeModaldel">
            <div class="p-8 w-full max-w-md mx-auto">
                <!-- Icono de advertencia animado -->
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-gradient-to-br from-red-500 to-red-600 rounded-full p-5 shadow-2xl animate-pulse"
                    >
                        <i
                            class="fas fa-exclamation-triangle text-5xl text-white"
                        ></i>
                    </div>
                </div>

                <!-- Título y mensaje -->
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3">
                    ¿Estás seguro de realizar esta acción?
                </h1>
                <div
                    class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg"
                >
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                        Esta información no se eliminará, solo se cambiará el
                        estado a <strong class="text-red-600">Inactivo</strong>.
                    </p>
                </div>

                <!-- Información del programa -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-1">
                        Programa a inactivar:
                    </p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ v.names_pro }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Código: {{ v.code_pro }}
                    </p>
                </div>

                <!-- Botones mejorados -->
                <div class="flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModaldel"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteprogram"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-check mr-2"></i>
                        Sí, continuar
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
