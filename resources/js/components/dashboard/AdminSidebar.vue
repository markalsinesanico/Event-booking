<template>
  <div class="sidebar">
    <div class="logo">
      <img src="/img/logo.png" alt="Event Vista Logo" />
    </div>
    <ul>
      <li><router-link to="/admin/home" :class="{ active: isActive('home') }">Dashboard</router-link></li>
      <li v-if="isAdmin"><router-link to="/admin/customers" :class="{ active: isActive('customers') }">Customer List</router-link></li>
      <li v-if="isAdmin || isAdministrator"><router-link to="/admin/venues" :class="{ active: isActive('venues') }">Venue</router-link></li>
      <li><router-link to="/admin/bookings" :class="{ active: isActive('bookings') }">Booking</router-link></li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'AdminSidebar',
  computed: {
    userRole() {
      return localStorage.getItem('userRole');
    },
    isAdmin() {
      return this.userRole === 'admin';
    },
    isAdministrator() {
      return this.userRole === 'administrator';
    }
  },
  methods: {
    isActive(route) {
      return this.$route.path.includes(route);
    }
  }
};
</script>

<style scoped>
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

.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: #c999c9;
}
</style> 