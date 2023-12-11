import axios from "axios";

const URL = import.meta.env.VITE_BASE_URL + '/api'
export default {
    login(email, password, deviceToken) {
        console.log(URL)
        return axios.post(URL + '/login', {
            email: email,
            password: password,
            deviceToken: deviceToken
        })
    },
    logout(token, deviceToken) {
        return axios.post(URL + '/logout', {
            deviceToken: deviceToken
        }, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
    },
    changePassword(token, old_password, new_password, new_password_confirm) {
        return axios.post(URL+ '/change-password', {
            old_password: old_password,
            new_password: new_password,
            new_password_confirmation: new_password_confirm,
        }, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
    },
    checkEmail(email) {
        return axios.post(URL+ '/check-email', {
            email: email
        })
    },
    forgotPassword(email) {
        return axios.post(URL+ '/forgot-password', {
            email: email,
        })
    },
    resetPassword(token, email, password, password_confirm) {
        return axios.post(URL+ '/reset-password', {
            token: token,
            email: email,
            password: password,
            password_confirmation: password_confirm,
        })
    },
    updateProfile(token, data) {
        return axios.post(URL+ '/update-profile', data, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })
    }
}