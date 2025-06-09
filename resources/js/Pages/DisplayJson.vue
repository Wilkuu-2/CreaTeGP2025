<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps( {
  name: String,
  json: Object,
})

</script>

<template title="Json display">
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Displaying json: {{name}}
            </h2>
        </template>
        <pre>{{json}}</pre>
    </AppLayout>
</template>
