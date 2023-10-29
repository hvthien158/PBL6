import { defineStore } from "pinia";
import {ref} from "vue";
import {useLocalStorage} from "@vueuse/core";

export const useUserStore = defineStore('user', () => {
    const user = ref(
        useLocalStorage('vueUseUser', {
            id: '',
            token: '',
            name: '',
            email: '',
            password: '',
            expired: ''
        })
    )

    function logout() {
        user.value.id = ''
        user.value.token = ''
        user.value.name = ''
        user.value.email = ''
        user.value.password = ''
        user.value.expired = ''
    }

    function isExpired(){
        if(user.value.token){
            return Date.now() > Date.parse(user.value.expired);
        }
        return false
    }

    return {user, logout, isExpired}
})