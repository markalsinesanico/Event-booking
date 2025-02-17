<template>
  <div class="profile" @click="toggleDropdown">
    <img :src="profileStore.state.profileImage" alt="Profile Picture" />
    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
    <div v-if="isDropdownVisible" class="dropdown-menu">
      <router-link to="/customerprofile">Profile</router-link>
      <router-link to="/receipt">Receipt</router-link>
      <a href="#" @click.prevent="handleLogout">Log Out</a>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useProfileStore } from '@/store/profileStore';
import axios from 'axios';

export default {
  name: 'ProfileDropdown',
  setup() {
    const { logout } = useAuth();
    const profileStore = useProfileStore();
    const isDropdownVisible = ref(false);

    const toggleDropdown = () => {
      isDropdownVisible.value = !isDropdownVisible.value;
    };

    const handleClickOutside = (event) => {
      if (!event.target.closest('.profile')) {
        isDropdownVisible.value = false;
      }
    };

    const fetchUserProfile = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('/api/user/profile', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        if (response.data.status === 'success') {
          profileStore.updateUserData(response.data.user);
          if (response.data.user.profile_image) {
            profileStore.updateProfileImage(response.data.user.profile_image);
          }
        }
      } catch (error) {
        console.error('Error fetching profile:', error);
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
      fetchUserProfile();
    });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });

    return {
      isDropdownVisible,
      profileStore,
      toggleDropdown,
      handleLogout: logout
    };
  }
};
</script>

<style scoped>
.profile {
  display: flex;
  align-items: center;
  position: relative;
  cursor: pointer;
}

.profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 8px;
  border: 2px solid #e8a6e6;
  object-fit: cover;
}

.dropdown-menu {
  position: absolute;
  top: 150%;
  right: 0;
  background-color: white;
  min-width: 150px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  overflow: hidden;
  z-index: 1000;
}

.dropdown-menu a {
  display: block;
  padding: 12px 16px;
  color: #333;
  text-decoration: none;
}

.dropdown-menu a:hover {
  background-color: #f1f1f1;
  color: #e8a6e6;
}
</style> 