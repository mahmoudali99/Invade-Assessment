<template>
    <div class="popup-overlay" @click="$emit('close')">
      <div class="popup-content" @click.stop>
        <h2>{{ task.title }}</h2>
        <p><strong>Due Date:</strong> {{ task.dueDate }}</p>
        <p><strong>Status:</strong> {{ task.status }}</p>
        <p><strong>Category:</strong> {{ task.categoryTitle }}</p>
        <p><strong>Description:</strong></p>
        <p>{{ task.description }}</p>
        <button class="close-button" @click="$emit('close')">Close</button>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    task: {
      type: Object,
      required: true
    }
  })
  </script>
  
  <style scoped>
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .popup-content {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
  }
  
  h2 {
    margin-top: 0;
  }
  
  .close-button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #1a73e8;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .close-button:hover {
    background-color: #1557b0;
  }
  </style>