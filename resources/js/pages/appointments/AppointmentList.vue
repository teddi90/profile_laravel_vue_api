<script setup>
import AppointmentListItem from "./AppointmentListItem.vue";
import {onMounted, ref, watch} from "vue";
import {useToaster} from "../../toastr.js";
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from "@headlessui/vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/24/outline';
import {Field, Form} from "vee-validate";
import * as yup from "yup";
import {Bootstrap5Pagination} from "laravel-vue-pagination";

const appointmentsStatus={'scheduled':1,'confirmed':2,'cancelled':3,}
const appointments = ref({data:[]});
const isDialog = ref(false);
const isDangerDialog = ref(false);
const editMode = ref(false);
const appointmentId = ref();
const formValues = ref();
const toastr = useToaster();

const createAppointmentSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});
const editAppointmentSchema = yup.object({
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
const getAppointments = async (status, page=1) => {
    const params={};
    if(status){
        params.status=status;
    }
    const resp = await axios.get(`/api/appointments?page=${page}`,{params:params});
    appointments.value = resp.data;
}
const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateAppointment(values, actions);
    } else {
        createAppointment(values, actions);
    }
}
const createAppointment = (values, actions) => {
    axios.post('/api/appointments', values)
        .then(response => {
            let appointmentData = response.data;
            appointmentData.role = 'USER'
            appointments.value.data.unshift(appointmentData);
            isDialog.value = false;
            actions.resetForm();
            toastr.success('Appointment created successful')
        }).catch((error) => {
        if (error.response.data.errors) {
            actions.setErrors(error.response.data.errors);
        }
    });
}
const showDangerDialog = (appointment) => {
    isDangerDialog.value = true;
    appointmentId.value = appointment.id;
}
const editAppointment = (appointment) => {
    isDialog.value = true;
    editMode.value = true;
    formValues.value = {
        id: appointment.id,
        name: appointment.name,
        email: appointment.email,
    };
}
const updateAppointment = (values, actions) => {
    axios.put('/api/appointments/' + values.id, values)
        .then((resp => {
            const index = appointments.value.findIndex(appointment => appointment.id === values.id);
            appointments.value[index] = resp.data;
            actions.resetForm();
            isDialog.value = false;
            toastr.success('Appointment updated successful');
        })).catch((error) => {
        if (error.response.data.errors) {
            actions.setErrors(error.response.data.errors);
        }
    });
}
const deleteAppointment = () => {
    axios.delete('/api/appointments/' + appointmentId.value)
        .then(resp => {
            appointments.value = appointments.value.filter(appointment => appointment.id !== appointmentId.value);
            toastr.success('Appointment deleted successful');
            isDangerDialog.value = false;
        }).catch(err => {
        console.log(err);
    })
}
onMounted(()=>{
    getAppointments();
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
                    <button @click="isDialog = true; editMode=false;" class="btn mr-2 bg-blue-700 hover:bg-blue-800 text-white">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add new Appointment
                    </button>
                </div>
                <div>
                    <button @click="getAppointments()" type="button" class="btn mr-1 btn-default">
                        <span class="mr-1">All</span>
                        <span class="badge badge-pill badge-info">0</span>
                    </button>
                    <button @click="getAppointments(appointmentsStatus.scheduled)" type="button" class="btn mr-1 btn-default">
                        <span class="mr-1">Scheduled</span>
                        <span class="badge badge-pill badge-primary">0</span>
                    </button>
                    <button @click="getAppointments(appointmentsStatus.confirmed)" type="button" class="btn mr-1 btn-default">
                        <span class="mr-1">Confirmed</span>
                        <span class="badge badge-pill badge-success">0</span>
                    </button>
                    <button @click="getAppointments(appointmentsStatus.cancelled)" type="button" class="btn btn-default">
                        <span class="mr-1">Cancelled</span>
                        <span class="badge badge-pill badge-danger">0</span>
                    </button>
                </div>
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
                                                     class="text-base font-semibold leading-6 text-gray-900">Edit appointment
                                        </DialogTitle>
                                        <DialogTitle v-else as="h3"
                                                     class="text-base font-semibold leading-6 text-gray-900">Add new
                                            appointment
                                        </DialogTitle>
                                    </div>
                                    <Form @submit="handleSubmit"
                                          :validation-schema="editMode ? editAppointmentSchema : createAppointmentSchema"
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
                                  @edit-appointment="editAppointment"
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
