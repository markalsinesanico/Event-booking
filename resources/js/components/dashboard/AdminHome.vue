<template>
    <div class="dashboard">
      <AdminSidebar />
      <div class="main-content">
        <header>
          <h1>DASHBOARD</h1>
          <AdminProfileDropdown />
        </header>
  
        <div class="summary">
          <div class="card" v-for="(item, index) in summaryCards" :key="index">
            <span class="icon">{{ item.icon }}</span>
            <p>{{ item.title }}</p>
            <h3>{{ item.value }}</h3>
          </div>
        </div>
  
        <!-- Event List Section -->
        <div class="events-list">
          <h2>Upcoming Events</h2>
          <div v-if="loading" class="loading-state">
            Loading events...
          </div>
  
          <div v-else-if="error" class="error-message">
            {{ error }}
          </div>
  
          <div v-else-if="events.length === 0" class="no-events">
            No upcoming events found.
          </div>
  
          <table v-else>
            <thead>
              <tr>
                <th>Venue</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone #</th>
                <th>Category</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in events" :key="event.id">
                <td>{{ event.venue }}</td>
                <td>{{ event.fullName }}</td>
                <td>{{ event.email }}</td>
                <td>{{ event.phone }}</td>
                <td>{{ event.category }}</td>
                <td>{{ formatDate(event.startDate) }}</td>
                <td>{{ formatDate(event.endDate) }}</td>
                <td>{{ event.status }}</td>
                <td>
                  <button 
                    v-if="event.status === 'pending'"
                    class="approve" 
                    @click="approveEvent(event.id)"
                  >
                    Approve
                  </button>
                  <button class="delete" @click="deleteEvent(event.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, computed } from 'vue';
  import AdminSidebar from './AdminSidebar.vue';
  import AdminProfileDropdown from './AdminProfileDropdown.vue';
  import axios from 'axios';
  
  export default {
    name: 'AdminHome',
    components: {
      AdminSidebar,
      AdminProfileDropdown
    },
    setup() {
      const events = ref([]);
      const loading = ref(false);
      const error = ref(null);
      const summaryData = ref({
        totalBookings: 0,
        totalUsers: 0
      });
  
      const summaryCards = computed(() => [
        { icon: '📊', title: 'Total Bookings', value: summaryData.value.totalBookings },
        { icon: '👥', title: 'Total Customers', value: summaryData.value.totalUsers }
      ]);
  
      const fetchDashboardData = async () => {
        try {
          const response = await axios.get('/api/admin/dashboard', {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          if (response.data.status === 'success') {
            summaryData.value = response.data.summary;
            console.log('Dashboard data:', response.data);
          }
        } catch (err) {
          console.error('Error fetching dashboard data:', err);
        }
      };
  
      const fetchEvents = async () => {
        loading.value = true;
        error.value = null;
        try {
          const response = await axios.get('/api/admin/events', {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          if (response.data.status === 'success') {
            events.value = response.data.events;
          }
        } catch (err) {
          error.value = 'Failed to load events. Please try again.';
          console.error('Error fetching events:', err);
        } finally {
          loading.value = false;
        }
      };
  
      const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      };
  
      const approveEvent = async (eventId) => {
        try {
          const response = await axios.put(`/api/admin/events/${eventId}/approve`, {}, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          if (response.data.status === 'success') {
            await fetchEvents();
            await fetchDashboardData();
          }
        } catch (error) {
          console.error('Error approving event:', error);
          alert('Failed to approve event');
        }
      };
  
      const deleteEvent = async (eventId) => {
        if (!confirm('Are you sure you want to delete this event?')) return;
  
        try {
          const response = await axios.delete(`/api/admin/events/${eventId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          if (response.data.status === 'success') {
            await fetchEvents();
            await fetchDashboardData();
          }
        } catch (error) {
          console.error('Error deleting event:', error);
          alert('Failed to delete event');
        }
      };
  
      onMounted(() => {
        fetchEvents();
        fetchDashboardData();
      });
  
      return {
        events,
        loading,
        error,
        summaryCards,
        formatDate,
        approveEvent,
        deleteEvent
      };
    }
  };
  </script>
  
  <style scoped>
  /* Add these new styles */
  .loading-state {
    text-align: center;
    padding: 20px;
    color: #666;
  }
  
  .error-message {
    text-align: center;
    padding: 20px;
    color: #ff4444;
    background-color: #ffe6e6;
    border-radius: 4px;
    margin: 20px 0;
  }
  
  .no-events {
    text-align: center;
    padding: 40px;
    color: #666;
    background-color: #f9f9f9;
    border-radius: 8px;
    margin-top: 20px;
  }
  
  /* Your existing styles remain the same */
  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f9f9f9;
  display: flex;
}

.sidebar {
  width: 250px;
  background-color: #b398d3;
  color: white;
  position: fixed;
  height: 100%;
  padding-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo {
  padding: 10px;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 15px;
  margin-bottom: 20px;
}

.logo img {
  width: 140px;
  border-radius: 10px;
}

.sidebar ul {
  list-style: none;
  width: 100%;
}

.sidebar ul li {
  margin: 10px 0;
  width: 100%;
  text-align: center;
}

.sidebar ul li a {
  color: white;
  text-decoration: none;
  font-size: 18px;
  padding: 10px 20px;
  display: block;
  border-radius: 5px;
  background-color: #6b4a86;
  transition: background-color 0.3s ease;
}

.sidebar ul li a:hover {
  background-color: #c999c9;
}

.main-content {
  margin-left: 250px;
  padding: 20px;
  width: calc(100% - 250px);
  background-color: #ffffff;
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
  display: none;
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

.dropdown a:hover {
  background-color: #f1f1f1;
}

.summary {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 100px;
}

.card {
  width: 250px;
  padding: 20px;
  text-align: center;
  background-color: #f5b7f0;
  border-radius: 10px;
  color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.icon {
  font-size: 40px;
  margin-bottom: 10px;
  display: block;
}

.card p {
  font-size: 16px;
  margin-bottom: 8px;
  font-weight: 500;
}

.card h3 {
  font-size: 24px;
  margin-top: 5px;
  font-weight: bold;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

thead {
  background-color: #f3f3f3;
  color: #4c4c4c;
}

th, td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

tr:hover {
  background-color: #f1f1f1;
}

th {
  text-transform: uppercase;
}
  .events-list {
    margin-top: 20px;
  }
  
  .events-list h2 {
    font-size: 24px;
    margin-bottom: 15px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  thead {
    background-color: #f3f3f3;
    color: #4c4c4c;
  }
  
  th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  tr:hover {
    background-color: #f1f1f1;
  }
  
  th {
    text-transform: uppercase;
  }
  
  button {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .approve {
    background-color: #3498db;
    color: white;
  }
  
  .approve:hover {
    background-color: #2980b9;
  }
  
  .delete {
    background-color: #e74c3c;
    color: white;
  }
  
  .delete:hover {
    background-color: #c0392b;
  }
  </style>
  