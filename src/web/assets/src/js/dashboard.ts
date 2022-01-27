// Import our CSS
import '@/css/app.pcss';
import Dashboard from '@/vue/dashboard/Dashboard.vue';
import { createApp } from 'vue';
import { createPinia } from 'pinia'

const main = async () => {
    const app = createApp(Dashboard);
    app.use(createPinia())

    const vm = app.mount('#dashboard');

    return vm
};

main().then( (vm) => {
});
