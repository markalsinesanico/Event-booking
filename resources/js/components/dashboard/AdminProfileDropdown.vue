<template>
  <div class="user-profile" @click="toggleDropdown" v-click-outside="closeDropdown">
    <img :src="profileImage" alt="User" />
    <div class="dropdown" v-if="isDropdownVisible">
      <div class="user-info">
        <p class="name">{{ userData.firstname }} {{ userData.lastname }}</p>
        <p class="role">{{ userData.role }}</p>
      </div>
      <div class="divider"></div>
      <router-link to="/admin/profile" class="dropdown-item">Profile Settings</router-link>
      <div class="divider"></div>
      <a href="#" @click.prevent="handleLogout" class="dropdown-item">Logout</a>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';
import axios from 'axios';
import { useProfile } from '@/composables/useProfile';

export default {
  name: 'AdminProfileDropdown',
  setup() {
    const router = useRouter();
    const { logout } = useAuth();
    const isDropdownVisible = ref(false);
    const profileImage = ref('/img/Profile.png');
    const userData = ref({
      firstname: '',
      lastname: '',
      role: ''
    });

    const toggleDropdown = () => {
      isDropdownVisible.value = !isDropdownVisible.value;
    };

    const closeDropdown = () => {
      isDropdownVisible.value = false;
    };

    // Add click outside handler
    const handleClickOutside = (event) => {
      const dropdown = document.querySelector('.user-profile');
      if (dropdown && !dropdown.contains(event.target)) {
        closeDropdown();
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
      // Fetch user data
      fetchUserData();
    });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });

    const fetchUserData = async () => {
      try {
        const response = await axios.get('/api/user/profile', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        if (response.data.status === 'success') {
          userData.value = response.data.user;
          if (response.data.user.profile_image) {
            profileImage.value = `/storage/profile_images/${response.data.user.profile_image}`;
          }
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };

    const handleLogout = async () => {
      try {
        await logout();
        router.push('/admin/login');
      } catch (error) {
        console.error('Logout error:', error);
      }
    };

    return {
      isDropdownVisible,
      profileImage,
      userData,
      toggleDropdown,
      closeDropdown,
      handleLogout
    };
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  }
};
</script>

<style scoped>
.user-profile {
  position: relative;
  cursor: pointer;
}

.user-profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  min-width: 200px;
  z-index: 1000;
}

.user-info {
  padding: 15px;
  text-align: center;
}

.name {
  font-weight: bold;
  margin-bottom: 5px;
}

.role {
  color: #666;
  font-size: 0.9em;
  text-transform: capitalize;
}

.divider {
  height: 1px;
  background: #eee;
  margin: 5px 0;
}

.dropdown-item {
  display: block;
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
}

.dropdown-item:hover {
  background: #f5f5f5;
}
</style> 