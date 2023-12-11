import axios from "axios";

const url = import.meta.env.VITE_BASE_URL + '/api/user'

export default {
    getListUser(token, page, data) {
        return axios.post(url + `/list/${page}`, {
            name: data.name.toLowerCase(),
            address: data.address.toLowerCase(),
            phoneNumber: data.phoneNumber,
            email: data.email.toLowerCase(),
            position: data.position === 'none' ? '' : data.position,
            role: data.role === 'none' ? '' : data.role,
            department: data.department === 0 ? '' : data.department
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    getUserInDepartment(token, page, departmentID) {
        return axios.post(url + `/list/${page}`, {
            department: departmentID
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    getAllUser(token) {
        return axios.get(url, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    get1User(token, userID) {
        return axios.get(url + `/${userID}`, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    createUser(token, form, DOB) {
        return axios.post(url + `/create`, {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password,
            department_id: form.department,
            address: form.address,
            DOB: DOB,
            phone_number: form.phoneNumber,
            salary: form.salary,
            position: form.position === 'none' ? null : form.position,
            role: form.role
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    updateUser(token, form, DOB, userID) {
        return axios.put(url + `/update/${userID}`, {
            name: form.name,
            email: form.email,
            department_id: form.department,
            address: form.address,
            DOB: DOB,
            phone_number: form.phoneNumber,
            salary: form.salary,
            position: form.position === 'none' ? null : form.position,
            role: form.role
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    deleteUser(token, userID) {
        return axios.delete(`http://127.0.0.1:8000/api/delete-user/${userID}`, {
            headers: {Authorization: `Bearer ${token}`},
        })
    }
}