<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    models: Object,
    settings: Array,
});

const features = ['soft_delete', 'user_tracking'];

const isEnabled = (modelClass, feature) => {
    const setting = props.settings.find(s => s.model_class === modelClass && s.feature_name === feature);
    return setting ? setting.is_enabled : false;
};

const toggleFeature = (modelClass, feature) => {
    const currentStatus = isEnabled(modelClass, feature);
    router.post(route('system-settings.update'), {
        model_class: modelClass,
        feature_name: feature,
        is_enabled: !currentStatus,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="$t('System Settings')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $t('System Settings') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-8">
                            <h3 class="text-lg font-bold mb-2">{{ $t('Model Feature Management') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Enable or disable advanced model features. Note: Turning off Soft Delete will make deletions permanent.') }}
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="py-4 px-6 font-semibold">{{ $t('Model / Entity') }}</th>
                                        <th class="py-4 px-6 font-semibold text-center">{{ $t('Soft Delete') }}</th>
                                        <th class="py-4 px-6 font-semibold text-center">{{ $t('User Tracking') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    <tr v-for="(label, modelClass) in models" :key="modelClass" class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                                        <td class="py-4 px-6">
                                            <span class="font-medium capitalize">{{ $t(label) }}</span>
                                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">{{ modelClass }}</p>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <button 
                                                @click="toggleFeature(modelClass, 'soft_delete')"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                :class="isEnabled(modelClass, 'soft_delete') ? 'bg-brand-600' : 'bg-gray-200 dark:bg-gray-700'"
                                            >
                                                <span 
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                    :class="isEnabled(modelClass, 'soft_delete') ? 'translate-x-6' : 'translate-x-1'"
                                                />
                                            </button>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <button 
                                                @click="toggleFeature(modelClass, 'user_tracking')"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                :class="isEnabled(modelClass, 'user_tracking') ? 'bg-brand-600' : 'bg-gray-200 dark:bg-gray-700'"
                                            >
                                                <span 
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                    :class="isEnabled(modelClass, 'user_tracking') ? 'translate-x-6' : 'translate-x-1'"
                                                />
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
