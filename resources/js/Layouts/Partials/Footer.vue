<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <div class="container max-w-2xl mx-auto mt-12">
        <footer class="py-2 text-right">
            <div>
                <span class="text-gray-500">Rendered with {{ renderedWith }}.
                </span>
                <a :href="route('set_render_type')" class="underline">Swap to
                    <span v-if="$page.props.renderType === $page.props.inertiaType">
                        Blade Templates
                    </span>
                    <span v-else>
                        Vue.js + Inertia components
                    </span>
                </a>
            </div>
            <div class="">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ $page.props.currentYear }}<a href="https://flowbite.com/" class="hover:underline"></a></span>
                [<a :href="123" class="text-gray-500 pr-1 last:pr-0">Model.</a>
                <a :href="123" class="text-gray-500 pr-1 last:pr-0">Controller.</a>
                <a :href="123" class="text-gray-500 pr-1 last:pr-0">View</a>]
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                    PHP:{{ $page.props.phpVersion }} Laravel:{{ $page.props.laravelVersion }}
                </span>
            </div>

        </footer>
    </div>
</template>

<script>
export default {
    name: "Footer",
    computed: {
        renderedWith() {
            return this.$page.props.inertiaType.charAt(0).toUpperCase()
                 + this.$page.props.inertiaType.slice(1);
        },
    }
}
</script>

<style scoped>

</style>
