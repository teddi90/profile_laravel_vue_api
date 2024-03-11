<script setup>
import AppointmentListItem from "./AppointmentListItem.vue";
import {computed, onMounted, ref} from "vue";
import {useToaster} from "../../toastr.js";
import {Bootstrap5Pagination} from "laravel-vue-pagination";
import Swal from 'sweetalert2'
import '@sweetalert2/theme-bulma/bulma.css';
import Prelodaer from "../../components/Prelodaer.vue";

const appointmentsStatus=ref([]);
const selectedStatus=ref();
const appointments = ref({data:[]});
const toastr = useToaster();
const loading=ref(false);

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
    loading.value=true;
    selectedStatus.value=status;
    const params={};
    if(status){
        params.status=status;
    }
    const resp = await axios.get(`/api/appointments?page=${page}`,{params:params});
    appointments.value = resp.data;
    loading.value=false;

}
const updateAppointmentStatusCount=(id)=>{
    const deletedAppointmentStatus= appointments.value.data.find(appointment=>appointment.id===id).status.name;
    const statusToUpdate= appointmentsStatus.value.find(status=> status.name===deletedAppointmentStatus);
    statusToUpdate.count--;
}


const deleteAppointment = (appointmentId) => {
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
            axios.delete('/api/appointments/' + appointmentId)
                .then(resp => {
                    updateAppointmentStatusCount(appointmentId);
                    appointments.value.data = appointments.value.data.filter(appointment => appointment.id !== appointmentId);
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your appointments has been deleted.",
                        icon: "success"
                    });
                }).catch(err => {
                console.log(err);
            });

        }
    });
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

    <div class="content">
       <Prelodaer :loading="loading"/>

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
                    <AppointmentListItem v-for="appointment in appointments.data"
                                         :key="appointment.client_id"
                                         @delete-appointment="deleteAppointment"
                                         :appointment="appointment"
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
<!--            <Bootstrap5Pagination-->
<!--                :data="appointments"-->
<!--                @pagination-change-page="getAppointments"-->
<!--            />-->
            <nav v-if="appointments.last_page>1" aria-label="Page navigation example">
                <ul class="flex items-center -space-x-px h-10 text-base">
                    <li>
                        <a v-if="appointments.prev_page_url"
                           @click="getAppointments(selectedStatus,appointments.current_page-1)"
                           class="cursor-pointer flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Previous</span>
                            <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                            </svg>
                        </a>
                    </li>
                    <li v-for="page in appointments.links">
                        <a v-if="page.label.length<5" href="#" @click="getAppointments(selectedStatus,page.label)"
                           :class="[page.active? 'z-10 flex items-center justify-center px-4 h-10 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white':'flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white']"
                           >{{page.label}}</a>
                    </li>

                    <li>
                        <a href="#" v-if="appointments.next_page_url"
                           @click="getAppointments(selectedStatus,appointments.current_page+1)"
                           class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Next</span>
                            <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<style scoped>

</style>
