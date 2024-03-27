<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { createOrder, getRandomRecipes } from '@/services/api/backend.js'
import AlertMessage from './AlertMessage.vue';
import LoaderComponent from './LoaderComponent.vue';

const router = useRouter();
const alert = reactive({
  message: '',
  alertType: '',
  value: false
})
const amount = ref(1)
const loader = ref(false)
const orderItems = [];

const getRandomDishes = async () => {
    try {
        const { data } =await getRandomRecipes(amount.value)
        data.forEach((recipe) => {
                orderItems.push({
                    recipe_id: recipe.id,
                    quantity: recipe.quantity
                })
            })
    } catch (error) {
        alert.value = true
        alert.alertType = 'error'
        alert.message = 'There was an error selecting the random dishes for the order'
        setTimeout(() => {
            alert.value = false
        }, 3000)
    }
}

const createOrderClient = () => {
    createOrder({
        order: {
            items: orderItems
        }
    })
        .then((data) => {
            console.log(data)
            alert.value = true
            alert.alertType = 'success'
            alert.message = data.message
            setTimeout(() => {
                alert.value = false
                loader.value = false
                router.push(`/orders/${data.order.id}`)
            }, 3000)
        })
        .catch((err) => {
            loader.value = false
            alert.value = true
            alert.alertType = 'error'
            alert.message = err?.response?.data?.message ? err.response.data.message : 'There was an error creating the order'
            setTimeout(() => {
                alert.value = false
            }, 3000)
        })
}

const handleSubmit = async () => {
    loader.value = true
    if (amount.value < 0 || amount.value > 20) {
        alert.value = true
        alert.alertType = 'error'
        alert.message = 'The maximum amount of dishes is 20 and the minimum is 1'
        setTimeout(() => {
            alert.value = false
        }, 3000)
    } 
    try {
        await getRandomDishes()
        createOrderClient()
    } catch (error) {
        alert.value = true
        alert.alertType = 'error'
        alert.message = 'There was an error creating the order'
        setTimeout(() => {
            alert.value = false
        }, 3000)
    
    }

}

</script>

<template>
    <v-container>
        <v-form v-if="!loader">
            <h3 class="text-center">Order Form</h3>
            <h4 class="text-center">The order will be generated at random, selecting the amount of dishes you set.</h4>
            <v-row justify="center">
                <v-col cols="12" sm="6" md="4">
                    <v-text-field
                        v-model="amount"
                        label="Amount of dishes to order"
                        type="number"
                        min="1"
                        max="20"
                        outlined
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col cols="12" sm="6" md="4">
                    The maximum amount of dishes is 20
                </v-col>
            </v-row>
            <v-row justify="center">
                <v-col cols="12" sm="6" md="4">
                    <v-btn @click="handleSubmit" color="primary" block>
                        Save
                    </v-btn>
                </v-col>
            </v-row>
        </v-form>
        <LoaderComponent v-else />
        <AlertMessage
            v-model="alert.value" :message="alert.message" :alertType="alert.alertType"
        />
    </v-container>
</template>