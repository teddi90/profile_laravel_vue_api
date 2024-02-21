<script setup>
import {onMounted,ref} from 'vue';
const settings=ref(null);
const fetchSettings=()=>{
    axios.get('/api/settings')
        .then((response)=>{
            settings.value=response.data;
        })
}

onMounted(()=>{
    fetchSettings();
})
</script>

<template>

</template>

<style scoped>

</style>
