// Import our CSS
import '@/css/app.pcss';
import Export from '@/vue/export/Export.vue';
import { createApp } from 'vue';
import { createPinia } from 'pinia'

const main = async () => {
    const app = createApp(Export);
    app.use(createPinia())

    console.log("export?")

    const vm = app.mount('#export');

    return vm
};

main().then( (vm) => {
});
