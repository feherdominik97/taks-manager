<script setup lang="ts">
import {onMounted, ref} from "vue";

const runtimeConfig = useRuntimeConfig()
const apiUrl = runtimeConfig.public.apiBase
const tasks = ref([])

const getTasks = async () => {
  $fetch(`${apiUrl}/tasks`,)
      .then(response => {
        tasks.value = response
  })
}

onMounted(async () => {
  await getTasks()
})
</script>

<template lang="pug">
  div(v-if="tasks.length")
    TaskTable(:tasks="tasks")
</template>

<style scoped>

</style>