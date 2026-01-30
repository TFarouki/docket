<script setup>
import { onMounted, ref, watch } from 'vue';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Arabic } from 'flatpickr/dist/l10n/ar.js';

const props = defineProps({
    modelValue: String,
    placeholder: String,
    required: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);
let picker = null;

onMounted(() => {
    picker = flatpickr(input.value, {
        enableTime: true,
        dateFormat: "Y-m-d H:i", // Internal format for the model
        altInput: true,
        altFormat: "d/m/Y H:i", // Display format requested: DD/MM/YYYY hh:mm
        time_24hr: true,
        locale: Arabic,
        defaultDate: props.modelValue,
        onChange: (selectedDates, dateStr) => {
            emit('update:modelValue', dateStr);
        },
    });
});

watch(() => props.modelValue, (newVal) => {
    if (picker && newVal !== picker.input.value) {
        picker.setDate(newVal, false);
    }
});
</script>

<template>
    <div class="relative">
        <input
            ref="input"
            type="text"
            :placeholder="placeholder"
            :required="required"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm transition-all"
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none opacity-40">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>
</template>

<style>
/* Custom styles to match the brand */
.flatpickr-day.selected {
    background: #c2410c !important; /* matches brand-600 colors usually */
    border-color: #c2410c !important;
}
.flatpickr-day.today {
    border-color: #f97316 !important;
}
</style>
