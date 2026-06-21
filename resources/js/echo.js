import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
        },
    },
});

// After Pusher connects, stamp every axios request with this socket's ID
// so broadcast()->toOthers() can exclude the sender's socket correctly
window.Echo.connector.pusher.connection.bind('connected', () => {
    window.axios.defaults.headers.common['X-Socket-Id'] = window.Echo.socketId();
});
