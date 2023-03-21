<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps(['href', 'active']);

const classes = computed(() => props.active
    ? 'hover:bg-gray-900 sm:hover:bg-inherit block hover:text-white sm:hover:text-black sm:text-left sm:grow sm:mx-0 border-black uppercase text-black py-2 text-xl sm:text-base font-medium border-b-2'
    : 'hover:bg-gray-900 sm:hover:bg-inherit block hover:text-white sm:hover:text-black sm:text-left sm:grow sm:mx-0 border-black uppercase text-black py-2 text-xl sm:text-base font-medium'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
