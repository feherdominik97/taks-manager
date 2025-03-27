<script setup lang="ts">
import { computed, defineProps } from 'vue'
import { parseISO, format } from 'date-fns'
import type { Task } from '~/types/Task'

const props = defineProps<{
  tasks: Task[]
  editTask: (task: Task) => void
  deleteTask: (task: Task) => void
}>()

const days: string[] = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']

const structuredTasks = computed<Record<string, Task[]>>(() => {
  const taskMap: Record<string, Task[]> = Object.fromEntries(days.map(day => [day, []]))

  props.tasks.forEach((task: Task) => {
    const date = parseISO(task.scheduled_day)
    const weekday = format(date, 'EEEE')
    if (taskMap[weekday]) {
      taskMap[weekday].push(task)
    }
  })

  return taskMap
})
</script>
<template lang="pug">
  div.overflow-x-auto
    table.w-full.border-collapse.rounded-lg.shadow-lg
      thead.bg-gray-100
        tr
          th.p-3.text-left(v-for="day in days" :key="day") {{ day }}

      tbody
        tr
          td.p-3.align-top(v-for="(tasks, day) in structuredTasks" :key="day")
            div.mb-2.border-b.pb-2(v-for="(task, index) in tasks" :key="index")
              div.font-semibold {{ task.name }}
              div(v-for="assignee in task.assignees").text-gray-500.text-sm {{ assignee }}
              div.flex.gap-2.mt-1
                button.text-yellow-500(@click="editTask(task)") ✏️
                button.text-red-500(@click="deleteTask(task)") ❌
</template>
