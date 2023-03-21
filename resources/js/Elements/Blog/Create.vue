<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Head, usePage} from '@inertiajs/inertia-vue3';
import InputError from '@/Components/InputErrorFront.vue';
import InputLabel from '@/Components/InputLabelFront.vue';
import TextInput from '@/Components/TextInputFront.vue';
import TextareaInput from '@/Components/TextareaFront.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const post = usePage().props.value.post;

const form = useForm({
    title: post ? post.title : null,
    content: post ? post.content : null,
});

const saveButtonText = post ? 'Save' : 'Add';
</script>

<template>
    <Head :title="$page.props.title" />

    <AppLayout>
        <div class="container max-w-2xl mx-auto mb-auto" >
            <form @submit.prevent="submitForm(form)">
<!--                <input v-if="post" type="hidden" name="_method" value="PATCH">-->
                <div class="mb-4">
                    <InputLabel for="title" value="Post title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" />
                    <InputError class="" :message="form.errors.title" />
                </div>

                <div class="mb-4">
                    <InputLabel for="content" value="Post content" />
                    <TextareaInput id="content" type="text" class="mt-1 block w-full" v-model="form.content" />
                    <InputError class="" :message="form.errors.content" />
                </div>

                <div class="mb-4">
                    <button type="submit"
                            :disabled="form.processing"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        {{ saveButtonText }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: "Blog Create",
    methods: {
        submitForm(form) {
            if (this.$page.props.post) {
                form.patch(route(this.$page.props.action, this.$page.props.post.id))
            } else {
                form.post(route(this.$page.props.action))
            }
        },
    },
}
</script>

<style scoped>

</style>
