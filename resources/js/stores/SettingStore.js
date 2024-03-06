import { defineStore } from 'pinia'
import {ref} from 'vue';
import { useStorage } from '@vueuse/core'
export const useSettingStore = defineStore('SettingStore',()=>{
    const setting=ref({
        app_name:'',

    });
    const theme=useStorage('SettingStore:theme',ref('light'));

    const changeTheme=()=>{
        theme.value=theme.value==='light'?'dark':'light';
    }

    const getSetting=async ()=>{
        await axios.get('/api/settings')
            .then(resp=>{
                setting.value=resp.data;
            })
    };

    return {setting,theme,getSetting, changeTheme};
});
