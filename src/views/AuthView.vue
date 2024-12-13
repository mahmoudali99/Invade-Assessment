<template>
  <div class="auth-view">
    <h1 class="text-3xl font-bold text-center my-8">Welcome to Todo App</h1>
    <AuthTabs />
  </div>
</template>

<script setup>
import AuthTabs from '../components/AuthTabs.vue'
</script>

<style scoped>
.auth-view {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}
</style>

