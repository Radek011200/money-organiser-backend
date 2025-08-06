import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './components/HomePage.vue';

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomePage
        }
    ]
});

// Create Vue app
const app = createApp({});

// Register components
app.component('home-page', HomePage);

// Use router
app.use(router);

// Mount app
app.mount('#app');
