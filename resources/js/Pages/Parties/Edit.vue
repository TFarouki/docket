<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    party: Object,
    document_categories: Array,
});

const form = useForm({
    full_name: props.party.full_name,
    national_id: props.party.national_id,
    phone: props.party.phone,
    email: props.party.email,
    address: props.party.address,
    notes: props.party.notes,
});

const docForm = useForm({
    title: '',
    document_category_id: null,
    file: null,
    documentable_id: props.party.id,
    documentable_type: 'App\\Models\\Party',
    description: '',
});

const categories = ref(props.document_categories);
const showUploadForm = ref(false);
const docFileError = ref(null);
const docFileSize = ref(null);

const addCategory = async (name) => {
    try {
        const response = await axios.post(route('document-categories.store'), { name });
        categories.value.push(response.data);
        docForm.document_category_id = response.data.id;
    } catch (error) {
        console.error('Failed to add category:', error);
    }
};

const handleDocFileChange = (event) => {
    const file = event.target.files[0];
    docForm.file = file;
    
    if (file) {
        docFileSize.value = (file.size / 1024 / 1024).toFixed(2); // MB
        if (file.size > 10 * 1024 * 1024) { // 10MB
            docFileError.value = 'File size exceeds 10MB limit.';
        } else {
            docFileError.value = null;
        }
    } else {
        docFileSize.value = null;
        docFileError.value = null;
    }
};

const submitDoc = () => {
    if (docFileError.value) return;

    docForm.post(route('documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            docForm.reset('title', 'document_category_id', 'file', 'description');
            showUploadForm.value = false;
            docFileSize.value = null;
            docFileError.value = null;
        }
    });
};

const deleteDoc = (docId) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('documents.destroy', docId), {
            preserveScroll: true
        });
    }
};

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

        <div class="py-6 space-y-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Party Information Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $t('Basic Information') }}</h3>
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
                                :invalid="!!form.errors.full_name"
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
                                    :invalid="!!form.errors.national_id"
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
                                    :invalid="!!form.errors.phone"
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
                                :invalid="!!form.errors.email"
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

                        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                             <DangerButton type="button" @click="destroy" :loading="form.processing">
                                {{ $t('Delete Party') }}
                            </DangerButton>

                            <div class="flex items-center gap-4">
                                <Link :href="route('parties.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    {{ $t('Cancel') }}
                                </Link>

                                <PrimaryButton :loading="form.processing">
                                    {{ $t('Save Changes') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Documents Section -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ $t('Documents') }}
                        </h3>
                        <button 
                            @click="showUploadForm = !showUploadForm" 
                            class="px-4 py-2 bg-brand-50 text-brand-700 rounded-lg text-sm font-semibold hover:bg-brand-100 transition-colors flex items-center gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            {{ $t('Add Document') }}
                        </button>
                    </div>

                    <!-- Upload Form -->
                    <div v-if="showUploadForm" class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <form @submit.prevent="submitDoc" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="doc_title" :value="$t('Document Title')" :required="true" />
                                    <TextInput id="doc_title" type="text" class="mt-1 block w-full bg-white" v-model="docForm.title" required :invalid="!!docForm.errors.title" />
                                    <InputError class="mt-2" :message="docForm.errors.title" />
                                </div>
                                <div>
                                    <SearchableSelect 
                                        v-model="docForm.document_category_id"
                                        :options="categories"
                                        :label="$t('Document Category')"
                                        :placeholder="$t('Select or add category...')"
                                        :required="true"
                                        :allow-add="true"
                                        @add="addCategory"
                                    />
                                    <InputError class="mt-2" :message="docForm.errors.document_category_id" />
                                </div>
                            </div>
                            <div>
                                <InputLabel for="doc_file" :value="$t('File (PDF/Image)')" :required="true" />
                                <input 
                                    type="file" 
                                    id="doc_file" 
                                    @change="handleDocFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-colors"
                                    accept=".pdf,image/*"
                                    required
                                />
                                <p v-if="docFileSize" class="text-xs text-gray-500 mt-1" :class="{'text-red-500': docFileError}">
                                    {{ $t('Size') }}: {{ docFileSize }} MB
                                </p>
                                <p v-if="docFileError" class="text-xs text-red-500 mt-1 font-bold">
                                    {{ $t(docFileError) }}
                                </p>
                                <InputError class="mt-2" :message="docForm.errors.file" />
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showUploadForm = false" class="text-sm text-gray-600 hover:text-gray-900">{{ $t('Cancel') }}</button>
                                <PrimaryButton :loading="docForm.processing">{{ $t('Upload') }}</PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Documents List -->
                    <div v-if="party.documents && party.documents.length > 0" class="divide-y divide-gray-100">
                        <div v-for="doc in party.documents" :key="doc.id" class="py-4 flex items-center justify-between group transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded bg-gray-50 flex items-center justify-center text-gray-400">
                                    <svg v-if="doc.file_type === 'pdf'" class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8l-6-6H7zm6 7V3.5L18.5 9H13z"/></svg>
                                    <svg v-else class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ doc.title }}</p>
                                    <p class="text-xs text-gray-500 capitalize">
                                        {{ doc.category?.name || $t('General') }} • {{ (doc.file_size / 1024).toFixed(1) }} KB
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a :href="route('documents.download', doc.id)" class="p-2 text-gray-400 hover:text-brand-600 rounded-full hover:bg-brand-50 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                <button @click="deleteDoc(doc.id)" class="p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="mt-2 text-sm">{{ $t('No documents found.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
