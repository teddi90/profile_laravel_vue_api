<script setup>
import {onMounted, ref} from 'vue';
import {useToaster} from "../../toastr.js";
const settings =ref([]);
const toastr=useToaster();
const errors=ref();

const getSettings=()=>{
    axios.get('/api/settings')
        .then(resp=>{
            settings.value=resp.data;
        })
        .catch(err=>{
         console.log(err);
    })
}

const updateSettings=()=>{
    axios.post('/api/settings',settings.value)
        .then(resp=>{
            errors.value=''
            toastr.success('Settings updated successfully');
        }).catch(error=>{
        if (error.response && error.response.status===422) {
            errors.value=error.response.data.errors;
        }
    })
}

onMounted(()=>{
    getSettings();
})

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <router-link :to="{name:'admin.dashboard'}" class="breadcrumb-item">Home</router-link>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">General Setting</h3>
                        </div>

                        <form @submit.prevent="updateSettings" >
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="appName">App Display Name</label>
                                    <input v-model="settings.app_name" type="text" class="form-control" id="appName" placeholder="Enter app display name">
                                    <span v-if="errors && errors.app_name"
                                          class="text-danger text-sm">{{errors.app_name[0]}}</span>
                                </div>
                                <div class="form-group">
                                    <label for="dateFormat">Date Format</label>
                                    <select v-model="settings.date_format" class="form-control" id="dateFormat">
                                        <option value="m/d/Y">MM/DD/YYYY</option>
                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                        <option value="F j, Y">Month DD, YYYY</option>
                                        <option value="j F Y">DD Month YYYY</option>
                                    </select>
                                    <span v-if="errors && errors.date_format" class="text-danger text-sm">{{errors.date_format[0]}}</span>
                                </div>
                                <div class="form-group">
                                    <label for="paginationLimit">Pagination Limit</label>
                                    <input v-model="settings.pagination_limit" type="text" class="form-control" id="paginationLimit" placeholder="Enter pagination limit">
                                    <span v-if="errors && errors.pagination_limit" class="text-danger text-sm">{{errors.pagination_limit[0]}}</span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white"><i class="fa fa-save mr-1"></i>Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
