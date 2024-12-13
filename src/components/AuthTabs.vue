<template>
  <div class="auth-container">
    <div class="auth-tabs">
      <button 
        :class="['tab-button', { active: activeTab === 'signin' }]" 
        @click="activeTab = 'signin'"
      >
        Sign In
      </button>
      <button 
        :class="['tab-button', { active: activeTab === 'signup' }]" 
        @click="activeTab = 'signup'"
      >
        Sign Up
      </button>
    </div>
    <div class="auth-form">
      <form @submit.prevent="handleSubmit">
        <template v-if="activeTab === 'signin'">
          <div class="form-group">
            <label for="signin-email">Email or Phone</label>
            <input id="signin-email" v-model="signinEmail" type="text" required>
          </div>
          <div class="form-group">
            <label for="signin-password">Password</label>
            <input id="signin-password" v-model="signinPassword" type="password" required>
          </div>
          <button type="submit" class="submit-button">Sign In</button>
        </template>
        <template v-else>
          <div class="form-group">
            <label for="signup-name">Name</label>
            <input id="signup-name" v-model="signupName" type="text" required>
          </div>
          <div class="form-group">
            <label for="signup-email">Email</label>
            <input id="signup-email" v-model="signupEmail" type="email" required>
          </div>
          <div class="form-group">
            <label for="signup-phone">Phone</label>
            <input id="signup-phone" v-model="signupPhone" type="tel" required>
          </div>
          <div class="form-group">
            <label for="signup-password">Password</label>
            <input id="signup-password" v-model="signupPassword" type="password" required>
          </div>
          <button type="submit" class="submit-button">Sign Up</button>
        </template>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeTab = ref('signin')

const signinEmail = ref('')
const signinPassword = ref('')

const signupName = ref('')
const signupEmail = ref('')
const signupPhone = ref('')
const signupPassword = ref('')

const handleSubmit = () => {
  // Here you would typically handle authentication
  // For this example, we'll just navigate to the todo page
  router.push('/todos')
}
</script>

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