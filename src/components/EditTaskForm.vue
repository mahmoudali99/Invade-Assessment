<template>
  <div class="popup-overlay" @click="$emit('close')">
    <form class="edit-task-form" @submit.prevent="handleSubmit" @click.stop>
      <h2>Edit Task</h2>
      <div class="form-group">
        <label for="title">Title</label>
        <input id="title" v-model="editedTask.title" type="text" required>
      </div>
      <div class="form-group category-group">
        <label for="category">Category</label>
        <div class="category-input-group">
          <select id="category" v-model="editedTask.category">
            <option value="">Select a category</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
          <button type="button" class="icon-button" @click="showAddCategory = true">
            <Plus class="icon" />
          </button>
        </div>
      </div>
      <div v-if="showAddCategory" class="form-group">
        <label for="newCategory">New Category</label>
        <div class="category-input-group">
          <input id="newCategory" v-model="newCategory" type="text" placeholder="Enter new category">
          <button type="button" class="icon-button" @click="addCategory">
            <Check class="icon" />
          </button>
        </div>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" v-model="editedTask.description" required></textarea>
      </div>
      <div class="form-group">
        <label for="dueDate">Due Date</label>
        <input id="dueDate" v-model="editedTask.dueDate" type="date" required>
      </div>
      <div class="form-actions">
        <button type="button" class="cancel-button" @click="$emit('close')">Cancel</button>
        <button type="submit" class="submit-button">Save Changes</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Check } from 'lucide-vue-next'

const props = defineProps({
  task: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'save', 'addCategory'])

const editedTask = ref({ ...props.task })
const showAddCategory = ref(false)
const newCategory = ref('')

const handleSubmit = () => {
  emit('save', editedTask.value)
  emit('close')
}

const addCategory = () => {
  if (newCategory.value.trim()) {
    emit('addCategory', newCategory.value.trim())
    editedTask.value.category = newCategory.value.trim()
    newCategory.value = ''
    showAddCategory.value = false
  }
}

onMounted(() => {
  editedTask.value = { ...props.task }
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

.edit-task-form {
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

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

input, textarea, select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

textarea {
  height: 100px;
  resize: vertical;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.cancel-button, .submit-button {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-button {
  background-color: #f0f2f5;
  color: #333;
  margin-right: 10px;
}

.cancel-button:hover {
  background-color: #e4e6e8;
}

.submit-button {
  background-color: #1a73e8;
  color: white;
}

.submit-button:hover {
  background-color: #1557b0;
}

.category-group {
  margin-bottom: 10px;
}

.category-input-group {
  display: flex;
  align-items: center;
}

.category-input-group select,
.category-input-group input {
  flex-grow: 1;
  margin-right: 10px;
}

.icon-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  color: #1a73e8;
}

.icon {
  width: 20px;
  height: 20px;
}
</style>

