<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
defineProps(['items'])
const emit = defineEmits(['deleteItem'])
const handleClick = (item) => {
  if (item?.link) {
    router.push(item.link)
  } else if (item?.event) {
    emit(item.event)
  }
}
</script>

<template>
    <v-menu>
      <template v-slot:activator="{ props }">
        <v-btn
          color="primary"
          v-bind="props"
        >
          Options
        </v-btn>
      </template>
      <v-list>
        <v-list-item
          v-for="(item, index) in items"
          :key="index"
          :value="index"
          @click="handleClick(item)"
        >
          <v-list-item-title>
            {{ item.title }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
</template>

<style>
  .no-underline {
    text-decoration: none;
    color: inherit;
  }
</style>