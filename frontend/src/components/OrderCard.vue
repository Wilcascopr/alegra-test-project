<script setup>
import { defineProps } from 'vue';
const props = defineProps(['order']);
</script>

<template>
  <v-card>
    <div class="card-body">
      <v-card-item>
        <v-card-title>Order #{{ order.id }}</v-card-title>
        <v-card-subtitle>Status: {{ order.status }}</v-card-subtitle>
      </v-card-item>
      <v-card-text>
        <v-timeline align="start" density="compact">
            <v-timeline-item
              v-for="dish in order.items"
              :key="dish.pivot.id"
              size="x-small"
            >
              <div class="mb-4">
                <div class="font-weight-normal">
                  <strong>{{ dish.name }}</strong> &times; {{ dish.pivot.quantity }} <br>
                  <small>{{ dish.description }}</small>
                </div>
                <div v-if="dish.ingredients" class="d-flex flex-wrap">
                    <v-chip
                      v-for="ingredient in dish.ingredients"
                      :key="ingredient.id"
                      size="x-small"
                    >
                      {{ ingredient.name }}: {{ ingredient.pivot.quantity }}
                    </v-chip>
                </div>
              </div>
            </v-timeline-item>
          </v-timeline>
      </v-card-text>
    </div>
  </v-card>
</template>