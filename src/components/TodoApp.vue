<template>
  <div class="todo-app" @scroll="handleScroll" style="overflow-y: auto; height: 100vh;">
    <NavBar @logout="handleLogout" @openDeletedTasks="showDeletedTasks = true" />
    <div class="actions-bar">
      <div class="search-bar">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search tasks..." 
          class="search-input"
          @keyup.enter="performSearch"
        >
      </div>
      
      <div class="sort-options">
        <select v-model="sortBy" class="sort-select">
          <option value="title">Sort by Title</option>
          <option value="dueDate">Sort by Due Date</option>
        </select>
        <button @click="toggleSortOrder" class="sort-order-btn">
          <ArrowUp v-if="sortOrder === 'asc'" class="icon" />
          <ArrowDown v-else class="icon" />
        </button>
      </div>
      
      <div class="filter-options">
        <button @click="toggleCategoryFilter" class="filter-btn">
          Filter by Category
          <ChevronDown class="icon" />
        </button>
        <div v-if="showCategoryFilter" class="filter-dropdown">
          <label v-for="category in categories" :key="category.id" class="filter-option">
            <input 
              type="checkbox" 
              :value="category.id" 
              v-model="selectedCategories"
            >
            {{ category.title }}
          </label>
        </div>
        
        <button @click="toggleStatusFilter" class="filter-btn">
          Filter by Status
          <ChevronDown class="icon" />
        </button>
        <div v-if="showStatusFilter" class="filter-dropdown">
          <label class="filter-option">
            <input 
              type="checkbox" 
              value="completed" 
              v-model="selectedStatuses"
            >
            Completed
          </label>
          <label class="filter-option">
            <input 
              type="checkbox" 
              value="pending" 
              v-model="selectedStatuses"
            >
            Pending
          </label>
        </div>
      </div>
    </div>
    <div class="task-list">
      <TaskCard v-for="task in tasks" :key="task.id" :task="task" @edit="openEditForm" @delete="deleteTask"
        @toggle="toggleTaskStatus" @open="openTaskDetails" />
    </div>
    <button class="fab" @click="showAddTaskForm = true">
      <Plus class="icon" />
    </button>
    <TaskPopup v-if="selectedTask" :task="selectedTask" @close="selectedTask = null" @toggle="toggleTaskStatus" />
    <AddTaskForm v-if="showAddTaskForm" :categories="categories" @close="showAddTaskForm = false" @add="addTask"
      @addCategory="addCategory" />
    <EditTaskForm v-if="editingTask" :task="editingTask" :categories="categories" @close="editingTask = null"
      @save="saveEditedTask" @addCategory="addCategory" />
    <DeletedTasks v-if="showDeletedTasks" :deletedTasks="deletedTasks" @close="showDeletedTasks = false"
      @restore="restoreTask" @load-more="loadMoreDeletedTasks" />
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref , computed, watch} from 'vue'
import { useRouter } from 'vue-router'
import { CloudCog, Plus, ArrowUp, ArrowDown, ChevronDown } from 'lucide-vue-next'
import NavBar from './NavBar.vue'
import TaskCard from './TaskCard.vue'
import TaskPopup from './TaskPopup.vue'
import AddTaskForm from './AddTaskForm.vue'
import EditTaskForm from './EditTaskForm.vue'
import DeletedTasks from './DeletedTasks.vue'
import { onMounted } from 'vue';

const router = useRouter();
const tasks = ref([]);
const deletedTasks = ref([]);
const selectedTask = ref(null);
const showAddTaskForm = ref(false);
const showDeletedTasks = ref(false);
const editingTask = ref(null);
const categories = ref([]);
const currentPage = ref(1);
const totalPages = ref(null);
const isLoading = ref(false);
const hasMoreTasks = ref(true);
const currentPageDeletedTasks = ref(1); 
const totalPagesDeletedTasks = ref(null);
const isLoadingDeletedTasks = ref(false);
const hasMoreDeletedTasks = ref(true);

const searchQuery = ref('')
const sortBy = ref('title')
const sortOrder = ref('asc')
const showCategoryFilter = ref(false)
const showStatusFilter = ref(false)
const selectedCategories = ref([])
const selectedStatuses = ref([])

onMounted(async () => {
  await fetchTasks();
  await fetchCategories();
  await fetchDeletedTasks();
});
const getAuthTokenFromCookies = () => {
  const cookieString = document.cookie;
  const match = cookieString.match(/(^| )auth_token=([^;]+)/);

  if (match) {
    return match[2]; // Return the value of the auth_token
  }

  return null; // Return null if the token is not found
};

const fetchTasks = async (page = 1) => {
  if (isLoading.value || !hasMoreTasks.value) return;

  try {
    isLoading.value = true;
    const token = getAuthTokenFromCookies();
    if (!token) throw new Error('Token is missing or expired.');

    const response = await axios.get('/tasks', {
      headers: { Authorization: `Bearer ${token}` },
      params: {
        page,
        search: searchQuery.value,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
        categories: selectedCategories.value,
        statuses: selectedStatuses.value,
      },
    });
    if (page === 1) {
      tasks.value = response.data.data;
    } else {
      tasks.value = [...tasks.value, ...response.data.data];
    }
    totalPages.value = response.data.last_page;

    if (page >= totalPages.value) hasMoreTasks.value = false;

    currentPage.value = page;
  } catch (error) {
    console.error('Error fetching tasks:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleScroll = (event) => {
  const target = event.target;
  if (target.scrollHeight - target.scrollTop <= target.clientHeight + 100) {
    fetchTasks(currentPage.value + 1);
  }
};

const fetchCategories = async () => {
  try {

    const response = await axios.get('/categories',);
    categories.value = response.data.categories;
    // Adjust to match the categories structure
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
};

const fetchDeletedTasks = async () => {
  if (isLoadingDeletedTasks.value || !hasMoreDeletedTasks.value) return;

  try {
    isLoadingDeletedTasks.value = true;
    const token = getAuthTokenFromCookies();
    if (!token) throw new Error('Token is missing or expired.');

    const response = await axios.get(`/tasks/deleted?page=${currentPageDeletedTasks.value}`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const { data, last_page } = response.data;
    deletedTasks.value = [...deletedTasks.value, ...data];
    totalPagesDeletedTasks.value = last_page;

    if (currentPageDeletedTasks.value >= totalPagesDeletedTasks.value) hasMoreDeletedTasks.value = false;
    currentPageDeletedTasks.value++;
  } catch (error) {
    console.error('Error fetching deleted tasks:', error);
  } finally {
    isLoadingDeletedTasks.value = false;
  }
};
const loadMoreDeletedTasks = () => {
  fetchDeletedTasks();
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
    const token = getAuthTokenFromCookies();
    if (!token) {
      throw new Error('Token is missing or expired.');
    }

    const response = await axios.post('/tasks', {
      title: newTask.title,
      categoryId: newTask.categoryId,
      description: newTask.description,
      dueDate: newTask.dueDate,
    });

    tasks.value.push(response.data.task);
    fetchTasks(); // Refresh the task list
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Display validation errors
      console.error("Validation error:", error.response.data.errors);
// Show the first error for 'dueDate'
    } else {
      console.error("Error adding task:", error);
      alert('An unexpected error occurred.');
    }
  }
};


const openEditForm = (taskId) => {
  const taskToEdit = tasks.value.find(task => task.id === taskId);
  if (taskToEdit) {
    editingTask.value = { ...taskToEdit };
  }
};

const saveEditedTask = async(updatedTask) => {
  const index = tasks.value.findIndex(task => task.id === updatedTask.id);
  if (index !== -1) {
    tasks.value[index] = updatedTask;
    const response = await axios.put(`/tasks/${updatedTask.id}`,
      {
        title: updatedTask.title, categoryId: updatedTask.categoryId, description: updatedTask.description, dueDate: updatedTask.dueDate
      },);
  }
  editingTask.value = null;
};

const deleteTask = async (taskId) => {
  try {
    const token = getAuthTokenFromCookies();
    if (!token) throw new Error('Token is missing or expired.');
    await axios.delete(`/tasks/${taskId}`, {
      headers: {
        'Authorization': `Bearer ${token}`, // Attach the token
      }
    });
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
    const token = getAuthTokenFromCookies();
    if (!token) throw new Error('Token is missing or expired.');
    const response = await axios.put(`/tasks/${taskId}/toggle-status`, {
      headers: {
        'Authorization': `Bearer ${token}`, // Attach the token
      }
    });

    const task = tasks.value.find(task => task.id === taskId);
    if (task) {
      task.status = response.data.task.status; // Update status locally
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
      restoredTask.status = response.data.task.status;
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



const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  performSearch();
};

const toggleCategoryFilter = () => {
  showCategoryFilter.value = !showCategoryFilter.value;
  if (showCategoryFilter.value) {
    showStatusFilter.value = false;
  }
  performSearch();
};

const toggleStatusFilter = () => {
  showStatusFilter.value = !showStatusFilter.value;
  if (showStatusFilter.value) {
    showCategoryFilter.value = false;
  }
  performSearch();
};

const performSearch = () => {
  currentPage.value = 1;
  hasMoreTasks.value = true;
  tasks.value = [];
  fetchTasks(1);
};

watch(sortBy, () => performSearch());
watch(selectedCategories, () => performSearch());
watch(selectedStatuses, () => performSearch());

</script>

<style scoped>
.todo-app {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.actions-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.search-bar {
  flex-grow: 1;
}

.search-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.sort-options, .filter-options {
  display: flex;
  align-items: center;
}

.sort-select, .filter-btn {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
  font-size: 14px;
  cursor: pointer;
}

.sort-order-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.filter-dropdown {
  position: absolute;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 8px;
  z-index: 10;
}

.filter-option {
  display: block;
  margin-bottom: 4px;
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
  width: 20px;
  height: 20px;
}
</style>

