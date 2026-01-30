<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    events: Array,
    initialMonth: Number,
    initialYear: Number,
    initialDate: String,
    view: String,
    filters: Object,
});

const getLocalDateString = (date = new Date()) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const currentView = ref(props.view || 'month');
const currentDate = ref(props.initialDate || getLocalDateString());
const currentMonth = ref(props.initialMonth);
const currentYear = ref(props.initialYear);
const search = ref(props.filters.search || '');

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

watch(search, (value) => {
    router.get(route('calendar.index'), { 
        view: currentView.value,
        date: currentDate.value,
        search: value 
    }, { preserveState: true, replace: true });
});

const switchView = (view) => {
    currentView.value = view;
    router.get(route('calendar.index'), { 
        view: view,
        date: currentDate.value,
        search: search.value 
    }, { preserveState: true });
};

const calendarDays = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value - 1, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value, 0);
    
    const days = [];
    
    // Fill previous month days
    const startDay = firstDay.getDay();
    const prevLastDay = new Date(currentYear.value, currentMonth.value - 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = startDay - 1; i >= 0; i--) {
        const dayDate = new Date(currentYear.value, currentMonth.value - 2, prevLastDay - i);
        days.push({
            day: prevLastDay - i,
            month: currentMonth.value - 1,
            year: currentYear.value,
            isCurrentMonth: false,
            isPast: dayDate < today,
        });
    }
    
    // Current month days
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const dayDate = new Date(currentYear.value, currentMonth.value - 1, i);
        days.push({
            day: i,
            month: currentMonth.value,
            year: currentYear.value,
            isCurrentMonth: true,
            isToday: dayDate.toDateString() === new Date().toDateString(),
            isPast: dayDate < today,
        });
    }
    
    // Next month days
    const remainingDays = 42 - days.length;
    for (let i = 1; i <= remainingDays; i++) {
        const dayDate = new Date(currentYear.value, currentMonth.value, i);
        days.push({
            day: i,
            month: currentMonth.value + 1,
            year: currentYear.value,
            isCurrentMonth: false,
            isPast: dayDate < today,
        });
    }
    
    return days;
});

const parseLocalDate = (dateStr) => {
    if (!dateStr) return new Date();
    const [y, m, d] = dateStr.split('-').map(Number);
    return new Date(y, m - 1, d);
};

const weekDays = computed(() => {
    const date = parseLocalDate(currentDate.value);
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Adjust for Monday start
    const monday = new Date(date.getFullYear(), date.getMonth(), diff);
    
    const days = [];
    const today = new Date();
    today.setHours(0,0,0,0);

    for (let i = 0; i < 7; i++) {
        const d = new Date(monday);
        d.setDate(monday.getDate() + i);
        days.push({
            date: d,
            day: d.getDate(),
            month: d.getMonth() + 1,
            year: d.getFullYear(),
            isToday: d.toDateString() === new Date().toDateString(),
            isPast: d < today
        });
    }
    return days;
});

const weekHours = Array.from({ length: 15 }, (_, i) => i + 8); // 8 AM to 10 PM

const getEventsForDay = (day, month, year) => {
    const dateString = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    return props.events.filter(event => event.start.split('T')[0] === dateString);
};

const navigate = (direction) => {
    const date = parseLocalDate(currentDate.value);
    if (currentView.value === 'month') {
        date.setMonth(date.getMonth() + direction);
    } else {
        date.setDate(date.getDate() + (direction * 7));
    }
    
    // Format manually to avoid ISO timezone shift (toISOString uses UTC)
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    const newDateStr = `${y}-${m}-${d}`;

    currentDate.value = newDateStr;
    currentMonth.value = date.getMonth() + 1;
    currentYear.value = date.getFullYear();

    router.get(route('calendar.index'), { 
        view: currentView.value,
        date: newDateStr,
        search: search.value,
        month: currentMonth.value,
        year: currentYear.value
    }, { preserveState: true });
};

const goToToday = () => {
    const today = getLocalDateString();
    currentDate.value = today;
    const now = new Date();
    currentMonth.value = now.getMonth() + 1;
    currentYear.value = now.getFullYear();
    
    router.get(route('calendar.index'), { 
        view: currentView.value, 
        date: today, 
        search: search.value,
        month: currentMonth.value,
        year: currentYear.value
    }, { preserveState: true });
};

const selectedEvent = ref(null);
const showPopover = ref(false);
const popoverStyle = ref({ top: '0px', left: '0px' });
const hoveredHour = ref(null);
const hoveredMinute = ref(null);

const openEventDetails = (event, clickEvent) => {
    selectedEvent.value = event;
    
    // Calculate position
    const x = clickEvent.clientX;
    const y = clickEvent.clientY;
    
    // Simple positioning with some offset and boundary check
    const popHeight = 250;
    const popWidth = 320;
    
    let top = y;
    let left = x;
    
    // Check screen boundaries
    if (y + popHeight > window.innerHeight) top = y - popHeight;
    if (x + popWidth > window.innerWidth) left = x - popWidth;
    
    popoverStyle.value = {
        top: `${top}px`,
        left: `${left}px`
    };
    
    showPopover.value = true;
};

const editEvent = (event) => {
    router.get(event.route);
};

const formatTime24 = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false });
};

const formatFullDateTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const d = String(date.getDate()).padStart(2, '0');
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const y = date.getFullYear();
    const h = String(date.getHours()).padStart(2, '0');
    const min = String(date.getMinutes()).padStart(2, '0');
    return `${d}/${m}/${y} ${h}:${min}`;
};

const createOnDay = (day, month, year, time = '09:00') => {
    const date = new Date(year, month - 1, day);
    const dateString = date.getFullYear() + '-' + 
                      String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(date.getDate()).padStart(2, '0');
    router.get(route('appointments.create'), { date: dateString, time: time });
};
</script>

<template>
    <Head :title="$t('Calendar')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight shrink-0">{{ $t('Calendar') }}</h2>
                    <TextInput
                        v-model="search"
                        type="search"
                        class="block w-full max-w-xs text-sm"
                        :placeholder="$t('Search by title or client...')"
                    />
                </div>
                
                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                    <!-- View Switcher -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 p-1 rounded-lg me-2">
                        <button 
                            @click="switchView('month')" 
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                            :class="currentView === 'month' ? 'bg-white dark:bg-gray-600 shadow text-brand-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            {{ $t('Month') }}
                        </button>
                        <button 
                            @click="switchView('week')" 
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                            :class="currentView === 'week' ? 'bg-white dark:bg-gray-600 shadow text-brand-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            {{ $t('Week') }}
                        </button>
                    </div>

                    <button @click="goToToday" class="px-3 py-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors whitespace-nowrap">
                        {{ $t('Today') }}
                    </button>
                    <div class="flex items-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded overflow-hidden">
                        <button @click="navigate(-1)" class="p-1 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-r border-gray-300 dark:border-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span class="px-4 py-1 text-sm font-bold min-w-[120px] text-center dark:text-gray-200 whitespace-nowrap">
                            <template v-if="currentView === 'month'">
                                {{ $t(months[currentMonth - 1]) }} {{ currentYear }}
                            </template>
                            <template v-else>
                                {{ weekDays[0].day }} {{ $t(months[weekDays[0].month - 1]) }} - {{ weekDays[6].day }} {{ $t(months[weekDays[6].month - 1]) }}
                            </template>
                        </span>
                        <button @click="navigate(1)" class="p-1 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-l border-gray-300 dark:border-gray-600">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <Link :href="route('appointments.index')" class="ms-2 text-sm text-brand-600 hover:text-brand-700 font-medium whitespace-nowrap">
                        {{ $t('View List') }}
                    </Link>
                    <Link :href="route('appointments.create')" class="ms-2 inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500 transition ease-in-out duration-150 whitespace-nowrap">
                        {{ $t('Add Appointment') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 h-full flex flex-col">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex-1 h-full w-full">
                <!-- Month View Grid -->
                <div v-if="currentView === 'month'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 flex flex-col h-full min-h-[600px]">
                    <div class="grid grid-cols-7 border-b border-gray-100 dark:border-gray-700">
                        <div v-for="day in daysOfWeek" :key="day" class="py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ $t(day) }}
                        </div>
                    </div>
                    <div class="grid grid-cols-7 flex-1">
                        <div 
                            v-for="(cell, index) in calendarDays" 
                            :key="index"
                            @click="cell.isCurrentMonth && createOnDay(cell.day, cell.month, cell.year)"
                            class="min-h-[100px] border-b border-r border-gray-50 dark:border-gray-700 p-2 transition-all relative group/day cursor-pointer"
                            :class="[
                                cell.isCurrentMonth ? 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50' : 'bg-gray-50/50 dark:bg-gray-900/50',
                                cell.isToday ? 'ring-2 ring-inset ring-brand-500 z-10' : '',
                                cell.isPast ? 'opacity-50 grayscale-[0.3]' : ''
                            ]"
                        >
                            <div class="flex justify-between items-start" :class="{ 'opacity-50': cell.isPast }">
                                <span class="text-xs font-semibold" :class="[cell.isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600', cell.isToday ? 'text-brand-600 font-bold' : '']">{{ cell.day }}</span>
                                <span v-if="cell.isCurrentMonth && !cell.isPast" class="opacity-0 group-hover/day:opacity-100 transition-opacity text-brand-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                            </div>
                            <div class="mt-1 space-y-1 overflow-y-auto max-h-[120px] scrollbar-hide">
                                <div 
                                    v-for="event in getEventsForDay(cell.day, cell.month, cell.year)" 
                                    :key="event.id"
                                    @click.stop="openEventDetails(event, $event)"
                                    @dblclick.stop="editEvent(event)"
                                    class="block px-2 py-1 text-[10px] rounded border transition-all truncate relative z-10 cursor-pointer"
                                    :class="[
                                        event.color === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800' : 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                        event.type === 'hearing' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-blue-500'
                                    ]"
                                    :title="event.title"
                                >
                                    <span class="font-bold mr-1">{{ formatTime24(event.start) }}</span>
                                    {{ event.title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Week View -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 flex flex-col h-full min-h-[600px]">
                    <!-- Week Timeline -->
                    <div class="flex-1 overflow-y-auto relative h-[500px] scroll-smooth">
                        <!-- Sticky Week Header inside scrollable area to fix alignment -->
                        <div class="flex sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="w-20 border-r border-gray-100 dark:border-gray-700 flex-shrink-0 bg-white dark:bg-gray-800"></div>
                            <div class="grid grid-cols-7 flex-1">
                                <div 
                                    v-for="day in weekDays" :key="day.date" 
                                    class="py-3 text-center border-r border-gray-50 dark:border-gray-700 last:border-r-0"
                                    :class="{ 'bg-brand-50/30 dark:bg-brand-900/10': day.isToday }"
                                >
                                    <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $t(daysOfWeek[day.date.getDay()]) }}</div>
                                    <div class="text-lg font-bold" :class="day.isToday ? 'text-brand-600' : 'text-gray-900 dark:text-gray-100'">{{ day.day }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex min-h-full">
                            <!-- Time Gaps -->
                             <div class="w-20 border-r border-gray-100 dark:border-gray-700 flex-shrink-0 relative">
                                <!-- Floating Time Marker -->
                                <div v-if="hoveredHour !== null && hoveredMinute !== null" 
                                     class="absolute right-2 z-20 transition-all duration-150 ease-out pointer-events-none"
                                     :style="{ top: ((hoveredHour - 8) * 80 + (hoveredMinute / 15) * 20) + 'px' }"
                                >
                                    <div class="bg-brand-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm flex items-center gap-1 -translate-y-1/2">
                                        {{ String(hoveredHour).padStart(2, '0') }}:{{ String(hoveredMinute).padStart(2, '0') }}
                                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                                    </div>
                                </div>

                                <div v-for="hour in weekHours" :key="hour" 
                                     class="h-20 border-b border-gray-50 dark:border-gray-700 px-2 text-right transition-all flex flex-col items-end pt-1"
                                     :class="{ 'bg-brand-50/30 dark:bg-brand-900/10': hoveredHour === hour }"
                                >
                                    <span class="text-[10px] font-medium transition-all duration-200"
                                          :class="hoveredHour === hour ? 'text-brand-600 font-bold' : 'text-gray-400'">
                                        {{ String(hour).padStart(2, '0') }}:00
                                    </span>
                                </div>
                            </div>

                            <!-- Hour Grid -->
                            <div class="grid grid-cols-7 flex-1 relative">
                                <div v-for="day in weekDays" :key="day.date" class="border-r border-gray-50 dark:border-gray-700 last:border-r-0 relative group/col">
                                    <!-- Create Overlay on Click (15 min intervals) -->
                                    <div v-for="hour in weekHours" :key="hour" 
                                         class="h-20 border-b border-gray-50 dark:border-gray-700 relative"
                                    >
                                        <div v-for="minute in [0, 15, 30, 45]" :key="minute"
                                             @click="!day.isPast && createOnDay(day.day, day.month, day.year, `${String(hour).padStart(2,'0')}:${String(minute).padStart(2,'0')}`)"
                                             @mouseenter="hoveredHour = hour; hoveredMinute = minute"
                                             @mouseleave="hoveredHour = null; hoveredMinute = null"
                                             class="h-1/4 hover:bg-brand-50/50 dark:hover:bg-brand-900/20 cursor-pointer border-b border-gray-50/10 last:border-b-0 relative z-0 transition-colors"
                                             :class="{ 'bg-brand-50/20 dark:bg-white/5': hoveredHour === hour && hoveredMinute === minute }"
                                        ></div>
                                    </div>

                                    <!-- Events Placement -->
                                    <div v-for="event in getEventsForDay(day.day, day.month, day.year)" :key="event.id"
                                         class="absolute left-1 right-1 rounded border px-1.5 py-1 overflow-hidden z-10 transition-all hover:z-30 hover:shadow-md cursor-pointer group/event"
                                         :style="{
                                             top: ((new Date(event.start).getHours() - 8) * 80 + (new Date(event.start).getMinutes() / 60) * 80) + 'px',
                                             height: Math.max(20, (event.end ? (new Date(event.end) - new Date(event.start)) / (1000 * 60 * 60) * 80 : 20)) + 'px'
                                         }"
                                         :class="[
                                             event.color === 'blue' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/60 dark:text-blue-100 dark:border-blue-700' : 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900/60 dark:text-red-100 dark:border-red-700',
                                             event.type === 'hearing' ? 'border-l-4 border-l-red-500' : 'border-l-4 border-l-blue-500'
                                         ]"
                                         @click.stop="openEventDetails(event, $event)"
                                         @dblclick.stop="editEvent(event)"
                                         @mouseenter="() => { const d = new Date(event.start); hoveredHour = d.getHours(); hoveredMinute = d.getMinutes(); }"
                                         @mouseleave="hoveredHour = null; hoveredMinute = null"
                                         :title="`${event.title} - ${event.party || ''}`"
                                    >
                                        <div class="text-[9px] font-bold truncate h-full flex items-center leading-none">
                                            {{ event.title }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="mt-4 flex items-center gap-6 px-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $t('Appointments') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $t('Hearings') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Details Tooltip/Popover -->
        <div v-if="showPopover" 
             class="fixed inset-0 z-[60]" 
             @click="showPopover = false"
        >
            <div 
                class="absolute bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 w-80 overflow-hidden animate-in fade-in zoom-in duration-200"
                :style="popoverStyle"
                @click.stop
            >
                <!-- Popover Header -->
                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" :class="selectedEvent.color === 'blue' ? 'bg-blue-500' : 'bg-red-500'"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                            {{ selectedEvent.type === 'hearing' ? $t('Hearing') : $t('Appointment') }}
                        </span>
                    </div>
                    <button @click="showPopover = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Popover Body -->
                <div class="p-4 space-y-3">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                        {{ selectedEvent.title }}
                    </h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center gap-2.5 text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-semibold">
                                {{ formatFullDateTime(selectedEvent.start) }}
                                {{ selectedEvent.end ? ' - ' + formatTime24(selectedEvent.end) : '' }}
                            </span>
                        </div>
                        
                        <div v-if="selectedEvent.party" class="flex items-center gap-2.5 text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-xs font-medium">{{ selectedEvent.party }}</span>
                        </div>

                        <div v-if="selectedEvent.assignee" class="flex items-center gap-2.5 text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="text-xs font-medium">{{ $t('Assigned to') }}: {{ selectedEvent.assignee }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button 
                            @click="editEvent(selectedEvent)" 
                            class="flex-1 bg-brand-600 text-white py-1.5 px-3 rounded text-xs font-bold hover:bg-brand-500 transition-all flex items-center justify-center gap-2"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            {{ $t('Edit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
