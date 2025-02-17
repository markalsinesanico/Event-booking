import { createRouter, createWebHistory } from 'vue-router';
import EventVista from '@/components/EventVista.vue';
import Login from '@/components/Login.vue';
import Register from '@/components/Register.vue';
import Home from '@/components/Home.vue';
import Appointment from '@/components/Appointment.vue';
import About from '@/components/About.vue';
import AboutLogin from '@/components/AboutLogin.vue';
import Receipt from '@/components/Receipt.vue';
import Profile from '@/components/Profile.vue';
import Venue from '@/components/Venue.vue';
import Booking from '@/components/Booking.vue';
import Privacy from '@/components/Privacy.vue';
import Terms from '@/components/Terms.vue';

// Import Dashboard Components
import AdminLogin from '@/components/dashboard/AdminLogin.vue';
import AdminHome from '@/components/dashboard/AdminHome.vue';
import AdminBooking from '@/components/dashboard/AdminBooking.vue';
import AdminCustomer from '@/components/dashboard/AdminCustomer.vue';
import AdminVenue from '@/components/dashboard/AdminVenue.vue';
import AdminProfile from './components/dashboard/AdminProfile.vue';

const routes = [
    // Public Routes
    { 
        path: '/', 
        name: 'EventVista', 
        component: EventVista,
        meta: { requiresGuest: true }
    },
    { 
        path: '/login', 
        name: 'Login', 
        component: Login,
        meta: { requiresGuest: true }
    },
    { 
        path: '/register', 
        name: 'Register', 
        component: Register,
        meta: { requiresGuest: true }
    },
    { 
        path: '/home', 
        name: 'Home', 
        component: Home 
    },
    { 
        path: '/appointment', 
        name: 'Appointment', 
        component: Appointment 
    },
    { 
        path: '/about', 
        name: 'About', 
        component: About 
    },
    { 
        path: '/receipt', 
        name: 'Receipt', 
        component: Receipt 
    },
    { 
        path: '/customerprofile', 
        name: 'CustomerProfile', 
        component: Profile 
    },
    { 
        path: '/venue', 
        name: 'Venue', 
        component: Venue 
    },
    { 
        path: '/venue/:adminId', 
        name: 'venue', 
        component: Venue,
        props: true
    },
    { 
        path: '/booking/:venueId?', 
        name: 'Booking', 
        component: Booking,
        props: true
    },
    { 
        path: '/privacy', 
        name: 'Privacy', 
        component: Privacy 
    },
    { 
        path: '/terms', 
        name: 'Terms', 
        component: Terms 
    },

    // Admin Dashboard Routes
    {
        path: '/admin',
        children: [
            {
                path: 'login',
                name: 'AdminLogin',
                component: AdminLogin
            },
            {
                path: 'home',
                name: 'AdminHome',
                component: AdminHome,
                meta: { requiresAuth: true }
            },
            {
                path: 'bookings',
                name: 'AdminBooking',
                component: AdminBooking,
                meta: { requiresAuth: true }
            },
            {
                path: 'customers',
                name: 'AdminCustomer',
                component: AdminCustomer,
                meta: { requiresAuth: true, requiresAdmin: true }
            },
            {
                path: 'venues',
                name: 'AdminVenue',
                component: AdminVenue,
                meta: { requiresAuth: true, requiresEitherRole: true }
            },
            {
                path: 'profile',
                name: 'AdminProfile',
                component: AdminProfile,
                meta: { requiresAuth: true, roles: ['admin', 'administrator'] }
            }
        ]
    },

    // Logout Route
    { 
        path: '/logout',
        name: 'Logout',
        component: {
            beforeRouteEnter(to, from, next) {
                next('/login');
            }
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation Guard for Admin Routes
router.beforeEach((to, from, next) => {
    const isLoggedIn = localStorage.getItem('token');
    const isAdminLoggedIn = localStorage.getItem('adminToken');
    const userRole = localStorage.getItem('userRole');

    // Redirect logged-in users away from guest-only routes
    if (to.meta.requiresGuest && isLoggedIn) {
        next('/home');
        return;
    }

    // Handle admin routes
    if (to.meta.requiresAuth) {
        if (!isAdminLoggedIn) {
            next('/admin/login');
            return;
        }

        // Check for admin-only routes
        if (to.meta.requiresAdmin && userRole !== 'admin') {
            alert('Access denied. This section is only accessible by admins.');
            next('/admin/home');
            return;
        }

        // Check for routes that require either admin or administrator role
        if (to.meta.requiresEitherRole && userRole !== 'admin' && userRole !== 'administrator') {
            alert('Access denied. This section is only accessible by admins or administrators.');
            next('/admin/home');
            return;
        }

        next();
    } else {
        next();
    }
});

export default router;

