<script setup>
import { reactive, ref } from 'vue';
import { getRecipes } from '@/services/api/backend.js'
import AlertMessage from '@/components/AlertMessage.vue'
import RecipeCard from '@/components/RecipeCard.vue'
import Loader from '../components/LoaderComponent.vue';
const alert = reactive({
  message: '',
  alertType: '',
  value: false
})
const loader = ref(true);
const recipes = ref([]);
const getRecipesClient = async () => {
    loader.value = true
    try {
        const { data } = await getRecipes()
        recipes.value = data
    } catch (error) {
        alert.message = 'There was an error getting the recipes' 
        alert.alertType = 'error'
        alert.value = true
    } finally {
        loader.value = false
    }
}
getRecipesClient()
</script>

<template>
  <main>
    <v-container>
      <h2>Recipes</h2>
      <div v-if="recipes.length" class="cards-container" >
        <RecipeCard v-for="recipe in recipes" :key="recipe.id" :recipe="recipe" />
      </div>
      <Loader v-else-if="loader" />
      <div v-else>
        <p>No recipes were found</p>
      </div>
      <AlertMessage v-if="alert.value" :message="alert.message" :alertType="alert.alertType" />
    </v-container>
  </main>
</template>
