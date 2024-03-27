<script setup>
import { reactive, ref } from 'vue';
import { getIngredients } from '@/services/api/backend.js'
import AlertMessage from '@/components/AlertMessage.vue'
import IngredientCard from '@/components/IngredientCard.vue'
import Loader from '../components/LoaderComponent.vue';
const alert = reactive({
  message: '',
  alertType: '',
  value: false
})
const loader = ref(true);
const ingredients = ref([]);
const getIngredientsClient = async () => {
    loader.value = true
    try {
        const { data } = await getIngredients()
        ingredients.value = data
    } catch (error) {
        alert.message = 'There was an error getting the ingredients' 
        alert.alertType = 'error'
        alert.value = true
    } finally {
        loader.value = false
    }
}
getIngredientsClient()
</script>

<template>
  <main>
    <v-container>
      <h2>Ingredients</h2>
      <div v-if="ingredients.length" class="cards-container">
        <IngredientCard v-for="ingredient in ingredients" :key="ingredient.id" :ingredient="ingredient" />
      </div>
      <Loader v-else-if="loader" />
      <div v-else>
        <p>No ingredients were found</p>
      </div>
      <AlertMessage v-if="alert.value" :message="alert.message" :alertType="alert.alertType" />
    </v-container>
  </main>
</template>
