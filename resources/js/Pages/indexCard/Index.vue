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
import GreenButton from "@/Components/GreenButton.vue";
import { useForm } from "@inertiajs/vue3";
import CreateButton from "@/Components/CreateButton.vue";
import EditButton from "@/Components/EditButton.vue";
import SearchIndexCard from "@/Components/SearchIndexCard.vue";

const showModalvue = ref(false);
const showModalForm = ref(false);
const showModalDel = ref(false);

const form = useForm({
    number: "",
    program_id: "",
    errors: {},
});

const searchTerm = ref("");
const filteredindexCard = computed(() => {
    if (!searchTerm.value) {
        return props.indexCard.data;
    }
    const search = searchTerm.value.toLowerCase();
    return props.indexCard.data.filter((indexCard) => {
        const programName =
            props.programs.find((p) => p.id === indexCard.program_id)
                ?.names_pro || "";
        return (
            indexCard.number.toLowerCase().includes(search) ||
            programName.toLowerCase().includes(search)
        );
    });
});

const v = ref({ id: "", number: "", program_id: "" });

const title = ref("");
const operation = ref(1);
const msj = ref("");
const classMsj = ref("hidden");

const openModalvue = (indexCard) => {
    showModalvue.value = true;
    v.value = { ...indexCard };
};

const openModalForm = (op, indexCard) => {
    showModalForm.value = true;
    operation.value = op;
    if (op === 1) {
        title.value = "Crear Ficha";
        form.reset();
    } else {
        title.value = "Editar Ficha";
        v.value = { ...indexCard };
        form.number = indexCard.number;
        form.program_id = indexCard.program_id;
    }
};

const openModalDel = (indexCard) => {
    showModalDel.value = true;
    v.value = { ...indexCard };
};

const closeModalvue = () => {
    showModalvue.value = false;
};

const closeModalForm = () => {
    showModalForm.value = false;
    form.reset();
};

const closeModaldel = () => {
    showModalDel.value = false;
};

const props = defineProps({
    indexCard: {
        type: Object,
        required: true,
    },
    programs: Array,
});

const save = () => {
    if (operation.value === 1) {
        form.post(route("indexCard.store"), {
            onSuccess: () => {
                closeModalForm();
                showSuccessAlert("Ficha Creada con éxito");
            },
        });
    } else {
        form.put(route("indexCard.update", v.value.id), {
            onSuccess: () => {
                showSuccessAlert("Ficha Editada con éxito");
            },
        });
    }
};

const downloadPdf = () => {
    window.location.href = route("pdfIndexCard");
};

const ok = (m) => {
    closeModalForm();
    closeModaldel();
    form.reset();
    msj.value = m;
    classMsj.value = "programa";
    setTimeout(() => {
        classMsj.value = "hidden";
    }, 8000);
};

const deleteficha = () => {
    form.delete(route("indexCard.destroy", v.value.id), {
        onSuccess: (response) => {
            // Mostrar mensaje de éxito
            closeModaldel();
            showSuccessAlert(response.props.flash.success);
        },
        onError: (errors) => {
            // Mostrar mensaje de error
            if (errors.error) {
                closeModaldel();
                showErrorAlert(errors.error);
            } else {
                showErrorAlert("Ocurrió un error inesperado.");
            }
        },
    });
};

const activateProgram = (indexCard) => {
    form.put(route("indexCard.activate", indexCard.id), {
        onSuccess: () => {
            // Mostrar mensaje de éxito
            showSuccessAlert("Programa activado con éxito");
        },
        onError: (errors) => {
            // Mostrar mensaje de error
            if (errors.error) {
                showErrorAlert(errors.error);
            } else {
                showErrorAlert("Ocurrió un error al activar el programa.");
            }
        },
    });
};

// Alertas de fichas
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
    <Head title="Fichas" />
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
                            <i class="fas fa-file-alt text-3xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Gestión de Fichas
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Administra las fichas del sistema
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
                        Nueva Ficha
                    </CreateButton>
                </div>

                <!-- Barra de búsqueda mejorada -->
                <div class="w-full sm:w-auto">
                    <SearchIndexCard
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
                class="inline-flex overflow-hidden mb-6 w-full bg-white rounded-lg shadow-lg border-l-4 border-indigo-500"
                :class="classMsj"
            >
                <div
                    class="flex justify-center items-center w-16 bg-gradient-to-br from-indigo-500 to-purple-600"
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
                        <span class="font-bold text-indigo-600 text-lg"
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
                                        <i class="fas fa-hashtag mr-2"></i>
                                        Número de Ficha
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i
                                            class="fas fa-graduation-cap mr-2"
                                        ></i>
                                        Programa
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
                                v-for="indexCard in filteredindexCard"
                                :key="indexCard.id"
                                class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3"
                                        >
                                            <i
                                                class="fas fa-file-alt text-white"
                                            ></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ indexCard.number }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                    >
                                        <i class="fas fa-book mr-1"></i>
                                        {{
                                            programs.find(
                                                (p) =>
                                                    p.id ===
                                                    indexCard.program_id
                                            )?.names_pro
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="indexCard.status === 'activo'"
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
                                            v-if="indexCard.status === 'activo'"
                                        >
                                            <EditButton
                                                @click="
                                                    openModalForm(2, indexCard)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                                >Editar</EditButton
                                            >
                                            <DangerButton
                                                @click="openModalDel(indexCard)"
                                                class="transform hover:scale-105 transition-transform"
                                                >Inactivar</DangerButton
                                            >
                                        </template>
                                        <template v-else>
                                            <GreenButton
                                                @click="
                                                    activateProgram(indexCard)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                                >Reactivar</GreenButton
                                            >
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
                    <Pagination :links="indexCard.links" />
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
                        <i class="fas fa-file-alt text-4xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 text-center">
                        {{ title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Complete la información de la ficha
                    </p>
                </div>

                <!-- Campos del formulario con diseño mejorado -->
                <div class="space-y-5">
                    <!-- Número de Ficha -->
                    <div class="relative">
                        <InputLabel
                            for="number"
                            value="Número de Ficha"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <TextInput
                                v-model="form.number"
                                required
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Ej: 2459874"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.number"
                        />
                    </div>

                    <!-- Programa -->
                    <div class="relative">
                        <InputLabel
                            for="program_id"
                            value="Programa"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none z-10"
                            >
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <select
                                v-model="form.program_id"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none bg-white"
                            >
                                <option value="" disabled>
                                    Seleccione un programa
                                </option>
                                <option
                                    v-for="program in programs"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.names_pro }}
                                </option>
                            </select>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 pointer-events-none"
                            >
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.program_id"
                        />
                    </div>
                </div>

                <!-- Botones mejorados -->
                <div class="mt-8 flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModalForm"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </SecondaryButton>
                    <EditButton
                        @click="save"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-save mr-2"></i> Guardar
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
                        Al inactivar la Ficha se
                        <strong class="text-red-600"
                            >inactivarán todos los aprendices</strong
                        >
                        relacionados a ella, pero no se eliminarán, solo
                        cambiarán el estado a
                        <strong class="text-red-600">Inactivo</strong>.
                    </p>
                </div>

                <!-- Información de la ficha -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-1">Ficha a inactivar:</p>
                    <p class="text-lg font-bold text-gray-900">
                        N° {{ v.number }}
                    </p>
                </div>

                <!-- Botones mejorados -->
                <div class="flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModaldel"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-arrow-left mr-2"></i> Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteficha"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-check mr-2"></i> Sí, continuar
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
