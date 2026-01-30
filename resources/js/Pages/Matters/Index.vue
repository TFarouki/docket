<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import SearchInput from '@/Components/SearchInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    matters: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const isLoading = ref(false);

watch(search, (value) => {
    router.get(
        route('matters.index'),
        { search: value },
        {
            preserveState: true,
            replace: true,
            onStart: () => (isLoading.value = true),
            onFinish: () => (isLoading.value = false),
        }
    );
});
</script>

<template>
    <Head :title="$t('Matters')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Matters') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Actions Bar -->
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <SearchInput
                            v-model="search"
                            class="block w-full"
                            :placeholder="$t('Search by title or reference...')"
                            :loading="isLoading"
                            :aria-label="$t('Search matters')"
                        />
                    </div>
                    <Link
                        :href="route('matters.create')"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500 active:bg-brand-700 focus:outline-none transition ease-in-out duration-150"
                    >
                        {{ $t('New Matter') }}
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">{{ $t('Reference') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Title') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Client') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Lawyer') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ $t('Status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end">{{ $t('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="matter in matters.data" :key="matter.id" class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">
                                        {{ matter.reference_number || '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ matter.title }}
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $t(matter.type) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Link v-if="matter.party" :href="route('parties.edit', matter.party.id)" class="text-brand-600 hover:underline">
                                            {{ matter.party.full_name }}
                                        </Link>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ matter.responsible_lawyer?.name || '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': matter.status === 'open',
                                                'bg-gray-100 text-gray-800': matter.status === 'closed',
                                                'bg-yellow-100 text-yellow-800': matter.status === 'pending',
                                                'bg-red-100 text-red-800': matter.status === 'archived'
                                            }"
                                        >
                                            {{ $t(matter.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-end">
                                        <Link :href="route('matters.show', matter.id)" class="font-medium text-brand-600 hover:underline">{{ $t('View') }}</Link>
                                    </td>
                                </tr>
                                <tr v-if="matters.data.length === 0">
                                    <td colspan="6" class="px-6 py-4">
                                        <EmptyState
                                            :title="$t('No matters found')"
                                            :description="$t('Create a new matter to manage cases or procedures.')"
                                            :action="{ text: $t('New Matter'), href: route('matters.create') }"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between" v-if="matters.links.length > 3">
                        <div class="flex gap-1">
                             <template v-for="(link, key) in matters.links" :key="key">
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
