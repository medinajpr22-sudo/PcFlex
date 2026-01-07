<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name_user: "",
    lastname_user: "",
    number_identification: "",
    password: "",
    password_confirmation: "",
    user_type: "estudiante",
});

const submit = () => {
    form.post(route("borrower.register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <div>
        <Head title="Registro - Portal de Usuario" />

        <div
            class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 to-indigo-600"
        >
            <div
                class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-lg"
            >
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Crear Cuenta
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Regístrate para acceder al portal
                    </p>
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <label
                            for="name_user"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Nombre
                        </label>
                        <input
                            id="name_user"
                            v-model="form.name_user"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                            autofocus
                        />
                        <div
                            v-if="form.errors.name_user"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.name_user }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <label
                            for="lastname_user"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Apellido
                        </label>
                        <input
                            id="lastname_user"
                            v-model="form.lastname_user"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <div
                            v-if="form.errors.lastname_user"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.lastname_user }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <label
                            for="number_identification"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Número de Identificación
                        </label>
                        <input
                            id="number_identification"
                            v-model="form.number_identification"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <div
                            v-if="form.errors.number_identification"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.number_identification }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <label
                            for="user_type"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Tipo de Usuario
                        </label>
                        <select
                            id="user_type"
                            v-model="form.user_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="estudiante">Estudiante</option>
                            <option value="profesor">Profesor</option>
                            <option value="administrativo">
                                Administrativo
                            </option>
                            <option value="investigador">Investigador</option>
                        </select>
                        <div
                            v-if="form.errors.user_type"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.user_type }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Contraseña
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <div
                            v-if="form.errors.password"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <label
                            for="password_confirmation"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Confirmar Contraseña
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <Link
                            :href="route('borrower.login')"
                            class="text-sm text-indigo-600 hover:text-indigo-900"
                        >
                            ¿Ya tienes cuenta? Inicia sesión
                        </Link>

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Registrarse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
