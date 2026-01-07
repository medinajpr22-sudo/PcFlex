<template>
    <div
        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
    >
        <div :class="gradientClass" class="p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p
                        class="text-sm opacity-90 uppercase tracking-wide font-semibold"
                    >
                        {{ title }}
                    </p>
                    <h3 class="text-4xl font-bold mt-2">{{ value }}</h3>
                    <p v-if="subtitle" class="text-sm mt-2 opacity-80">
                        {{ subtitle }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <i :class="icon" class="text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
        <div
            v-if="$slots.footer"
            class="bg-gray-50 px-6 py-3 border-t border-gray-100"
        >
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    subtitle: {
        type: String,
        default: "",
    },
    icon: {
        type: String,
        default: "fas fa-chart-bar",
    },
    color: {
        type: String,
        default: "blue",
        validator: (value) =>
            [
                "blue",
                "green",
                "purple",
                "red",
                "yellow",
                "indigo",
                "pink",
            ].includes(value),
    },
});

const gradientClass = computed(() => {
    const gradients = {
        blue: "bg-gradient-to-br from-blue-500 to-blue-600",
        green: "bg-gradient-to-br from-green-500 to-green-600",
        purple: "bg-gradient-to-br from-purple-500 to-purple-600",
        red: "bg-gradient-to-br from-red-500 to-red-600",
        yellow: "bg-gradient-to-br from-yellow-500 to-yellow-600",
        indigo: "bg-gradient-to-br from-indigo-500 to-indigo-600",
        pink: "bg-gradient-to-br from-pink-500 to-pink-600",
    };
    return gradients[props.color];
});
</script>
