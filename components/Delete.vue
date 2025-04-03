<script setup lang="ts">
defineProps<{
  show: boolean
  task: Task
}>()

const emit = defineEmits(["close", "delete"])
</script>

<template lang="pug">
  .modal-overlay(v-if="show")
    .modal-content
      h2.modal-title Feladat törlése
      .container
        span.mb-4 Biztosan törölni szeretné ezt az elemet?
      .commands-container
        button(@click="emit('delete', task.id)") Törlés
        button(@click="emit('close')") Mégsem
</template>

<style scoped>
.modal-overlay {
  @apply fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50
}
.modal-content {
  @apply bg-white p-6 rounded-lg shadow-lg max-w-md w-full
}
.modal-title {
  @apply text-xl font-bold mb-4
}
.container {
  @apply max-w-md mx-auto bg-white p-2
}
.commands-container {
  @apply flex justify-end items-center gap-4
}
button {
  @apply mt-4 px-4 py-2 bg-green-600 text-white rounded
}
</style>