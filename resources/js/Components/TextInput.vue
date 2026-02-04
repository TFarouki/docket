<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    invalid: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md shadow-sm"
        :class="{
            'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600': !invalid,
            'border-red-500 dark:border-red-500 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-500': invalid,
        }"
        v-model="model"
        ref="input"
        :aria-invalid="invalid"
    />
</template>
