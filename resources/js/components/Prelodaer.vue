<script setup>
defineProps({
    loading:{
        type:Boolean,
        default:false,
        required:true,
    },
})
</script>

<template>
    <div v-if="loading" class="preloader flex-column justify-content-center align-items-center">
        <!--            <img class="animation__shake" src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" height="60" width="60" alt="logo">-->
        <div class="loader"></div>
    </div>
</template>

<style scoped>
.preloader{
    background-color: black;
    opacity: .95;
}
.loader {
    width: 50px;
    padding: 8px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #007bff;
    --_m:
        conic-gradient(#0000 10%,#000),
        linear-gradient(#000 0 0) content-box;
    -webkit-mask: var(--_m);
    mask: var(--_m);
    -webkit-mask-composite: source-out;
    mask-composite: subtract;
    animation: l3 1s infinite linear;
}
@keyframes l3 {to{transform: rotate(1turn)}}
</style>
