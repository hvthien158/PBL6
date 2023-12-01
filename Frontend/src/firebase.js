import { initializeApp } from "firebase/app";
import { getMessaging, onMessage } from "firebase/messaging";
const firebaseConfig = {
    apiKey: "AIzaSyCpvXBC6WnUnRBQyRJVoLAPUC1Uu8q2Gaw",
    authDomain: "attendance-405902.firebaseapp.com",
    databaseURL: "https://attendance-405902-default-rtdb.firebaseio.com",
    projectId: "attendance-405902",
    storageBucket: "attendance-405902.appspot.com",
    messagingSenderId: "984259660658",
    appId: "1:984259660658:web:ee31f4f5ba25dad8a76beb",
    measurementId: "G-68XPWTRHCZ"
};
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);
const payload = onMessage(messaging, (payload) => {
    return payload
});
export { messaging, payload }