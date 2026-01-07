<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const notifications = ref([]);
let channel = null;

const addNotification = (notification) => {
    const id = Date.now();
    notifications.value.push({ id, ...notification });

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        removeNotification(id);
    }, 5000);
};

const removeNotification = (id) => {
    notifications.value = notifications.value.filter((n) => n.id !== id);
};

const getIcon = (type) => {
    const icons = {
        warning: "⏰",
        info: "📋",
        success: "✅",
        error: "❌",
    };
    return icons[type] || "ℹ️";
};

onMounted(() => {
    // Verificar que Echo esté disponible
    if (!window.Echo) {
        console.warn(
            "Echo no está configurado. Las notificaciones en tiempo real no funcionarán."
        );
        return;
    }

    try {
        // Escuchar eventos en el canal de bibliotecarios
        channel = window.Echo.channel("bibliotecarios")
            .listen("LoanExpiringSoon", (e) => {
                addNotification({
                    type: "warning",
                    title: "⏰ Préstamo por vencer",
                    message: `El préstamo de ${e.user_name} (${e.equipment_serie}) vence en ${e.hours_remaining} horas`,
                });
            })
            .listen("NewReservationCreated", (e) => {
                addNotification({
                    type: "info",
                    title: "📋 Nueva reserva",
                    message: `${e.user_name} ha creado una reserva para ${e.equipment_serie}`,
                });
            });

        console.log("✅ Notificaciones en tiempo real activadas");
    } catch (error) {
        console.error("Error al configurar notificaciones:", error);
    }
});

onUnmounted(() => {
    // Limpiar el canal cuando el componente se destruya
    if (channel) {
        window.Echo.leaveChannel("bibliotecarios");
    }
});
</script>

<template>
    <div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
        <TransitionGroup name="notification">
            <div
                v-for="notif in notifications"
                :key="notif.id"
                :class="{
                    'bg-yellow-50 border-yellow-400 text-yellow-800':
                        notif.type === 'warning',
                    'bg-blue-50 border-blue-400 text-blue-800':
                        notif.type === 'info',
                    'bg-green-50 border-green-400 text-green-800':
                        notif.type === 'success',
                    'bg-red-50 border-red-400 text-red-800':
                        notif.type === 'error',
                }"
                class="border-l-4 p-4 rounded-r-lg shadow-lg backdrop-blur-sm bg-opacity-95"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">{{
                                getIcon(notif.type)
                            }}</span>
                            <p class="font-bold text-sm">{{ notif.title }}</p>
                        </div>
                        <p class="text-sm">{{ notif.message }}</p>
                    </div>
                    <button
                        @click="removeNotification(notif.id)"
                        class="text-gray-500 hover:text-gray-700 transition-colors flex-shrink-0"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
/* Animaciones para las notificaciones */
.notification-enter-active {
    animation: slide-in 0.3s ease-out;
}

.notification-leave-active {
    animation: slide-out 0.3s ease-in;
}

@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>
