<template>
    <div class="main-container">
      <div class="login-container">
        <div class="logo">
          <img src="/img/logo.png" alt="Event Vista Logo" />
        </div>
        <h2>Admin</h2>
        <form @submit.prevent="handleSubmit">
          <label for="email">Email</label>
          <input
            type="email"
            v-model="email"
            placeholder="username@gmail.com"
            required
          />
          <label for="password">Password</label>
          <input
            type="password"
            v-model="password"
            placeholder="Password"
            required
          />
          <div v-if="error" class="error-message">
            {{ error }}
          </div>
          <button type="submit" class="login-btn">Log in</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import { useAuth } from '../../composables/useAuth';
  import axios from 'axios';
  
  export default {
    name: 'AdminLogin',
    data() {
      return {
        email: '',
        password: '',
        error: null
      };
    },
    setup() {
      const { login } = useAuth();
      return { login };
    },
    methods: {
      async handleSubmit() {
        try {
          const credentials = {
            email: this.email,
            password: this.password
          };
          
          const response = await axios.post('/api/login', credentials);
          
          if (response.data.status === 'success') {
            const userRole = response.data.user.role;
            
            if (userRole !== 'admin' && userRole !== 'administrator') {
              this.error = 'Access denied. This login is for administrators only.';
              return;
            }
            
            const success = await this.login(credentials);
            
            if (!success) {
              this.error = 'Invalid credentials';
            }
          }
        } catch (error) {
          console.error('Login error:', error);
          this.error = 'An error occurred during login';
        }
      }
    }
  };
  </script>
  
  <style scoped>
  /* Global Reset */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
  }
  
  /* Ensure the background color fills the entire screen */
  html, body {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgb(241, 232, 221); /* Background color for the entire screen */
  }
  
  #app {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .main-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
  }
  
  .login-container {
  width: 500px;
  padding: 60px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: white; /* Optional: set a white background for the login box */
  border-radius: 10px 10px 5px 5px; /* Adjust the border-radius for top and bottom corners */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

  
  .logo img {
    width: 150px;
    margin-bottom: 20px;
  }
  
  h2 {
    font-family: 'Georgia', serif;
    font-size: 40px;
    color: #333;
    margin-bottom: 30px;
  }
  
  form {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  label {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
    width: 100%;
    text-align: left;
  }
  
  input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
  }
  
  .login-btn {
    padding: 12px;
    width: 100%;
    background-color: #233f78;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .login-btn:hover {
    background-color: #1a2d5b;
  }
  
  .error-message {
    color: #ff4444;
    background-color: #ffe6e6;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    width: 100%;
    text-align: center;
  }
  </style>
  