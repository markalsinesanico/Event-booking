<template>
  <header class="header">
    <div class="logo">
      <img src="/img/logo.png" alt="Event Vista Logo" />
    </div>
  </header>
  <div class="login-container">
    <div class="login-left">
      <div class="form-container">
        <h2>Login</h2>
        <form @submit.prevent="handleLogin">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            placeholder="username@gmail.com"
            required
          />

          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            v-model="password"
            placeholder="Password"
            required
          />

          <button type="submit" class="login-btn">Login</button>

          <p class="continue-text">or continue with</p>

          <div class="social-login">
            <button class="google-btn">Google</button>
            <button class="github-btn">GitHub</button>
            <button class="facebook-btn">Facebook</button>
          </div>
          <p class="register-text">
            Don't have an account yet?
            <router-link to="/register">Register for free</router-link>
          </p>
        </form>
      </div>
    </div>
    <div class="login-right">
      
      <h3>Booking</h3>
      <h3>Event Venue</h3>
      <img src="/img/loginfirst.png" alt="People celebrating" class="illustration" />
    </div>
  </div>
  <div class="footer-links">
    <router-link to="/privacy">Privacy Policy</router-link>
    <router-link to="/terms">Terms & Conditions</router-link>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post("/api/login", {
          email: this.email,
          password: this.password,
        }, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          // Check if user has correct role
          if (response.data.user.role !== 'user') {
            alert('Access denied. This login is for regular users only.');
            return;
          }
          
          localStorage.setItem('user_data', JSON.stringify(response.data.user));
          localStorage.setItem('token', response.data.token);
          
          // Verify token was set
          const storedToken = localStorage.getItem('token');
          console.log('Stored token:', storedToken);
          
          this.$router.push("/home");
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message 
          || "Login failed. Please check your credentials.";
        alert(errorMessage);
        console.error('Login error:', error);
      }
    },
  },
};
</script>
  <style scoped>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box; 
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #FFD1D1;
    display: flex;
    flex-direction: column; 
}

.header {
    display: flex;
    align-items: center;
    padding: 0;
    background-color: #FFD1D1;
}

.logo {
    margin-bottom: 15px; 
}

.logo img {
    width: 150px; 
}

.login-container {
    display: flex;
    flex: 2; 
    width: 100%;
    max-width: 2000px;
    margin: 0 auto; 
    background-color: #fff;
}

.login-left {
    flex: 1;
    padding: 10px; 
    display: flex;
    flex-direction: column;
    align-items: center; 
    background-color: #FFD1D1;
    text-align: left;
}

.form-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    width: 100%;
    max-width: 400px; 
}

.login-left h2 {
    font-family: 'Georgia', serif;
    font-size: 35px;
    color: #000;
    margin-bottom: 30px;
    text-align: center; 
}

form {
    display: flex;
    flex-direction: column;
    width: 100%; 
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

input {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
    width: 100%;
}

.login-btn {
    padding: 15px;
    background-color: #233f78;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 60%;
    align-self: center; 
}

.login-btn:hover {
    background-color: #1a2d5b;
}

.continue-text {
    margin: 20px 0;
    font-size: 14px;
    color: #555;
    text-align: center;
}

.social-login {
    display: flex;
    justify-content: space-around;
    margin-bottom: 30px;
    width: 100%; 
}

.social-login button {
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    flex: 1; 
    margin: 0 5px; 
}

.google-btn {
    background-color: #4285F4;
    color: white;
}

.github-btn {
    background-color: #333;
    color: white;
}

.facebook-btn {
    background-color: #3b5998;
    color: white;
}

.register-text {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}

.register-text a {
    color: #233f78;
    text-decoration: none;
}

.register-text a:hover {
    text-decoration: underline;
}

.login-right {
    flex: 1;
    background-color: #FFD1D1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 30px;
    text-align: left;
}

.login-right h3 {
    font-family: 'Georgia', serif;
    font-size: 50px;
    color: #000;
    margin-top: -100px;
    font-weight: normal;
    line-height: 1;
}

.illustration {
    top: -50px; 
    width: 100%;
    max-width: 1300px;
    height: auto;
    margin-top: 50px;
}

.footer-links {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    background-color: #FFD1D1;
}

.footer-links a {
    color: #333;
    text-decoration: none;
}

.footer-links a:hover {
    text-decoration: underline;
}

  </style>
  