<template>
  <div>
    <!-- Header Section -->
    <header>
        <nav class="topnav">
          <div class="logo-container">
            <img src="/img/logo.png" alt="Event Vista Logo" class="logo"/>  
          </div>
          <ul class="nav-links">
            <li><router-link to="/" exact-active-class="active">Home</router-link></li>
            <li><router-link to="/appointment" exact-active-class="active">Appointment</router-link></li>
            <li><router-link to="/about" exact-active-class="active">About Us</router-link></li>
          </ul>
          <div class="profile">
            <router-link to="/login" class="login-btn">Login</router-link>
        </div>
        </nav>
      </header>


    <section class="content">
      <div class="intro-image">
        <img src="/img/loginfirst.png" alt="People Celebrating" />
      </div>
      <div class="intro-text">
        <p>
          Event Vista comes up with the best event booking software designed to manage your venue
          spaces with ease. Whatever may be the events, from meetings and conferences to social events
          like weddings and parties, Event Vista booking software is there to manage events with timeline,
          food, beverages, rental, and services. The software is ideal for businesses including banquet
          halls, meeting rooms, convention centers, clubs, catering services, parks, resorts, and organizations
          such as community centers, religious places, schools & universities.
        </p>
      </div>
    </section>



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
import { ref } from "vue";

export default {
  name: "EventVista",
  setup() {
    // Define a reactive property to control the dropdown visibility
    const dropdownVisible = ref(false);

    // Function to toggle dropdown visibility
    const toggleDropdown = () => {
      dropdownVisible.value = !dropdownVisible.value;
    };

    return { dropdownVisible, toggleDropdown };
  },
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
  top: 120%;
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

/*code here*/
.content {
    background-color: #fad9dd;
    padding: 3rem 2rem;
    text-align: center;
  }
  
  .intro-image img {
    width: 500px;
  }
  
  .intro-text {
    max-width: 800px;
    margin: 0 auto;
    font-size: 1.1rem;
    color: #5a5a5a;
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
