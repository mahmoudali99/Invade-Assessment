<template>
  <div class="task-form">
    <h2>Add a New Task</h2>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="title">Task Title</label>
        <input
          type="text"
          id="title"
          v-model="title"
          placeholder="Enter task title"
          required
        />
      </div>

      <div class="form-group">
        <label for="description">Task Description</label>
        <textarea
          id="description"
          v-model="description"
          placeholder="Enter task description"
          required
        ></textarea>
      </div>

      <div class="form-group">
        <label for="dueDate">Due Date</label>
        <input
          type="date"
          id="dueDate"
          v-model="dueDate"
          required
        />
      </div>

      <div class="form-actions">
        <button type="submit" class="submit-btn">Add Task</button>
        <button type="button" class="close-btn" @click="closeForm">Close</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      title: '',
      description: '',
      dueDate: '',
    };
  },
  methods: {
    submitForm() {
      if (this.title && this.description && this.dueDate) {
        this.$emit('add-task', {
          title: this.title,
          description: this.description,
          dueDate: this.dueDate,
          status: 'Incomplete',
        });
        this.closeForm();
      }
    },
    closeForm() {
      this.$emit('close-form');
    },
  },
};
</script>

<style scoped>
.task-form {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
}

h2 {
  text-align: center;
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  font-size: 1rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
  display: block;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px;
  font-size: 1rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #4caf50;
  outline: none;
}

.form-group textarea {
  height: 120px;
  resize: vertical;
}

.form-actions {
  display: flex;
  justify-content: space-between;
}

.submit-btn,
.close-btn {
  background-color: #28a745;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 48%;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.submit-btn:hover {
  background-color: #218838;
}

.close-btn {
  background-color: #f44336;
  
}

.close-btn:hover {
  background-color: #d32f2f;
}
</style>
