import axios from "axios";

const url = import.meta.env.VITE_BASE_URL + '/api/timekeeping'

export default {
    getListTimeKeep(from, to, token) {
        return axios.get(url + '/list/' + from + '/' + to, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
    },
    getListTimeKeepOfUser(from, to, token, userID) {
        return axios.get(url + '/list/' + from + '/' + to + '/' + userID, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
    },
    getMyTodayTimeKeep(token) {
        return axios.get(url + '/me', {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    checkIn(token) {
        return axios.post(url + '/check-in', {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        )
    },
    checkOut(token) {
        return axios.put(url + '/check-out', null,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        )
    },
    updateTimeKeep(token, userID, date, checkin, checkout, status_AM, status_PM) {
        return axios.post(url + '/update', {
            user_id: userID,
            date: date,
            time_check_in: checkin,
            time_check_out: checkout,
            status_am: status_AM,
            status_pm: status_PM,
        }, {
            headers: { Authorization: `Bearer ${token}` },
        })
    },
    updateStatus(token, userID, date, status_AM, status_PM) {
        return axios.post('http://127.0.0.1:8000/api/timekeeping/update', {
            user_id: userID,
            date: date,
            status_am: status_AM,
            status_pm: status_PM,
        }, {
            headers: { Authorization: `Bearer ${token}` },
        })
    },
    statisticTimeKeep(token, page, dateInterval, data) {
        return axios.post(url + `/manage/${page}`, {
            from: dateInterval[0],
            to: dateInterval[1],
            name: data.name,
            department: data.department,
        }, {
            headers: { Authorization: `Bearer ${token}` }
        })
    },
    exportData(token, start, end, userID) {
        return axios.get(url + `/export/${start}/${end}/${userID}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
    },
    exportTimeKeeping(token, start, end) {
        return axios.post(url + `/export-statistic`,
            {
                from: start,
                to: end
            }, 
            { headers: { Authorization: `Bearer ${token}` }
        })
    }
}