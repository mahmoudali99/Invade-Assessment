<template>
    <div class="todo-view">
      <TodoApp />
    </div>
  </template>
  
  <script setup>
  import TodoApp from '../components/TodoApp.vue'
  </script>
  
  <style scoped>
  .todo-view {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  </style>
  
  