<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const cleanLinks = computed(() => {
    return props.links.map(link => {
        let label = link.label;
        if (label === '&laquo; Previous') {
            label = 'Previous';
        } else if (label === 'Next &raquo;') {
            label = 'Next';
        }
        return { ...link, label };
    });
});
</script>

<template>
    <nav v-if="links.length > 3" aria-label="Pagination" class="flex items-center justify-between">
         <div class="flex gap-1 flex-wrap">
             <template v-for="(link, key) in cleanLinks" :key="key">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1 text-sm border rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center min-w-[32px]"
                    :class="{
                        'bg-brand-50 dark:bg-brand-900/40 border-brand-500 dark:border-brand-600 text-brand-700 dark:text-brand-400 font-bold': link.active,
                        'text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600': !link.active
                    }"
                    :aria-current="link.active ? 'page' : undefined"
                    :aria-label="link.label === 'Previous' ? 'Previous page' : link.label === 'Next' ? 'Next page' : `Page ${link.label}`"
                >
                    <span v-if="link.label === 'Previous'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                    <span v-else-if="link.label === 'Next'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                    <span v-else v-html="link.label"></span>
                </Link>
                <span
                    v-else
                    class="px-3 py-1 text-sm text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 rounded flex items-center justify-center min-w-[32px]"
                    aria-hidden="true"
                >
                    <span v-if="link.label === 'Previous'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                    <span v-else-if="link.label === 'Next'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                    <span v-else v-html="link.label"></span>
                </span>
             </template>
        </div>
    </nav>
</template>
