<template>
  <div>
    <header>
      <nav class="topnav">
        <!-- Logo Section -->
        <div class="logo-container">
          <img src="/img/logo.png" alt="Event Vista Logo" class="logo" />
        </div>

        <!-- Navigation Links -->
        <ul class="nav-links">
          <li><router-link to="/home">Home</router-link></li>
          <li><router-link to="/appointment">Appointment</router-link></li>
          <li><router-link to="/about">About Us</router-link></li>
        </ul>

        <!-- Profile Dropdown -->
        <div class="profile" @click="toggleDropdown">
          <img :src="profileStore.state.profileImage" alt="Profile Picture" />
          <span class="arrow"><i class="fas fa-chevron-down"></i></span>
          <!-- Dropdown Menu -->
          <div v-if="dropdownVisible" class="dropdown-menu">
            <router-link to="/customerprofile">Profile</router-link>
            <router-link to="/receipt">Receipt</router-link>
            <a href="#" @click.prevent="handleLogout">Log Out</a>
          </div>
        </div>
      </nav>
    </header>

    <main class="profile-main">
      <div class="profile-container">
        <h2>Profile Information</h2>
        
        <div class="profile-content">
          <div class="profile-image-section">
            <img :src="profileStore.state.profileImage" alt="Profile Picture" />
            <input 
              v-if="isEditing"
              type="file"
              @change="handleImageUpload"
              accept="image/*"
              ref="fileInput"
              style="display: none"
            />
            <button v-if="isEditing" @click="$refs.fileInput.click()" class="btn">
              Change Photo
            </button>
          </div>

          <div class="profile-details">
            <div class="form-group">
              <label>First Name:</label>
              <input v-if="isEditing" v-model="profileStore.state.userData.firstname" type="text" />
              <span v-else>{{ profileStore.state.userData.firstname }}</span>
            </div>

            <div class="form-group">
              <label>Last Name:</label>
              <input v-if="isEditing" v-model="profileStore.state.userData.lastname" type="text" />
              <span v-else>{{ profileStore.state.userData.lastname }}</span>
            </div>

            <div class="form-group">
              <label>Email:</label>
              <input v-if="isEditing" v-model="profileStore.state.userData.email" type="email" />
              <span v-else>{{ profileStore.state.userData.email }}</span>
            </div>

            <div class="form-group">
              <label>Phone:</label>
              <input v-if="isEditing" v-model="profileStore.state.userData.phone" type="tel" />
              <span v-else>{{ profileStore.state.userData.phone }}</span>
            </div>

            <div class="button-group">
              <button v-if="!isEditing" @click="isEditing = true" class="btn">Edit Profile</button>
              <template v-else>
                <button @click="saveChanges" class="btn save-btn">Save Changes</button>
                <button @click="cancelEditing" class="btn cancel-btn">Cancel</button>
              </template>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer>
      <div class="footer-left">
        <img src="/img/logo.png" alt="Event Vista Logo">
      </div>
      <div class="social-media">
        <a href="#"><i class="fas fa-envelope"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
      </div>
      <div class="contact-info">
        <strong>CONTACT</strong>
        <p>Brgy. Washington</p>
        <p>+629123456789</p>
        <a href="mailto:alskwe@gmail.com">alskwe@gmail.com</a>
      </div>
      <div class="footer-right">
        <router-link to="/privacy">Privacy</router-link><br><br>
        <router-link to="/terms">Terms & Conditions</router-link><br><br>
        <p>©2024 AlsKwen. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useProfileStore } from '@/store/profileStore';
import axios from 'axios';

export default {
  setup() {
    const { logout } = useAuth();
    const profileStore = useProfileStore();
    const isEditing = ref(false);
    const dropdownVisible = ref(false);
    const originalData = ref(null);

    const toggleDropdown = () => {
      dropdownVisible.value = !dropdownVisible.value;
    };

    const saveChanges = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/user/profile/update', profileStore.state.userData, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });
        
        if (response.data.status === 'success') {
          isEditing.value = false;
          originalData.value = { ...profileStore.state.userData };
          await fetchUserProfile();
        }
      } catch (error) {
        console.error('Error updating profile:', error);
        alert('Failed to update profile. Please try again.');
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
          const user = response.data.user;
          profileStore.updateUserData(user);
          originalData.value = { ...user };

          if (user.profile_image) {
            profileStore.updateProfileImage(user.profile_image);
          }
        }
      } catch (error) {
        console.error('Error fetching profile:', error);
        alert('Failed to load profile data. Please refresh the page.');
      }
    };

    const cancelEditing = () => {
      isEditing.value = false;
      if (originalData.value) {
        profileStore.updateUserData(originalData.value);
      }
    };

    const handleImageUpload = async (event) => {
      const file = event.target.files[0];
      if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
          alert('Image size should be less than 2MB');
          return;
        }

        const formData = new FormData();
        formData.append('image', file);

        try {
          const token = localStorage.getItem('token');
          const response = await axios.post('/api/user/profile/image', formData, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          });

          if (response.data.status === 'success') {
            profileStore.updateProfileImage(response.data.image_path);
            await fetchUserProfile(); // Refresh profile data
          }
        } catch (error) {
          console.error('Error uploading image:', error);
          alert('Failed to upload image. Please try again.');
        }
      }
    };

    onMounted(() => {
      fetchUserProfile();
    });

    return {
      profileStore,
      isEditing,
      dropdownVisible,
      toggleDropdown,
      handleImageUpload,
      handleLogout: logout,
      saveChanges,
      cancelEditing
    };
  }
};
</script>

<style scoped>
/* Header Styles */
.topnav {
  background-color: #e8a6e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 40px;
  height: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo-container {
  display: flex;
  align-items: left;
}

.logo {
  height: auto;
  width: 150px;
  margin-right: 0px;
  margin-left: 50px;
}

.nav-links {
  display: flex;
  margin-left: 10px;
  list-style: none;
  gap: 100px;
}

.nav-links li {
  margin-left: 30px;
}

.nav-links li a {
  font-size: 18px;
  color: #333;
  font-weight: bold;
  text-decoration: none;
}

.nav-links li a:hover,
.nav-links li a.active {
  color: pink;
}

/* Profile Dropdown Styles */
.profile {
  display: flex;
  align-items: center;
  position: relative;
}

.profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 8px;
  border: 2px solid #e8a6e6;
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
  z-index: 1;
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

/* Main Profile Content Styles */
.profile-main {
  background-color: #ffd1d1;
  min-height: calc(100vh - 90px - 200px); /* Adjust based on header and footer height */
  padding: 40px 20px;
}

.profile-container {
  max-width: 800px;
  margin: 0 auto;
  background-color: white;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-container h2 {
  color: #333;
  margin-bottom: 30px;
  text-align: center;
}

.profile-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 30px;
}

.profile-image-section {
  text-align: center;
}

.profile-image-section img {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 20px;
  border: 3px solid #e8a6e6;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  color: #666;
  margin-bottom: 8px;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
}

.form-group span {
  display: block;
  padding: 10px;
  color: #333;
  font-size: 16px;
}

.button-group {
  margin-top: 30px;
  display: flex;
  gap: 10px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  background-color: #e8a6e6;
  color: white;
  transition: background-color 0.3s;
}

.btn:hover {
  background-color: #d695d4;
}

.save-btn {
  background-color: #4CAF50;
}

.save-btn:hover {
  background-color: #45a049;
}

.cancel-btn {
  background-color: #f44336;
}

.cancel-btn:hover {
  background-color: #da190b;
}

/* Footer Styles */
footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #FFFFFF;
  padding: 20px;
}

.footer-left img {
  width: 150px;
}

.social-media {
  display: flex;
  gap: 30px;
  font-size: 40px;
}

.contact-info p {
  margin: 5px 0;
}

.footer-right a {
  text-decoration: none;
  margin-left: 20px;
  font-weight: bold;
  color: black;
}
</style>
