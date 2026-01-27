<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    clients: Array,
    lawyers: Array,
});

const form = useForm({
    title: '',
    party_id: '',
    responsible_lawyer_id: '',
    reference_number: '',
    year: new Date().getFullYear(),
    type: 'litigation',
    case_type: '',
    court_name: '',
    status: 'open',
    agreed_fee: '',
    description: '',
});

const submit = () => {
    form.post(route('matters.store'));
};
</script>

<template>
    <Head :title="$t('New Matter')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Create New Matter') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Title & Reference -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="title" :value="$t('Matter Title')" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    :placeholder="$t('e.g., Case against Company X')"
                                    autofocus
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="reference_number" :value="$t('Reference Number')" />
                                <TextInput
                                    id="reference_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.reference_number"
                                    :placeholder="$t('e.g., 2025/001')"
                                />
                                <InputError class="mt-2" :message="form.errors.reference_number" />
                            </div>

                            <div>
                                <InputLabel for="year" :value="$t('Year')" />
                                <TextInput
                                    id="year"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.year"
                                />
                                <InputError class="mt-2" :message="form.errors.year" />
                            </div>
                        </div>

                        <!-- Client & Lawyer -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="party_id" :value="$t('Client (Party)')" />
                                <select
                                    id="party_id"
                                    v-model="form.party_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>{{ $t('Select Client') }}</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.full_name }} {{ client.type ? '(' + (client.type === 'lead' ? $t('Lead') : $t('Client')) + ')' : '' }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.party_id" />
                            </div>

                            <div>
                                <InputLabel for="responsible_lawyer_id" :value="$t('Responsible Lawyer')" />
                                <select
                                    id="responsible_lawyer_id"
                                    v-model="form.responsible_lawyer_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                >
                                    <option value="">{{ $t('Unassigned') }}</option>
                                    <option v-for="lawyer in lawyers" :key="lawyer.id" :value="lawyer.id">
                                        {{ lawyer.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.responsible_lawyer_id" />
                            </div>
                        </div>

                        <!-- Type & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="type" :value="$t('Matter Nature')" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                >
                                    <option value="litigation">{{ $t('litigation') }}</option>
                                    <option value="procedure">{{ $t('procedure') }}</option>
                                    <option value="consultation">{{ $t('consultation') }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>

                            <div>
                                <InputLabel for="case_type" :value="$t('Case Type')" />
                                <select
                                    id="case_type"
                                    v-model="form.case_type"
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                >
                                    <option value="">{{ $t('Select Case Type') }}</option>
                                    <option value="civil">{{ $t('Civil') }}</option>
                                    <option value="criminal">{{ $t('Criminal') }}</option>
                                    <option value="commercial">{{ $t('Commercial') }}</option>
                                    <option value="family">{{ $t('Family') }}</option>
                                    <option value="administrative">{{ $t('Administrative') }}</option>
                                    <option value="social">{{ $t('Social') }}</option>
                                    <option value="other">{{ $t('Other') }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.case_type" />
                            </div>
                        </div>

                        <!-- Court & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="court_name" :value="$t('Court / Tribunal')" />
                                <TextInput
                                    id="court_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.court_name"
                                    :placeholder="$t('e.g., Casablanca First Instance Court')"
                                />
                                <InputError class="mt-2" :message="form.errors.court_name" />
                            </div>

                            <div>
                                <InputLabel for="status" :value="$t('Status')" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                >
                                    <option value="open">{{ $t('open') }}</option>
                                    <option value="pending">{{ $t('pending') }}</option>
                                    <option value="closed">{{ $t('closed') }}</option>
                                    <option value="archived">{{ $t('archived') }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                        </div>

                        <!-- Agreed Fee -->
                         <div>
                            <InputLabel for="agreed_fee" :value="$t('Agreed Fee')" />
                            <TextInput
                                id="agreed_fee"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                v-model="form.agreed_fee"
                            />
                            <InputError class="mt-2" :message="form.errors.agreed_fee" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" :value="$t('Description')" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                            <Link :href="route('matters.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :loading="form.processing">
                                {{ $t('Create Matter') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
