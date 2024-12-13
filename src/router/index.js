import { createRouter, createWebHistory } from 'vue-router'
import AuthView from '../views/AuthView.vue'
import TodoView from '../views/TodoView.vue'

const routes = [
  {
    path: '/',
    name: 'Auth',
    component: AuthView
  },
  {
    path: '/todos',
    name: 'Todos',
    component: TodoView,
    meta: {
      requiresAuth: true
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

