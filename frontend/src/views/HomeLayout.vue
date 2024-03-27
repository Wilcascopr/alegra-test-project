<script setup>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
const route = useRoute();
const currentRoute = ref(route.name);
const routes = [
    {
        path: '/orders',
        name: 'Orders'
    },
    {
        path: '/recipes',
        name: 'Recipes'
    },
    {
        path: '/purchases',
        name: 'Purchases'
    },
    {
        path: '/ingredients',
        name: 'Ingredients'
    },
    {
        path: '/create',
        name: 'Create Order'
    },
];
watch(() => route.name, (newVal) => {
    currentRoute.value = newVal;
});
</script>

<template>
    <v-app-bar>
        <v-btn v-for="(item, idx) of routes" :key="idx" :to="item.path">
            {{ item.name }}
        </v-btn>
    </v-app-bar>
    <div style="margin-bottom: 84px;" />
    <div v-if="currentRoute == 'home'" style="text-align: center;">
        <h1>Welcome to the Home Page</h1>
        <h2>Lunch Day Free! Project</h2>
    </div>
    <router-view></router-view>
</template>