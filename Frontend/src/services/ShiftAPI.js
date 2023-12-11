import axios from "axios";

const url = import.meta.env.VITE_BASE_URL + '/api/shift'

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