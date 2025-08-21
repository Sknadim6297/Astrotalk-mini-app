import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY || 'local',
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: process.env.MIX_PUSHER_HOST || '127.0.0.1',
    wsPort: process.env.MIX_PUSHER_PORT || 6001,
    wssPort: process.env.MIX_PUSHER_PORT || 6001,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});
