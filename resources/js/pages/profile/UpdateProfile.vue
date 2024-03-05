<script setup>
import {onMounted, ref,reactive} from 'vue';
import {useToaster} from "../../toastr.js";
import {useAuthUserStore} from "../../stores/AuthUserStore.js";

const authUserStore = useAuthUserStore();
const changePasswordForm = reactive({
    currentPassword:'',
    password:'',
    passwordConfirmation:'',
});
const toastr=useToaster();
const errors=ref();

const fileInput=ref(null);

const updateProfile=()=>{
    axios.put('/api/profile',{
        name:authUserStore.user.name,
        email:authUserStore.user.email,
        role:authUserStore.user.role,
    })
        .then(resp=>{
            toastr.success('Profile updated successfully');
        }).catch(error=> {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        }
    });
}
const openFileInput=()=>{
    fileInput.value.click();
}
const handleFileChange=(event)=>{
    const file = event.target.files[0];
    authUserStore.user.avatar=URL.createObjectURL(file);

    const formData=new FormData();
    formData.append('profile_picture',file);

    axios.post('/api/upload-profile-image',formData)
        .then(resp=>{
            toastr.success('Image uploaded successfully');
        }).catch(err=>{
        console.log(err)
    })
}
const handleChangePassword=()=>{
    errors.value="";
    axios.post('/api/change-user-password',changePasswordForm)
        .then(resp=>{
            toastr.success(resp.data.message)
          for(const filed in changePasswordForm){
              changePasswordForm[filed]="";
          }
        }).catch(error=> {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        })
}

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <router-link :to="{name:'admin.dashboard'}" class="breadcrumb-item">Home</router-link>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <input
                                @change="handleFileChange"
                                ref="fileInput" type="file" class="d-none">
                            <img @click="openFileInput" class="profile-user-img img-circle" :src="authUserStore.user.avatar" alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center">{{authUserStore.user.name}}</h3>

                        <p class="text-muted text-center">{{authUserStore.user.role}}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab"><i class="fa fa-user mr-1"></i> Edit Profile</a></li>
                            <li class="nav-item"><a class="nav-link" href="#changePassword" data-toggle="tab"><i class="fa fa-key mr-1"></i> Change
                                Password</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="profile">
                                <form @submit.prevent="updateProfile()" class="form-horizontal">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                        <div class="col-sm-10">
                                            <input v-model="authUserStore.user.name" type="text" class="form-control" id="inputName" placeholder="Name">
                                            <span v-if="errors && errors.name"
                                                  class="text-danger text-sm">{{errors.name[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input v-model="authUserStore.user.email" type="email" class="form-control " id="inputEmail" placeholder="Email">
                                            <span v-if="errors && errors.email"
                                                  class="text-danger text-sm">{{errors.email[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" class="btn bg-emerald-700 text-white">
                                                <i class="fa fa-save mr-1"></i>
                                                Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="changePassword">
                                <form @submit.prevent="handleChangePassword" class="form-horizontal">
                                    <div class="form-group row">
                                        <label for="currentPassword" class="col-sm-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-sm-9">
                                            <input v-model="changePasswordForm.currentPassword" type="password" class="form-control " id="currentPassword" placeholder="Current Password">
                                            <span v-if="errors && errors.current_password"
                                                  class="text-danger text-sm">{{errors.current_password[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="newPassword" class="col-sm-3 col-form-label">New
                                            Password</label>
                                        <div class="col-sm-9">
                                            <input v-model="changePasswordForm.password" type="password" class="form-control " id="newPassword" placeholder="New Password">
                                            <span v-if="errors && errors.password"
                                                  class="text-danger text-sm">{{errors.password[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="passwordConfirmation" class="col-sm-3 col-form-label">Confirm
                                            New Password</label>
                                        <div class="col-sm-9">
                                            <input v-model="changePasswordForm.passwordConfirmation" type="password" class="form-control " id="passwordConfirmation" placeholder="Confirm New Password">
                                            <span v-if="errors && errors.password_confirmation"
                                                  class="text-danger text-sm">{{errors.password_confirmation[0]}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn bg-emerald-700 text-white"><i class="fa fa-save mr-1"></i> Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .profile-user-img:hover{
        border-color: #2c8dee;
        cursor: pointer;
    }
</style>
