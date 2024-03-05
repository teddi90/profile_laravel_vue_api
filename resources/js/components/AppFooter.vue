<script setup>
 import {useAuthUserStore} from "../stores/AuthUserStore.js";
 import {useSettingStore} from "../stores/SettingStore.js";

 const authUserStore=useAuthUserStore();
 const settingStore=useSettingStore();
</script>

<template>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-inline">
            {{authUserStore.user.name}}
        </div>

        <strong>Copyright &copy; 2024 <a href="#">{{settingStore.setting.app_name}}</a>.</strong> All rights reserved.
    </footer>
</template>

<style scoped>

</style>
