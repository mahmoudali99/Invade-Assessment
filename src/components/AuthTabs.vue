<template>
  <div class="auth-container">
    <div class="auth-tabs">
      <button :class="['tab-button', { active: activeTab === 'signin' }]" @click="activeTab = 'signin'">
        Sign In
      </button>
      <button :class="['tab-button', { active: activeTab === 'signup' }]" @click="activeTab = 'signup'">
        Sign Up
      </button>
    </div>
    <div class="auth-form">
      <form @submit.prevent="handleSubmit">
        <template v-if="activeTab === 'signin'">
          <div class="form-group">
            <label for="signin-email">Email or Phone</label>
            <input id="signin-email" v-model="signinEmail" type="text" name="login" required>
            <span v-if="errors.login" class="error">{{ errors.login }}</span>
          </div>
          <div class="form-group">
            <label for="signin-password">Password</label>
            <input id="signin-password" v-model="signinPassword" type="password" name="password" required>
            <span v-if="errors.password" class="error">{{ errors.password }}</span>
          </div>
          <div v-if="errors.general" class="error">{{ errors.general }}</div>
          <button type="submit" class="submit-button">Sign In</button>
        </template>

        <template v-else>
          <div class="form-group">
            <label for="signup-name">Name</label>
            <input id="signup-name" v-model="signupName" name="name" type="text" required>
            <span v-if="errors.name" class="error">{{ errors.name }}</span>
          </div>
          <div class="form-group">
            <label for="signup-email">Email</label>
            <input id="signup-email" v-model="signupEmail" type="email" required name="email">
            <span v-if="errors.email" class="error">{{ errors.email }}</span>
          </div>
          <div class="form-group">
            <label for="signup-phone">Phone</label>
            <input id="signup-phone" v-model="signupPhone" type="tel" required name="phoneNumber">
            <span v-if="errors.phoneNumber" class="error">{{ errors.phoneNumber }}</span>
          </div>
          <div class="form-group">
            <label for="signup-password">Password</label>
            <input id="signup-password" v-model="signupPassword" type="password" required name="password">
            <span v-if="errors.password" class="error">{{ errors.password }}</span>
          </div>
          <button type="submit" class="submit-button">Sign Up</button>
        </template>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      activeTab: 'signin',
      signinEmail: '',
      signinPassword: '',
      signupName: '',
      signupEmail: '',
      signupPhone: '',
      signupPassword: '',
      errors: {}, // Store validation errors
    };
  },
  methods: {
    async handleSubmit() {
      const endpoint = this.activeTab === 'signin'
        ? 'http://127.0.0.1:8000/api/login'
        : 'http://127.0.0.1:8000/api/signup';

      const payload = this.activeTab === 'signin'
        ? {
          login: this.signinEmail,
          password: this.signinPassword,
        }
        : {
          name: this.signupName,
          email: this.signupEmail,
          phoneNumber: this.signupPhone,
          password: this.signupPassword,
        };

      try {
        const response = await axios.post(endpoint, payload);

        if (response.status === 200) {
          const token = response.data.token; // Assuming the token is returned in `response.data.token`

          // Store the token in cookies
          document.cookie = `auth_token=${token.accessToken};path=/;secure;samesite=lax`;
          console.log('Token stored in cookies:', token);

          // Navigate to /todos
          this.$router.push('/todos'); // Assumes you're using Vue Router
        }
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors; // Populate errors object
        }else if (error.response.data.message) {
          // Handle login-specific error message
          this.errors = { general: error.response.data.message };
        } 
        else {
          console.error('An unexpected error occurred', error);
        }
      }
    },

    getCookie(name) {
      const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
      return match ? match[2] : null;
    },
  },
  mounted() {
    // Check for auth_token in cookies
    const token = this.getCookie('auth_token');
    if (token) {
      // Navigate to /todos if token exists
      this.$router.push('/todos');
    }
  },

};
</script>

<style scoped>
/* Additional styling for error messages */
.error {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}
</style>

<style scoped>
.auth-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.auth-tabs {
  display: flex;
  margin-bottom: 20px;
}

.tab-button {
  flex: 1;
  padding: 10px;
  border: none;
  background-color: #f0f2f5;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  transition: background-color 0.3s;
}

.tab-button.active {
  background-color: white;
  border-bottom: 2px solid #1a73e8;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.submit-button {
  width: 100%;
  padding: 12px;
  background-color: #1a73e8;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #1557b0;
}
</style>