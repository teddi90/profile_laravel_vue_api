<script setup>
import {computed, onMounted, ref} from "vue";
import AppNavbar from "./components/AppNavbar.vue";
import SidebarLeft from "./components/SidebarLeft.vue";
import SidebarRight from "./components/SidebarRight.vue";
import AppFooter from "./components/AppFooter.vue";
import {useAuthUserStore} from "./stores/AuthUserStore.js";
import {useSettingStore} from "./stores/SettingStore.js";


const authUserStore=useAuthUserStore();
const settingStore=useSettingStore();

const currentTheme=computed(()=>{
   return settingStore.theme==='dark' ? 'dark-mode dark' : '';
});

</script>

<template>
    <div v-if="authUserStore.user.name !==''" class="wrapper" :class="currentTheme" id="app">

        <AppNavbar/>
        <SidebarLeft />

        <div class="content-wrapper pb-[20px]">
            <router-view></router-view>
        </div>

        <SidebarRight/>
        <AppFooter/>

    </div>
    <div v-else class="login-page" :class="currentTheme">
        <router-view></router-view>
    </div>
</template>

<style scoped>

</style>
