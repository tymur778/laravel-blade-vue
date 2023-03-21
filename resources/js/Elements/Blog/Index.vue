<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Elements/Blog/Card.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Head :title="$page.props.title" />

    <AppLayout>
        <div class="container max-w-2xl mx-auto mb-auto" >
            <div v-if="$page.props.success" class="alert alert-success">
                {{ $page.props.success }}
            </div>

            <div class="col-12 pt-2">
                <div v-if="$page.props.auth.user" class="col-4 mb-6">
                    <a :href="route('blog.create')" class="text-gray-500 italic">Add Post</a>
                </div>

                <Card v-for="(post) in $page.props.posts.data" :post="post" />

                <div class="pagination" v-html="$page.props.pagination"></div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: "Blog Index",
}
</script>

<style scoped>

</style>
