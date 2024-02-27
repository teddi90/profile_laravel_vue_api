import Dashboard from "./components/Dashboard.vue";
import AppointmentList from "./pages/appointments/AppointmentList.vue";
import UpdateSetting from "./pages/settings/UpdateSetting.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import UserList from "./pages/users/UserList.vue";
import AppointmentForm from "./pages/appointments/AppointmentForm.vue";

export default [
    {
        path:'/admin/dashboard',
        name:'admin.dashboard',
        component:Dashboard,
    },
    {
        path:'/admin/appointments',
        name:'admin.appointments',
        component:AppointmentList,
    },
    {
        path:'/admin/appointments/create',
        name:'admin.appointments.create',
        component:AppointmentForm,
    },
    {
        path:'/admin/appointments/:id/edit',
        name:'admin.appointments.edit',
        component:AppointmentForm,
    },
    {
        path:'/admin/users',
        name:'admin.users',
        component:UserList,
    },
    {
        path:'/admin/settings',
        name:'admin.settings',
        component:UpdateSetting,
    },
    {
        path:'/admin/profile',
        name:'admin.profile',
        component:UpdateProfile,
    },
]
