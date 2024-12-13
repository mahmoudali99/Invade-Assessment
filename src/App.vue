<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script setup>
// No setup needed for this component
</script>

<style>
@import 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';

body {
  font-family: 'Inter', sans-serif;
  background-color: #f0f2f5;
  margin: 0;
  padding: 0;
}

#app {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
</style>