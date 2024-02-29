<script setup>
import {ref, watch} from 'vue';
import {formatDate} from "../../helper.js";
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
import {useToaster} from "@/toastr.js";

const props=defineProps({
    user:Object,
    selectAll:Boolean,
});
const emit = defineEmits(['showDangerDialog','editUser','toggleSelection']);
const toastr = useToaster();

const userRoles = [
    {
        id:1,
        name:'USER',
        value:2,
    },
    {
        id:2,
        name:'ADMIN',
        value:1,
    },

];
const selected = ref(props.user.role);
const changeUserRole=()=>{
    console.log(selected.value)
    axios.patch(`/api/users/${props.user.id}/change-role`, {'role':selected.value.value})
        .then(()=>{
            toastr.success('Role changed successfully')
        }).catch(err=>{
            console.log(err);
    })
}
const toggleSelection=()=>{
    emit('toggleSelection', props.user);
}
watch(selected,()=>{
    changeUserRole();
})

</script>

<template>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <input @change="toggleSelection" type="checkbox" :checked="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        </th>
        <td class="px-6 py-4">
            {{ user.id }}
        </td>
        <td class="px-6 py-4">
            {{ user.name }}
        </td>
        <td class="px-6 py-4">
            {{ user.email }}
        </td>
        <td class="px-6 py-4">
            {{ user.formatted_created_at }}
        </td>
        <td class="px-6 py-2">
            <Listbox as="div" v-model="selected" >
                <div class="relative">
                    <ListboxButton
                        class="relative w-full cursor-default rounded-md bg-gray-50 py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        <span class="flex items-center">
                          <span class="ml-1 block truncate">{{ selected.name ? selected.name : selected}}</span>
                        </span>
                        <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
                          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true"/>
                        </span>
                    </ListboxButton>

                    <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                                leave-to-class="opacity-0">
                        <ListboxOptions
                            class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <ListboxOption as="template" v-for="role in userRoles" :key="role.id" :value="role"
                                           v-slot="{ active, selected }">
                                <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                    <div class="flex items-center">
                                        <span
                                            :class="[selected ? 'font-semibold' : 'font-normal', 'ml-1 block truncate']">{{
                                                role.name
                                            }}</span>
                                    </div>

                                    <span v-if="selected"
                                          :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                        <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                    </span>
                                </li>
                            </ListboxOption>
                        </ListboxOptions>
                    </transition>
                </div>
            </Listbox>
        </td>
        <td class="flex content-center justify-around px-6 py-4 text-right">
            <a @click.prevent="emit('editUser', user)" href="#"
               class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                </svg>
            </a>
            <a href="#" @click="emit('showDangerDialog', user);"
               class="font-medium text-red-600 dark:text-red-500 hover:text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
            </a>
        </td>
    </tr>
</template>
