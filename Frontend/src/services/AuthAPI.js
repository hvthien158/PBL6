import axios from "axios";
import {API_URL} from "../constants";

export default {
    login(email, password, deviceToken) {
        return axios.post(API_URL + '/login', {
            email: email,
            password: password,
            deviceToken: deviceToken
        })
    },
    logout(token, deviceToken) {
        return axios.post(API_URL + '/logout', {
            deviceToken: deviceToken
        }, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
    },
    changePassword(token, old_password, new_password, new_password_confirm) {
        return axios.post(API_URL + '/change-password', {
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
        return axios.post(API_URL + '/check-email', {
            email: email
        })
    },
    forgotPassword(email) {
        return axios.post(API_URL + '/forgot-password', {
            email: email,
        })
    },
    resetPassword(token, email, password, password_confirm) {
        return axios.post(API_URL + '/reset-password', {
            token: token,
            email: email,
            password: password,
            password_confirmation: password_confirm,
        })
    },
    updateProfile(token, data) {
        return axios.post(API_URL + '/update-profile', data, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })
    }
}