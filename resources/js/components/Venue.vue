<template>
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

    <main>
      <div v-if="loading" class="loading">Loading venues...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else>
        <h2 class="admin-name">Venues by {{ adminName }}</h2>
        
        <div class="venues-container">
          <section v-for="venue in venues" :key="venue.id" class="venue-section">
            <div class="text">
              <div class="venue-header">
                <h2>{{ venue.name }}</h2>
                <span :class="['status-badge', getStatusClass(venue.status)]">
                  {{ formatStatus(venue.status) }}
                </span>
              </div>
              <p>{{ venue.description }}</p>
              <router-link 
                v-if="isVenueBookable(venue.status)" 
                :to="{ name: 'Booking', params: { venueId: venue.id }}" 
                class="btn"
              >
                Book Now!
              </router-link>
              <span v-else class="unavailable-message">
                {{ getUnavailableMessage(venue.status) }}
              </span>
            </div>
            <img 
              :src="venue.image || '/storage/default-venue.png'" 
              :alt="venue.name"
              @error="handleImageError"
            />
          </section>
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
  </template>

 
  
  <script>
   import { ref, onMounted } from 'vue';
   import { useAuth } from '../composables/useAuth';
   import { useRoute } from 'vue-router';
   import axios from 'axios';
   import ProfileDropdown from '@/components/ProfileDropdown.vue';

  export default {
    name: 'Venue',
    components: {
      ProfileDropdown
    },
    props: {
      adminId: {
        type: String,
        required: true
      }
    },
    setup(props) {
    // Define a reactive property to control the dropdown visibility
    const dropdownVisible = ref(false);
    const venues = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const adminName = ref('');
    const { logout } = useAuth();
    const route = useRoute();

    // Function to toggle dropdown visibility
    const toggleDropdown = () => {
      dropdownVisible.value = !dropdownVisible.value;
    };

    const fetchVenues = async () => {
      try {
        loading.value = true;
        const adminId = props.adminId || route.params.adminId;
        
        console.log('Fetching venues for admin:', adminId); // Debug log
        
        const response = await axios.get(`/api/venues/admin/${adminId}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        console.log('API Response:', response.data); // Debug log

        if (response.data.status === 'success') {
          venues.value = response.data.venues;
          adminName.value = response.data.admin_name;
        } else {
          throw new Error('Failed to fetch venues');
        }
      } catch (err) {
        console.error('Error details:', err.response || err); // Enhanced error logging
        error.value = err.response?.data?.message || 'Failed to load venues. Please try again.';
      } finally {
        loading.value = false;
      }
    };

    const handleImageError = (e) => {
      console.error('Image failed to load:', e.target.src);
      e.target.src = `${window.location.origin}/img/default-venue.png`;
    };

    const getStatusClass = (status) => {
      const statusMap = {
        'available': 'status-available',
        'occupied': 'status-occupied',
        'maintenance': 'status-maintenance',
        'unavailable': 'status-unavailable'
      };
      return statusMap[status?.toLowerCase()] || 'status-unknown';
    };

    const formatStatus = (status) => {
      const statusMap = {
        'available': 'Available',
        'occupied': 'Occupied',
        'maintenance': 'Under Maintenance',
        'unavailable': 'Unavailable'
      };
      return statusMap[status?.toLowerCase()] || status;
    };

    const isVenueBookable = (status) => {
      return status?.toLowerCase() === 'available';
    };

    const getUnavailableMessage = (status) => {
      const messageMap = {
        'occupied': 'This venue is currently occupied',
        'maintenance': 'This venue is under maintenance',
        'unavailable': 'This venue is not available for booking'
      };
      return messageMap[status?.toLowerCase()] || 'This venue cannot be booked at this time';
    };

    onMounted(() => {
      fetchVenues();
    });

    return { dropdownVisible, toggleDropdown, handleLogout: logout, venues, loading, error, adminName, handleImageError, getStatusClass, formatStatus, isVenueBookable, getUnavailableMessage };
  },
  };
  </script>
  
  <style scoped>
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

  main {
    padding: 50px;
  }
  
  section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #FFD1D1;
    padding: 30px;
    margin-bottom: 40px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  }
  
  section img {
    max-width: 45%;
    height: auto;
    border-radius: 10px;
  }
  
  section .text {
    max-width: 50%;
  }
  
  section h2 {
    font-family: 'Georgia', serif;
    color: #000;
    font-size: 28px;
    margin-bottom: 15px;
  }
  
  section p {
    font-family: 'Arial', sans-serif;
    color: #333;
    font-size: 18px;
    line-height: 1.5;
    margin-bottom: 20px;
  }
  
  .btn {
    background-color: #F596CD;
    color: white;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  
  .btn:hover {
    background-color: #E583B6;
  }
  
  .restaurant {
    flex-direction: row-reverse;
  }
  
  .function-halls {
    flex-direction: row-reverse;
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

.venues-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.venue-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #FFD1D1;
  padding: 30px;
  margin-bottom: 40px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.venue-section img {
  max-width: 45%;
  height: auto;
  border-radius: 10px;
  object-fit: cover;
}

.admin-name {
  text-align: center;
  color: #59292A;
  font-size: 32px;
  margin: 30px 0;
}

.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #59292A;
}

.error {
  color: #ff4444;
}

.debug {
  font-size: 12px;
  color: #666;
  font-family: monospace;
}

/* Add these new styles for venue status */
.venue-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 20px;
}

.venue-header h2 {
  margin: 0;
  flex: 1;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  text-transform: capitalize;
  display: inline-block;
  min-width: 100px;
  text-align: center;
}

.status-available {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-occupied {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

.status-maintenance {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.status-unavailable {
  background-color: #e2e3e5;
  color: #383d41;
  border: 1px solid #d6d8db;
}

.unavailable-message {
  display: inline-block;
  padding: 12px 25px;
  background-color: #f8f9fa;
  color: #6c757d;
  border-radius: 8px;
  font-weight: 500;
  border: 1px solid #dee2e6;
  margin-top: 15px;
}

/* Update venue section styles */
.venue-section {
  transition: transform 0.3s ease;
}

.venue-section:hover {
  transform: translateY(-5px);
}

/* Update button styles */
.btn {
  display: inline-block;
  margin-top: 15px;
}

.btn:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}
</style>
