<script setup>
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import Pagination from "@/Components/Pagination.vue";
import InputGroup from '@/Components/Forms/InputGroup.vue'
import DefaultCard from '@/Components/Forms/DefaultCard.vue'
import { ref } from 'vue';
import SelectGroupTwo from '@/Components/Forms/SelectGroup/SelectGroupTwo.vue'
import InputError from '@/Components/InputError.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineProps({

});

let sections = ref({});

const form = useForm({
  title: "",
  date: "",

})



const createDocument = () => {
  form.post(route('documents.store'));
}

</script>

<template>

  <Head title="Documents" />
  <AuthenticatedLayout>
    <DefaultLayout>
    <div class="grid grid-cols-1">
      <div class="flex flex-col ">
        <!-- Contact Form Start -->
        <DefaultCard cardTitle="Add Document">
          <form  @submit.prevent="createDocument">
            <div class="p-6.5">

              <div class="mb-4.5">
                <InputGroup
                  v-model="form.title"
                  label="Title"
                  type="text"
                  placeholder="Enter document title"
                />

                <InputError :message="form.errors.title" class="mt-2" />
              </div>


                <div class="mb-4.5">
                    <InputGroup
                        v-model="form.date"
                        label="Date"
                        type="date"
                        placeholder="Enter document title"
                    />

                    <InputError :message="form.errors.date" class="mt-2" />
                </div>

              <button
                type="submit"
                class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90"
              >
                Save
              </button>
            </div>
          </form>
        </DefaultCard>
        <!-- Contact Form End -->
      </div>

    </div>
  </DefaultLayout>
  </AuthenticatedLayout>
</template>
