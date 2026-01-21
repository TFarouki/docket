<script setup>
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
});

const search = ref('');

// In a real app, you'd add server-side search to UserController
// but for now we just show the basics.
</script>

<template>
    <Head :title="$t('User Management')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('User Management') }}</h2>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">{{ $t('Name') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Email') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Roles') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end">{{ $t('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4">{{ user.email }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span 
                                                v-for="role in user.roles" 
                                                :key="role.id"
                                                class="px-2 py-0.5 text-[10px] bg-brand-50 text-brand-700 rounded-full border border-brand-100 font-medium"
                                            >
                                                {{ $t(role.name) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-end">
                                        <Link :href="route('users.edit', user.id)" class="font-medium text-brand-600 hover:underline">{{ $t('Edit') }}</Link>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        {{ $t('No users found.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between" v-if="users.links.length > 3">
                        <div class="flex gap-1">
                             <template v-for="(link, key) in users.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 text-sm border rounded hover:bg-gray-50"
                                    :class="{ 'bg-brand-50 border-brand-500 text-brand-700': link.active, 'text-gray-600 border-gray-300': !link.active }"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 text-sm text-gray-400 border border-gray-200 rounded" v-html="link.label"></span>
                             </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
