<template>
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">My Tasks</h1>

    <!-- New Task Form -->
    <form @submit.prevent="createTask" class="space-y-4 mb-6">
      <div>
        <input v-model="form.title" type="text" placeholder="Title" class="w-full border px-3 py-2 rounded" />
        <span class="text-red-500 text-sm" v-if="errors.title">{{ errors.title }}</span>
      </div>
      <div>
        <textarea v-model="form.body" placeholder="Body" class="w-full border px-3 py-2 rounded"></textarea>
        <span class="text-red-500 text-sm" v-if="errors.body">{{ errors.body }}</span>
      </div>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Task</button>
    </form>

    <!-- Task List -->
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">Done</th>
          <th class="p-2">Title</th>
          <th class="p-2">Body</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id" class="border-t">
          <td class="p-2">
            <input
              type="checkbox"
              :checked="task.is_completed"
              @change="toggleCompletion(task)"
            />
          </td>
          <td :class="{ 'line-through text-gray-400': task.is_completed }" class="p-2">{{ task.title }}</td>
          <td :class="{ 'line-through text-gray-400': task.is_completed }" class="p-2">{{ task.body }}</td>
          <td class="p-2 space-x-2">
            <button @click="editTask(task)" class="text-blue-500">Edit</button>
            <button @click="deleteTask(task.id)" class="text-red-500">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Edit Task Modal -->
    <div v-if="editingTask" class="mt-6">
      <h2 class="text-lg font-semibold mb-2">Edit Task</h2>
      <form @submit.prevent="updateTask" class="space-y-4">
        <div>
          <input v-model="editForm.title" type="text" placeholder="Title" class="w-full border px-3 py-2 rounded" />
        </div>
        <div>
          <textarea v-model="editForm.body" placeholder="Body" class="w-full border px-3 py-2 rounded"></textarea>
        </div>
        <!-- <div class="flex items-center space-x-2">
          <input type="checkbox" id="edit_done" v-model="editForm.is_completed" class="h-4 w-4" />
          <label for="edit_done" class="text-sm">Mark as Completed</label>
        </div> -->
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Update Task</button>
        <button @click="editingTask = null" type="button" class="ml-2 text-gray-500">Cancel</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tasks = ref([])
const form = ref({ title: '', body: '' })
const editForm = ref({ title: '', body: '', is_completed: false })
const editingTask = ref(null)
const errors = ref({})

// Fetch tasks
const fetchTasks = async () => {
  const { data } = await axios.get('/api/tasks')
  tasks.value = data
}

// Create new task
const createTask = async () => {
  errors.value = {}
  try {
    await axios.post('/api/tasks', form.value)
    form.value = { title: '', body: '' }
    fetchTasks()
  } catch (e) {
    if (e.response.status === 422) {
      errors.value = e.response.data.errors
    }
  }
}

// Edit task
const editTask = (task) => {
  editingTask.value = task
  editForm.value = {
    title: task.title,
    body: task.body,
    is_completed: task.is_completed,
  }
}

// Update task
const updateTask = async () => {
  try {
    await axios.put(`/api/tasks/${editingTask.value.id}`, editForm.value)
    editingTask.value = null
    fetchTasks()
  } catch (e) {
    console.error('Update failed:', e)
  }
}

// Delete task
const deleteTask = async (id) => {
  await axios.delete(`/api/tasks/${id}`)
  fetchTasks()
}

// Toggle completion
const toggleCompletion = async (task) => {
  await axios.put(`/api/tasks/${task.id}`, {
    title: task.title,
    body: task.body,
    is_completed: !task.is_completed,
  })
  fetchTasks()
}

onMounted(fetchTasks)
</script>
