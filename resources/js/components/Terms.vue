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

    <!-- Main Content -->
    <section class="content">
      <h1>Terms & Conditions</h1>
      <div class="content-text">
        <h2>Terms of Service</h2>
        <p>Welcome to Event Vista. By using our service, you agree to the following terms and conditions:</p>
        
        <h3>1. Acceptance of Terms</h3>
        <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

        <h3>2. User Account</h3>
        <p>To use certain features of the website, you must register for an account. You agree to provide accurate information and keep it updated.</p>

        <h3>3. Privacy Policy</h3>
        <p>Your use of the Event Vista website is also governed by our Privacy Policy.</p>

        <!-- Add more terms sections as needed -->
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
import { onMounted } from 'vue';
import { useProfileStore } from '@/store/profileStore';
import ProfileDropdown from '@/components/ProfileDropdown.vue';
import { useProfile } from '@/composables/useProfile';

export default {
  name: 'Terms',
  components: {
    ProfileDropdown
  },
  setup() {
    const profileStore = useProfileStore();
    const { fetchUserProfile } = useProfile();

    onMounted(() => {
      fetchUserProfile();
    });

    return {
      profileStore
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

/* Content Styles */
.content {
  background-color: #fad9dd;
  padding: 3rem 2rem;
  min-height: calc(100vh - 90px - 100px);
}

.content h1 {
  text-align: center;
  color: #59292A;
  margin-bottom: 2rem;
}

.content-text {
  max-width: 800px;
  margin: 0 auto;
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h2, h3 {
  color: #59292A;
  margin: 1.5rem 0;
}

p {
  color: #5a5a5a;
  line-height: 1.6;
  margin-bottom: 1rem;
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