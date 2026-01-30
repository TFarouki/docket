<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    courtCase: Object,
});

const form = useForm({
    court_case_id: props.courtCase.id,
    date_time: '',
    procedure_result: '',
    notes: '',
});

const submit = () => {
    form.post(route('hearings.store'));
};
</script>

<template>
    <Head :title="$t('Add Hearing')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Add Hearing') }}</h2>
            <div class="text-sm text-gray-500 mt-1">{{ props.courtCase.court_name }} - {{ props.courtCase.case_number }}</div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Session Date -->
                        <div>
                            <InputLabel for="date_time" :value="$t('Session Date')" />
                            <TextInput
                                id="date_time"
                                type="datetime-local"
                                class="mt-1 block w-full"
                                v-model="form.date_time"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.date_time" />
                        </div>

                        <!-- Outcome -->
                         <div>
                            <InputLabel for="procedure_result" :value="$t('Outcome / Decision')" />
                            <TextInput
                                id="procedure_result"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.procedure_result"
                                :placeholder="$t('e.g., Postponed for notice')"
                            />
                            <InputError class="mt-2" :message="form.errors.procedure_result" />
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

                        <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                            <Link :href="route('matters.show', courtCase.matter_id)" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :loading="form.processing">
                                {{ $t('Save Hearing') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
