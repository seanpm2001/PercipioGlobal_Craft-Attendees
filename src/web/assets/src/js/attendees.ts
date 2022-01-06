// Import our CSS
import '@/css/app.pcss';
import Attendees from '@/vue/attendees/Attendees.vue';
import { createApp } from 'vue';
import { createPinia } from 'pinia'

const main = async () => {
    const app = createApp({...Attendees});
    app.use(createPinia())

    const vm = app.mount('#training');
};

main().then( (root) => {
});
