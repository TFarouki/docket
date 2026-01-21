<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    type: 'client',
    full_name: '',
    national_id: '',
    phone: '',
    email: '',
    address: '',
    notes: '',
});

const submit = () => {
    form.post(route('parties.store'));
};
</script>

<template>
    <Head :title="$t('Add Party')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Add New Party') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Type Selection -->
                        <div>
                            <InputLabel for="type" :value="$t('Party Type')" />
                            <div class="mt-2 flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" value="lead" v-model="form.type" class="text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                    <span>{{ $t('Lead') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" value="client" v-model="form.type" class="text-brand-600 focus:ring-brand-500 border-gray-300">
                                    <span>{{ $t('Client') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" value="opponent" v-model="form.type" class="text-red-600 focus:ring-red-500 border-gray-300">
                                    <span>{{ $t('Opponent') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" value="other" v-model="form.type" class="text-gray-600 focus:ring-gray-500 border-gray-300">
                                    <span>{{ $t('Other') }}</span>
                                </label>
                            </div>
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
                                autofocus
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

                        <div class="flex items-center justify-end gap-4">
                            <Link :href="route('parties.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('Save Party') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
