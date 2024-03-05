import { defineStore } from 'pinia'
import {ref} from 'vue';

export const useSettingStore = defineStore('SettingStore',()=>{
    const setting=ref({
        app_name:'',

    });

    const getSetting=()=>{
        axios.get('/api/settings')
            .then(resp=>{
                setting.value=resp.data;
            })
    };

    return {setting,getSetting};
});
