<script setup>
import { ref, reactive } from 'vue'
import { useRoute } from 'vue-router';
import { getOrder } from '@/services/api/backend.js'
import OrderCard from '@/components/OrderCard.vue'
import Loader from '../components/LoaderComponent.vue';
import AlertMessage from '../components/AlertMessage.vue';
const route = useRoute()
// get id from the route
const id = route.params.id
const order = ref(null)
const loader = ref(true)
const alert = reactive({
    message: '',
    alertType: '',
    value: false
})
const getOrderClient = async () => {
    try {
        const { data } = await getOrder(id)
        order.value = data
    } catch (error) {
        alert.message = 'There was an error getting the order' 
        alert.alertType = 'error'
        alert.value = true
    } finally {
        loader.value = false
    }
}
getOrderClient()
</script>

<template>
  <main>
    <v-container>
      <div v-if="order">
        <OrderCard :order="order" />
      </div>
      <Loader v-else-if="loader" />
      <div v-else>
        <p>No order with the id: {{ id }} was found</p>
      </div>
      <AlertMessage v-if="alert.value" :message="alert.message" :alertType="alert.alertType" />
    </v-container>
  </main>
</template>