<script setup>
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted, onUnmounted } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    placeholder: {
        type: String,
        default: 'Search...',
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const inputRef = ref(null);
const shortcutKey = ref('Ctrl K');

const clear = () => {
    model.value = '';
    // Focus the input after clearing
    if (inputRef.value) {
        inputRef.value.focus();
    }
};

const handleKeydown = (e) => {
    // Check for Cmd+K (Mac) or Ctrl+K (Windows/Linux)
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        inputRef.value?.focus();
    }
    // Check for / (if not already focusing an input)
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA' && document.activeElement.isContentEditable !== true) {
        e.preventDefault();
        inputRef.value?.focus();
    }
};

onMounted(() => {
    if (navigator.userAgent.indexOf('Mac') !== -1) {
        shortcutKey.value = '⌘K';
    }
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div class="relative text-gray-500 focus-within:text-brand-600 dark:focus-within:text-brand-400">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 transition-colors duration-200" :class="{'text-gray-400': !model, 'text-brand-500': model}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <TextInput
            ref="inputRef"
            v-model="model"
            :placeholder="placeholder"
            class="pl-10 pr-10 block w-full transition-all duration-200"
            v-bind="$attrs"
        />

        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <div v-if="!model && !loading" class="hidden sm:flex pointer-events-none items-center">
                <span class="text-xs text-gray-400 border border-gray-300 rounded px-1.5 py-0.5 bg-gray-50">{{ shortcutKey }}</span>
            </div>

            <button
                v-if="model && !loading"
                @click="clear"
                type="button"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-brand-600 transition-colors"
                :aria-label="$t ? $t('Clear search') : 'Clear search'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <svg
                v-if="loading"
                class="animate-spin h-5 w-5 text-brand-500"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
        </div>
    </div>
</template>
