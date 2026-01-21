<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.user.roles.map(r => r.name),
});

const submit = () => {
    form.patch(route('users.update', props.user.id));
};

const destroy = () => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', props.user.id));
    }
};
</script>

<template>
    <Head :title="$t('Edit User')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $t('Edit User') }}</h2>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Name -->
                        <div>
                            <InputLabel for="name" :value="$t('Name')" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" :value="$t('Email')" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Roles -->
                        <div>
                            <InputLabel :value="$t('Roles')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3 pb-2">
                                <label 
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    class="flex items-center gap-2 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                                    :class="{'bg-brand-50 border-brand-200': form.roles.includes(role.name)}"
                                >
                                    <input 
                                        type="checkbox" 
                                        :value="role.name" 
                                        v-model="form.roles"
                                        class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500"
                                    >
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $t(role.name) }}</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.roles" />
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                            <p class="text-sm text-gray-600 font-medium">{{ $t('Change Password (Optional)') }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <InputLabel for="password" :value="$t('New Password')" />
                                    <TextInput
                                        id="password"
                                        type="password"
                                        class="mt-1 block w-full bg-white"
                                        v-model="form.password"
                                    />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <InputLabel for="password_confirmation" :value="$t('Confirm New Password')" />
                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full bg-white"
                                        v-model="form.password_confirmation"
                                    />
                                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <DangerButton type="button" @click="destroy" v-if="user.id !== $page.props.auth.user.id" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('Delete User') }}
                            </DangerButton>
                            <div v-else></div>

                            <div class="flex items-center gap-4">
                                <Link :href="route('users.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    {{ $t('Cancel') }}
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ $t('Update User') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
