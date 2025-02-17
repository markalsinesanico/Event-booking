<template>
  <div>
    <!-- Header Section -->
    <header>
      <nav class="topnav">
        <div class="logo-container">
          <img src="/img/logo.png" alt="Event Vista Logo" class="logo" />
        </div>
        <ul class="nav-links">
          <li><router-link to="/home">Home</router-link></li>
          <li><router-link to="/appointment">Appointment</router-link></li>
          <li><router-link to="/about">About Us</router-link></li>
        </ul>
        <ProfileDropdown />
      </nav>
    </header>

    <!-- Loading and Error States -->
    <div v-if="loading" class="loading-message">Loading your bookings...</div>
    <div v-if="error" class="error-message">{{ error }}</div>

    <!-- Receipts Section -->
    <div v-if="!loading && !error" class="receipt-container" v-for="booking in bookings" :key="booking.id">
      <div class="receipt-header">Booking Receipt</div>
      <div class="receipt-content">
        <p><span>Full Name:</span> {{ booking.customer_name }}</p>
        <p><span>Email:</span> {{ booking.customer_email }}</p>
        <p><span>Phone:</span> {{ booking.customer_phone }}</p>
        <p><span>Category:</span> {{ booking.category }}</p>
        <p><span>Start Date:</span> {{ formatDate(booking.start_date) }}</p>
        <p><span>End Date:</span> {{ formatDate(booking.end_date) }}</p>
        <p>
          <span>Status:</span> 
          <span :class="['status-badge', booking.status.toLowerCase()]">
            {{ booking.status }}
          </span>
        </p>
      </div>
      <div class="receipt-footer">
        <button @click="printReceipt(booking)">Print Receipt</button>
        <p>Thank you for your booking!</p>
        <p>Any concern just email the hotel</p>
      </div>
    </div>

    <!-- No Bookings Message -->
    <div v-if="!loading && !error && bookings.length === 0" class="no-bookings">
      You haven't made any bookings yet.
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ProfileDropdown from '@/components/ProfileDropdown.vue';

export default {
  name: 'Receipt',
  components: {
    ProfileDropdown
  },
  setup() {
    const bookings = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const fetchBookings = async () => {
      try {
        // Get user data from localStorage
        const userData = JSON.parse(localStorage.getItem('user_data'));
        
        if (!userData || !userData.id) {
          error.value = 'User data not found. Please log in again.';
          return;
        }

        const response = await axios.get(`/bookings/user/${userData.id}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (response.data.status === 'success') {
          // Sort bookings by date (most recent first)
          bookings.value = response.data.bookings.sort((a, b) => {
            return new Date(b.created_at) - new Date(a.created_at);
          });
        } else {
          error.value = 'Failed to load bookings. Please try again.';
        }
      } catch (err) {
        console.error('Error fetching bookings:', err);
        error.value = err.response?.data?.message || 'Failed to load your bookings. Please try again later.';
      } finally {
        loading.value = false;
      }
    };

    // Add a refresh method that can be called to reload bookings
    const refreshBookings = () => {
      loading.value = true;
      error.value = null;
      fetchBookings();
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const printReceipt = (booking) => {
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
          <head>
            <title>Booking Receipt #${booking.id}</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; }
              .receipt { max-width: 800px; margin: 0 auto; }
              .header { text-align: center; margin-bottom: 30px; }
              .content { margin-bottom: 30px; }
              .footer { text-align: center; margin-top: 50px; }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="header">
                <h1>Event Vista</h1>
                <h2>Booking Receipt #${booking.id}</h2>
              </div>
              <div class="content">
                <p><strong>Full Name:</strong> ${booking.customer_name}</p>
                <p><strong>Email:</strong> ${booking.customer_email}</p>
                <p><strong>Phone:</strong> ${booking.customer_phone}</p>
                <p><strong>Category:</strong> ${booking.category}</p>
                <p><strong>Start Date:</strong> ${formatDate(booking.start_date)}</p>
                <p><strong>End Date:</strong> ${formatDate(booking.end_date)}</p>
                <p><strong>Status:</strong> ${booking.status}</p>
              </div>
              <div class="footer">
                <p>Any COncern just Email the Hotel</p>
                <p>Thank you for choosing Event Vista!</p>
              </div>
            </div>
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
    };

    onMounted(() => {
      fetchBookings();
    });

    return { 
      bookings, 
      loading, 
      error, 
      printReceipt,
      formatDate,
      refreshBookings
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
.receipt-container {
width: 650px;
background-color: #fff;
padding: 40px;
margin: 40px auto;
border-radius: 12px;
box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
font-family: 'Helvetica Neue', Arial, sans-serif;
text-align: left;
color: #444;
}

.receipt-header {
  border-bottom: 2px solid #f4f4f4;
  padding-bottom: 20px;
  margin-bottom: 30px;
  text-align: center;
}

.receipt-header h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 28px;
  font-weight: 600;
  letter-spacing: 1px;
}

.receipt-content {
  margin-bottom: 30px;
}

.receipt-item {
  margin-bottom: 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.receipt-item .label {
  font-weight: bold;
  color: #7f8c8d;
  width: 200px;
  font-size: 16px;
}

.receipt-item .value {
  color: #34495e;
  font-size: 16px;
  text-align: right;
}

.receipt-footer {
  text-align: center;
  margin-top: 20px;
}

.print-button {
  background-color: #3498db;
  color: white;
  padding: 12px 30px;
  border: none;
  font-size: 18px;
  cursor: pointer;
  border-radius: 6px;
  transition: background-color 0.3s ease, transform 0.2s ease;
  text-transform: uppercase;
}

.print-button:hover {
  background-color: #2980b9;
  transform: scale(1.05);
}

.receipt-footer p {
  margin-top: 20px;
  color: #7f8c8d;
  font-size: 14px;
  font-style: italic;
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

/* Add these new styles */
.loading-message, .error-message, .no-bookings {
  text-align: center;
  padding: 20px;
  margin: 20px auto;
  max-width: 600px;
}

.error-message {
  color: #dc3545;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}

.loading-message {
  color: #004085;
  background-color: #cce5ff;
  border: 1px solid #b8daff;
  border-radius: 4px;
}

.no-bookings {
  color: #383d41;
  background-color: #e2e3e5;
  border: 1px solid #d6d8db;
  border-radius: 4px;
}

.status {
  text-transform: capitalize;
  font-weight: bold;
}

.status.pending {
  color: #ffc107;
}

.status.approved {
  color: #28a745;
}

.status.rejected {
  color: #dc3545;
}

/* Add these new styles for status badges */
.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  text-transform: capitalize;
}

.pending {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

.approved {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.rejected {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

/* Update the print receipt button style to match the theme */
.receipt-footer button {
  background-color: #e8a6e6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.receipt-footer button:hover {
  background-color: #d691d4;
}

/* Add some spacing between receipts if there are multiple */
.receipt-container {
  margin-bottom: 30px;
}

/* Style the receipt content for better readability */
.receipt-content p {
  display: flex;
  justify-content: space-between;
  margin: 10px 0;
  padding: 8px 0;
  border-bottom: 1px solid #eee;
}

.receipt-content p span:first-child {
  font-weight: 600;
  color: #666;
}
</style>
  