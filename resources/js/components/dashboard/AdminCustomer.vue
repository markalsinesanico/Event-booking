<template>
  <div class="dashboard">
    <AdminSidebar />
    <div class="main-content">
      <header>
        <h1>USERS</h1>
        <AdminProfileDropdown />
      </header>

      <div class="customers-list">
        <h2>All Users</h2>
        
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          Loading users...
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          {{ error }}
          <button @click="fetchCustomers" class="retry-btn">Retry</button>
        </div>

        <!-- Data Table -->
        <table v-else>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers" :key="customer.id">
              <td>{{ customer.id }}</td>
              <td>{{ customer.firstname }} {{ customer.lastname }}</td>
              <td>{{ customer.email }}</td>
              <td>{{ customer.phone || 'N/A' }}</td>
              <td>
                <button class="edit-btn" @click="editCustomer(customer)">Edit</button>
                <button class="delete-btn" @click="confirmDelete(customer)">Delete</button>
              </td>
            </tr>
            <tr v-if="customers.length === 0">
              <td colspan="5" class="no-data">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add this modal markup after the table -->
      <div v-if="showEditModal" class="modal-overlay">
        <div class="modal">
          <h2>Edit User</h2>
          <form @submit.prevent="updateCustomer">
            <div class="form-group">
              <label>First Name:</label>
              <input type="text" v-model="editingCustomer.firstname" required>
            </div>
            <div class="form-group">
              <label>Last Name:</label>
              <input type="text" v-model="editingCustomer.lastname" required>
            </div>
            <div class="form-group">
              <label>Email:</label>
              <input type="email" v-model="editingCustomer.email" required>
            </div>
            <div class="form-group">
              <label>Phone:</label>
              <input type="tel" v-model="editingCustomer.phone" required>
            </div>
            <div class="modal-buttons">
              <button type="submit" class="save-btn">Save Changes</button>
              <button type="button" class="cancel-btn" @click="closeEditModal">Cancel</button>
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
  name: 'AdminCustomer',
  components: {
    AdminSidebar,
    AdminProfileDropdown
  },
  setup() {
    const customers = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const showEditModal = ref(false);
    const editingCustomer = ref({
      id: null,
      firstname: '',
      lastname: '',
      email: '',
      phone: ''
    });

    const fetchCustomers = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const response = await axios.get('/api/admin/customers', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.status === 'success') {
          customers.value = response.data.customers;
        } else {
          throw new Error('Failed to fetch customers');
        }
      } catch (err) {
        error.value = 'Error loading customers. Please try again.';
        console.error('Error:', err);
      } finally {
        loading.value = false;
      }
    };

    const editCustomer = (customer) => {
      editingCustomer.value = { ...customer };
      showEditModal.value = true;
    };

    const closeEditModal = () => {
      showEditModal.value = false;
      editingCustomer.value = {
        id: null,
        firstname: '',
        lastname: '',
        email: '',
        phone: ''
      };
    };

    const updateCustomer = async () => {
      try {
        const response = await axios.put(`/api/admin/customers/${editingCustomer.value.id}`, editingCustomer.value, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.status === 'success') {
          // Update the customer in the local list
          const index = customers.value.findIndex(c => c.id === editingCustomer.value.id);
          if (index !== -1) {
            customers.value[index] = { ...editingCustomer.value };
          }
          closeEditModal();
          alert('User updated successfully');
        }
      } catch (err) {
        alert('Failed to update user. Please try again.');
        console.error('Error:', err);
      }
    };

    const confirmDelete = async (customer) => {
      if (confirm(`Are you sure you want to delete ${customer.firstname} ${customer.lastname}? This action cannot be undone.`)) {
        try {
          const response = await axios.delete(`/api/admin/customers/${customer.id}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });

          if (response.data.status === 'success') {
            customers.value = customers.value.filter(c => c.id !== customer.id);
            alert('User deleted successfully');
          }
        } catch (err) {
          alert('Failed to delete user. Please try again.');
          console.error('Error:', err);
        }
      }
    };

    onMounted(() => {
      fetchCustomers();
    });

    return {
      customers,
      loading,
      error,
      showEditModal,
      editingCustomer,
      fetchCustomers,
      editCustomer,
      closeEditModal,
      updateCustomer,
      confirmDelete
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

.customers-list {
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

.loading-state, .error-state {
  text-align: center;
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  margin-top: 20px;
}

.error-state {
  color: #ff4444;
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

.retry-btn:hover {
  background-color: #5a3d71;
}

.no-data {
  text-align: center;
  padding: 20px;
  color: #666;
  font-style: italic;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1100;
}

.modal {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal h2 {
  margin-bottom: 20px;
  color: #333;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #666;
}

.form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.save-btn {
  background-color: #6b4a86;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.save-btn:hover {
  background-color: #5a3d71;
}
</style>
