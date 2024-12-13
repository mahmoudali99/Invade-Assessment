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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Plus } from 'lucide-vue-next'
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
const categories = ref(['Work', 'Personal', 'Shopping', 'Health'])

const handleLogout = () => {
  router.push('/')
}

const addTask = (newTask) => {
  tasks.value.push({ ...newTask, id: Date.now(), status: 'pending' })
}

const openEditForm = (taskId) => {
  const taskToEdit = tasks.value.find(task => task.id === taskId)
  if (taskToEdit) {
    editingTask.value = { ...taskToEdit }
  }
}

const saveEditedTask = (updatedTask) => {
  const index = tasks.value.findIndex(task => task.id === updatedTask.id)
  if (index !== -1) {
    tasks.value[index] = updatedTask
  }
  editingTask.value = null
}

const deleteTask = (taskId) => {
  const taskIndex = tasks.value.findIndex(task => task.id === taskId)
  if (taskIndex !== -1) {
    const deletedTask = tasks.value.splice(taskIndex, 1)[0]
    deletedTasks.value.push(deletedTask)
  }
}

const toggleTaskStatus = (taskId) => {
  const task = tasks.value.find(task => task.id === taskId)
  if (task) {
    task.status = task.status === 'completed' ? 'pending' : 'completed'
  }
}

const openTaskDetails = (task) => {
  selectedTask.value = task
}

const restoreTask = (taskId) => {
  const taskIndex = deletedTasks.value.findIndex(task => task.id === taskId)
  if (taskIndex !== -1) {
    const restoredTask = deletedTasks.value.splice(taskIndex, 1)[0]
    tasks.value.push(restoredTask)
  }
}

const addCategory = (newCategory) => {
  if (!categories.value.includes(newCategory)) {
    categories.value.push(newCategory)
  }
}
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

