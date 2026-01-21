<script setup>
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    parties: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('parties.index'),
        { search: value },
        { preserveState: true, replace: true }
    );
});
</script>

<template>
    <Head :title="$t('Parties')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Parties') }}</h2>
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
                            :placeholder="$t('Search by name, phone, or ID...')"
                        />
                    </div>
                    <Link
                        :href="route('parties.create')"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500 active:bg-brand-700 focus:outline-none transition ease-in-out duration-150"
                    >
                        {{ $t('Add New Party') }}
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">{{ $t('Name') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Phone') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('National ID') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end">{{ $t('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="party in parties.data" :key="party.id" class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ party.full_name }}
                                    </td>
                                    <td class="px-6 py-4">{{ party.phone || '-' }}</td>
                                    <td class="px-6 py-4">{{ party.national_id || '-' }}</td>
                                    <td class="px-6 py-4 text-end">
                                        <Link :href="route('parties.edit', party.id)" class="font-medium text-brand-600 hover:underline">{{ $t('Edit') }}</Link>
                                    </td>
                                </tr>
                                <tr v-if="parties.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        {{ $t('No parties found.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination (Simple) -->
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between" v-if="parties.links.length > 3">
                        <div class="flex gap-1">
                             <template v-for="(link, key) in parties.links" :key="key">
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
