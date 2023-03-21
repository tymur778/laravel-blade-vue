<script setup>
import Header from '@/Layouts/Partials/Header.vue';
import Footer from '@/Layouts/Partials/Footer.vue';

</script>

<template>
    <main class="flex flex-col h-screen justify-between px-5 sm:px-0">
        <Header />
        <slot />
        <Footer />
    </main>
</template>

<script>
export default {
    name: "layout",
}
</script>

<style scoped>

</style>
