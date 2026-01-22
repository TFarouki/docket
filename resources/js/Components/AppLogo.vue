<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    size: {
        type: String,
        default: 'md' // sm, md, lg, xl
    },
    withText: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div class="flex items-center group cursor-pointer select-none">
        <!-- Icon Container with Cutout D -->
        <div 
            :class="[
                'relative flex items-center justify-center flex-shrink-0 transition-all duration-500 ease-out',
                size === 'sm' ? 'h-8 w-8' : '',
                size === 'md' ? 'h-10 w-10' : '',
                size === 'lg' ? 'h-16 w-16' : '',
                size === 'xl' ? 'h-24 w-24' : '',
            ]"
        >
            <!-- Background Glow on Hover -->
            <div class="absolute inset-0 bg-brand-500/20 dark:bg-brand-400/20 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <ApplicationLogo class="block h-full w-full transition-transform duration-500 cubic-bezier group-hover:scale-110 group-hover:rotate-3" />
            
            <span 
                :class="[
                    'absolute inset-0 flex items-center justify-center font-black select-none font-sans text-white dark:text-gray-900 drop-shadow-sm',
                    size === 'sm' ? 'text-lg pb-0.5' : '',
                    size === 'md' ? 'text-2xl pb-1 pr-0.5' : '',
                    size === 'lg' ? 'text-4xl pb-1.5 pr-1' : '',
                    size === 'xl' ? 'text-6xl pb-2 pr-1.5' : '',
                ]"
            >
                D
            </span>
        </div>

        <!-- Remaining Text -->
        <span 
            v-if="withText"
            :class="[
                'font-bold tracking-tight transition-all duration-500 bg-clip-text text-transparent bg-gradient-to-r from-brand-700 to-brand-500 dark:from-brand-400 dark:to-brand-200',
                size === 'sm' ? 'text-xl -ml-2' : '',
                size === 'md' ? 'text-2xl -ml-2.5' : '',
                size === 'lg' ? 'text-5xl -ml-4' : '',
                size === 'xl' ? 'text-7xl -ml-6' : '',
            ]"
        >
            ocket
        </span>
    </div>
</template>

<style scoped>
.cubic-bezier {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>
