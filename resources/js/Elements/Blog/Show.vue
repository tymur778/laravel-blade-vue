<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm, usePage} from "@inertiajs/inertia-vue3";

const post = usePage().props.value.post;
const form = useForm({});
</script>

<template>
    <Head :title="$page.props.title" />

    <AppLayout>
        <div class="container max-w-2xl mx-auto mb-auto" >
            <div class="col-12 pt-2">
                <h1 v-if="$page.props.post" class="text-4xl uppercase mb-2">{{ post.title }}</h1>
                <p v-if="$page.props.post" class="text-black italic mb-6">{{ post.created_at }}</p>
                <p v-if="$page.props.post" class="text-black text-base font-thin mb-2" v-html="$page.props.post.content" />
                <p v-else class="text-warning">No such blog Post available</p>

                <span v-if="$page.props.auth.user">
                    {{ '(' }}<a :href="route('blog.edit', post.id)" class="text-gray-500 italic lowercase">Edit</a> |
                    <form @submit.prevent="form.delete(route('blog.destroy', post.id))" class="inline-block" method="POST">
                        <input type="hidden" name="id" value="post.id">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-gray-500 italic lowercase">Delete</button>
                    </form>{{ ')' }}
                </span>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: "Blog Show",
    props: {
        post: Object,
    },
}
</script>

<style scoped>

</style>
