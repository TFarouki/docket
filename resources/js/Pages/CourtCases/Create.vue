<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    matter: Object,
});

const form = useForm({
    matter_id: props.matter.id,
    court_name: '',
    case_number: '',
    judge_name: '',
    opponent_name: '',
    opponent_lawyer: '',
    current_stage: '',
});

const submit = () => {
    form.post(route('court-cases.store'));
};
</script>

<template>
    <Head :title="$t('Add Court Case')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Add Court Case') }}</h2>
            <div class="text-sm text-gray-500 mt-1">{{ props.matter.title }}</div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Court & Case Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="court_name" :value="$t('Court Name')" />
                                <TextInput
                                    id="court_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.court_name"
                                    :placeholder="$t('e.g., First Instance Court')"
                                    autofocus
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.court_name" />
                            </div>

                            <div>
                                <InputLabel for="case_number" :value="$t('Case Number')" />
                                <TextInput
                                    id="case_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.case_number"
                                    :placeholder="$t('e.g., 2024/123')"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.case_number" />
                            </div>
                        </div>

                        <!-- Opponent Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="opponent_name" :value="$t('Opponent Name')" />
                                <TextInput
                                    id="opponent_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.opponent_name"
                                />
                                <InputError class="mt-2" :message="form.errors.opponent_name" />
                            </div>

                            <div>
                                <InputLabel for="opponent_lawyer" :value="$t('Opponent Lawyer')" />
                                <TextInput
                                    id="opponent_lawyer"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.opponent_lawyer"
                                />
                                <InputError class="mt-2" :message="form.errors.opponent_lawyer" />
                            </div>
                        </div>
                        
                        <!-- Judge & Stage -->
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="judge_name" :value="$t('Judge Name')" />
                                <TextInput
                                    id="judge_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.judge_name"
                                />
                                <InputError class="mt-2" :message="form.errors.judge_name" />
                            </div>

                             <div>
                                <InputLabel for="current_stage" :value="$t('Current Stage')" />
                                <TextInput
                                    id="current_stage"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.current_stage"
                                    :placeholder="$t('e.g., Pleading, Expertise')"
                                />
                                <InputError class="mt-2" :message="form.errors.current_stage" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link :href="route('matters.show', matter.id)" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('Save Court Case') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
