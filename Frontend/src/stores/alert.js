import {defineStore} from "pinia";
import {ref} from "vue";

export const useAlertStore = defineStore('alert', () => {
    const alert = ref(false)
    const type = ref('')
    const msg = ref('')

    function clear(){
        alert.value = false
        type.value = ''
        msg.value = ''
    }

    return {alert, type, msg, clear}
})