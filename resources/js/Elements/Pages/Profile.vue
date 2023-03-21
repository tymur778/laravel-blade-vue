<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Head :title="$page.props.title" />

    <AppLayout>
        <div class="container max-w-2xl mx-auto mb-auto" v-html="$page.props.content"></div>
    </AppLayout>
</template>

<script>
export default {
    name: "Profile",
}
</script>

<style scoped>

</style>
