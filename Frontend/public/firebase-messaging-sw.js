// Give the services worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the services worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Initialize the Firebase app in the services worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
  apiKey: "AIzaSyCpvXBC6WnUnRBQyRJVoLAPUC1Uu8q2Gaw",
  authDomain: "attendance-405902.firebaseapp.com",
  databaseURL: "https://attendance-405902-default-rtdb.firebaseio.com",
  projectId: "attendance-405902",
  storageBucket: "attendance-405902.appspot.com",
  messagingSenderId: "984259660658",
  appId: "1:984259660658:web:ee31f4f5ba25dad8a76beb",
  measurementId: "G-68XPWTRHCZ"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log(
    '[firebase-messaging-sw.js] Received background message ',
    payload
  );
  
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
