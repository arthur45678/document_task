<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Pagination from "@/Components/Pagination.vue"
import InputGroup from '@/Components/Forms/InputGroup.vue'
import Swal from 'sweetalert2'

import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

defineProps({
    documents: {
        type: Object,
        required: true
    }
})

const page = usePage();
let search = ref(usePage().props.search), pageNumber = ref(1);

let documentsUrl = computed(() => {
    let url = new URL(route("documents.index"));
    url.searchParams.append("page", pageNumber.value);

    if (search.value) {
        url.searchParams.append("search", search.value);
    }

    return url;
});

const updatedPageNumber = (link) => {
    pageNumber.value = link.url.split("=")[1];
};

watch(
    () => documentsUrl.value,
    (updatedDocumentsUrl) => {
        router.visit(updatedDocumentsUrl, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);

const deleteForm = useForm({});

const deleteDocument = (documentId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            deleteForm.delete(route("documents.destroy", documentId), {
                preserveScroll: true,
            });
            Swal.fire({
                title: "Deleted!",
                text: "Your data has been deleted.",
                icon: "success"
            });
        }
    });
}

const downloadDocument = (documentId) => {
    window.location.href = route('documents.download', documentId);
}

</script>

<template>
    <Head title="Documents List" />
    <AuthenticatedLayout>
        <DefaultLayout>
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="pb-4 px-5">
                    <div class="flex flex-col justify-between sm:flex-row mt-4">
                        <InputGroup v-model="search" type="text" autocomplete="off" placeholder="Search..." autofocus customClasses="block" />

                        <div class="mt-4 sm:ml-16 sm:flex-none">
                            <Link :href="route('documents.create')" class="inline-flex items-center justify-center rounded bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                Add Document
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Table Header -->
                <div class="grid grid-cols-5 border-t border-stroke py-4.5 px-4 dark:border-strokedark sm:grid-cols-8 md:px-6 2xl:px-7.5">
                    <div class="col-span-2 flex items-center">
                        <p class="font-medium">Document Name</p>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <p class="font-medium">Date</p>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <p class="font-medium">Action</p>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <p class="font-medium">Download</p>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <p class="font-medium"></p>
                    </div>
                </div>

                <!-- Table Rows -->
                <div v-for="document in documents.data" :key="document.id" class="grid grid-cols-5 border-t border-stroke py-4.5 px-4 dark:border-strokedark sm:grid-cols-8 md:px-6 2xl:px-7.5">
                    <div class="col-span-2 hidden items-center sm:flex">
                        <p class="text-sm font-medium text-black dark:text-white">{{ document.title }}</p>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <p class="text-sm font-medium text-black dark:text-white">{{ document.date }}</p>
                    </div>
                    <div class="col-span-1 flex items-center">

                        <button @click="deleteDocument(document.id)" class="ml-2 text-indigo-600 hover:text-indigo-900">
                            Delete
                        </button>
                    </div>
                    <div class="col-span-1 flex items-center">
                        <button @click="downloadDocument(document.id)" class="text-indigo-600 hover:text-indigo-900">
                            Download
                        </button>
                    </div>
                </div>
            </div>

            <Pagination :data="documents" :updatedPageNumber="updatedPageNumber" />
        </DefaultLayout>
    </AuthenticatedLayout>
</template>
