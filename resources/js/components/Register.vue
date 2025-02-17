php <template>
  <header class="header">
    <div class="logo">
      <img src="/img/logo.png" alt="Event Vista Logo">
    </div>
  </header>
  <div class="login-container">
    <div class="login-left">
      <div class="form-container">
        <h2>Create Account</h2>
        <form @submit.prevent="handleLogin">
          <label for="firstname">First Name</label>
          <input
            type="text"
            id="firstname"
            placeholder="Type your first name"
            v-model="firstname"
            required
          />
          <label for="lastname">Last Name</label>
          <input
            type="text"
            id="lastname"
            placeholder="Type your last name"
            v-model="lastname"
            required
          />

          <label for="phone">Phone No.</label>
          <input
            type="text"
            id="phone"
            placeholder="Add your Phone no."
            v-model="phone"
            required
          />

          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            placeholder="username@gmail.com"
            v-model="email"
            required
          />

          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            placeholder="Password"
            v-model="password"
            required
          />

          <label for="confirm-password">Confirm Password</label>
          <input
            type="password"
            id="confirm-password"
            placeholder="Confirm Password"
            v-model="confirmPassword"
            required
          />

          <!-- Centered login button -->
          <div class="button-container">
            <button type="submit" class="login-btn">Create Account</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Swal from 'sweetalert2';

export default {
  name: "CreateAccount",
  data() {
    return {
      firstname: "",
      lastname: "",
      phone: "",
      email: "",
      password: "",
      confirmPassword: "",
    };
  },
  methods: {
    async handleLogin() {
      try {
        // Validate password confirmation
        if (this.password !== this.confirmPassword) {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Passwords do not match!'
          });
          return;
        }

        const response = await axios.post("/api/register", {
          firstname: this.firstname,
          lastname: this.lastname,
          phone: this.phone,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
        }, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          localStorage.setItem('user_data', JSON.stringify({
            firstname: this.firstname,
            lastname: this.lastname,
            email: this.email
          }));
          
          // Show success message
          await Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            text: 'Welcome to Event Vista!',
            showConfirmButton: false,
            timer: 1500
          });

          // Redirect after showing message
          this.$router.push("/login");
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          // Show validation errors
          const errors = Object.values(error.response.data.errors).flat();
          Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errors.join('\n'),
          });
        } else {
          const errorMessage = error.response?.data?.message 
            || "Registration failed. Please try again.";
          Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: errorMessage,
          });
        }
        console.error('Registration error:', error.response?.data);
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
  }
  
  .header {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #FFD1D1;
  }
  
  .logo img {
    width: 150px;
  }
  
  .login-container {
    display: flex;
    width: 100%;
    max-width: 2000px;
    justify-content: center;
    align-items: center; /* Align content to the top */
  }
  
  .login-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #FFD1D1;
    padding: 20px;
    width: 100%;
    max-width: 2000px;

  }
  
  .form-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    width: 100%;
    max-width: 400px; 
}
  
  h2 {
    font-family: 'Georgia', serif;
    font-size: 30px;
    color: #000;
    margin-bottom: 15px;
    text-align: center;
  }
  
  label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
  }
  
  input {
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
    width: 100%;
  }
  
  .button-container {
    display: flex;
    justify-content: center;
  }
  
  .login-btn {
    padding: 12px 20px;
    background-color: #233f78;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
  }
  
  .login-btn:hover {
    background-color: #1a2d5b;
  }
  
  .continue-text {
    margin: 15px 0;
    text-align: center;
  }
  
  .social-login {
    display: flex;
    justify-content: space-between;
  }
  
  .social-login button {
    flex: 1;
    padding: 10px;
    margin: 0 5px;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
  }
  
  .google-btn {
    background-color: #4285F4;
  }
  
  .facebook-btn {
    background-color: #3b5998;
  }
  </style>