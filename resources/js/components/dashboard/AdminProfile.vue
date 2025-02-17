<template>
  <div class="dashboard">
    <AdminSidebar />
    <div class="main-content">
      <header>
        <h1>PROFILE SETTINGS</h1>
        <AdminProfileDropdown />
      </header>

      <div class="profile-container">
        <div class="profile-section">
          <div class="profile-header">
            <div class="profile-image">
              <img :src="profileImage" alt="Profile Picture" />
              <button class="change-photo-btn" @click="triggerFileInput">Change Photo</button>
              <input
                type="file"
                ref="fileInput"
                style="display: none"
                @change="handleFileChange"
                accept="image/*"
              />
            </div>
            <div class="profile-info">
              <h2>{{ userData.firstname }} {{ userData.lastname }}</h2>
              <p class="role">{{ userData.role }}</p>
            </div>
          </div>

          <form @submit.prevent="updateProfile" class="profile-form">
            <div class="form-group">
              <label>First Name</label>
              <input type="text" v-model="formData.firstname" required />
            </div>

            <div class="form-group">
              <label>Last Name</label>
              <input type="text" v-model="formData.lastname" required />
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" v-model="formData.email" required />
            </div>

            <div class="form-group">
              <label>Phone</label>
              <input type="tel" v-model="formData.phone" required />
            </div>

            <div class="form-group">
              <label>New Password (leave blank to keep current)</label>
              <input type="password" v-model="formData.password" />
            </div>

            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" v-model="formData.password_confirmation" />
            </div>

            <div class="button-group">
              <button type="submit" class="save-btn">Save Changes</button>
              <button type="button" class="cancel-btn" @click="resetForm">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import AdminSidebar from './AdminSidebar.vue';
import AdminProfileDropdown from './AdminProfileDropdown.vue';
import axios from 'axios';

export default {
  name: 'AdminProfile',
  components: {
    AdminSidebar,
    AdminProfileDropdown
  },
  setup() {
    const profileImage = ref('/img/Profile.png');
    const fileInput = ref(null);
    const userData = ref({});
    const formData = ref({
      firstname: '',
      lastname: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: ''
    });

    const fetchAdminProfile = async () => {
      try {
        const response = await axios.get('/api/user/profile', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.status === 'success') {
          userData.value = response.data.user;
          formData.value = {
            firstname: response.data.user.firstname,
            lastname: response.data.user.lastname,
            email: response.data.user.email,
            phone: response.data.user.phone || '',
            password: '',
            password_confirmation: ''
          };
          if (response.data.user.profile_image) {
            profileImage.value = `/storage/profile_images/${response.data.user.profile_image}`;
          }
        }
      } catch (error) {
        console.error('Error fetching profile:', error);
        alert('Failed to load profile data');
      }
    };

    const triggerFileInput = () => {
      fileInput.value.click();
    };

    const handleFileChange = async (event) => {
      const file = event.target.files[0];
      if (!file) return;

      const formDataObj = new FormData();
      formDataObj.append('image', file);

      try {
        const response = await axios.post('/api/user/profile/image', formDataObj, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.status === 'success') {
          profileImage.value = `/storage/profile_images/${response.data.image_path}`;
          alert('Profile image updated successfully!');
          await fetchAdminProfile(); // Refresh profile data
        }
      } catch (error) {
        console.error('Error uploading image:', error);
        alert('Failed to update profile image');
      }
    };

    const updateProfile = async () => {
      try {
        const response = await axios.post('/api/user/profile/update', formData.value, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.status === 'success') {
          alert('Profile updated successfully!');
          await fetchAdminProfile(); // Refresh profile data
        }
      } catch (error) {
        console.error('Error updating profile:', error);
        if (error.response?.data?.message) {
          alert(error.response.data.message);
        } else {
          alert('Failed to update profile');
        }
      }
    };

    const resetForm = () => {
      fetchAdminProfile(); // Reset form by refetching profile data
    };

    onMounted(() => {
      fetchAdminProfile();
    });

    return {
      profileImage,
      fileInput,
      userData,
      formData,
      triggerFileInput,
      handleFileChange,
      updateProfile,
      resetForm
    };
  }
};
</script>

<style scoped>
.dashboard {
  display: flex;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.main-content {
  margin-left: 250px;
  padding: 20px;
  width: calc(100% - 250px);
  min-height: 100vh;
}

header {
  position: fixed;
  top: 0;
  left: 250px;
  right: 0;
  height: 80px;
  background-color: #dab0d8;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

header h1 {
  color: #333;
  font-size: 24px;
  font-weight: bold;
}

.profile-container {
  margin-top: 100px;
  padding: 20px;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.profile-section {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.profile-header {
  display: flex;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #eee;
}

.profile-image {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-right: 30px;
}

.profile-image img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #dab0d8;
  margin-bottom: 10px;
}

.change-photo-btn {
  background-color: #6b4a86;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.2s;
}

.change-photo-btn:hover {
  background-color: #5a3d71;
}

.profile-info h2 {
  margin: 0;
  color: #333;
  font-size: 24px;
}

.profile-info .role {
  color: #666;
  text-transform: capitalize;
  margin-top: 5px;
}

.profile-form {
  padding: 20px 0;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  transition: border-color 0.2s;
}

.form-group input:focus {
  border-color: #6b4a86;
  outline: none;
}

.button-group {
  display: flex;
  gap: 10px;
  margin-top: 30px;
  justify-content: flex-end;
}

.save-btn, .cancel-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.2s;
}

.save-btn {
  background-color: #6b4a86;
  color: white;
}

.save-btn:hover {
  background-color: #5a3d71;
}

.cancel-btn {
  background-color: #e0e0e0;
  color: #333;
}

.cancel-btn:hover {
  background-color: #d0d0d0;
}
</style> 