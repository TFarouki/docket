<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};
</script>

<template>
    <div class="h-screen flex flex-col bg-gray-50 overflow-hidden">
        <!-- Top Navbar (AppBar) - Full Width -->
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 shadow-sm z-20 relative flex-shrink-0">
            <!-- Unit 1: Hamburger (Now at Start) -->
            <div class="order-1 flex items-center">
                <button @click="toggleSidebar" class="p-2 -ml-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Unit 2: Centered Branding -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <Link :href="route('dashboard')" class="flex items-center gap-2 pointer-events-auto">
                    <ApplicationLogo class="block h-9 w-auto text-brand-600 fill-current" />
                    <span class="text-xl font-bold text-brand-700 tracking-tight">Docket</span>
                </Link>
            </div>

            <!-- Unit 3: User Info (Now at End) -->
            <div class="flex items-center gap-4 order-3">
                <!-- User Dropdown -->
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <span>{{ $page.props.auth.user.name }}</span>
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')"> {{ $t('Profile') }} </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button"> {{ $t('Log Out') }} </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </header>

        <!-- Body Area -->
        <div class="flex-1 flex overflow-hidden min-h-0">
            <!-- Sidebar -->
            <aside 
                :class="[
                    'bg-white shadow-md hidden sm:flex flex-col z-10 transition-all duration-300 flex-shrink-0 border-r border-gray-100',
                    isSidebarCollapsed ? 'w-20' : 'w-64'
                ]"
            >
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <Link :href="route('dashboard')" :class="{'bg-brand-50 text-brand-700': route().current('dashboard'), 'text-gray-600 hover:bg-gray-50': !route().current('dashboard')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Dashboard') }}</span>
                    </Link>

                    <div v-if="!isSidebarCollapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 px-2 transition-opacity duration-300">{{ $t('Matters') }}</div>
                    <div v-else class="h-4 mt-6 mb-2"></div> <!-- Spacer when collapsed -->
                    
                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Clients') }}</span>
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Matters') }}</span>
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Tasks') }}</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
                <div class="flex-1 overflow-y-auto p-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
