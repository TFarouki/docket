<script setup>
import AppLogo from '@/Components/AppLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useTheme } from '@/Services/ThemeService';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const { isDarkMode, toggleTheme } = useTheme();
const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};
</script>

<template>
    <div class="h-screen flex flex-col bg-gray-50 dark:bg-gray-900 overflow-hidden transition-colors duration-300">
        <!-- Top Navbar (AppBar) -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 h-16 flex items-center justify-between px-6 shadow-sm z-20 relative flex-shrink-0 transition-colors duration-300">
            <!-- Unit 1: Hamburger -->
            <div class="order-1 flex items-center gap-2">
                <button @click="toggleSidebar" class="p-2 -ml-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Unit 2: Centered Branding -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <Link :href="route('dashboard')" class="pointer-events-auto">
                    <AppLogo size="md" />
                </Link>
            </div>

            <!-- Unit 3: Right Actions -->
            <div class="flex items-center gap-2 order-3">
                <!-- Dark Mode Toggle -->
                <button @click="toggleTheme" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg v-if="isDarkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728L12 12.728"></path></svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <!-- User Dropdown -->
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                            <img :src="$page.props.auth.user.profile_photo_url" class="h-8 w-8 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm">
                            <span class="hidden md:block">{{ $page.props.auth.user.name }}</span>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                    'bg-white dark:bg-gray-800 shadow-md hidden sm:flex flex-col z-10 transition-all duration-300 flex-shrink-0 border-r border-gray-100 dark:border-gray-700',
                    isSidebarCollapsed ? 'w-20' : 'w-64'
                ]"
            >
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <Link :href="route('dashboard')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('dashboard'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Dashboard') }}</span>
                    </Link>

                    <div v-if="!isSidebarCollapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 px-2 transition-opacity duration-300">{{ $t('Matters') }}</div>
                    <div v-else class="h-4 mt-6 mb-2"></div>
                    
                    <Link :href="route('parties.index')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('parties.*'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('parties.*')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Parties') }}</span>
                    </Link>

                    <Link :href="route('calendar.index')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('calendar.index'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('calendar.index')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Calendar') }}</span>
                    </Link>

                    <Link :href="route('matters.index')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('matters.*'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('matters.*')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Matters') }}</span>
                    </Link>

                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('Tasks') }}</span>
                    </a>

                    <div v-if="!isSidebarCollapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 px-2 transition-opacity duration-300">{{ $t('Administration') }}</div>

                    <Link :href="route('users.index')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('users.*'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('users.*')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('User Management') }}</span>
                    </Link>

                    <Link v-if="$page.props.auth.user.roles.some(r => r.name === 'root')" :href="route('system-settings.index')" :class="{'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400': route().current('system-settings.*'), 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('system-settings.*')}" class="flex items-center px-4 py-3 rounded-lg transition-colors group">
                        <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span v-if="!isSidebarCollapsed" class="font-medium ms-3 whitespace-nowrap">{{ $t('System Settings') }}</span>
                    </Link>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 flex flex-col min-w-0 bg-gray-50 dark:bg-gray-900 overflow-hidden transition-colors duration-300">
                <!-- Page Heading -->
                <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
