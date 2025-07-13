import { createRouter, createWebHistory } from 'vue-router'

import Home from './components/HomePage.vue';
import Client from './pages/Clients.vue';
import Invoice from './pages/Invoices.vue';
import Profile from './pages/Profile.vue';
import Login from './pages/Login.vue';
import Signup from './pages/Signup.vue';


const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,

        children: [
             
            {
                path: '/Client',
                name: 'Client',
                component: Client
            },
            {
                path: '/Invoice',
                name: 'Invoice',
                component: Invoice
            },
            {
                path: '/Profile',
                name: 'Profile',
                component: Profile
            }
            
        ]

    },
    {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/signup',
    name: 'Signup',
    component: Signup,
  },
//   {
//     path: '/:pathMatch(.*)*',
//     name: 'NotFound',
//     component: NotFound
//   },
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router;