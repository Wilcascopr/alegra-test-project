<script setup>
import { defineProps } from 'vue';
defineProps(['ingredient']);
</script>
<template>
  <v-card class="card-dimensions-ingredient">
    <v-card-item>
      <v-card-title>Name: {{ ingredient.name }}</v-card-title>
    </v-card-item>
    <v-card-text>
      <p>Stock: {{ ingredient.quantity }}</p>
    </v-card-text>
  </v-card>
</template>