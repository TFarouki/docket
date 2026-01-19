<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const locale = computed(() => usePage().props.locale || 'en');
</script>

<template>
    <div class="relative ms-3">
        <div class="flex items-center space-x-2">
            <a href="/lang/en" :class="{'font-bold text-brand-700': locale === 'en', 'text-gray-500': locale !== 'en'}" class="text-sm hover:text-brand-600">EN</a>
            <span class="text-gray-300">|</span>
            <a href="/lang/fr" :class="{'font-bold text-brand-700': locale === 'fr', 'text-gray-500': locale !== 'fr'}" class="text-sm hover:text-brand-600">FR</a>
            <span class="text-gray-300">|</span>
            <a href="/lang/ar" :class="{'font-bold text-brand-700': locale === 'ar', 'text-gray-500': locale !== 'ar'}" class="text-sm hover:text-brand-600">AR</a>
        </div>
    </div>
</template>
