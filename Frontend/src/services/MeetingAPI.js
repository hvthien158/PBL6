import axios from "axios";

const url = import.meta.env.VITE_BASE_URL + '/api/meeting'

export default {
    getMyScheduleMeeting(token){
        return axios.get(url + '/me', {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    createNewScheduleMeeeting(token, title, start, end, attendees, detail){
        return axios.post(url + '/create',{
            'title': title,
            'start': start,
            'end': end,
            'detail': detail,
            'attendees': attendees,
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    deleteScheduleMeeting(token, meeting_id){
        return axios.post(url + '/delete', {
            'meeting_id': meeting_id,
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    markAsRead(token, meeting_id){
        return axios.post(url + '/read', {
            'meeting_id': meeting_id
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
}