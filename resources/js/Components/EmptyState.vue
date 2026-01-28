<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    action: {
        type: Object, // { text: 'Create New', href: '...' }
        default: null,
    },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12 text-center">
        <div class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700/50 rounded-full mb-4 text-gray-400 dark:text-gray-500">
            <slot name="icon">
                <!-- Default Icon (Folder Open) -->
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 012 2v-5a2 2 0 01-2-2H9a2 2 0 01-2-2v5a2 2 0 01-2 2z"></path>
                </svg>
            </slot>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ title }}</h3>
        <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm">{{ description }}</p>
        <div v-if="action" class="mt-6">
            <Link
                :href="action.href"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors duration-200"
            >
                <svg v-if="!action.icon" class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ action.text }}
            </Link>
        </div>
    </div>
</template>
