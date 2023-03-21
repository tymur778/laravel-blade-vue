<script setup>
defineProps(['message']);
</script>

<template>
    <div v-show="message">
        {{ message }}
    </div>
</template>
