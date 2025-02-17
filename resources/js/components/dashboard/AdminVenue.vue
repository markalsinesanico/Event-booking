<template>
  <div class="dashboard">
    <AdminSidebar />
    <div class="main-content">
      <header>
        <h1>VENUES</h1>
        <AdminProfileDropdown />
      </header>

      <div class="venues-list">
        <h2>All Venues</h2>
        <div class="add-venue">
          <button 
            v-if="userRole === 'admin' || userRole === 'administrator'" 
            class="add-btn" 
            @click="openModal"
          >
            Add New Venue
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          Loading venues...
        </div>

        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>

        <div v-else-if="venues.length === 0" class="no-venues">
          No venues found.
        </div>

        <table v-else>
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Created By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="venue in venues" :key="venue.id">
              <td>{{ venue.id }}</td>
              <td>
                <img 
                  v-if="venue.image" 
                  :src="venue.image" 
                  alt="Venue" 
                  class="venue-image" 
                  @error="handleImageError"
                  @click="openImageModal(venue.image)"
                />
                <span v-else>No image</span>
              </td>
              <td>{{ venue.name }}</td>
              <td>{{ venue.description }}</td>
              <td>
                <span :class="getStatusClass(venue.status)">
                  {{ venue.status }}
                </span>
              </td>
              <td>{{ venue.creator_name }}</td>
              <td>
                <button 
                  v-if="canManageVenue(venue)" 
                  class="edit-btn" 
                  @click="editVenue(venue)"
                >
                  Edit
                </button>
                <button 
                  v-if="canManageVenue(venue)" 
                  class="delete-btn" 
                  @click="deleteVenue(venue.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit Venue Modal -->
      <div v-if="showModal" class="modal">
        <div class="modal-content">
          <h2>{{ isEditing ? 'Edit Venue' : 'Add New Venue' }}</h2>
          <form @submit.prevent="saveVenue">
            <div class="form-group">
              <label>Name</label>
              <input v-model="venueForm.name" type="text" required />
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea v-model="venueForm.description" required></textarea>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select v-model="venueForm.status" required>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Under Maintenance</option>
              </select>
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" @change="handleImageUpload" accept="image/*" />
              <img 
                v-if="imagePreview" 
                :src="imagePreview" 
                class="image-preview" 
                alt="Preview"
              />
            </div>
            <div class="modal-buttons">
              <button type="submit" class="save-btn">Save</button>
              <button type="button" class="cancel-btn" @click="closeModal">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Add this right after your existing modal -->
      <div v-if="showImageModal" class="image-modal" @click="showImageModal = false">
        <div class="image-modal-content">
          <img :src="selectedImage" alt="Venue" class="full-size-image" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import AdminSidebar from './AdminSidebar.vue';
import AdminProfileDropdown from './AdminProfileDropdown.vue';
import axios from 'axios';

export default {
  name: 'AdminVenue',
  components: {
    AdminSidebar,
    AdminProfileDropdown
  },
  setup() {
    const showModal = ref(false);
    const isEditing = ref(false);
    const imagePreview = ref(null);
    const venues = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const showImageModal = ref(false);
    const selectedImage = ref(null);
    
    const venueForm = ref({
      id: null,
      name: '',
      description: '',
      status: 'available',
      image: null
    });

    const currentUserId = ref(null);
    const userRole = ref(localStorage.getItem('userRole'));

    const fetchVenues = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const response = await axios.get('/api/venues', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        if (response.data.status === 'success') {
          venues.value = response.data.venues;
        }
      } catch (err) {
        error.value = 'Failed to load venues. Please try again.';
        console.error('Error fetching venues:', err);
      } finally {
        loading.value = false;
      }
    };

    const getStatusClass = (status) => {
      return {
        'status-badge': true,
        'status-available': status === 'available',
        'status-occupied': status === 'occupied',
        'status-maintenance': status === 'maintenance'
      };
    };

    const openModal = () => {
      showModal.value = true;
      isEditing.value = false;
      resetForm();
    };

    const closeModal = () => {
      showModal.value = false;
      resetForm();
    };

    const resetForm = () => {
      venueForm.value = {
        id: null,
        name: '',
        description: '',
        status: 'available',
        image: null
      };
      imagePreview.value = null;
    };

    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        // Validate file type
        if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
          alert('Please upload a valid image file (JPEG, PNG, or JPG)');
          event.target.value = ''; // Clear the file input
          return;
        }
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          alert('Image size should be less than 2MB');
          event.target.value = ''; // Clear the file input
          return;
        }

        venueForm.value.image = file;
        imagePreview.value = URL.createObjectURL(file);
        
        console.log('Image selected:', {
          name: file.name,
          type: file.type,
          size: file.size
        });
      }
    };

    const handleImageError = (event) => {
      event.target.src = '/img/placeholder.jpg'; // Replace with your placeholder image
    };

    const editVenue = (venue) => {
      isEditing.value = true;
      venueForm.value = { 
        ...venue,
        id: venue.id,
        image: null
      };
      imagePreview.value = venue.image;
      showModal.value = true;
    };

    const saveVenue = async () => {
      try {
        const formData = new FormData();
        formData.append('name', venueForm.value.name);
        formData.append('description', venueForm.value.description);
        formData.append('status', venueForm.value.status);
        
        if (venueForm.value.image) {
          console.log('Appending image:', venueForm.value.image);
          formData.append('image', venueForm.value.image);
        }

        const url = isEditing.value ? `/api/venues/${venueForm.value.id}` : '/api/venues';
        const method = 'POST';

        if (isEditing.value) {
          formData.append('_method', 'PUT');
        }

        // Log the FormData contents
        for (let pair of formData.entries()) {
          console.log(pair[0] + ': ' + pair[1]);
        }

        const response = await axios({
          method,
          url,
          data: formData,
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        console.log('Save response:', response.data);

        if (response.data.status === 'success') {
          await fetchVenues(); // Refresh the venues list
          closeModal();
        }
      } catch (error) {
        console.error('Error saving venue:', error);
        console.error('Error response:', error.response?.data);
        alert('Failed to save venue: ' + (error.response?.data?.message || error.message));
      }
    };

    const deleteVenue = async (id) => {
      if (confirm('Are you sure you want to delete this venue?')) {
        try {
          const response = await axios.delete(`/api/venues/${id}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          if (response.data.status === 'success') {
            await fetchVenues();
          }
        } catch (error) {
          console.error('Error deleting venue:', error);
          alert('Failed to delete venue: ' + error.response?.data?.message || error.message);
        }
      }
    };

    const openImageModal = (imageUrl) => {
      selectedImage.value = imageUrl;
      showImageModal.value = true;
    };

    const canManageVenue = (venue) => {
      // Convert IDs to numbers for comparison
      const userId = Number(currentUserId.value);
      const venueCreatorId = Number(venue.created_by);
      
      console.log('Checking permissions:', {
        userRole: userRole.value,
        currentUserId: userId,
        venueCreatedBy: venueCreatorId,
        isAdmin: userRole.value === 'admin',
        isAdministrator: userRole.value === 'administrator',
        isCreator: userId === venueCreatorId
      });
      
      // Admin can manage all venues
      if (userRole.value === 'admin') return true;
      
      // Administrator can only manage their own venues
      return userRole.value === 'administrator' && userId === venueCreatorId;
    };

    const getCurrentUser = async () => {
      try {
        const response = await axios.get('/api/user/profile', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        console.log('User profile response:', response.data);
        
        if (response.data.status === 'success') {
          currentUserId.value = response.data.user.id;
          userRole.value = response.data.user.role;
          
          console.log('Current user set:', {
            id: currentUserId.value,
            role: userRole.value,
            token: localStorage.getItem('token')
          });
        }
      } catch (error) {
        console.error('Error fetching user profile:', error);
      }
    };

    const refreshVenueStatus = async (venueId) => {
      try {
        await fetchVenues(); // This will refresh the venues list
      } catch (error) {
        console.error('Error refreshing venue status:', error);
      }
    };

    onMounted(async () => {
      await getCurrentUser();
      await fetchVenues();
    });

    watch(() => venues.value, (newVenues, oldVenues) => {
      if (oldVenues && newVenues) {
        // Check for any venues that changed status
        newVenues.forEach(newVenue => {
          const oldVenue = oldVenues.find(v => v.id === newVenue.id);
          if (oldVenue && oldVenue.status !== newVenue.status) {
            console.log(`Venue ${newVenue.id} status changed from ${oldVenue.status} to ${newVenue.status}`);
          }
        });
      }
    }, { deep: true });

    return {
      showModal,
      isEditing,
      venues,
      venueForm,
      imagePreview,
      loading,
      error,
      getStatusClass,
      openModal,
      closeModal,
      handleImageUpload,
      editVenue,
      saveVenue,
      deleteVenue,
      handleImageError,
      showImageModal,
      selectedImage,
      openImageModal,
      canManageVenue,
      userRole,
      currentUserId
    };
  }
};
</script>

<style scoped>
.dashboard {
  display: flex;
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

.venues-list {
  margin-top: 100px;
  padding: 20px;
}

.add-venue {
  margin-bottom: 20px;
}

.add-btn {
  background-color: #6b4a86;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: white;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5b7f0;
  color: white;
}

.venue-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s;
  cursor: pointer;
}

.venue-image:hover {
  transform: scale(1.1);
}

.edit-btn, .delete-btn {
  padding: 6px 12px;
  margin: 0 4px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.edit-btn {
  background-color: #6b4a86;
  color: white;
}

.delete-btn {
  background-color: #ff4444;
  color: white;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

.modal-content {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input,
.form-group textarea {
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

.save-btn, .cancel-btn {
  padding: 8px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.save-btn {
  background-color: #6b4a86;
  color: white;
}

.cancel-btn {
  background-color: #ddd;
  color: #333;
}

h2 {
  color: #333;
  margin-bottom: 20px;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  text-transform: capitalize;
}

.status-available {
  background-color: #4CAF50;
  color: white;
}

.status-occupied {
  background-color: #FFC107;
  color: black;
}

.status-maintenance {
  background-color: #F44336;
  color: white;
}

.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
}

.image-preview {
  margin-top: 10px;
  max-width: 200px;
  max-height: 120px;
  border-radius: 4px;
}

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

.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 3000;
  cursor: pointer;
}

.image-modal-content {
  max-width: 90%;
  max-height: 90vh;
}

.full-size-image {
  max-width: 100%;
  max-height: 90vh;
  object-fit: contain;
  border-radius: 8px;
}

.no-venues {
  text-align: center;
  padding: 40px;
  color: #666;
  background-color: #f9f9f9;
  border-radius: 8px;
  margin-top: 20px;
}
</style>
  