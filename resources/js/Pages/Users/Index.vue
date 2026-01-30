<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
});

const search = ref('');

watch(search, (value) => {
    router.get(
        route('users.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
});
</script>

<template>
    <Head :title="$t('User Management')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $t('User Management') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Actions Bar -->
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <TextInput
                            v-model="search"
                            type="search"
                            class="block w-full"
                            :placeholder="$t('Search users...')"
                        />
                    </div>
                    <Link
                        :href="route('users.create')"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500 active:bg-brand-700 focus:outline-none transition ease-in-out duration-150"
                    >
                        {{ $t('Add New User') }}
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 dark:text-gray-200 uppercase bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">{{ $t('Name') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Email') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Roles') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end">{{ $t('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img :src="user.profile_photo_url" :alt="user.name" class="w-8 h-8 rounded-full object-cover">
                                            {{ user.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ user.email }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span 
                                                v-for="role in user.roles" 
                                                :key="role.id"
                                                class="px-2 py-0.5 text-[10px] bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-400 rounded-full border border-brand-100 dark:border-brand-800 font-medium"
                                            >
                                                {{ $t(role.name) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-end">
                                        <Link :href="route('users.edit', user.id)" class="font-medium text-brand-600 dark:text-brand-400 hover:underline">{{ $t('Edit') }}</Link>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="4" class="px-6 py-4">
                                        <EmptyState
                                            :title="$t('No users found')"
                                            :description="$t('Add users to the system to assign roles and permissions.')"
                                            :action="{ text: $t('Add New User'), href: route('users.create') }"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between" v-if="users.links.length > 3">
                        <div class="flex gap-1">
                             <template v-for="(link, key) in users.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 text-sm border rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    :class="{ 'bg-brand-50 dark:bg-brand-900/40 border-brand-500 dark:border-brand-600 text-brand-700 dark:text-brand-400': link.active, 'text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600': !link.active }"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 text-sm text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 rounded" v-html="link.label"></span>
                             </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
