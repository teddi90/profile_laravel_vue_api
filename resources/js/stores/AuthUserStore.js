import { defineStore } from 'pinia'
import {ref} from 'vue';

export const useAuthUserStore = defineStore('AuthUserStore',()=>{
    const user=ref({
        name:'',
        email:'',
        role:'',
        avatar:'',
    });

    const getAuthUser=()=>{
        axios.get('/api/profile')
            .then(resp=>{
                user.value=resp.data;
            })
    };

    return {user,getAuthUser};
});
