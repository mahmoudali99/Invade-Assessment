<template>
    <nav class="navbar">
      <h1 class="title">TODO App</h1>
      <div>
        <button class="nav-button" @click="$emit('openDeletedTasks')">Deleted Tasks</button>
        <button class="nav-button logout" @click="$emit('logout')">Log Out</button>
      </div>
    </nav>
  </template>
  
  <script setup>
  // No setup needed for this component
  </script>
  
  <style scoped>
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: #1a73e8;
    color: white;
  }
  
  .title {
    font-size: 24px;
    font-weight: 600;
    margin: 0;
  }
  
  .nav-button {
    background-color: transparent;
    color: white;
    border: none;
    padding: 8px 12px;
    margin-left: 10px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .nav-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .logout {
    font-weight: 600;
  }
  </style>