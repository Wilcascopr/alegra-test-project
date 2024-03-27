<script setup>
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
</script>

<template>
    <v-app-bar>
        <v-btn v-for="(item, idx) of routes" :key="idx" :to="item.path">
            {{ item.name }}
        </v-btn>
    </v-app-bar>
    <div style="margin-bottom: 84px;" />
    <router-view></router-view>
</template>