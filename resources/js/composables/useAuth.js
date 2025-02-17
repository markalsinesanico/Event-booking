import axios from 'axios';
import { useRouter } from 'vue-router';

export function useAuth() {
    const router = useRouter();

    const logout = async () => {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                // Check if user is admin and redirect accordingly
                const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
                if (userData.role === 'admin' || userData.role === 'administrator') {
                    router.push('/admin/login');
                } else {
                    router.push('/login');
                }
                return;
            }

            const response = await axios.post('/api/logout', {}, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            // Clear storage
            localStorage.removeItem('token');
            localStorage.removeItem('user_data');
            localStorage.removeItem('userRole');

            // Show success message
            alert('Logged out successfully!');

            // Check user role and redirect accordingly
            const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
            if (userData.role === 'admin' || userData.role === 'administrator') {
                router.push('/admin/login');
            } else {
                router.push('/login');
            }
            
        } catch (error) {
            console.error('Logout error:', error);
            // If we get any error, still clear storage and redirect
            localStorage.removeItem('token');
            localStorage.removeItem('user_data');
            localStorage.removeItem('userRole');
            
            // Show error message
            alert('An error occurred during logout. You have been logged out.');
            
            // Default to admin login if there's an error
            router.push('/admin/login');
        }
    };

    const login = async (credentials) => {
        try {
            const response = await axios.post('/api/login', credentials);
            
            if (response.data.status === 'success') {
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user_data', JSON.stringify(response.data.user));
                localStorage.setItem('userRole', response.data.user.role);

                // Show success message
                alert('Logged in successfully!');

                // Redirect based on role
                if (response.data.user.role === 'admin' || response.data.user.role === 'administrator') {
                    router.push('/admin/home');
                } else {
                    router.push('/home');
                }
                return true;
            }
            return false;
        } catch (error) {
            console.error('Login error:', error);
            alert('Login failed. Please check your credentials.');
            return false;
        }
    };

    return {
        logout,
        login
    };
} 