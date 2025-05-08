<template>
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Task</h1>

    <form @submit.prevent="updateTask" class="space-y-4">
      <div>
        <label class="block mb-1">Title</label>
        <input v-model="form.title" type="text" class="w-full border px-3 py-2 rounded" />
        <div class="text-red-500 text-sm" v-if="errors.title">{{ errors.title }}</div>
      </div>

      <div>
        <label class="block mb-1">Body</label>
        <textarea v-model="form.body" class="w-full border px-3 py-2 rounded"></textarea>
        <div class="text-red-500 text-sm" v-if="errors.body">{{ errors.body }}</div>
      </div>

      <div class="flex items-center space-x-2">
        <input
          type="checkbox"
          id="is_completed"
          v-model="form.is_completed"
          :checked="form.is_completed"
          class="h-4 w-4 text-green-600"
        />
        <label for="is_completed" class="text-sm text-gray-700">Mark as Completed</label>
      </div>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update Task</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const taskId = page.props.ziggy.location.split('/').pop()

const form = ref({
  title: '',
  body: '',
  is_completed: false,
})

const errors = ref({})

onMounted(async () => {
  const { data } = await axios.get(`/api/tasks/${taskId}`)
  form.value.title = data.title
  form.value.body = data.body
  form.value.is_completed = !!data.is_completed
})

const updateTask = async () => {
  errors.value = {}
  try {
    await axios.put(`/api/tasks/${taskId}`, form.value)
    router.visit('/tasks')
  } catch (error) {
    if (error.response.status === 422) {
      errors.value = error.response.data.errors
    }
  }
}
</script>
