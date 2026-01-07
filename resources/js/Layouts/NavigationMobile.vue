<template>
    <transition
        enter-active-class="transition ease-in-out duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in-out duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-show="$page.props.showingMobileMenu"
            class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
        ></div>
    </transition>
    <transition
        enter-active-class="transition ease-in-out duration-150"
        enter-from-class="opacity-0 transform -translate-x-20"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in-out duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0 transform -translate-x-20"
    >
        <aside
            v-show="$page.props.showingMobileMenu"
            class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white md:hidden"
        >
            <div class="py-4 text-gray-500">
                <Link
                    class="ml-6 text-lg font-bold text-gray-800"
                    :href="route('dashboard')"
                >
                    Equipres
                </Link>
                <ul class="mt-6">
                    <!-- Dashboard (visible para ambos roles) -->
                    <li class="relative px-6 py-3">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            <template #icon>
                                <i class="fas fa-tachometer-alt w-5 h-5"></i>
                            </template>
                            Dashboard
                        </ResponsiveNavLink>
                    </li>

                    <!-- Estadísticas -->
                    <li class="relative px-6 py-3">
                        <ResponsiveNavLink
                            :href="route('statistics.index')"
                            :active="route().current('statistics.index')"
                        >
                            <template #icon>
                                <i class="fas fa-chart-line w-5 h-5"></i>
                            </template>
                            Estadísticas
                        </ResponsiveNavLink>
                    </li>

                    <!-- Opción "Bibliotecarios" (solo visible para 'coordinador') -->
                    <template v-if="hasRole('coordinador')">
                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('users.index')"
                                :active="route().current('users.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-users w-5 h-5"></i>
                                </template>
                                Bibliotecarios
                            </ResponsiveNavLink>
                        </li>
                    </template>

                    <!-- Opciones visibles solo para 'bibliotecario' -->
                    <template v-if="hasRole('bibliotecario')">
                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('indexCard.index')"
                                :active="route().current('indexCard.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-id-card w-5 h-5"></i>
                                </template>
                                Fichas
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('programs.index')"
                                :active="route().current('programs.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-laptop-code w-5 h-5"></i>
                                </template>
                                Programas
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('environments.index')"
                                :active="route().current('environments.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-building w-5 h-5"></i>
                                </template>
                                Ambientes
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('reports.index')"
                                :active="route().current('reports.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-file-alt w-5 h-5"></i>
                                </template>
                                Reportes
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('Borrower_users.index')"
                                :active="
                                    route().current('Borrower_users.index')
                                "
                            >
                                <template #icon>
                                    <i class="fas fa-user-tag w-5 h-5"></i>
                                </template>
                                Usuarios
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('equipments.index')"
                                :active="route().current('equipments.index')"
                            >
                                <template #icon>
                                    <i class="fas fa-desktop w-5 h-5"></i>
                                </template>
                                Equipos
                            </ResponsiveNavLink>
                        </li>

                        <li class="relative px-6 py-3">
                            <ResponsiveNavLink
                                :href="route('historial.historico')"
                                :active="route().current('historial.historico')"
                            >
                                <template #icon>
                                    <i class="fas fa-history w-5 h-5"></i>
                                </template>
                                Historial
                            </ResponsiveNavLink>
                        </li>
                    </template>

                    <!-- Opción "Sobre nosotros" -->
                    <!-- <li class="relative px-6 py-3">
            <ResponsiveNavLink :href="route('about')" :active="route().current('about')">
              <template #icon>
                <i class="fas fa-info-circle w-5 h-5"></i>
              </template>
              Sobre nosotros
            </ResponsiveNavLink>
          </li> -->
                </ul>
            </div>
        </aside>
    </transition>
</template>

<script setup>
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

// Obtener los roles del usuario autenticado desde los props de Inertia
const roles = computed(() => page.props.auth.roles || []);

// Función para verificar si el usuario tiene un rol específico
const hasRole = (role) => roles.value.includes(role);
</script>
