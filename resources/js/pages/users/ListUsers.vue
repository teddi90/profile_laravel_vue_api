<script setup>
import {onMounted, ref,watch} from "vue";
import {Form, Field} from "vee-validate"
import { Dialog, DialogPanel, DialogTitle,TransitionChild, TransitionRoot } from '@headlessui/vue';
import * as yup from 'yup';

const isDialog = ref(false);
const users = ref([]);
const editMode=ref(false);
const formValues=ref();
const form=ref(null);

const createUserSchema = yup.object({
    name:yup.string().required(),
    email:yup.string().email().required(),
    password:yup.string().required().min(8),
});
const editUserSchema = yup.object({
    name:yup.string().required(),
    email:yup.string().email().required(),
    password:yup.string().when((password,schema)=>{
        return password[0] ? schema.required().min(8) :schema;
    })
});
watch(isDialog,()=>{
    if(!isDialog.value){
        formValues.value={
            id:'',
            name:'',
            email:'',
        };
    }
})
const handleSubmit=(values,{resetForm})=>{
    if(editMode.value){
        updateUser(values,resetForm);
    }else {
        createUser(values,resetForm);
    }
}
const createUser=(values,resetForm)=>{
    axios.post('/api/users',values)
        .then(response=>{
            users.value.unshift(response.data);
            isDialog.value = false;
            resetForm();
        });
}
const editUser=(user)=>{
    isDialog.value=true;
    editMode.value=true;
    formValues.value={
        id:user.id,
        name:user.name,
        email:user.email,
    };
}
const updateUser=(values,resetForm)=>{
    axios.put('/api/users/'+values.id,values)
        .then((resp=>{
            const index=users.value.findIndex(user=>user.id === values.id);
            users.value[index]=resp.data;
            resetForm();
            isDialog.value=false;
        })).catch((err)=>{
        console.log(err);
    });
}
const getUsers= async ()=>{
    const response= await axios('/api/users');
    users.value= response.data;
}
onMounted(()=>{
    getUsers();
})
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <router-link :to="{name:'admin.dashboard'}" class="breadcrumb-item">Home</router-link>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
            <div class="container">
                <div class="container-fluid">
                    <button @click="isDialog = true; editMode=false;" class="btn bg-blue-700 mb-3 text-white">Add new User</button>
                        <TransitionRoot as="template" :show="isDialog">
                            <Dialog as="div" class="relative z-10 " @close="isDialog = false">
                                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                                </TransitionChild>

                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full items-end justify-center text-center sm:items-center sm:p-0">
                                        <TransitionChild  as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                            <DialogPanel class="mt-72 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                <div class="mt-3 sm:ml-4 sm:mt-0 sm:text-left">
                                                <DialogTitle v-if="editMode" as="h3" class="text-base font-semibold leading-6 text-gray-900">Edit user</DialogTitle>
                                                <DialogTitle v-else as="h3" class="text-base font-semibold leading-6 text-gray-900">Add new user</DialogTitle>
                                                </div>
                                                <Form ref="form"
                                                      @submit="handleSubmit"
                                                      :validation-schema="editMode ? editUserSchema : createUserSchema"
                                                      :initial-values="formValues"
                                                      v-slot="{errors}">
                                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 ">
                                                        <div class="mb-2">
                                                            <label for="name"
                                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                                            <Field name="name" type="text" id="name" :class="{'is-invalid':errors.name}"
                                                                   class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                            <span class="invalid-feedback">{{errors.name}}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="email"
                                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                                            <Field name="email" type="email" id="email" :class="{'is-invalid':errors.email}"
                                                                   class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                            <span class="invalid-feedback">{{errors.email}}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="password"
                                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                                            <Field name="password" type="text" id="password" :class="{'is-invalid':errors.password}"
                                                                   class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                            <span class="invalid-feedback">{{errors.password}}</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex justify-between bg-gray-50 px-4 pt-2 pb-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                        <button type="submit"
                                                                class="inline-flex w-full justify-center rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-800 sm:mt-0 sm:w-auto"
                                                                ref="cancelButtonRef">{{editMode ? 'Update user' : 'Add user'}}
                                                        </button>
                                                        <button @click.prevent="isDialog = false" type="button"
                                                                class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                                               >Cancel
                                                        </button>
                                                    </div>
                                                </Form>
                                            </DialogPanel>
                                        </TransitionChild>
                                    </div>
                                </div>
                            </Dialog>
                        </TransitionRoot>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    id
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Registered Date
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Options
                                </th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users" :key="user.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{user.id}}
                                </th>
                                <td class="px-6 py-4">
                                    {{user.name}}
                                </td>
                                <td class="px-6 py-4">
                                    {{user.email}}
                                </td>
                                <td class="px-6 py-4">
                                    {{user.created_at.split('T')[0]}}
                                </td>
                                <td class="px-6 py-4">
                                    -
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a @click.prevent="editUser(user)" href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
</template>


