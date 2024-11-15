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

import '../../vendor/livewire/flux-pro/dist/flux.min.js';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import resize from '@alpinejs/resize';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import anchor from '@alpinejs/anchor';
import morph from '@alpinejs/morph';
import sort from '@alpinejs/sort';
window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(resize);
Alpine.plugin(persist);
Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.plugin(anchor);
Alpine.plugin(morph);
Alpine.plugin(sort);
Alpine.start();
