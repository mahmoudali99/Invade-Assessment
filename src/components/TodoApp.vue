<template>
  <div class="todo-app">
    <NavBar @logout="handleLogout" @openDeletedTasks="showDeletedTasks = true" />
    <div class="task-list">
      <TaskCard 
        v-for="task in tasks" 
        :key="task.id" 
        :task="task"
        @edit="openEditForm"
        @delete="deleteTask"
        @toggle="toggleTaskStatus"
        @open="openTaskDetails"
      />
    </div>
    <button class="fab" @click="showAddTaskForm = true">
      <Plus class="icon" />
    </button>
    <TaskPopup 
      v-if="selectedTask"
      :task="selectedTask"
      @close="selectedTask = null"
      @toggle="toggleTaskStatus"
    />
    <AddTaskForm 
      v-if="showAddTaskForm"
      :categories="categories"
      @close="showAddTaskForm = false"
      @add="addTask"
      @addCategory="addCategory"
    />
    <EditTaskForm
      v-if="editingTask"
      :task="editingTask"
      :categories="categories"
      @close="editingTask = null"
      @save="saveEditedTask"
      @addCategory="addCategory"
    />
    <DeletedTasks
      v-if="showDeletedTasks"
      :deletedTasks="deletedTasks"
      @close="showDeletedTasks = false"
      @restore="restoreTask"
    />
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { CloudCog, Plus } from 'lucide-vue-next'
import NavBar from './NavBar.vue'
import TaskCard from './TaskCard.vue'
import TaskPopup from './TaskPopup.vue'
import AddTaskForm from './AddTaskForm.vue'
import EditTaskForm from './EditTaskForm.vue'
import DeletedTasks from './DeletedTasks.vue'

const router = useRouter()
const tasks = ref([])
const deletedTasks = ref([])
const selectedTask = ref(null)
const showAddTaskForm = ref(false)
const showDeletedTasks = ref(false)
const editingTask = ref(null)
const categories = ref([])

import { onMounted } from 'vue';

onMounted(async () => {
  await fetchTasks();
  await fetchCategories();
  await fetchDeletedTasks();
});
const getAuthTokenFromCookies = () => {
  const cookieString = document.cookie;
  const match = cookieString.match(/(^| )auth_token=([^;]+)/);

  if (match) {
    console.log(match[2]);
    return match[2]; // Return the value of the auth_token
  }

  return null; // Return null if the token is not found
};

const fetchTasks = async () => {
  try {
    const response = await axios.get('/tasks');
    tasks.value = response.data.data;  // Adjust to match the paginated structure
  } catch (error) {
    console.error("Error fetching tasks:", error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data.data;  // Adjust to match the categories structure
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
};

const fetchDeletedTasks = async () => {
  try {
    const response = await axios.get('/tasks/deleted');
    deletedTasks.value = response.data.data;  // Adjust to match the paginated structure
  } catch (error) {
    console.error("Error fetching deleted tasks:", error);
  }
};

const handleLogout = () => {
  if (confirm('Are you sure you want to log out?')) {
    // Clear the auth_token cookie
    document.cookie = 'auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;';
    
    // Navigate to the home page
    router.push('/');
  }
};

const addTask = async (newTask) => {
  try {
    const response = await axios.post('/tasks', newTask);
    tasks.value.push(response.data.task); // Add the new task to the local tasks list
  } catch (error) {
    console.error("Error adding task:", error);
  }
};

const openEditForm = (taskId) => {
  const taskToEdit = tasks.value.find(task => task.id === taskId);
  if (taskToEdit) {
    editingTask.value = { ...taskToEdit };
  }
};

const saveEditedTask = (updatedTask) => {
  const index = tasks.value.findIndex(task => task.id === updatedTask.id);
  if (index !== -1) {
    tasks.value[index] = updatedTask;
  }
  editingTask.value = null;
};

const deleteTask = async (taskId) => {
  try {
    await axios.delete(`/tasks/${taskId}`);
    const taskIndex = tasks.value.findIndex(task => task.id === taskId);
    if (taskIndex !== -1) {
      const deletedTask = tasks.value.splice(taskIndex, 1)[0];
      deletedTasks.value.push(deletedTask); // Move the task to the deleted list
    }
  } catch (error) {
    console.error("Error deleting task:", error);
  }
};

const toggleTaskStatus = async (taskId) => {
  try {
    const response = await axios.put(`/tasks/${taskId}/toggle-status`);
    const task = tasks.value.find(task => task.id === taskId);
    if (task) {
      task.status = response.data.status; // Update status locally
    }
  } catch (error) {
    console.error("Error toggling task status:", error);
  }
};

const openTaskDetails = (task) => {
  selectedTask.value = task;
};

const restoreTask = async (taskId) => {
  try {
    const response = await axios.put(`/tasks/${taskId}/restore`);
    const taskIndex = deletedTasks.value.findIndex(task => task.id === taskId);
    if (taskIndex !== -1) {
      const restoredTask = deletedTasks.value.splice(taskIndex, 1)[0];
      tasks.value.push(restoredTask); // Restore task to the main list
    }
  } catch (error) {
    console.error("Error restoring task:", error);
  }
};

const addCategory = async (newCategory) => {
  try {
    // Get the token from cookies
    const token = getAuthTokenFromCookies();
    console.log(token);
    if (!token) {
      throw new Error('Token is missing or expired.');
    }

    // Send the API request with the token in the Authorization header
    const response = await axios.post('/categories', { title: newCategory }, {
      headers: {
        'Authorization': `Bearer ${token}`, // Attach the token
      }
    });

    categories.value.push(response.data.category); // Add to local categories list
  } catch (error) {
    console.error("Error adding category:", error.response?.data?.message || error.message);
  }
};
</script>

<style scoped>
.todo-app {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.task-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.fab {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #1a73e8;
  color: white;
  border: none;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s;
}

.fab:hover {
  background-color: #1557b0;
}

.icon {
  width: 24px;
  height: 24px;
}
</style>

