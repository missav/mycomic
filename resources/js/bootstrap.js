import lozad from 'lozad'
window.lozad = lozad

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Cookies from 'js-cookie';
window.Cookies = Cookies;

import recombee from 'recombee-js-api-client';
window.recombee = recombee;
window.recombeeClient = new recombee.ApiClient(
    import.meta.env.VITE_RECOMBEE_DATABASE,
    import.meta.env.VITE_RECOMBEE_PUBLIC_TOKEN,
    {
        baseUri: import.meta.env.VITE_RECOMBEE_BASE_URI,
    },
);
