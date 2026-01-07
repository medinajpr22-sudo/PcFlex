<template>
    <form @submit.prevent="submitSearch" class="relative">
        <div class="relative">
            <span
                class="absolute inset-y-0 left-0 flex items-center pl-4 text-indigo-400"
            >
                <i class="fas fa-search"></i>
            </span>
            <input
                v-model="localSearch"
                type="text"
                class="w-full pl-11 pr-4 py-2.5 border-2 border-indigo-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400"
                placeholder="Buscar ficha..."
            />
        </div>
    </form>
</template>

<script>
export default {
    props: {
        search: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            localSearch: this.search, // Inicializa con el valor de búsqueda
        };
    },
    watch: {
        search(newValue) {
            // Actualiza localSearch si la prop search cambia
            this.localSearch = newValue;
        },
        localSearch(newValue) {
            this.$emit("update:search", newValue);

            // Si el campo de búsqueda está vacío, realizar la búsqueda automáticamente
            if (newValue.trim() === "") {
                this.submitSearch();
            }
        },
    },
    methods: {
        submitSearch() {
            // Si el campo está vacío, enviamos una búsqueda sin parámetros para restablecer los resultados
            const searchQuery =
                this.localSearch.trim() === ""
                    ? {}
                    : { search: this.localSearch };

            // Realizamos la petición al servidor con el valor de búsqueda (o sin ella si está vacío)
            this.$inertia.get(this.route("indexCard.index"), searchQuery);
        },
    },
};
</script>
