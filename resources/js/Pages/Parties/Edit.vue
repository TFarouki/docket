<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    party: Object,
});

const form = useForm({
    type: props.party.type || 'client',
    full_name: props.party.full_name,
    national_id: props.party.national_id,
    phone: props.party.phone,
    email: props.party.email,
    address: props.party.address,
    notes: props.party.notes,
});

const submit = () => {
    form.put(route('parties.update', props.party.id));
};

const destroy = () => {
    if (confirm('Are you sure you want to delete this party?')) {
        form.delete(route('parties.destroy', props.party.id));
    }
};
</script>

<template>
    <Head :title="$t('Edit Party')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Edit Party') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Type -->
                        <div>
                            <InputLabel for="type" :value="$t('Party Type')" />
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="lead">{{ $t('Lead / Reference') }}</option>
                                <option value="client">{{ $t('Client') }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.type" />
                        </div>

                        <!-- Full Name -->
                        <div>
                            <InputLabel for="full_name" :value="$t('Full Name')" />
                            <TextInput
                                id="full_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.full_name"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.full_name" />
                        </div>

                        <!-- Two Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- National ID -->
                            <div>
                                <InputLabel for="national_id" :value="$t('National ID / RC')" />
                                <TextInput
                                    id="national_id"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.national_id"
                                />
                                <InputError class="mt-2" :message="form.errors.national_id" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <InputLabel for="phone" :value="$t('Phone Number')" />
                                <TextInput
                                    id="phone"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" :value="$t('Email')" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Address -->
                        <div>
                            <InputLabel for="address" :value="$t('Address')" />
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" :value="$t('Notes')" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                rows="2"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.notes" />
                        </div>

                        <div class="flex items-center justify-between">
                             <DangerButton type="button" @click="destroy" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('Delete Party') }}
                            </DangerButton>

                            <div class="flex items-center gap-4">
                                <Link :href="route('parties.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    {{ $t('Cancel') }}
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ $t('Save Changes') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
