<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import WarningButton from "@/Components/WarningButton.vue";
import GreenButton from "@/Components/GreenButton.vue";
import { useForm } from "@inertiajs/vue3";
import EditButton from "@/Components/EditButton.vue";
import Swal from "sweetalert2";
const showModalvue = ref(false);
const showModalForm = ref(false);
const showModalDel = ref(false);

const form = useForm({
    names: "",
});

const v = ref({ id: "", names: "" });

const title = ref("");
const operation = ref(1);
const msj = ref("");
const classMsj = ref("hidden");

const openModalvue = (indexCard) => {
    showModalvue.value = true;
    v.value = { ...environments };
};

const openModalForm = (op, environments) => {
    showModalForm.value = true;
    operation.value = op;
    if (op === 1) {
        title.value = "Crear Lugar de traslado";
        form.reset();
    } else {
        title.value = "Editar Lugar de traslado";
        v.value = { ...environments };
        form.names = environments.names;
    }
};

const openModalDel = (environments) => {
    showModalDel.value = true;
    v.value = { ...environments };
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
    environments: {
        type: Object,
        required: true,
    },
});

const save = () => {
    if (operation.value === 1) {
        form.post(route("environments.store"), {
            onSuccess: () => {
                ok("Ambiente Creada con éxito");
            },
        });
    } else {
        form.put(route("environments.update", v.value.id), {
            onSuccess: () => {
                ok("Ambiente Editada con éxito");
            },
        });
    }
};

const ok = (m) => {
    closeModalForm();
    closeModaldel();
    form.reset();
    msj.value = m;
    classMsj.value = "Ambiente";
    setTimeout(() => {
        classMsj.value = "hidden";
    }, 8000);
};

const deleteprogram = () => {
    form.delete(route("environments.destroy", v.value.id), {
        onSuccess: () => {
            ok("Ambiente inactivado con éxito");
        },
    });
};

const activateProgram = (indexCard) => {
    Swal.fire({
        title: "¿Reactivar ambiente?",
        text: "El ambiente volverá a estar activo en el sistema.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#10b981",
        cancelButtonColor: "#6b7280",
        confirmButtonText: '<i class="fas fa-check mr-2"></i> Sí, reactivar',
        cancelButtonText: '<i class="fas fa-times mr-2"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.put(route("environments.activate", indexCard.id), {
                onSuccess: () => {
                    ok("Ambiente activado con éxito");
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Lugar de Traslado" />
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
                                class="fas fa-location-arrow text-3xl text-white"
                            ></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-3xl text-white">
                                Lugares de Traslado
                            </h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                Gestión de ambientes y ubicaciones
                            </p>
                        </div>
                    </div>
                    <GreenButton
                        @click="openModalForm(1)"
                        class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i> Crear
                    </GreenButton>
                </div>
            </div>
        </template>

        <div
            class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-xl p-6"
        >
            <!-- Alerta de éxito -->
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
                                        <i class="fas fa-building mr-2"></i>
                                        Ambientes
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
                                v-for="environments in environments.data"
                                :key="environments.id"
                                class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3"
                                        >
                                            <i
                                                class="fas fa-location-arrow text-white"
                                            ></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{ environments.names }}</span
                                        >
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="environments.status === 'activo'"
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
                                            v-if="
                                                environments.status === 'activo'
                                            "
                                        >
                                            <EditButton
                                                @click="
                                                    openModalForm(
                                                        2,
                                                        environments
                                                    )
                                                "
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            >
                                                <i class="fas fa-edit mr-1"></i>
                                                Editar
                                            </EditButton>
                                            <DangerButton
                                                @click="
                                                    openModalDel(environments)
                                                "
                                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            >
                                                <i class="fas fa-ban mr-1"></i>
                                                Inactivar
                                            </DangerButton>
                                        </template>
                                        <template v-else>
                                            <GreenButton
                                                @click="
                                                    activateProgram(
                                                        environments
                                                    )
                                                "
                                                class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
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
                <div
                    class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9"
                >
                    <Pagination :links="environments.links" />
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
                        <i
                            class="fas fa-location-arrow text-4xl text-white"
                        ></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 text-center">
                        {{ title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Complete la información del ambiente
                    </p>
                </div>

                <!-- Formulario -->
                <div class="space-y-4">
                    <div class="relative">
                        <InputLabel
                            for="names"
                            value="Lugar de traslado"
                            class="mb-2 flex items-center text-gray-700 font-semibold"
                        >
                            <i
                                class="fas fa-map-marker-alt mr-2 text-indigo-500"
                            ></i>
                            Lugar de traslado
                        </InputLabel>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none"
                            >
                                <i class="fas fa-building"></i>
                            </span>
                            <TextInput
                                v-model="form.names"
                                required
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                                placeholder="Ej: Sala de Conferencias A"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.names" />
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 mt-6">
                    <SecondaryButton
                        @click="closeModalForm"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </SecondaryButton>
                    <GreenButton
                        @click="save"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium"
                    >
                        <i class="fas fa-save mr-2"></i> Guardar
                    </GreenButton>
                </div>
            </div>
        </Modal>

        <!-- Modal para eliminación con diseño mejorado -->
        <Modal :show="showModalDel" @close="closeModaldel">
            <div
                class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md mx-auto"
            >
                <!-- Icono de advertencia -->
                <div class="flex justify-center p-6 bg-red-50">
                    <i
                        class="fas fa-exclamation-triangle text-6xl text-red-600"
                    ></i>
                </div>

                <!-- Contenido -->
                <div class="p-6">
                    <h2
                        class="text-2xl font-bold text-gray-900 mb-4 text-center"
                    >
                        ¿Estás seguro?
                    </h2>
                    <p class="text-gray-600 text-center mb-2">
                        Esta acción cambiará el estado del ambiente a
                        <span class="font-semibold text-red-600">Inactivo</span
                        >.
                    </p>
                    <p class="text-sm text-gray-500 text-center">
                        La información no se eliminará permanentemente.
                    </p>
                </div>

                <!-- Botones -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <SecondaryButton
                        @click="closeModaldel"
                        class="px-6 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium"
                    >
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteprogram"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium shadow-md"
                    >
                        <i class="fas fa-check mr-2"></i> Sí, seguro
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
