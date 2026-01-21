<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    matter: Object,
});
</script>

<template>
    <Head :title="matter.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ matter.title }}</h2>
                <span
                    class="px-3 py-1 text-sm rounded-full font-medium"
                    :class="{
                        'bg-green-100 text-green-800': matter.status === 'open',
                        'bg-gray-100 text-gray-800': matter.status === 'closed',
                        'bg-yellow-100 text-yellow-800': matter.status === 'pending',
                        'bg-red-100 text-red-800': matter.status === 'archived'
                    }"
                >
                    {{ $t(matter.status) }}
                </span>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Basic Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Reference') }}</span>
                            <span class="block text-lg font-mono text-gray-900 mt-1">{{ matter.reference_number || '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Client') }}</span>
                            <Link v-if="matter.party" :href="route('parties.edit', matter.party.id)" class="block text-lg text-brand-600 hover:underline mt-1 font-medium">
                                {{ matter.party.full_name }}
                            </Link>
                            <span v-else class="text-gray-400">-</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Responsible Lawyer') }}</span>
                            <span class="block text-lg text-gray-900 mt-1">{{ matter.responsible_lawyer?.name || '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Nature') }}</span>
                            <span class="block text-lg text-gray-900 mt-1 capitalize">{{ $t(matter.type) }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Case Type') }}</span>
                            <span class="block text-lg text-gray-900 mt-1 capitalize">{{ matter.case_type ? $t(matter.case_type) : '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Court / Tribunal') }}</span>
                            <span class="block text-lg text-gray-900 mt-1">{{ matter.court_name || '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">{{ $t('Agreed Fee') }}</span>
                            <span class="block text-lg text-gray-900 mt-1 font-mono">{{ matter.agreed_fee ? Number(matter.agreed_fee).toFixed(2) : '-' }}</span>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-gray-100 pt-4" v-if="matter.description">
                        <span class="block text-sm font-medium text-gray-500">{{ $t('Description') }}</span>
                        <p class="mt-1 text-gray-700 whitespace-pre-line">{{ matter.description }}</p>
                    </div>
                </div>

                <!-- Court Cases Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">{{ $t('Court Cases / Procedures') }}</h3>
                        <Link
                            :href="route('court-cases.create', { matter_id: matter.id })"
                            class="text-sm font-medium text-brand-600 hover:text-brand-500"
                        >
                            + {{ $t('Add Court Case') }}
                        </Link>
                    </div>

                    <div v-if="matter.court_cases && matter.court_cases.length > 0">
                        <ul class="divide-y divide-gray-100">
                            <li v-for="courtCase in matter.court_cases" :key="courtCase.id" class="p-6 hover:bg-gray-50 transition block">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">{{ courtCase.court_name }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <span class="font-medium text-gray-700">{{ $t('Case No') }}:</span> {{ courtCase.case_number }}
                                        </p>
                                        <div class="mt-2 text-sm text-gray-600 flex gap-4">
                                            <span v-if="courtCase.judge_name">{{ $t('Judge') }}: {{ courtCase.judge_name }}</span>
                                            <span v-if="courtCase.opponent_lawyer">{{ $t('Opponent Lawyer') }}: {{ courtCase.opponent_lawyer }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ courtCase.current_stage || $t('Ongoing') }}
                                        </span>
                                        <div class="mt-4">
                                             <Link
                                                :href="route('hearings.create', { court_case_id: courtCase.id })"
                                                class="text-xs font-medium text-brand-600 hover:text-brand-500 border border-brand-200 rounded px-2 py-1 bg-brand-50 hover:bg-brand-100"
                                            >
                                                + {{ $t('Add Session') }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hearings List -->
                                <div class="mt-4" v-if="courtCase.hearings && courtCase.hearings.length > 0">
                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $t('Sessions History') }}</h5>
                                    <div class="bg-white border rounded-md overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Date') }}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Outcome') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr v-for="hearing in courtCase.hearings" :key="hearing.id">
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        {{ new Date(hearing.session_date).toLocaleDateString() }}
                                                        <span class="text-gray-400 text-xs ml-1">{{ new Date(hearing.session_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ hearing.outcome || '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else class="p-6 text-center text-gray-500">
                        {{ $t('No court cases registered for this matter yet.') }}
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
