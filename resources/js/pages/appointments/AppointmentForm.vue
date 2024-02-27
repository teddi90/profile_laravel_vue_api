<script setup>
import {onMounted, reactive,ref} from 'vue';
import {useToaster} from "../../toastr.js";
import {useRoute, useRouter} from "vue-router";
import {Field, Form} from "vee-validate";
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/material_blue.css';

const route=useRoute();
const router=useRouter();
const editMode=ref(false);
const toastr = useToaster();
const clientsList=ref();
const form=reactive({
   title:'',
   client_id:'',
   start_time:'',
   end_time:'',
   description:'',
});
const getAppointment=()=>{
    axios.get(`/api/appointments/${route.params.id}/edit`)
        .then(({data})=>{
            form.title=data.title;
            form.client_id=data.client_id;
            form.start_time=data.formatted_start_time;
            form.end_time=data.formatted_end_time;
            form.description=data.description;
        })
}

const getClients=()=>{
    axios.get('/api/clients')
        .then(resp=>{
            clientsList.value=resp.data;
        }).catch(err=>{
            console.log(err);
    })
}
const createAppointment=(values, actions)=>{
    axios.post('/api/appointments/create', form)
        .then(response => {
            router.push({name:'admin.appointments'});
            toastr.success('Appointment created successfully');
        }).catch((error) => {
        if (error.response.data.errors) {
            actions.setErrors(error.response.data.errors);
        }
    });
}
const updateAppointment=(values, actions)=>{
    axios.put(`/api/appointments/${route.params.id}/edit`,form)
        .then(()=>{
            router.push({name:'admin.appointments'});
            toastr.success('Appointment updated successfully');
    }).catch(err=>{
        if (err.response.data.errors) {
            actions.setErrors(err.response.data.errors);
        }
    })
}
const handleSubmit=(values, actions)=>{
    if(editMode.value){
        updateAppointment(values, actions);
    }else {
        createAppointment(values, actions)
    }
}
onMounted(()=>{
    if(route.name==='admin.appointments.edit'){
        editMode.value=true;
        getAppointment();
    }
    getClients();
    flatpickr('.flatpickr',{
      enableTime:true,
        dateFormat:"Y-m-d h:i K",
        defaultHour:10,
    });
})
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Create</span>
                        Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <router-link :to="{name:'admin.dashboard'}" class="breadcrumb-item">Home</router-link>
                        <router-link :to="{name:'admin.appointments'}" class="breadcrumb-item">Appointments</router-link>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <Form @submit="handleSubmit"
          class="rounded m-3"
          v-slot="{errors}">
        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 ">
            <div class="grid gap-4 mb-2 md:grid-cols-2">
                <div class="form-group">
                    <label for="title"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                    <input  v-model="form.title" name="title" type="text" id="title"
                           class="is-invalid block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                    <span class="invalid-feedback">{{errors.title}}</span>
                </div>
                <div class="form-group">
                    <label for="client_name"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client Name</label>
                    <select v-model="form.client_id" name="client_name" id="client_name"
                            class="is-invalid block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="client in clientsList"
                                :key="client.id"
                                :value="client.id"
                        >{{client.first_name}} {{client.last_name}}</option>
                    </select>
                    <span class="invalid-feedback">{{errors.client_id}}</span>
                </div>

                <div class="form-group">
                    <label for="start_time"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Time</label>
                    <input v-model="form.start_time" name="start_time" type="date" id="start_time"
                           class="is-invalid flatpickr block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                    <span class="invalid-feedback">{{errors.start_time}}</span>

                </div>
                <div class="form-group">
                    <label for="end_time"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Time</label>
                    <input v-model="form.end_time" name="end_time" type="time" id="end_time"
                           class="is-invalid flatpickr block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                    <span class="invalid-feedback">{{errors.end_time}}</span>
                </div>
            </div>

            <div class="form-group">
                <label for="description"
                       class="is-invalid block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                <textarea
                            v-model="form.description"
                           rows="3"
                           class="form-control block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           id="description"
                           placeholder="Enter description"
                ></textarea>
                <span class="invalid-feedback">{{errors.description}}</span>
            </div>
        </div>
        <div
            class="flex justify-between bg-white px-4 pt-2 pb-3 sm:flex sm:flex-row-reverse sm:px-6">
            <button type="submit"
                    class="inline-flex w-full justify-center rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-800 sm:mt-0 sm:w-auto"
                    ref="cancelButtonRef">Add appointment
            </button>

        </div>
    </Form>
</template>

