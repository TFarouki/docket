<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DateTimePicker from '@/Components/DateTimePicker.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    parties: Array,
    users: Array,
});

const urlParams = new URLSearchParams(window.location.search);
const initialDate = urlParams.get('date');
const initialTime = urlParams.get('time') || '09:00';

const formatDateForInput = (date) => {
    if (!date) return '';
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    const h = String(date.getHours()).padStart(2, '0');
    const min = String(date.getMinutes()).padStart(2, '0');
    return `${y}-${m}-${d} ${h}:${min}`;
};

const getDefaultEndTime = (startStr) => {
    if (!startStr) return '';
    const date = new Date(startStr);
    date.setMinutes(date.getMinutes() + 15);
    return formatDateForInput(date);
};

const startVal = initialDate ? `${initialDate} ${initialTime}` : '';

const form = useForm({
    title: '',
    party_id: '',
    assigned_to: '',
    start_time: startVal,
    end_time: getDefaultEndTime(startVal),
    status: 'scheduled',
    notes: '',
});

const submit = () => {
    form.post(route('appointments.store'));
};
</script>

<template>
    <Head :title="$t('Schedule Appointment')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Schedule Appointment') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Title -->
                        <div>
                            <InputLabel for="title" :value="$t('Title')" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Party -->
                        <div>
                            <InputLabel for="party_id" :value="$t('Client / Lead')" />
                            <select
                                id="party_id"
                                v-model="form.party_id"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                            >
                                <option value="">{{ $t('Select Client/Lead') }}</option>
                                <option v-for="party in parties" :key="party.id" :value="party.id">
                                    {{ party.full_name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.party_id" />
                        </div>

                        <!-- Assigned To -->
                        <div>
                            <InputLabel for="assigned_to" :value="$t('Assigned Staff')" />
                            <select
                                id="assigned_to"
                                v-model="form.assigned_to"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                            >
                                <option value="">{{ $t('Unassigned') }}</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.assigned_to" />
                        </div>

                        <!-- Times -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <div>
                                <InputLabel for="start_time" :value="$t('Start Time')" />
                                <DateTimePicker
                                    id="start_time"
                                    v-model="form.start_time"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.start_time" />
                            </div>

                            <div>
                                <InputLabel for="end_time" :value="$t('End Time (Optional)')" />
                                <DateTimePicker
                                    id="end_time"
                                    v-model="form.end_time"
                                />
                                <InputError class="mt-2" :message="form.errors.end_time" />
                            </div>
                        </div>

                        <!-- Status -->
                         <div>
                            <InputLabel for="status" :value="$t('Status')" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                            >
                                <option value="scheduled">{{ $t('Scheduled') }}</option>
                                <option value="completed">{{ $t('Completed') }}</option>
                                <option value="cancelled">{{ $t('Cancelled') }}</option>
                                <option value="no_show">{{ $t('No Show') }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" :value="$t('Notes')" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.notes" />
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <Link :href="route('appointments.index')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :loading="form.processing">
                                {{ $t('Save Appointment') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
