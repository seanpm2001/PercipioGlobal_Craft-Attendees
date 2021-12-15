// Import our CSS
import '@/css/app.pcss';
import Attendees from '@/vue/attendees.vue';
import { createApp } from 'vue';

const main = async () => {
    const app = createApp({...Attendees});
    const vm = app.mount('#training');
};

main().then( (root) => {
});
