<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    roles: Array,
    document_categories: Array,
});

const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email,
    phone1: props.user.phone1 || '',
    phone2: props.user.phone2 || '',
    address: props.user.address || '',
    professional_card_id: props.user.professional_card_id || '',
    profile_photo: null,
    roles: props.user.roles.map(r => r.name),
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

const docForm = useForm({
    title: '',
    document_category_id: null,
    file: null,
    documentable_id: props.user.id,
    documentable_type: 'App\\Models\\User',
    description: '',
});

const categories = ref(props.document_categories);

const addCategory = async (name) => {
    try {
        const response = await axios.post(route('document-categories.store'), { name });
        categories.value.push(response.data);
        docForm.document_category_id = response.data.id;
    } catch (error) {
        console.error('Failed to add category:', error);
    }
};

const submit = () => {
    // We use router.post with _method: 'patch' for file uploads on update
    router.post(route('users.update', props.user.id), {
        _method: 'patch',
        ...form.data(),
    }, {
        onSuccess: () => {
            form.reset('profile_photo');
            photoPreview.value = null;
        }
    });
};



const docFileError = ref(null);
const docFileSize = ref(null);

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
    if (docFileError.value) return; // Prevent submission if file is too large

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

const destroy = () => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', props.user.id));
    }
};

const deleteDoc = (docId) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('documents.destroy', docId), {
            preserveScroll: true
        });
    }
};

const showUploadForm = ref(false);
</script>

<template>
    <Head :title="$t('Edit User')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $t('Edit User') }}</h2>
        </template>

        <div class="py-6 space-y-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- User Profile Form -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 p-6 transition-colors">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4 text-right">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $t('Update User Information') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Modify the user details and professional information.') }}</p>
                        </div>
                        
                        <!-- Profile Photo -->
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                    <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                    <img v-else :src="user.profile_photo_url" class="w-full h-full object-cover">
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
                            <p class="text-xs text-gray-500">{{ $t('Click to change profile photo (Max 1MB)') }}</p>
                            <InputError class="mt-2" :message="form.errors.profile_photo" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="first_name" :value="$t('First Name')" :required="true" />
                                <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.first_name" />
                            </div>
                            <div>
                                <InputLabel for="last_name" :value="$t('Last Name')" :required="true" />
                                <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required />
                                <InputError class="mt-2" :message="form.errors.last_name" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="email" :value="$t('Email')" :required="true" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="phone1" :value="$t('Phone 1')" />
                                <TextInput id="phone1" type="text" class="mt-1 block w-full" v-model="form.phone1" />
                                <InputError class="mt-2" :message="form.errors.phone1" />
                            </div>
                            <div>
                                <InputLabel for="phone2" :value="$t('Phone 2')" />
                                <TextInput id="phone2" type="text" class="mt-1 block w-full" v-model="form.phone2" />
                                <InputError class="mt-2" :message="form.errors.phone2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="address" :value="$t('Address')" />
                            <textarea id="address" v-model="form.address" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" rows="2"></textarea>
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <div>
                            <InputLabel for="professional_card_id" :value="$t('Professional Card ID')" />
                            <TextInput id="professional_card_id" type="text" class="mt-1 block w-full" v-model="form.professional_card_id" />
                            <InputError class="mt-2" :message="form.errors.professional_card_id" />
                        </div>

                        <div>
                            <InputLabel :value="$t('Roles')" :required="true" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3 pb-2">
                                <label v-for="role in roles" :key="role.id" class="flex items-center gap-2 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" :class="{'bg-brand-50 border-brand-200 dark:bg-brand-900/40 dark:border-brand-800': form.roles.includes(role.name)}">
                                    <input type="checkbox" :value="role.name" v-model="form.roles" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-brand-600 dark:text-brand-500 shadow-sm focus:ring-brand-500">
                                    <span class="text-[11px] font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $t(role.name) }}</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.roles" />
                        </div>



                        <div class="flex items-center justify-between pt-4">
                            <DangerButton type="button" @click="destroy" v-if="user.id !== $page.props.auth.user.id" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ $t('Delete User') }}
                            </DangerButton>
                            <div v-else></div>
                            <div class="flex items-center gap-4">
                                <Link :href="route('users.index')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">{{ $t('Cancel') }}</Link>
                                <PrimaryButton :loading="form.processing">{{ $t('Update User') }}</PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Professional File Section -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 p-6 transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ $t('Professional File') }}
                        </h3>
                        <button 
                            @click="showUploadForm = !showUploadForm" 
                            class="px-4 py-2 bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-400 rounded-lg text-sm font-semibold hover:bg-brand-100 dark:hover:bg-brand-900/60 transition-colors flex items-center gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            {{ $t('Add Document') }}
                        </button>
                    </div>

                    <!-- Upload Form -->
                    <div v-if="showUploadForm" class="mb-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="submitDoc" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="doc_title" :value="$t('Document Title')" :required="true" />
                                    <TextInput id="doc_title" type="text" class="mt-1 block w-full bg-white dark:bg-gray-900" v-model="docForm.title" required />
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
                                    class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 dark:file:bg-brand-900/40 file:text-brand-700 dark:file:text-brand-400 hover:file:bg-brand-100 dark:hover:file:bg-brand-900/60 transition-colors"
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
                                <button type="button" @click="showUploadForm = false" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">{{ $t('Cancel') }}</button>
                                <PrimaryButton :loading="docForm.processing">{{ $t('Upload') }}</PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Documents List -->
                    <div v-if="user.documents && user.documents.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="doc in user.documents" :key="doc.id" class="py-4 flex items-center justify-between group transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 dark:text-gray-500">
                                    <svg v-if="doc.file_type === 'pdf'" class="h-6 w-6 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8l-6-6H7zm6 7V3.5L18.5 9H13z"/></svg>
                                    <svg v-else class="h-6 w-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ doc.title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                        {{ doc.category?.name || $t('General') }} • {{ (doc.file_size / 1024).toFixed(1) }} KB
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a :href="route('documents.download', doc.id)" class="p-2 text-gray-400 dark:text-gray-500 hover:text-brand-600 dark:hover:text-brand-400 rounded-full hover:bg-brand-50 dark:hover:bg-brand-900/40 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                <button @click="deleteDoc(doc.id)" class="p-2 text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-red-900/40 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="mt-2 text-sm">{{ $t('No documents in professional file.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
