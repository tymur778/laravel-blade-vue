<script setup>
import {useForm} from "@inertiajs/inertia-vue3";

const form = useForm({});
</script>

<template>
    <div class="mb-10">
        <a class="text-black uppercase text-xl bold visited:black mr-2" :href="route('blog.show', post.id)">{{ post.title }}</a>
        <span v-if="$page.props.auth.user">
            {{ '(' }}<a :href="route('blog.edit', post.id)" class="text-gray-500 italic lowercase">Edit</a> |
            <form @submit.prevent="form.delete(route('blog.destroy', post.id))" class="inline-block" method="POST">
                <input type="hidden" name="id" value="post.id">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-gray-500 italic lowercase">Delete</button>
            </form>{{ ')' }}
        </span>
        <p class="italic text-black">{{ post.created_at }}</p>
    </div>

</template>

<script>
export default {
    name: "Blog Card",
    props: {
        post: Object,
    },
};
</script>
