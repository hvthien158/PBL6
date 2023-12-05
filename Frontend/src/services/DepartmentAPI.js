import axios from "axios";
import {API_URL} from "../constants";

const url = API_URL + '/department'

export default {
    getListDepartment(token, page, data) {
        return axios.post(url + `/list/${page}`, {
            name: data.name.toLowerCase(),
            address: data.address.toLowerCase(),
            phoneNumber: data.phoneNumber,
            email: data.email.toLowerCase(),
            manager: data.manager,
            minStaff: data.minStaff,
            maxStaff: data.maxStaff
        }, {
            headers: {Authorization: `Bearer ${token}`}
        })
    },
    getAllDepartment() {
        return axios.get(url)
    },
    get1Department(token, departmentID) {
        return axios.get(url + `/${departmentID}`, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    createDepartment(token, data, manager) {
        return axios.post(url + `/create`, {
            departmentName: data.name,
            address: data.address,
            email: data.email,
            phoneNumber: data.phoneNumber,
            manager: manager
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    updateDepartment(token, data, manager, departmentID) {
        return axios.put(url + `/update/${departmentID}`, {
            departmentName: data.name,
            address: data.address,
            email: data.email,
            phoneNumber: data.phoneNumber,
            manager: manager
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    deleteDepartment(id, token) {
        return axios.delete(url + `/delete/${id}`, {
            headers: {Authorization: `Bearer ${token}`},
        })
    }
}