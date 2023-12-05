import axios from "axios";
import {API_URL} from "../constants";

const url = API_URL + '/message'

export default {
    get5UnreadMessage(token){
        return axios.get(url + '/limit-unread', {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    get5Message(token){
        return axios.get(url + '/limit', {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    sendMessage(token, date, title, content){
      return axios.post(url + '/send', {
          'title': title,
          'content': content,
          'time_keeping_date': date,
      }, {
          headers: {
              Authorization: `Bearer ${token}`
          },
      })
    },
    markAsRead(token, messageID){
        return axios.post(url + '/read', {
            id: messageID
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    },
    markAsConfirmed(token, messageID){
        return axios.post(url + '/pass', {
            id: messageID
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
    }
}