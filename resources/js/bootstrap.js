import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Optional: Laravel Echo for real-time events
try {
	const enableEcho = import.meta?.env?.VITE_BROADCAST_ENABLED === 'true';
	if (enableEcho) {
		const { default: Echo } = await import('laravel-echo');
		// Pusher-compatible client (could be Pusher or Reverb)
		const { default: Pusher } = await import('pusher-js');
		window.Pusher = Pusher;
		if (import.meta.env.DEV) {
			Pusher.logToConsole = true;
		}
		const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1';
		const customHost = import.meta.env.VITE_PUSHER_HOST;
		const base = {
			broadcaster: 'pusher',
			key: import.meta.env.VITE_PUSHER_APP_KEY,
			cluster,
			forceTLS: true,
			authEndpoint: '/broadcasting/auth',
			auth: { headers: { 'X-Requested-With': 'XMLHttpRequest' } },
		};
		// If custom host provided (e.g., Reverb or self-hosted), wire low-level WS options
		if (customHost) {
			Object.assign(base, {
				wsHost: customHost,
				wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
				wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
				forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'http') === 'https',
				enabledTransports: ['ws', 'wss'],
				disableStats: true,
			});
		}
		window.Echo = new Echo(base);
	}
} catch (e) {
	// Echo not initialized; ignore in non-realtime contexts
}
