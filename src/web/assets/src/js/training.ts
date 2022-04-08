// Import our CSS
import '@/css/app.pcss';
import Trainings from '@/vue/trainings/Trainings.vue';
import { createApp } from 'vue';
import { createPinia } from 'pinia'

const main = async () => {
    const app = createApp(Trainings);
    app.use(createPinia())

    const vm = app.mount('#trainings');

    return vm
};

main().then( (vm) => {
});
