<script setup lang="ts">

const props = defineProps<{
  show: boolean,
  task: Task
}>()

const emit = defineEmits(["close", "save"])
const form = ref(JSON.parse(JSON.stringify(props.task)) as Task)


const assigneeInput = ref('')

const addAssignee = () => {
  if (assigneeInput.value.trim()) {
    form.value.assignees.push(assigneeInput.value.trim())
    assigneeInput.value = ''
  }
}

const clearAssignees = () => {
  form.value.assignees = []
}
</script>

<template lang="pug">
  .modal-overlay(v-if="show")
    .modal-content
      h2.modal-title {{ (task.id ? "Modify" : "Create") + "task"}}
      div.container
        .input
          label Megnevezés:
          input#name(v-model="form.name" type="text")
        .input
          label Hossz (perc):
          input#duration(v-model="form.duration" type="number" min="0")
        .input
          label Ütemezett nap:
          input#scheduled_day(v-model="form.scheduled_day" type="date")
        .input
          label Megbízottak:
          .flex.items-center
            input#assignees(v-model="assigneeInput" @blur="addAssignee" type="email")
            button(@click="clearAssignees") ❌
          div.assignees-text(v-if="form.assignees.length") Megbízott: {{ form.assignees.join(", ") }}
        .input
          label Prioritás:
          select#priority(v-model="form.priority")
            option(value="alacsony") Alacsony
            option(value="normál") Normál
            option(value="magas") Magas
        .input
          label.checkbox-container
            input.mr-2(type="checkbox" v-model="form.is_done")
            span Kész
      .commands-container
        button(@click="emit('save', form)") Save
        button(@click="emit('close')") Close
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
  @apply max-w-md mx-auto bg-white p-6 rounded shadow-lg grid grid-cols-2 gap-4
}
label {
  @apply block text-sm font-medium mb-1
}
input:not([type="checkbox"]), select{
  @apply w-full p-2 border rounded mb-4
}
.assignees-text {
  @apply text-sm text-gray-600
}
.checkbox-container {
  @apply w-full p-2 flex mt-6 justify-center items-center
}
.commands-container button {
  @apply mt-4 px-4 py-2 bg-green-600 text-white rounded
}

input ~ button {
  @apply mb-4 ml-2
}

.commands-container {
  @apply flex justify-end items-center gap-4
}
</style>