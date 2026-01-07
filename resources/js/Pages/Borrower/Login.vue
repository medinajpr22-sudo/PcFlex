<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    number_identification: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("borrower.login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div>
        <Head title="Iniciar Sesión - Portal de Usuario" />

        <div
            class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 to-indigo-600"
        >
            <div
                class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-lg"
            >
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Portal de Usuario
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Accede a tus préstamos y reservas
                    </p>
                </div>

                <form @submit.prevent="submit">
                    <div>
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
                            autofocus
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

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <span class="ml-2 text-sm text-gray-600"
                                >Recordarme</span
                            >
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <Link
                            :href="route('borrower.register')"
                            class="text-sm text-indigo-600 hover:text-indigo-900"
                        >
                            ¿No tienes cuenta? Regístrate
                        </Link>

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Iniciar Sesión
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <Link
                            :href="route('login')"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            ¿Eres bibliotecario? Ingresa aquí
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
