import './bootstrap';

window.timeouts = [];

window.recommendId = null;

if (window.location.hash.slice(1).length === 32) {
    window.recommendId = window.location.hash.slice(1);
    history.pushState('', document.title, `${window.location.pathname}${window.location.search}`);
}

if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
}

document.addEventListener('DOMContentLoaded', () => {
    window.userUuid = Cookies.get('user_uuid');

    if (! window.userUuid || window.userUuid.length !== 36) {
        if (window.crypto && window.crypto.randomUUID) {
            window.userUuid = window.crypto.randomUUID();
        } else {
            const generateUuid = () => {
                let d = new Date().getTime();
                let d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;

                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                    let r = Math.random() * 16;

                    if (d > 0) {
                        r = (d + r) % 16 | 0;
                        d = Math.floor(d / 16);
                    } else {
                        r = (d2 + r) % 16 | 0;
                        d2 = Math.floor(d2 / 16);
                    }

                    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
            }

            window.userUuid = generateUuid();
        }

        Cookies.set('user_uuid', window.userUuid, { expires: 365 });
    }

    Alpine.store('darkMode', {
        on: false,

        toggle() {
            this.on = ! this.on;
        },

        init() {
            this.on = this.wantsDarkMode();

            Alpine.effect(() => {
                document.dispatchEvent(new CustomEvent('dark-mode-toggled', { detail: { isDark: this.on }, bubbles: true }));

                this.applyToBody();
            })

            let media = window.matchMedia('(prefers-color-scheme: dark)');

            media.addEventListener('change', e => {
                this.on = media.matches;
            })
        },

        wantsDarkMode() {
            let media = window.matchMedia('(prefers-color-scheme: dark)');

            if (! window.localStorage.getItem('darkMode')) {
                return media.matches;
            } else {
                return JSON.parse(window.localStorage.getItem('darkMode'));
            }
        },

        applyToBody() {
            let state = this.on;

            window.localStorage.setItem('darkMode', JSON.stringify(state));

            state ? document.body.classList.add('dark') : document.body.classList.remove('dark');
        },
    });
});
