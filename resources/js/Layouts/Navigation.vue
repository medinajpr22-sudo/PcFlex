<template>
    <aside
        class="z-20 hidden w-64 overflow-y-auto bg-white md:block flex-shrink-0"
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
                    <NavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        <template #icon>
                            <i class="fas fa-tachometer-alt w-5 h-5"></i>
                        </template>
                        Dashboard
                    </NavLink>
                </li>

                <!-- Estadísticas -->
                <li class="relative px-6 py-3">
                    <NavLink
                        :href="route('statistics.index')"
                        :active="route().current('statistics.index')"
                    >
                        <template #icon>
                            <i class="fas fa-chart-line w-5 h-5"></i>
                        </template>
                        Estadísticas
                    </NavLink>
                </li>

                <!-- Opción "Bibliotecarios" (solo visible para 'coordinator') -->
                <template v-if="hasRole('coordinador')">
                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('users.index')"
                            :active="route().current('users.index')"
                        >
                            <template #icon>
                                <i class="fas fa-users w-5 h-5"></i>
                            </template>
                            Bibliotecarios
                        </NavLink>
                    </li>
                </template>

                <!-- Opciones visibles solo para 'bibliotecario' -->
                <template v-if="hasRole('bibliotecario')">
                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('indexCard.index')"
                            :active="route().current('indexCard.index')"
                        >
                            <template #icon>
                                <i class="fas fa-id-card w-5 h-5"></i>
                            </template>
                            Fichas
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('programs.index')"
                            :active="route().current('programs.index')"
                        >
                            <template #icon>
                                <i class="fas fa-laptop-code w-5 h-5"></i>
                            </template>
                            Programas
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('environments.index')"
                            :active="route().current('environments.index')"
                        >
                            <template #icon>
                                <i class="fas fa-building w-5 h-5"></i>
                            </template>
                            Ambientes
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('reports.index')"
                            :active="route().current('reports.index')"
                        >
                            <template #icon>
                                <i class="fas fa-file-alt w-5 h-5"></i>
                            </template>
                            Reportes
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('Borrower_users.index')"
                            :active="route().current('Borrower_users.index')"
                        >
                            <template #icon>
                                <i class="fas fa-user-tag w-5 h-5"></i>
                            </template>
                            Usuarios
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('equipments.index')"
                            :active="route().current('equipments.index')"
                        >
                            <template #icon>
                                <i class="fas fa-desktop w-5 h-5"></i>
                            </template>
                            Equipos
                        </NavLink>
                    </li>

                    <li class="relative px-6 py-3">
                        <NavLink
                            :href="route('historial.historico')"
                            :active="route().current('historial.historico')"
                        >
                            <template #icon>
                                <i class="fas fa-history w-5 h-5"></i>
                            </template>
                            Historial
                        </NavLink>
                    </li>
                </template>
                <!-- <li class="relative px-6 py-3">
            <NavLink :href="route('about')" :active="route().current('about')">
              <template #icon>
                <i class="fas fa-info-circle w-5 h-5"></i>
              </template>
              Sobre nosotros
            </NavLink>
          </li> -->
            </ul>
        </div>
    </aside>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export default {
    components: {
        NavLink,
        Link,
    },
    setup() {
        const page = usePage();

        // Obtener los roles del usuario autenticado desde los props de Inertia
        const roles = computed(() => page.props.auth.roles || []);

        console.log("Roles del usuario:", roles.value);

        // Función para verificar si el usuario tiene un rol específico
        const hasRole = (role) => roles.value.includes(role);

        return {
            hasRole,
        };
    },
};
</script>
