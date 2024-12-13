<template>
    <div class="popup-overlay" @click="$emit('close')">
      <div class="deleted-tasks-menu" @click.stop>
        <h2>Deleted Tasks</h2>
        <ul v-if="deletedTasks.length > 0">
          <li v-for="task in deletedTasks" :key="task.id" class="deleted-task">
            <span>{{ task.title }}</span>
            <button @click="$emit('restore', task.id)" class="restore-button">Restore</button>
          </li>
        </ul>
        <p v-else>No deleted tasks</p>
        <button @click="$emit('close')" class="close-button">Close</button>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    deletedTasks: {
      type: Array,
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
  
  .deleted-tasks-menu {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
  }
  
  h2 {
    margin-top: 0;
    margin-bottom: 20px;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  .deleted-task {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
  }
  
  .restore-button {
    background-color: #1a73e8;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .restore-button:hover {
    background-color: #1557b0;
  }
  
  .close-button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #f0f2f5;
    color: #333;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .close-button:hover {
    background-color: #e4e6e8;
  }
  </style>