<script setup>
import AppointmentListItem from "./AppointmentListItem.vue";
import {computed, onMounted, ref} from "vue";
import {useToaster} from "../../toastr.js";
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from "@headlessui/vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/24/outline';
import {Bootstrap5Pagination} from "laravel-vue-pagination";

const appointmentsStatus=ref([]);
const selectedStatus=ref();
const appointments = ref({data:[]});
const isDangerDialog = ref(false);
const appointmentId = ref();
const toastr = useToaster();

const getAppointmentStatus=()=>{
    axios.get('/api/appointments-status')
        .then(resp=>{
            appointmentsStatus.value=resp.data;
        })
};
const appointmentsCount =computed(()=>{
    return appointmentsStatus.value.map(status=>status.count).reduce((acc,value)=>acc+value,0);
});

const getAppointments = async (status, page=1) => {
    selectedStatus.value=status;
    const params={};
    if(status){
        params.status=status;
    }
    const resp = await axios.get(`/api/appointments?page=${page}`,{params:params});
    appointments.value = resp.data;
}

const showDangerDialog = (appointment) => {
    isDangerDialog.value = true;
    appointmentId.value = appointment.id;
}

const deleteAppointment = () => {
    axios.delete('/api/appointments/' + appointmentId.value)
        .then(resp => {
            appointments.value.data = appointments.value.data.filter(appointment => appointment.id !== appointmentId.value);
            toastr.success('Appointment deleted successful');
            isDangerDialog.value = false;
        }).catch(err => {
        console.log(err);
    })
}
onMounted(()=>{
    getAppointments();
    getAppointmentStatus();
})
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <router-link :to="{name:'admin.dashboard'}" class="breadcrumb-item">Home</router-link>
                        <li class="breadcrumb-item active">Appointments</li>
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
                                            you sure you want to delete this Appointment
                                        </DialogTitle>

                                    </div>
                                </div>
                            </div>
                            <div class="bg-white px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                        @click="deleteAppointment()">Delete
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

    <div class="content">
        <div class="container-fluid">
            <div class="flex justify-between">
                <div class="mb-3 flex items-center">
                    <router-link :to="{name:'admin.appointments.create'}"  class="btn mr-2 bg-blue-700 hover:bg-blue-800 text-white">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add new Appointment
                    </router-link>
                </div>
                <div>
                    <button @click="getAppointments()" type="button" class="btn mr-1" :class="[!selectedStatus? 'btn-secondary bg-gray-600' : 'btn-default']">
                        <span class="mr-1">All</span>
                        <span class="badge badge-pill badge-info">{{appointmentsCount}}</span>
                    </button>
                    <button v-for="status in appointmentsStatus" @click="getAppointments(status.value)" type="button" class="btn mr-1" :class="[selectedStatus === status.value ? 'btn-secondary bg-gray-600' : 'btn-default']">
                        <span class="mr-1">{{status.name}}</span>
                        <span class="badge badge-pill" :class="`badge-${status.color}`">{{status.count}}</span>
                    </button>
                </div>
            </div>
            <div class="relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            id
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Client Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Time
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            Options
                        </th>
                    </tr>
                    </thead>
                    <tbody v-if="appointments.data.length">
                    <AppointmentListItem v-for="appointment in appointments.data" :key="appointment.client_id" :appointment="appointment"
                                  @show-danger-dialog="showDangerDialog"
                                  />

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
                :data="appointments"
                @pagination-change-page="getAppointments"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
