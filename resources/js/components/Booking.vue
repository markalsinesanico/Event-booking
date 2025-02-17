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
        <div class="profile" @click="toggleDropdown">
          <img src="/img/Profile.png" alt="Profile Picture" />
          <span class="arrow"><i class="fas fa-chevron-down"></i></span>
          <!-- Dropdown Menu -->
          <div v-if="dropdownVisible" class="dropdown-menu">
            <router-link to="/customerprofile">Profile</router-link>
            <router-link to="/receipt">Receipt</router-link>
            <router-link to="/logout">Log Out</router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Form Section -->
    <div class="form-container">
      <form @submit.prevent="submitForm" id="appointment-form">
        <h2>Gateway</h2>
        <div class="form-content">
          <div class="form-group">
            <input 
              type="text" 
              v-model="form.name" 
              placeholder="Full Name" 
              required
            />
          </div>
          <div class="form-group">
            <input 
              type="email" 
              v-model="form.email" 
              placeholder="Email" 
              required
            />
          </div>
          <div class="form-group">
            <input 
              type="tel" 
              v-model="form.phone" 
              placeholder="Phone" 
              required
            />
          </div>
          <div class="form-group">
            <select v-model="form.category" required>
              <option value="" disabled selected>Category</option>
              <option value="wedding">Wedding</option>
              <option value="party">Birthday</option>
              <option value="meeting">Meeting</option>
            </select>
          </div>
          <div class="form-group">
            <label>Start Date</label>
            <input type="date" v-model="form.startDate" required />
          </div>
          <div class="form-group">
            <label>End Date</label>
            <input type="date" v-model="form.endDate" required />
          </div>
        </div>
        <div v-if="error" class="error-message">
          {{ error }}
        </div>
        <div v-if="successMessage" class="success-message">
          <i class="fas fa-check-circle"></i> {{ successMessage }}
        </div>
        <div class="form-buttons">
          <button type="submit">Book Now!</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

export default {
  name: 'Booking',
  setup() {
    const route = useRoute();
    const venueId = route.params.venueId;
    const successMessage = ref('');
    const dropdownVisible = ref(false);
    const error = ref('');

    const form = ref({
      name: '',
      email: '',
      phone: '',
      category: '',
      startDate: '',
      endDate: '',
      venueId: venueId
    });

    const submitForm = async () => {
      try {
        error.value = '';
        
        // Basic validation
        if (!form.value.startDate || !form.value.endDate) {
          error.value = 'Please select both start and end dates';
          return;
        }

        const response = await axios.post('/api/bookings', form.value, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          successMessage.value = 'Your appointment has been successfully submitted!';
          setTimeout(() => {
            successMessage.value = '';
            window.location.href = '/receipt';
          }, 1500);
        }
      } catch (error) {
        console.error('Booking error:', error);
        if (error.response?.data?.message) {
          error.value = error.response.data.message;
        } else {
          error.value = 'Failed to submit booking. Please try again.';
        }
      }
    };

    const toggleDropdown = () => {
      dropdownVisible.value = !dropdownVisible.value;
    };

    return { 
      form, 
      successMessage,
      error, 
      submitForm, 
      dropdownVisible, 
      toggleDropdown,
      venueId 
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
/* Form Styles */
.form-container {
    background-color: #e8a6e6;
    padding: 40px;
    width: 40%;
    margin: 100px auto; /* Centers the form */
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #333;
    font-family: 'Arial', sans-serif;
    font-size: 28px;
}

.form-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-group select {
    background-color: #fff;
}
label {
    display: block;
    font-weight: 10;
    margin-right: 180px;
}
.form-buttons {
    display: flex;
    justify-content: center;
}

.form-buttons button {
    background-color: #FF69B4; /* Pink button color */
    color: #fff;
    padding: 15px 50px;
    border: none;
    border-radius: 30px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-buttons button:hover {
    background-color: #e550b4;
}

#success-message {
    margin-top: 10px;
    color: green;
    font-size: 16px;
}

#error-message {
    margin-top: 10px;
    color: red;
    font-size: 16px;
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

/* Add these styles */
.error-message {
  color: #dc3545;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 10px;
  margin: 10px 0;
  border-radius: 4px;
  text-align: center;
}

.success-message {
  color: #28a745;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  padding: 10px;
  margin: 10px 0;
  border-radius: 4px;
  text-align: center;
}
</style>
