<script setup>
import {onMounted, ref, watch} from "vue";
import {Form, Field} from "vee-validate"
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import {ExclamationTriangleIcon} from '@heroicons/vue/24/outline';
import * as yup from 'yup';
import {useToaster} from "../../toastr.js";
import UserListItem from "./UserListItem.vue";
import {debounce} from "lodash-es";
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
const isDialog = ref(false);
const isDangerDialog = ref(false);
const users = ref({data:[]});
const editMode = ref(false);
const formValues = ref();
const toastr = useToaster();
const userId=ref();
const searchQuery=ref(null);

const searchUser=async()=>{
    await axios.get('/api/users/search',{
        params:{
            query:searchQuery.value
        }
    }).then(resp=>{
        users.value=resp.data;
    }).catch(err=>{
        console.log(err)
    })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});
const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password[0] ? schema.required().min(8) : schema;
    })
});
watch(isDialog, () => {
    if (!isDialog.value) {
        formValues.value = {
            id: '',
            name: '',
            email: '',
        };
    }
});
watch(searchQuery,debounce(()=>{
   searchUser();
},300));
const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}
const createUser = (values, actions) => {
    axios.post('/api/users', values)
        .then(response => {
            let userData=response.data;
            userData.role='USER'
            users.value.data.unshift(userData);
            isDialog.value = false;
            actions.resetForm();
            toastr.success('User created successful')
        }).catch((error) => {
        if (error.response.data.errors) {
            actions.setErrors(error.response.data.errors);
        }
    });
}
const showDangerDialog=(user)=>{
    isDangerDialog.value=true;
    userId.value=user.id;
}
const editUser = (user) => {
    isDialog.value = true;
    editMode.value = true;
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
}
const updateUser = (values, actions) => {
    axios.put('/api/users/' + values.id, values)
        .then((resp => {
            const index = users.value.findIndex(user => user.id === values.id);
            users.value[index] = resp.data;
            actions.resetForm();
            isDialog.value = false;
            toastr.success('User updated successful');
        })).catch((error) => {
        if (error.response.data.errors) {
            actions.setErrors(error.response.data.errors);
        }
    });
}
const deleteUser = () => {
    axios.delete('/api/users/' + userId.value)
        .then(resp => {
            // const index = users.value.findIndex(user => user.id === userId.value);
            // users.value.splice(index,1);
            users.value.data = users.value.data.filter(user => user.id !== userId.value);
            toastr.success('User deleted successful');
            isDangerDialog.value=false;
        }).catch(err=>{
        console.log(err);
    })
}

const getUsers = async (page=1) => {
    const response = await axios(`/api/users?page=${page}`);
    users.value = response.data;
    selectedUsers.value=[];
    selectAll.value=false;
}
const selectedUsers=ref([]);
const toggleSelection=(user)=>{
    const index=selectedUsers.value.indexOf(user.id);
    if(index===-1){
        selectedUsers.value.push(user.id);
    }else {
        selectedUsers.value.splice(index,1);
    }
};

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value,
        }
    }).then((resp) => {
        users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
        selectedUsers.value=[];
        selectAll.value=false;
        toastr.success(resp.data.message);
    }).catch(err => {
        console.log(err);
    })
};
const selectAll=ref(false);
const selectAllUsers=()=>{
    if(selectAll.value){
        selectedUsers.value=users.value.data.map(user=>user.id);
    }else {
        selectedUsers.value=[];
    }
}

onMounted(() => {
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

    <!--  Delete modal-->
    <TransitionRoot as="template" :show="isDangerDialog">
        <Dialog as="div" class="relative z-10" @close="isDangerDialog = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                             leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                                     enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                     leave-from="opacity-100 translate-y-0 sm:scale-100"
                                     leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div>
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true"/>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Are
                                            you sure you want to delete this User
                                        </DialogTitle>

                                    </div>
                                </div>
                            </div>
                            <div class="bg-white px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                        @click="deleteUser()">Delete
                                </button>
                                <button type="button"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                        @click="isDangerDialog = false" ref="cancelButtonRef">Cancel
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <div class="container">
        <div class="container-fluid">
            <div class="flex justify-between">
                <div class="mb-3 flex items-center">
                    <button @click="isDialog = true; editMode=false;" class="btn mr-2 bg-blue-700 hover:bg-blue-800 text-white">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add new User
                    </button>
                    <button :disabled="selectedUsers.length<1" @click="bulkDelete" class="btn bg-danger hover:bg-red-800 text-white">
                        <i class="fa fa-trash mr-1"></i>
                        Delete Selected
                    </button>
                    <span class="px-2  inline-block" v-if="selectedUsers.length">Selected {{selectedUsers.length}} {{selectedUsers.length>1 ? 'users':'user'}}</span>
                </div>
                <input v-model="searchQuery" type="text" name="search" id="search" class="rounded-md border-0 h-[40px] pl-3 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Search..." />
            </div>
            <TransitionRoot as="template" :show="isDialog">
                <Dialog as="div" class="relative z-10 " @close="isDialog = false">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                                     enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                                     leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
                    </TransitionChild>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center text-center sm:items-center sm:p-0">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                             enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             enter-to="opacity-100 translate-y-0 sm:scale-100"
                                             leave="ease-in duration-200"
                                             leave-from="opacity-100 translate-y-0 sm:scale-100"
                                             leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                <DialogPanel
                                    class="mt-72 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <div class="mt-3 sm:ml-4 sm:mt-0 sm:text-left px-2">
                                        <DialogTitle v-if="editMode" as="h3"
                                                     class="text-base font-semibold leading-6 text-gray-900">Edit user
                                        </DialogTitle>
                                        <DialogTitle v-else as="h3"
                                                     class="text-base font-semibold leading-6 text-gray-900">Add new
                                            user
                                        </DialogTitle>
                                    </div>
                                    <Form @submit="handleSubmit"
                                          :validation-schema="editMode ? editUserSchema : createUserSchema"
                                          :initial-values="formValues"
                                          v-slot="{errors}">
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 ">
                                            <div class="mb-2">
                                                <label for="name"
                                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                                <Field name="name" type="text" id="name"
                                                       :class="{'is-invalid':errors.name}"
                                                       class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                <span class="invalid-feedback">{{ errors.name }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <label for="email"
                                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                                <Field name="email" type="email" id="email"
                                                       :class="{'is-invalid':errors.email}"
                                                       class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                <span class="invalid-feedback">{{ errors.email }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <label for="password"
                                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                                <Field name="password" type="text" id="password"
                                                       :class="{'is-invalid':errors.password}"
                                                       class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                                <span class="invalid-feedback">{{ errors.password }}</span>
                                            </div>
                                        </div>
                                        <div
                                            class="flex justify-between white px-4 pt-2 pb-3 sm:flex sm:flex-row-reverse sm:px-6">
                                            <button type="submit"
                                                    class="inline-flex w-full justify-center rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-800 sm:mt-0 sm:w-auto"
                                                    ref="cancelButtonRef">{{ editMode ? 'Update user' : 'Add user' }}
                                            </button>
                                            <button @click.prevent="isDialog = false" type="button"
                                                    class="inline-flex w-full justify-center rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-500 hover:bg-gray-300 sm:mt-0 sm:w-auto"
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

            <div class="relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                        <input @change="selectAllUsers" v-model="selectAll" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </th>
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
                        <th scope="col" class="px-6 py-3 text-center">
                            Options
                        </th>
                    </tr>
                    </thead>
                    <tbody v-if="users.data.length">
                    <UserListItem v-for="user in users.data" :key="user.id" :user="user" :select-all="selectAll"
                        @show-danger-dialog="showDangerDialog"
                        @edit-user="editUser"
                        @toggle-selection="toggleSelection" />

                    </tbody>
                    <tbody v-else>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th colspan="6" class="text-center px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                No results found...
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="pt-4 flex justify-center">
            <Bootstrap5Pagination
                :data="users"
                @pagination-change-page="getUsers"
            />
        </div>
    </div>
</template>


