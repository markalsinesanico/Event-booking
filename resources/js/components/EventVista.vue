<template>
    <div>
      <!-- Header Section -->
      <header>
        <nav class="topnav">
          <div class="logo-container">
            <img src="/img/logo.png" alt="Event Vista Logo" class="logo"/>  
          </div>
          <ul class="nav-links">
            <li><router-link to="/" exact-active-class="active">Home</router-link></li>
            <li><router-link to="/login" exact-active-class="active">Appointment</router-link></li>
            <li><router-link to="/login" exact-active-class="active">About Us</router-link></li>
          </ul>
          <div class="profile">
            <router-link to="/login" class="login-btn">Login</router-link>
        </div>
        </nav>
      </header>
  
      <!-- Main Section -->
      <section class="main-section">
        <div class="content">
          <div class="image"></div>
          <div class="text-content">
            <h1>Event Vista</h1>
            <h1>Booking Event</h1>
            <h1>Management</h1>
            <p>All-in-one platform to manage events,</p>
            <p>catering, table reservations, online reservations,</p>
            <p>event bookings, POS, Inventory management, digital signage,</p>
            <p>food ordering app, and more.</p>
            <router-link to="/login" class="book-btn">Book Now!</router-link>
          </div>
        </div>
      </section>
  
      <!-- Booking Section -->
      <section class="booking-section">
        <div class="event-booking">
          <div class="image1"></div>
          <div>
            <h2>Event Booking</h2>
            <p>Event Vista comes up with the best event booking</p>
            <p>software designed to manage your venue spaces</p>
            <p>with ease. Whatever may be the events, from</p>
            <p>meetings and conferences to social events like</p>
            <p>weddings and parties, Pxier event booking software</p>
            <p>is there to manage events with timeline, food,</p>
            <p>beverages, rental, and services. The software is</p>
            <p>ideal for businesses including banquet halls,</p>
            <p>meeting rooms, convention centers, clubs, catering</p>
            <p>services, parks, resorts, and organizations such as</p>
            <p>community centers, religious places, schools &</p>
            <p>universities.</p>
          </div>
        </div>
      </section>
  
      <!-- Footer Section -->
      <footer>
        <div class="footer-left">
          <img :src="logo" alt="Event Vista Logo" class="logo"/>  
        </div>
        <div class="social-media">
          <a href="#"><i class="fas fa-envelope"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <div class="contact-info">
          <a href="#">CONTACT</a>
          <p>Brgy. Washington</p>
          <p>+629123456789</p>
          <a href="#">alskwe@gmail.com</a>
        </div>
        <div class="footer-right">
          <a href="#">Privacy</a><br />
          <a href="#">Terms & Conditions</a><br />
          <p>©2024 AlsKwen. All rights reserved.</p>
        </div>
      </footer>
    </div>
  </template>
  
  <script>
  export default {
    name: "EventVista",
    data() {
      return {
        logo: "/img/logo.png"
      };
    },
    methods: {
      navigateToLogin() {
        this.$router.push("/login");
      },
    },
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
  .login-btn {
    background-color: #369bf5;
    border: none;
    padding: 10px 30px;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    text-decoration: none;
  }
  .login-btn:hover {
    background-color: #1f7dd6;
    transform: scale(1.05);
  }
  
  /* Main Section Styles */
  .main-section {
    background-color: #ffd1d1;
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .content {
    max-width: 1200px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
  }
  .image {
    flex-shrink: 0;
    width: 563px;
    height: 484px;
    background: url('/img/scale.png') no-repeat center center;
    background-size: cover;
    border-radius: 10px;
  }
  .text-content {
    max-width: 600px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }
  .text-content h1 {
    font-size: 48px;
    font-weight: bold;
    color: #333333;
    margin-bottom: 20px;
  }
  .text-content p {
    font-size: 18px;
    line-height: 1;
    color: #333333;
    margin-bottom: 30px;
  }
  .book-btn {
    padding: 15px 30px;
    background-color: #f596cd;
    color: white;
    border: none;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s, transform 0.2s;
  }
  .book-btn:hover {
    background-color: #e583b6;
    transform: scale(1.05);
  }
  
  /* Booking Section Styles */
  .booking-section {
    background-color: #ffd1d1;
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    text-align: left;
  }
  .event-booking {
    max-width: 1200px;
    display: flex;
    gap: 50px;
  }
  .image1 {
    width: 563px;
    height: 484px;
    background: url('/img/login.png') no-repeat;
    background-size: cover;
  }
  .event-booking h2 {
    font-size: 48px;
    font-weight: bold;
    color: #59292a;
  }
  .event-booking p {
    font-size: 18px;
    margin-top: 20px;
    line-height: 0;
    color: #333333;
  }
  
  /* Footer Styles */
  footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffff;
    padding: 20px;
  }
  .footer-left img {
    width: 150px;
    height: auto;
  }
  .contact-info p {
    margin: 5px 0;
  }
  .social-media {
    display: flex;
    gap: 30px;
    font-size: 40px;
  }
  .footer-right a {
    text-decoration: none;
    margin-left: 20px;
    font-weight: bold;
    color: black;
  }
  </style>
  