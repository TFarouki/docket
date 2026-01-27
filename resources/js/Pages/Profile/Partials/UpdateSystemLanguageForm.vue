<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    system_locale: 'en',
});

const updateSystemLocale = () => {
    form.post(route('settings.updateLocale'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            window.location.reload();
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ $t('System Language') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ $t('Update the global system language for all users.') }}
            </p>
        </header>

        <form @submit.prevent="updateSystemLocale" class="mt-6 space-y-6">
            <div>
                <InputLabel for="system_locale" :value="$t('Select Language')" />

                <select
                    id="system_locale"
                    v-model="form.system_locale"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm"
                >
                    <option value="en">English</option>
                    <option value="ar">العربية</option>
                    <option value="fr">Français</option>
                </select>

                <div v-if="form.errors.system_locale" class="text-red-500 text-sm mt-1">{{ form.errors.system_locale }}</div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :loading="form.processing">{{ $t('Save') }}</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">{{ $t('Saved.') }}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
