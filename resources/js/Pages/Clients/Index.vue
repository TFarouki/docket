<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    clients: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const typeFilter = ref(props.filters.type || '');

let timeout = null;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('clients.index'), { search: value, type: typeFilter.value }, { preserveState: true, replace: true });
    }, 300);
});

watch(typeFilter, (value) => {
    router.get(route('clients.index'), { search: search.value, type: value }, { preserveState: true, replace: true });
});

const showingModal = ref(false);
const isEditing = ref(false);
const editingClient = ref(null);

const form = useForm({
    type: 'lead',
    full_name: '',
    phone: '',
    email: '',
    national_id: '',
    address: '',
    notes: '',
});

const openModal = (client = null) => {
    if (client) {
        isEditing.value = true;
        editingClient.value = client;
        form.type = client.type;
        form.full_name = client.full_name;
        form.phone = client.phone;
        form.email = client.email;
        form.national_id = client.national_id;
        form.address = client.address;
        form.notes = client.notes;
    } else {
        isEditing.value = false;
        editingClient.value = null;
        form.reset();
        form.type = 'lead';
    }
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('clients.update', editingClient.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteClient = (client) => {
    if (confirm('Are you sure you want to delete this client?')) {
        router.delete(route('clients.destroy', client.id));
    }
};
</script>

<template>
    <Head title="Clients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Clients') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="flex justify-between mb-6">
                    <div class="flex gap-4">
                        <TextInput
                            v-model="search"
                            :placeholder="$t('Search') + '...'"
                            class="block w-64"
                        />
                        <select
                            v-model="typeFilter"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">{{ $t('All Types') }}</option>
                            <option value="lead">{{ $t('Lead') }}</option>
                            <option value="client">{{ $t('Client') }}</option>
                        </select>
                    </div>
                    <PrimaryButton @click="openModal()">
                        {{ $t('Add Client') }}
                    </PrimaryButton>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Phone') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="client in clients.data" :key="client.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ client.full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ client.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ client.phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="client.type === 'client' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                        {{ client.type === 'client' ? $t('Client') : $t('Lead') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="openModal(client)" class="text-indigo-600 hover:text-indigo-900 mr-4">{{ $t('Edit') }}</button>
                                    <button @click="deleteClient(client)" class="text-red-600 hover:text-red-900">{{ $t('Delete') }}</button>
                                </td>
                            </tr>
                            <tr v-if="clients.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    {{ $t('No clients found.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="clients.links.length > 3" class="px-6 py-4 flex justify-between items-center">
                        <div>
                             <!-- Simple pagination links rendering -->
                            <template v-for="(link, key) in clients.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                    :class="{ 'bg-blue-700 text-white': link.active }"
                                    v-html="link.label"
                                />
                                <span v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showingModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isEditing ? $t('Edit Client') : $t('Add Client') }}
                </h2>

                <div class="mt-6">
                    <InputLabel for="type" :value="$t('Type')" />
                    <select
                        id="type"
                        v-model="form.type"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="lead">{{ $t('Lead') }}</option>
                        <option value="client">{{ $t('Client') }}</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="full_name" :value="$t('Full Name')" />
                    <TextInput
                        id="full_name"
                        v-model="form.full_name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.full_name" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="phone" :value="$t('Phone')" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" :value="$t('Email')" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                 <div class="mt-4">
                    <InputLabel for="national_id" :value="$t('National ID')" />
                    <TextInput
                        id="national_id"
                        v-model="form.national_id"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.national_id" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="address" :value="$t('Address')" />
                    <textarea
                        id="address"
                        v-model="form.address"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="3"
                    ></textarea>
                     <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <div class="mt-4">
                    <InputLabel for="notes" :value="$t('Notes')" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="3"
                    ></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>


                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        {{ $t('Cancel') }}
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="submit"
                    >
                        {{ $t('Save') }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
