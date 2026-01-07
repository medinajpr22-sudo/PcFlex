<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue"; // Importar 'computed' de Vue
import DangerButton from "@/Components/DangerButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import GreenButton from "@/Components/GreenButton.vue";
import EditButton from "@/Components/EditButton.vue";
import CreateButton from "@/Components/CreateButton.vue";
import { useForm } from "@inertiajs/vue3";
import ShowButton from "@/Components/ShowButton.vue";
import SearchForm from "@/Components/SearchForm.vue";
import NavLink from "@/Components/NavLink.vue";
const showModalvue = ref(false);
const showModalForm = ref(false);
const showModalDel = ref(false);
const showModalrepa = ref(false);
const showModalReactive = ref(false);

// Configuración del formulario
const form = useForm({
    type_equi: "",
    characteristics: "",
    serie_equi: "",
    errors: {},
});

// Información de equipo seleccionada
const v = ref({ id: "", type_equi: "", characteristics: "", serie_equi: "" });
const title = ref("");
const operation = ref(1);
const msj = ref("");
const classMsj = ref("hidden");
const searchTerm = ref("");

// Propiedades pasadas desde el componente padre
const props = defineProps({
    equipments: {
        type: Object,
        required: true,
    },
});

// Filtrar equipos en base al término de búsqueda
const filteredEquipments = computed(() => {
    if (!searchTerm.value) {
        return props.equipments.data;
    }
    return props.equipments.data.filter(
        (equipment) =>
            equipment.serie_equi
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase()) ||
            equipment.type_equi
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase()) ||
            equipment.characteristics
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase())
    );
});
// Métodos para abrir y cerrar modales
const openModalForm = (op, equipment) => {
    showModalForm.value = true;
    operation.value = op;
    if (op === 1) {
        title.value = "Crear Equipo";
        form.reset();
    } else {
        title.value = "Editar Equipo";
        v.value = { ...equipment };
        form.type_equi = equipment.type_equi;
        form.characteristics = equipment.characteristics;
        form.serie_equi = equipment.serie_equi;
    }
};
const openModalDel = (equipment) => {
    showModalDel.value = true;
    v.value = { ...equipment };
};

const openModalrepa = (equipment) => {
    showModalrepa.value = true;
    v.value = { ...equipment };
};
const openModalReactive = (equipment) => {
    showModalReactive.value = true;
    v.value = { ...equipment };
};

const closeModalForm = () => {
    showModalForm.value = false;
    errors: {
    }
    form.reset();
};
const closeModaldel = () => {
    showModalDel.value = false;
};

const closeModalrepa = () => {
    showModalrepa.value = false;
};
const closeModalReactive = () => {
    showModalReactive.value = false;
};

// Guardar equipo (crear o editar)
const save = () => {
    if (operation.value === 1) {
        form.post(route("equipments.store"), {
            onSuccess: () => {
                showSuccessAlert("Equipo creado con éxito");
            },
        });
    } else {
        form.put(route("equipments.update", v.value.id), {
            onSuccess: () => {
                showSuccessAlert("Equipo editado con éxito");
            },
            onError: (errors) => {
                closeModalForm();

                showErrorAlert(errors.error);
            },
        });
    }
};

// Mostrar mensaje de éxito

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

// Eliminar equipo
// Eliminar equipo
const deleteprogram = () => {
    form.delete(route("equipments.destroy", v.value.id), {
        onSuccess: () => {
            closeModaldel(); // Cerrar el modal de eliminación
            showSuccessAlert("Equipo inactivado con éxito"); // Mostrar el mensaje de éxito
        },
        onError: (errors) => {
            closeModaldel(); // Cerrar el modal de eliminación

            showErrorAlert(errors.error);
        },
    });
};

// Enviar a reparación
const reparationEquipment = (equipments) => {
    form.put(route("equipments.reparation", equipments.id), {
        onSuccess: () => {
            closeModalrepa(); // Cerrar el modal de reparación
            showSuccessAlert("Equipo enviado a reparación"); // Mostrar el mensaje de éxito
        },
        onError: (errors) => {
            closeModalrepa();
            showErrorAlert(errors.error);
        },
    });
};

const showErrorAlert = (message) => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message,
    });
};

const activateProgram = (equipments) => {
    form.put(route("equipments.activate", equipments.id), {
        onSuccess: () => {
            closeModalReactive();
            showSuccessAlert("Equipo activado con éxito");
        },
    });
};

// Descargar PDF
const downloadPdf = () => {
    window.location.href = route("pdfequipos");
};
</script>

<template>
    <Head title="Equipos" />
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
                            <i class="fas fa-laptop text-3xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Gestión de Equipos
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Administra el inventario de equipos
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor de botones y búsqueda -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6"
            >
                <div class="flex gap-3">
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
                    <CreateButton
                        @click="openModalForm(1)"
                        class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-green-600 hover:to-green-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <i
                            class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"
                        ></i>
                        Nuevo Equipo
                    </CreateButton>
                </div>
                <div class="w-full sm:w-auto">
                    <SearchForm
                        v-model:search="searchTerm"
                        class="w-full sm:w-64"
                    />
                </div>
            </div>
        </template>

        <!-- Contenedor principal con fondo degradado -->
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
                                        <i class="fas fa-laptop mr-2"></i>
                                        Tipo de Equipo
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Características
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode mr-2"></i>
                                        Número de Serie
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
                                v-for="equipment in filteredEquipments"
                                :key="equipment.id"
                                class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3"
                                        >
                                            <i
                                                class="fas fa-desktop text-white"
                                            ></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ equipment.type_equi }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">
                                        {{ equipment.characteristics }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        <i class="fas fa-hashtag mr-1"></i>
                                        {{ equipment.serie_equi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="equipment.status === 'disponible'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-green-400 to-green-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Disponible
                                    </span>
                                    <span
                                        v-else-if="
                                            equipment.status === 'prestado'
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-hand-holding mr-1"></i>
                                        Prestado
                                    </span>
                                    <span
                                        v-else-if="
                                            equipment.status === 'reparacion'
                                        "
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-400 to-purple-500 text-white shadow-sm"
                                    >
                                        <i class="fas fa-tools mr-1"></i>
                                        Reparación
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
                                            v-if="
                                                equipment.status === 'prestado'
                                            "
                                        >
                                            <NavLink
                                                :href="
                                                    route(
                                                        'detalles.show',
                                                        equipment.id
                                                    )
                                                "
                                            >
                                                <ShowButton
                                                    class="transform hover:scale-105 transition-transform"
                                                    >Info</ShowButton
                                                >
                                            </NavLink>
                                        </template>
                                        <template
                                            v-else-if="
                                                equipment.status ===
                                                'disponible'
                                            "
                                        >
                                            <EditButton
                                                @click="
                                                    openModalForm(2, equipment)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                                >Editar</EditButton
                                            >
                                            <ShowButton
                                                @click="
                                                    openModalrepa(equipment)
                                                "
                                                class="transform hover:scale-105 transition-transform"
                                                >Reparación</ShowButton
                                            >
                                            <DeleteButton
                                                @click="openModalDel(equipment)"
                                                class="transform hover:scale-105 transition-transform"
                                                >Inactivar</DeleteButton
                                            >
                                        </template>
                                        <template v-else>
                                            <GreenButton
                                                @click="
                                                    openModalReactive(equipment)
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
                <div
                    class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200"
                >
                    <Pagination
                        :links="props.equipments.links"
                        :query="searchTerm.value"
                    />
                </div>
            </div>
        </div>
        <!-- Modal para formulario mejorado -->
        <Modal :show="showModalForm" @close="closeModalForm">
            <div class="p-8 w-full max-w-lg mx-auto">
                <!-- Header del modal -->
                <div class="flex flex-col items-center mb-6">
                    <div
                        class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full p-4 mb-4 shadow-lg"
                    >
                        <i class="fas fa-laptop text-4xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 text-center">
                        {{ title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Complete la información del equipo
                    </p>
                </div>

                <!-- Campos del formulario -->
                <div class="space-y-5">
                    <div class="relative">
                        <InputLabel
                            for="type_equi"
                            value="Tipo de Equipo"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-laptop"></i>
                            </span>
                            <TextInput
                                v-model="form.type_equi"
                                id="type_equi"
                                required
                                placeholder="Ej: Portátil"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.type_equi"
                        />
                    </div>

                    <div class="relative">
                        <InputLabel
                            for="characteristics"
                            value="Características"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <TextInput
                                v-model="form.characteristics"
                                id="characteristics"
                                required
                                placeholder="Ej: Lenovo Gaming i7"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.characteristics"
                        />
                    </div>

                    <div class="relative">
                        <InputLabel
                            for="serie_equi"
                            value="Número de Serie"
                            class="text-sm font-semibold text-gray-700 mb-2"
                        />
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
                            >
                                <i class="fas fa-barcode"></i>
                            </span>
                            <TextInput
                                v-model="form.serie_equi"
                                id="serie_equi"
                                required
                                placeholder="Ej: SN123456789"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                            />
                        </div>
                        <InputError
                            class="mt-1"
                            :message="form.errors.serie_equi"
                        />
                    </div>
                </div>

                <!-- Botones -->
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

        <!-- Modal para eliminación mejorado -->
        <Modal :show="showModalDel" @close="closeModaldel">
            <div class="p-8 w-full max-w-md mx-auto">
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-gradient-to-br from-red-500 to-red-600 rounded-full p-5 shadow-2xl animate-pulse"
                    >
                        <i
                            class="fas fa-exclamation-triangle text-5xl text-white"
                        ></i>
                    </div>
                </div>
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

        <!-- Modal para reparación mejorado -->
        <Modal :show="showModalrepa" @close="closeModalrepa">
            <div class="p-8 w-full max-w-md mx-auto">
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-full p-5 shadow-2xl"
                    >
                        <i class="fas fa-tools text-5xl text-white"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3">
                    ¿Enviar equipo a reparación?
                </h1>
                <div
                    class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6 rounded-r-lg"
                >
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-wrench text-purple-500 mr-2"></i>
                        El equipo no estará disponible para préstamo y su estado
                        cambiará a
                        <strong class="text-purple-600">Reparación</strong>.
                    </p>
                </div>
                <div class="flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModalrepa"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="reparationEquipment(v)"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-tools mr-2"></i>
                        Enviar a reparación
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Modal para reactivación mejorado -->
        <Modal :show="showModalReactive" @close="closeModalReactive">
            <div class="p-8 w-full max-w-md mx-auto">
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-gradient-to-br from-green-500 to-green-600 rounded-full p-5 shadow-2xl"
                    >
                        <i class="fas fa-check-circle text-5xl text-white"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3">
                    ¿Reactivar este equipo?
                </h1>
                <div
                    class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg"
                >
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Al reactivar el equipo, el sistema asumirá que está
                        <strong class="text-green-600"
                            >disponible en biblioteca</strong
                        >.
                    </p>
                </div>
                <div class="flex justify-center gap-3">
                    <SecondaryButton
                        @click="closeModalReactive"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="activateProgram(v)"
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-check-circle mr-2"></i>
                        Sí, Reactivar
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
