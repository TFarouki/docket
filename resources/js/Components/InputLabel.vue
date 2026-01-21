<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
        <span v-if="value">
            {{ value }}
            <span v-if="required" class="text-red-500 ms-0.5">*</span>
        </span>
        <span v-else>
            <slot />
            <span v-if="required" class="text-red-500 ms-0.5">*</span>
        </span>
    </label>
</template>
