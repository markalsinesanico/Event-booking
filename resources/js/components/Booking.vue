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
            <input 
              type="date" 
              v-model="form.startDate"
              :min="getCurrentDate()"
              :class="{ 'date-booked': isDateBooked(form.startDate) }"
              @change="validateDates"
              @click="fetchBookedDates"
              required 
            />
          </div>
          <div class="form-group">
            <label>End Date</label>
            <input 
              type="date" 
              v-model="form.endDate"
              :min="form.startDate || getCurrentDate()"
              :class="{ 'date-booked': isDateBooked(form.endDate) }"
              @change="validateDates"
              required 
            />
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
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useProfileStore } from '@/store/profileStore';
import ProfileDropdown from '@/components/ProfileDropdown.vue';

export default {
  name: 'Booking',
  components: {
    ProfileDropdown
  },
  setup() {
    const route = useRoute();
    const venueId = route.params.venueId;
    const successMessage = ref('');
    const dropdownVisible = ref(false);
    const error = ref('');
    const bookedDates = ref([]);

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
        
        if (isDateBooked(form.value.startDate) || isDateBooked(form.value.endDate)) {
          error.value = 'Please select available dates';
          return;
        }
        
        // Format dates to include time
        const startDate = new Date(form.value.startDate);
        startDate.setHours(0, 0, 0, 0);
        
        const endDate = new Date(form.value.endDate);
        endDate.setHours(23, 59, 59, 999);

        const formData = {
          name: form.value.name,
          email: form.value.email,
          phone: form.value.phone,
          category: form.value.category,
          startDate: startDate.toISOString(),
          endDate: endDate.toISOString(),
          venueId: form.value.venueId
        };

        const response = await axios.post('/api/booking', formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          successMessage.value = 'Your appointment has been successfully submitted!';
          
          // Make sure user data is stored in localStorage
          const userData = {
            id: response.data.booking.user_id,
            // other user data if needed
          };
          localStorage.setItem('user_data', JSON.stringify(userData));
          
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

    const isDateBooked = (date) => {
      if (!date || !bookedDates.value.length) return false;
      
      const checkDate = new Date(date);
      checkDate.setHours(0, 0, 0, 0); // Normalize time to start of day
      
      return bookedDates.value.some(booking => {
        const startDate = new Date(booking.start_date);
        const endDate = new Date(booking.end_date);
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(23, 59, 59, 999);
        
        return checkDate >= startDate && checkDate <= endDate;
      });
    };

    const getCurrentDate = () => {
      return new Date().toISOString().split('T')[0];
    };

    const validateDates = () => {
      if (!form.value.startDate || !form.value.endDate) return;

      const start = new Date(form.value.startDate);
      const end = new Date(form.value.endDate);
      
      // Check if any date in the range is booked
      let currentDate = new Date(start);
      while (currentDate <= end) {
        if (isDateBooked(currentDate)) {
          error.value = 'One or more selected dates are not available';
          form.value.startDate = '';
          form.value.endDate = '';
          return;
        }
        currentDate.setDate(currentDate.getDate() + 1);
      }
      error.value = '';
    };

    const fetchBookedDates = async () => {
      try {
        const response = await axios.get(`/api/venue/${venueId}/booked-dates`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.data.bookings) {
          // Store the booked dates
          bookedDates.value = response.data.bookings.filter(booking => 
            booking.status !== 'rejected'
          );
          console.log('Booked dates:', bookedDates.value); // Debug log
        }
      } catch (err) {
        console.error('Error fetching booked dates:', err);
      }
    };

    onMounted(() => {
      fetchBookedDates();
      // Refresh booked dates every minute to keep the calendar up to date
      setInterval(fetchBookedDates, 60000);
    });

    return {
      form,
      successMessage,
      error,
      submitForm,
      dropdownVisible,
      toggleDropdown,
      venueId,
      bookedDates,
      isDateBooked,
      getCurrentDate,
      validateDates,
      fetchBookedDates
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

.date-booked {
  background-color: #ffebee !important;
  border-color: #ef5350 !important;
  color: #d32f2f !important;
  cursor: not-allowed !important;
  position: relative;
}

.date-booked::after {
  content: '⚠️ Booked';
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #d32f2f;
}

input[type="date"].date-booked::-webkit-calendar-picker-indicator {
  opacity: 0.4;
  cursor: not-allowed;
}

input[type="date"] {
  background-color: white;
  transition: all 0.3s ease;
  padding-right: 35px;
  position: relative;
}

.form-group {
  position: relative;
}
</style>