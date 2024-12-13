<template>
    <div class="task-card" @click="$emit('open', task)">
      <h3 class="task-title">{{ task.title }}</h3>
      <p class="task-due-date">Due: {{ formatDate(task.dueDate) }}</p>
      <div class="task-actions">
        <button class="icon-button" @click.stop="$emit('edit', task.id)">
          <Edit class="icon" />
        </button>
        <button class="icon-button" @click.stop="$emit('delete', task.id)">
          <Trash class="icon" />
        </button>
      </div>
      <div class="task-status">
        <button 
          class="toggle-button" 
          @click.stop="$emit('toggle', task.id)"
          :aria-label="task.status === 'completed' ? 'Mark as pending' : 'Mark as completed'"
        >
          <div class="toggle-slider" :class="{ 'is-completed': task.status === 'completed' }"></div>
        </button>
        <span>{{ task.status === 'completed' ? 'Completed' : 'Pending' }}</span>
      </div>
    </div>
  </template>
  
  <script setup>
  import { Edit, Trash } from 'lucide-vue-next'
  
  defineProps({
    task: {
      type: Object,
      required: true
    }
  })
  
  defineEmits(['edit', 'delete', 'toggle', 'open'])
  
  const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
  }
  </script>
  
  <style scoped>
  .task-card {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: box-shadow 0.3s;
  }
  
  .task-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
  
  .task-title {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 600;
  }
  
  .task-due-date {
    font-size: 14px;
    color: #666;
    margin: 0 0 10px 0;
  }
  
  .task-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 10px;
  }
  
  .icon-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    margin-left: 5px;
    color: #666;
    transition: color 0.3s;
  }
  
  .icon-button:hover {
    color: #1a73e8;
  }
  
  .icon {
    width: 18px;
    height: 18px;
  }
  
  .task-status {
    display: flex;
    align-items: center;
  }
  
  .toggle-button {
    width: 40px;
    height: 20px;
    background-color: #ccc;
    border-radius: 10px;
    position: relative;
    cursor: pointer;
    border: none;
    margin-right: 10px;
  }
  
  .toggle-slider {
    width: 18px;
    height: 18px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 1px;
    left: 1px;
    transition: 0.3s;
  }
  
  .toggle-slider.is-completed {
    transform: translateX(20px);
    background-color: #1a73e8;
  }
  
  .toggle-button:focus {
    outline: 2px solid #1a73e8;
    outline-offset: 2px;
  }
  </style>
  
  