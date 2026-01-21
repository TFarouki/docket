<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    modelValue: [String, Number, Array],
    options: {
        type: Array,
        default: () => [],
    },
    label: String,
    placeholder: {
        type: String,
        default: 'Select an option...',
    },
    required: {
        type: Boolean,
        default: false,
    },
    allowAdd: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'add']);

const isOpen = ref(false);
const search = ref('');
const container = ref(null);

const filteredOptions = computed(() => {
    return props.options.filter(option => 
        option.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const selectedOption = computed(() => {
    return props.options.find(opt => opt.id === props.modelValue) || null;
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        search.value = '';
    }
};

const selectOption = (option) => {
    emit('update:modelValue', option.id);
    isOpen.value = false;
    search.value = '';
};

const handleAdd = () => {
    if (search.value.trim()) {
        emit('add', search.value.trim());
        search.value = '';
    }
};

const handleClickOutside = (event) => {
    if (container.value && !container.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div class="relative" ref="container">
        <label v-if="label" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div 
            @click="toggleDropdown"
            class="flex items-center justify-between w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm cursor-pointer bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
        >
            <span v-if="selectedOption" class="truncate">{{ selectedOption.name }}</span>
            <span v-else class="text-gray-400 dark:text-gray-500 truncate">{{ placeholder }}</span>
            
            <svg class="h-5 w-5 text-gray-400 translate-y-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </div>

        <!-- Dropdown -->
        <div v-if="isOpen" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden transition-all duration-200">
            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <input 
                    v-model="search"
                    type="text"
                    class="w-full px-3 py-1.5 text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded focus:border-brand-500 focus:ring-brand-500"
                    :placeholder="$t('Search or add...')"
                    @click.stop
                    autofocus
                >
            </div>
            
            <ul class="max-h-60 overflow-y-auto py-1">
                <li 
                    v-for="option in filteredOptions" 
                    :key="option.id"
                    @click="selectOption(option)"
                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/40 hover:text-brand-700 dark:hover:text-brand-400 cursor-pointer transition-colors"
                    :class="{'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-400 font-semibold': option.id === modelValue}"
                >
                    {{ option.name }}
                </li>
                
                <li v-if="filteredOptions.length === 0 && !allowAdd" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400 italic">
                    {{ $t('No matches found') }}
                </li>

                <!-- Add New Action -->
                <li 
                    v-if="allowAdd && search && !filteredOptions.some(opt => opt.name.toLowerCase() === search.toLowerCase())"
                    @click="handleAdd"
                    class="px-4 py-3 text-sm text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/40 cursor-pointer flex items-center gap-2 border-t border-gray-100 dark:border-gray-700 mt-1"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>{{ $t('Add') }} "{{ search }}"</span>
                </li>
            </ul>
        </div>
    </div>
</template>
