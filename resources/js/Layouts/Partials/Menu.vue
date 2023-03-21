<script setup>
import NavLink from '@/Components/NavLinkFront.vue';
import {ref} from "vue";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <nav class="bg-white">
        <div class="relative flex justify-center h-16 items-center">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
                <button @click="showingNavigationDropdown = ! showingNavigationDropdown" type="button" id="mobile-menu-button" class="inline-flex items-center justify-center text-gray-700 hover:bg-gray-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path :class="{'hidden': showingNavigationDropdown, 'block': ! showingNavigationDropdown }" stroke-linecap="square" stroke-linejoin="square" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        <path :class="{'hidden': ! showingNavigationDropdown, 'block': showingNavigationDropdown }" stroke-linecap="square" stroke-linejoin="square" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-1 items-center">
                <div class="hidden flex sm:flex justify-between w-full">
                    <div class="flex space-x-0 justify-between w-full">
                        <NavLink v-for="(menu) in $page.props.menu" :href=menu.url :active="checkUrlMatch(menu.url)">{{ menu.title }}</NavLink>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
            <div class="space-y-1 pt-2 pb-3">
                <NavLink v-for="(menu) in $page.props.menu" :href=menu.url :active="checkUrlMatch(menu.url)">{{ menu.title }}</NavLink>
                <NavLink v-if="$page.props.auth.user" :href="route('logout')">Log Out</NavLink>
            </div>
        </div>

        <div class="uppercase absolute top-0 right-0 p-2 opacity-0">
            <NavLink v-if="$page.props.auth.user" :href="route('login')" method="post" as="button">
                Log Out
            </NavLink>
            <NavLink v-else :href="route('logout')" class="p-2" method="post" as="button">
                Log In
            </NavLink>
        </div>
    </nav>
</template>

<script>
export default {
    name: "menuFront",
    mounted() {},
    methods: {
        checkUrlMatch(url) {
            if (url === '/') {
                return window.location.pathname === url;
            } else {
                return window.location.pathname.startsWith(url);
            }
        },
    },
}
</script>

<style scoped>

</style>
