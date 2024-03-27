<script setup>
import { defineProps }  from 'vue';
defineProps(['recipe']);
</script>

<template>
  <v-card class="card-dimensions">
    <v-card-item>
      <v-card-title>Recipe #{{ recipe.id }}</v-card-title>
      <v-card-subtitle>Name: {{ recipe.name }}</v-card-subtitle>
    </v-card-item>
    <v-card-text>
      <h3>Description:</h3>
      <p>{{ recipe.description }}</p>
      <h3>Ingredients:</h3>
      <div class="d-flex flex-wrap">
        <v-chip v-for="ingredient in recipe.ingredients" :key="ingredient.id" class="mr-1 my-1">
          {{ ingredient.name }}: {{ ingredient.pivot.quantity }}
        </v-chip>
      </div>
    </v-card-text>
  </v-card>
</template>