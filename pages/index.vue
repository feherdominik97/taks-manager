<script setup lang="ts">
import { onMounted, ref, computed } from "vue"
import type {Task} from "~/types/Task"
import { useTask } from "~/composables/task"
import Delete from "~/components/Delete.vue"
import Info from "~/components/Info.vue"


const headers = {
  "Content-Type": "application/json",
  "Accept": "application/json",
}
const { getDefaultTask, formatDate } = useTask()
const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const tasks = ref([] as Task[])
const formVisible = ref(false)
const deleteMessageVisible = ref(false)
const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
const days_hun = ["Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek"]
const task = ref(getDefaultTask())
const infoVisible = ref(false)
const infoForceButtonVisible = ref(false)
const infoDivideButtonVisible = ref(false)
const showDone = ref(false)
let assigneesChecked = ref({})
let maxDuration = 0
const _8HoursInMinutes = 8 * 60

const getNumberOfWeek = (date: Date) => {
  const startOfYear = new Date(date.getFullYear(), 0, 1)
  const diff = date.getTime() - startOfYear.getTime()
  const oneDay = 86400000 // 1000 * 60 * 60 * 24 (ms in a day)
  const dayOfYear = Math.floor(diff / oneDay) + 1 // Days since Jan 1

  const jan1Day = startOfYear.getDay() || 7 // Sunday = 7
  return Math.ceil((dayOfYear + jan1Day - 1) / 7)
}

const week = ref(getNumberOfWeek(new Date()))

const dates = computed(() => {
  const year = new Date().getFullYear()
  const firstDayOfYear = new Date(year, 0, 1)
  const daysOffset = (week.value - 1) * 7
  const start = new Date(firstDayOfYear.getTime() + daysOffset * 86400000)

  start.setDate(start.getDate() - start.getDay() + 1) // Adjust to Monday
  const end = new Date(start)
  end.setDate(start.getDate() + 6) // End of the week (Sunday)

  return { start, end }
})

const getDayOfTheWeek = (date: Date) => {
  const dayIndex = (date.getDay() + 6) % 7
  return days[dayIndex]
}

const structuredTasks = computed(() => {
  let structuredTasks: { [x: string]: any }[] = []

  tasks.value.forEach((task: Task) => {
    const scheduled_day = new Date(task.scheduled_day)
    if(!structuredTasks[getNumberOfWeek(scheduled_day)]) {
      structuredTasks[getNumberOfWeek(scheduled_day)] = []
      structuredTasks[getNumberOfWeek(scheduled_day)] = Object.fromEntries(days.map((field) => [field, []]))
    }

    if(showDone.value || !task.is_done)
      structuredTasks[getNumberOfWeek(scheduled_day)][getDayOfTheWeek(scheduled_day)].push(task)
  })

  return structuredTasks
})

const checkAssignees = (t: Task) => {
  const scheduled_day = new Date(t.scheduled_day)
  const day = JSON.parse(JSON.stringify(structuredTasks.value[getNumberOfWeek(scheduled_day)][getDayOfTheWeek(scheduled_day)]))

  const index = day.findIndex((st: Task) => st.id === t.id)

  if (index !== -1) {
    day[index] = t
  } else {
    day.push(t)
  }

  return addWorkTimeOfTask(day)
}

const addWorkTimeOfTask = (day: any) => {
  let durations: { [x: string]: any } = []

  for (const st of day) {
    for (const assignee of st.assignees) {
      if (!durations[assignee]) {
        durations[assignee] = 0
      }

      if (durations[assignee] + st.duration > _8HoursInMinutes) {
        return { checks: false, durations }
      }

      durations[assignee] += st.duration
    }
  }

  return { checks: true, durations }
}

const getTasks = async () => {
  await $fetch(`${apiUrl}/tasks`,)
      .then(response => {
        tasks.value = response
      })
}
const postTask = async (t: Task) => {
  await fetch(`${apiUrl}/tasks`, {
    method: "POST",
    headers,
    body: JSON.stringify(t),
  })
      .then(() => {
        getTasks()
        console.log("Task successfully created")
      })
      .catch(e => console.log(e))
  hideForm()
}

const putTask = async (t: Task) => {
  await fetch(`${apiUrl}/tasks/${t.id}`, {
    method: "PUT",
    headers,
    body: JSON.stringify(t),
  })
      .then(() => {
        getTasks()
        console.log("Task successfully modified")
      })
      .catch(e => console.log(e))
  hideForm()
}

const deleteTask = async (id: number) => {
  await fetch(`${apiUrl}/tasks/${id}`, {
    method: "DELETE",
    headers,
  })
      .then(() => {
        getTasks()
        console.log("Task successfully deleted")
      })
      .catch(e => console.log(e))
  hideDeleteMessage()
}

const save = (t: Task) => {
  assigneesChecked.value = checkAssignees(t)
  if(!assigneesChecked.value.checks) {
    maxDuration = Math.max(...Object.values(assigneesChecked.value.durations) as number[])
    hideForm()
    task.value = t
    console.log(maxDuration, _8HoursInMinutes, maxDuration < _8HoursInMinutes)
    showInfo(maxDuration < _8HoursInMinutes)
    return
  }
  if (t.id) {
    putTask(t)
    return
  }
  postTask(t)
}

const divide = (t: Task) => {
  let remainingOfDay = _8HoursInMinutes - maxDuration
  let remainingOfTask = t.duration - remainingOfDay


  t.duration = remainingOfDay
  postTask(t)

  t.duration = remainingOfTask
  const date = new Date(t.scheduled_day)
  const nextWeekDay = date.getDay() === 5 ? date.getDate() + 3 : date.getDate() + 1
  date.setDate(nextWeekDay)
  t.scheduled_day = formatDate(date)
  save(t)

  hideInfo()
}

const showInfo = (divide: boolean = false, force: boolean = false) => {
  infoForceButtonVisible.value = force
  infoDivideButtonVisible.value = divide
  infoVisible.value = true
}

const hideInfo = () => {
  infoVisible.value = false
}

const prevWeek = () => {
  week.value--
}

const nextWeek = () => {
  week.value++
}

const showDeleteMessage = (id: number) => {
  task.value.id = id
  deleteMessageVisible.value = true
}

const hideDeleteMessage = () => {
  task.value.id = 0
  deleteMessageVisible.value = false
}

const showForm = (t: Task, isNew: boolean = false) => {
  task.value = JSON.parse(JSON.stringify(t))
  task.value.id = isNew ? 0 : task.value.id
  formVisible.value = true
}

const hideForm = () => {
  task.value = getDefaultTask()
  formVisible.value = false
}

const getClass = (task: Task) => {
  return {
    'bg-red-100': task.priority === 'magas',
    'bg-green-100': task.priority === 'alacsony',
    'bg-yellow-100': task.priority === 'normál',
    'mb-2 border-b p-2 rounded shadow': true
  }
}

const getFormattedDuration = (task: Task) => {
  const hours = Math.floor(task.duration / 60)
  const minutes = task.duration % 60

  const minutes_str = minutes + 'min'

  return hours ? hours + 'h ' + minutes_str : minutes_str
}

onMounted(async () => {
  await getTasks()
})
</script>

<template lang="pug">
  div.overflow-x-auto(v-if="tasks.length")
    table.w-full
      thead.bg-gray-100
        tr
          th(colspan="100%")
            .head-controls
              button(@click="prevWeek()") ⬅️
              span {{ dates.start.toLocaleDateString() + ' - ' + dates.end.toLocaleDateString() }}
              button(@click="nextWeek()") ➡️
              button.plus(@click="showForm(task, true)") ➕
              .checkbox-container
                label Készek mutatása
                input(type="checkbox" v-model="showDone")
        tr
          th(v-for="day in days_hun" :key="day") {{ day }}
      tbody
        tr
          td(v-for="(tasks, day) in structuredTasks[week]" :key="day")
            div(:class="getClass(task)" v-for="(task, index) in tasks" :key="index")
              .font-semibold {{ task.name }}
              .font-semibold {{ getFormattedDuration(task) }}
              .text-gray-500.text-sm(v-for="assignee in task.assignees") {{ assignee }}
              .flex.gap-2.mt-1
                button(@click="showForm(task, true)") 📄
                button(@click="showForm(task)") ✏️
                button(@click="showDeleteMessage(task.id)") ❌
                .ml-auto(v-if="task.is_done") ✅
    Delete(
      @close="hideDeleteMessage"
      @delete="deleteTask"
      :task="task"
      :key="deleteMessageVisible"
      :show="deleteMessageVisible")
    Info(
      @close="hideInfo"
      @divide="divide"
      @force="hideInfo"
      :task="task"
      :key="infoVisible"
      :show="infoVisible"
      :show-divide="infoDivideButtonVisible"
      :show-force="infoForceButtonVisible")
    Form(
      @close="hideForm"
      @save="save"
      :show="formVisible"
      :task="task"
      :key="formVisible")
</template>

<style scoped>
td, th {
  @apply w-1/5 p-3
}
td {
  @apply align-top
}
th {
  @apply text-left
}
.head-controls {
  @apply flex justify-center items-center gap-4 text-center text-xl w-full
}
.plus {
  @apply absolute right-2
}
.checkbox-container {
  @apply absolute left-2 p-2 flex justify-center items-center gap-2 text-base
}
</style>