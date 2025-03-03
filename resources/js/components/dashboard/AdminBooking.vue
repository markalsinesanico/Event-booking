<template>
  <div class="dashboard">
    <AdminSidebar />
    <div class="main-content">
      <header>
        <h1>BOOKINGS</h1>
        <AdminProfileDropdown />
      </header>

      <div class="bookings-list">
        <h2>All Bookings</h2>
        
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          Loading bookings...
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          {{ error }}
          <button @click="fetchBookings" class="retry-btn">Retry</button>
        </div>

        <!-- Bookings Table -->
        <table v-else>
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer Name</th>
              <th>Venue</th>
              <th>Category</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in bookings" :key="booking.id">
              <td>#{{ booking.id }}</td>
              <td>{{ booking.customer_name }}</td>
              <td>{{ booking.venue?.name || 'N/A' }}</td>
              <td>{{ booking.category }}</td>
              <td>{{ formatDate(booking.start_date) }}</td>
              <td>{{ formatDate(booking.end_date) }}</td>
              <td>
                <span :class="['status-badge', booking.status]">
                  {{ booking.status }}
                </span>
              </td>
              <td class="actions">
                <button 
                  v-if="booking.status === 'pending'"
                  @click="updateStatus(booking.id, 'approved')" 
                  class="approve-btn"
                >
                  Approve
                </button>
                <button 
                  v-if="booking.status === 'pending'"
                  @click="updateStatus(booking.id, 'rejected')" 
                  class="reject-btn"
                >
                  Reject
                </button>
                <button 
                  @click="deleteBooking(booking.id)" 
                  class="delete-btn"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- No Bookings Message -->
        <div v-if="!loading && !error && bookings.length === 0" class="no-bookings">
          No bookings found.
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
  name: 'AdminBooking',
  components: {
    AdminSidebar,
    AdminProfileDropdown
  },
  setup() {
    const bookings = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const fetchBookings = async () => {
      try {
        loading.value = true;
        error.value = null;
        
        const token = localStorage.getItem('token');
        if (!token) {
          error.value = 'Authentication token not found. Please log in again.';
          return;
        }

        const response = await axios.get('/api/admin/booking', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          bookings.value = response.data.bookings;
        } else {
          throw new Error(response.data.message || 'Failed to fetch bookings');
        }
      } catch (err) {
        console.error('Error details:', err.response || err);
        error.value = err.response?.data?.message || 'Failed to load bookings. Please try again.';
      } finally {
        loading.value = false;
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const updateStatus = async (bookingId, newStatus) => {
      try {
        const response = await axios.put(`/api/admin/booking/${bookingId}/status`, 
          { status: newStatus },
          {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`,
              'Accept': 'application/json'
            }
          }
        );

        if (response.data.status === 'success') {
          await fetchBookings(); // Refresh the bookings list
        } else {
          throw new Error(response.data.message || 'Failed to update status');
        }
      } catch (err) {
        console.error('Error updating booking status:', err);
        alert(err.response?.data?.message || 'Failed to update booking status');
      }
    };

    const deleteBooking = async (bookingId) => {
      if (!confirm('Are you sure you want to delete this booking?')) return;

      try {
        const response = await axios.delete(`/api/admin/booking/${bookingId}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          await fetchBookings(); // Refresh the bookings list
        } else {
          throw new Error(response.data.message || 'Failed to delete booking');
        }
      } catch (err) {
        console.error('Error deleting booking:', err);
        alert(err.response?.data?.message || 'Failed to delete booking');
      }
    };

    onMounted(() => {
      console.log('Admin role:', localStorage.getItem('userRole'));
      console.log('Token:', localStorage.getItem('token'));
      fetchBookings();
    });

    return {
      bookings,
      loading,
      error,
      formatDate,
      updateStatus,
      deleteBooking,
      fetchBookings
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

.user-profile {
  position: relative;
  cursor: pointer;
}

.user-profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.dropdown {
  position: absolute;
  right: 0;
  top: 50px;
  background-color: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  border-radius: 5px;
  z-index: 1000;
}

.dropdown a {
  display: block;
  padding: 10px 20px;
  text-decoration: none;
  color: #333;
}

.bookings-list {
  margin-top: 100px;
  padding: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: white;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5b7f0;
  color: #333;
  font-weight: bold;
  font-size: 14px;
}

td {
  font-size: 14px;
  color: #666;
}

.edit-btn, .delete-btn {
  padding: 6px 12px;
  margin: 0 4px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.edit-btn {
  background-color: #6b4a86;
  color: white;
}

.edit-btn:hover {
  background-color: #5a3d71;
}

.delete-btn {
  background-color: #ff4444;
  color: white;
}

.delete-btn:hover {
  background-color: #ff3333;
}

h2 {
  color: #333;
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 20px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.pending {
  background-color: #fff3cd;
  color: #856404;
}

.approved {
  background-color: #d4edda;
  color: #155724;
}

.rejected {
  background-color: #f8d7da;
  color: #721c24;
}

.loading-state, .error-state, .no-bookings {
  text-align: center;
  padding: 20px;
  margin: 20px 0;
  border-radius: 8px;
}

.loading-state {
  background-color: #e2e3e5;
  color: #383d41;
}

.error-state {
  background-color: #f8d7da;
  color: #721c24;
}

.retry-btn {
  margin-top: 10px;
  padding: 6px 12px;
  background-color: #6b4a86;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.actions {
  display: flex;
  gap: 5px;
}

.approve-btn {
  background-color: #28a745;
  color: white;
}

.reject-btn {
  background-color: #dc3545;
  color: white;
}

.approve-btn, .reject-btn {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.approve-btn:hover {
  background-color: #218838;
}

.reject-btn:hover {
  background-color: #c82333;
}
</style>
  