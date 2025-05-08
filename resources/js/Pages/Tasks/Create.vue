<template>
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create New Task</h1>

    <form @submit.prevent="createTask" class="space-y-4">
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

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Task</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const form = ref({ title: '', body: '' })
const errors = ref({})

const createTask = async () => {
  errors.value = {}

  try {
    await axios.post('/api/tasks', form.value)
    router.visit('/tasks') // Redirect to index
  } catch (error) {
    if (error.response.status === 422) {
      errors.value = error.response.data.errors
    }
  }
}
</script>
