import {defineStore} from "pinia";
import {ref} from "vue";

export const useStatusStore = defineStore('alert', () => {
    const status_AM = ref(0)
    const status_PM = ref(0)

    return {status_AM, status_PM}
})