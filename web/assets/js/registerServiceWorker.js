import { Workbox } from 'workbox-window';

let wb;

if ('serviceWorker' in navigator) {
    wb = new Workbox(`/service-worker.js`);
    wb.addEventListener('controlling', () => {
        // At this point, reloading will ensure that the current
        // tab is loaded under the control of the new service worker.
        // Depending on your web app, you may want to auto-save or
        // persist transient state before triggering the reload.
        window.location.reload();
    });
    wb.register();
} else {
    wb = null;
}

export default wb;
