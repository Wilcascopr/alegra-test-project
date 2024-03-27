<script setup>
    defineProps(['message', 'alertType'])
</script>

<template>
    <v-snackbar 
        v-bind="$attrs" 
        :color="alertType ==='success' ? 'green' : 'red'"
    >
        <v-icon v-if="alertType ==='success'">
            mdi-check-circle
        </v-icon>
        <v-icon v-else>
            mdi-alert
        </v-icon>
        {{ message }}
    </v-snackbar>
</template>