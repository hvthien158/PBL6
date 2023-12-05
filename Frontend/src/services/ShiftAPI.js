import axios from "axios";
import {API_URL} from "../constants";

const url = API_URL + '/shift'

export default {
    getListShift(token, page, name) {
        return axios.post(url + `/list/${page}`, {
            name: name
        }, {
            headers: {Authorization: `Bearer ${token}`}
        })
    },
    get1Shift(token, shiftID) {
        return axios.get(url + `/${shiftID}`, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    createShift(token, data, checkIn, checkOut) {
        return axios.post(url + `/create`, {
            name: data.name,
            timeValidCheckIn: checkIn,
            timeValidCheckOut: checkOut,
            amount: data.amount
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    },
    updateShift(token, data, checkIn, checkOut, shiftID) {
        return axios.put(url + `/update/${shiftID}`, {
            name: data.name,
            timeValidCheckIn: checkIn,
            timeValidCheckOut: checkOut,
            amount: data.amount
        }, {
            headers: {Authorization: `Bearer ${token}`},
        })
    }
}