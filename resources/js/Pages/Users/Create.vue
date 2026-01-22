<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone1: '',
    phone2: '',
    address: '',
    professional_card_id: '',
    profile_photo: null,
    password: '',
    password_confirmation: '',
    roles: [],
});

const photoPreview = ref(null);
const photoInput = ref(null);

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.profile_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <Head :title="$t('Add User')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $t('Add New User') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 p-6 transition-colors">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $t('Create New User Account') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Fill in the details below to create a new user.') }}</p>
                        </div>
                        
                        <!-- Profile Photo -->
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                    <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                    <svg v-else class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <button 
                                    type="button"
                                    @click="$refs.photoInput.click()"
                                    class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                            </div>
                            <input 
                                type="file" 
                                ref="photoInput" 
                                class="hidden" 
                                @change="handlePhotoChange"
                                accept="image/*"
                            >
                            <p class="text-xs text-gray-500">{{ $t('Click to upload profile photo (Max 1MB)') }}</p>
                            <InputError class="mt-2" :message="form.errors.profile_photo" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <InputLabel for="first_name" :value="$t('First Name')" :required="true" />
                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.first_name" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <InputLabel for="last_name" :value="$t('Last Name')" :required="true" />
                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.last_name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.last_name" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" :value="$t('Email')" :required="true" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Phone 1 -->
                            <div>
                                <InputLabel for="phone1" :value="$t('Phone 1')" />
                                <TextInput
                                    id="phone1"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone1"
                                />
                                <InputError class="mt-2" :message="form.errors.phone1" />
                            </div>

                            <!-- Phone 2 -->
                            <div>
                                <InputLabel for="phone2" :value="$t('Phone 2')" />
                                <TextInput
                                    id="phone2"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone2"
                                />
                                <InputError class="mt-2" :message="form.errors.phone2" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <InputLabel for="address" :value="$t('Address')" />
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm"
                                rows="2"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <!-- Professional Card ID -->
                        <div>
                            <InputLabel for="professional_card_id" :value="$t('Professional Card ID')" />
                            <TextInput
                                id="professional_card_id"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.professional_card_id"
                            />
                            <InputError class="mt-2" :message="form.errors.professional_card_id" />
                        </div>

                        <!-- Roles -->
                        <div>
                            <InputLabel :value="$t('Roles')" :required="true" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3 pb-2">
                                <label 
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    class="flex items-center gap-2 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                                    :class="{'bg-brand-50 border-brand-200 dark:bg-brand-900/40 dark:border-brand-800': form.roles.includes(role.name)}"
                                >
                                    <input 
                                        type="checkbox" 
                                        :value="role.name" 
                                        v-model="form.roles"
                                        class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-brand-600 dark:text-brand-500 shadow-sm focus:ring-brand-500"
                                    >
                                    <span class="text-[11px] font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $t(role.name) }}</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.roles" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <InputLabel for="password" :value="$t('Password')" :required="true" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.password"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <InputLabel for="password_confirmation" :value="$t('Confirm Password')" :required="true" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.password_confirmation"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link :href="route('users.index')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                                {{ $t('Cancel') }}
                            </Link>

                            <PrimaryButton :loading="form.processing">
                                {{ $t('Save User') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
