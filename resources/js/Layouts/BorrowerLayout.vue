<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('borrower.dashboard')">
                                    <h1 class="text-xl font-bold text-gray-800">
                                        Portal de Usuario
                                    </h1>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <Link
                                    :href="route('borrower.dashboard')"
                                    :class="
                                        route().current('borrower.dashboard')
                                            ? 'border-indigo-400'
                                            : 'border-transparent'
                                    "
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    :href="route('borrower.history')"
                                    :class="
                                        route().current('borrower.history')
                                            ? 'border-indigo-400'
                                            : 'border-transparent'
                                    "
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                                >
                                    Historial
                                </Link>
                                <Link
                                    :href="route('borrower.sanctions')"
                                    :class="
                                        route().current('borrower.sanctions')
                                            ? 'border-indigo-400'
                                            : 'border-transparent'
                                    "
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                                >
                                    Sanciones
                                </Link>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <span class="inline-flex rounded-md">
                                    <button
                                        @click="
                                            showingNavigationDropdown =
                                                !showingNavigationDropdown
                                        "
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                    >
                                        {{
                                            page.props.auth?.user?.name ||
                                            "Usuario"
                                        }}

                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                            />
                                        </svg>
                                    </button>
                                </span>

                                <div
                                    v-show="showingNavigationDropdown"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                >
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white"
                                    >
                                        <Link
                                            :href="route('borrower.logout')"
                                            method="post"
                                            as="button"
                                            class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                        >
                                            Cerrar Sesión
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <Link
                            :href="route('borrower.dashboard')"
                            :class="
                                route().current('borrower.dashboard')
                                    ? 'bg-indigo-50 border-indigo-400 text-indigo-700'
                                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'
                            "
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="route('borrower.history')"
                            :class="
                                route().current('borrower.history')
                                    ? 'bg-indigo-50 border-indigo-400 text-indigo-700'
                                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'
                            "
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                        >
                            Historial
                        </Link>
                        <Link
                            :href="route('borrower.sanctions')"
                            :class="
                                route().current('borrower.sanctions')
                                    ? 'bg-indigo-50 border-indigo-400 text-indigo-700'
                                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'
                            "
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                        >
                            Sanciones
                        </Link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ page.props.auth?.user?.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ page.props.auth?.user?.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <Link
                                :href="route('borrower.logout')"
                                method="post"
                                as="button"
                                class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out"
                            >
                                Cerrar Sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
