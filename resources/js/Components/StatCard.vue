<template>
  <div class="bg-white rounded-lg shadow p-6 flex items-center gap-4">
    <div class="text-3xl">{{ icon }}</div>
    <div>
      <p class="text-sm text-gray-500">{{ label }}</p>
      <p class="text-2xl font-bold text-gray-800">{{ value }}</p>
      <p v-if="sousTexte" class="text-xs text-gray-400 mt-1">{{ sousTexte }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [String, Number],
  icon: String,
  sousTexte: String,
});
</script>