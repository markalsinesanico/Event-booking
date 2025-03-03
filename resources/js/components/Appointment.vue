<template>
  <div>
    <!-- Header Section -->
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
        <ProfileDropdown />
      </nav>
    </header>

    <!-- Main Section with Logos -->
    <section class="main-section">
      <h2>Featured List of Venues</h2>
      <p>
        We serve a variety of businesses including established brand names, small businesses, and startups.
        This includes Clubs, Hotels, Convention centers, Bars, Resorts, banquet facilities, and more.
      </p>
      
      <!-- Admin Users Section -->
      <h3 class="admin-users-title">Click to View Their Venues</h3>
      <div class="admin-users">
        <router-link 
          v-for="admin in adminUsers" 
          :key="admin.id" 
          :to="{ name: 'venue', params: { adminId: admin.id }}" 
          class="admin-card"
        >
          <img :src="admin.profile_image ? `/storage/profile_images/${admin.profile_image}` : '/img/Profile.png'" 
               :alt="admin.firstname + ' ' + admin.lastname" 
               class="admin-image" />
          <div class="admin-info">
            <h4>{{ admin.firstname }} {{ admin.lastname }}</h4>
            <!--<p class="admin-role">{{ admin.role }}</p> -->
            <p class="admin-contact">{{ admin.email }}</p>
          </div>
        </router-link>
      </div>
    </section>

    <!-- Footer -->
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
import { onMounted, ref } from 'vue';
import { useProfileStore } from '@/store/profileStore';
import ProfileDropdown from '@/components/ProfileDropdown.vue';
import axios from 'axios';
import { useProfile } from '@/composables/useProfile';

export default {
  components: {
    ProfileDropdown
  },
  setup() {
    const profileStore = useProfileStore();
    const adminUsers = ref([]);
    const { fetchUserProfile } = useProfile();

    const fetchAdminUsers = async () => {
      try {
        const response = await axios.get('/api/admin/users', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        if (response.data.status === 'success') {
          adminUsers.value = response.data.users;
        }
      } catch (error) {
        console.error('Error fetching admin users:', error);
      }
    };

    onMounted(() => {
      fetchUserProfile();
      fetchAdminUsers();
    });

    return {
      profileStore,
      adminUsers
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
}
.nav-links li a:hover,
.nav-links li a.active {
  color: pink;
}
.profile {
  display: flex;
  align-items: center;
  position: relative; /* Ensure the dropdown is positioned relative to the profile container */
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
  top: 150%; /* Move the dropdown above the profile image */
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
:root {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.main-section {
  flex-grow: 1; /* Allows the section to grow and take up space */
  padding: 70px 20px;
  text-align: left;
  background-color: #FFD1D1;
}

.main-section h2 {
  font-size: 36px;
  margin-bottom: 20px;
  color: #59292A;
}

.admin-users-title {
  font-size: 20px;
  margin-top: 20px;
  text-align: center;
  margin-bottom: 20px;
  color: #59292A;
}

.main-section p {
  font-size: 18px;
  margin-bottom: 0px;
  color: #59292A;
}

.logos {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 200px;
  flex-wrap: wrap;
}

.logos img {
  width: 200px;
  height: auto;
}
footer {
display: flex;
justify-content: space-between;
align-items: center;
background-color: #FFFFFF;
padding: 20px;
margin-top: auto;
}

.footer-left {
display: flex;
align-items: center;
}

.footer-left img {
width: 150px;
height: auto;
}


.contact-info p {
margin: 5px 0;
}

.social-media {
display: flex; 
gap: 30px; 
font-size: 40px; 
}
.social-icon {
font-size: 100px; 
color: #333; 
transition: transform 0.3s, color 0.3s; 
}
.social-icon:hover {
transform: scale(1.1); 
color: #ff9a74; 
}
.footer-right a {
text-decoration: none;
margin-left: 20px;
font-weight: bold;
color: black;
}

.admin-users {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  margin: 30px auto;
  max-width: 1200px;
  padding: 0 20px;
}

.admin-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  width: 250px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.3s ease;
  text-decoration: none;
  color: inherit;
}

.admin-card:hover {
  transform: translateY(-5px);
}

.admin-image {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin: 0 auto 15px;
  display: block;
  border: 3px solid #e8a6e6;
}

.admin-info h4 {
  color: #333;
  margin: 0 0 5px 0;
  font-size: 1.2em;
}

.admin-role {
  color: #666;
  font-size: 0.9em;
  margin: 5px 0;
  text-transform: capitalize;
}

.admin-contact {
  color: #888;
  font-size: 0.85em;
  margin: 5px 0;
}
</style> 



