<script setup>
import { reactive, ref, watch } from 'vue';
import { getPurchases, getIngredients } from '@/services/api/backend.js'
import AlertMessage from '@/components/AlertMessage.vue'
import Loader from '../components/LoaderComponent.vue';
const alert = reactive({
  message: '',
  alertType: '',
  value: false
})
const paginator = reactive({
  page: 1,
  lastPage: 1
})
const ingredientFilter = ref(null);
const loader = ref(true);
const purchases = ref([]);
const ingredients = ref([]);
const headersPurchases = [
    { name: 'Id', key: 'id' },
    { name: 'Amount bought', key: 'quantity' },
    { name: 'Ingredient', key: 'ingredient.name'},
    { name: 'Creation Date', key: 'created_at'},
    { name: 'Update Date', key: 'updated_at' },
];
const getPurchasesClient = async () => {
    loader.value = true
    purchases.value = []
    try {
        const { data } = await getPurchases(paginator.page, ingredientFilter.value)
        purchases.value = data.data.map(purchase => ({
            id: purchase.id,
            quantity: purchase.quantity,
            ingredient: purchase.ingredient,
            created_at: (new Date(purchase.created_at)).toLocaleString(),
            updated_at: (new Date(purchase.updated_at)).toLocaleString(),
        }))
        paginator.lastPage = data.last_page
        paginator.page = data.current_page
    } catch (error) {
        alert.message = 'Error al obtener los documentos'
        alert.alertType = 'error'
        alert.value = true
    } finally {
        loader.value = false
    }
}
const getIngredientsClient = async () => {
    try {
        const { data } = await getIngredients()
        ingredients.value = data
    } catch (error) {
        alert.message = 'There was an error getting the ingredients'
        alert.alertType = 'error'
        alert.value = true
    }
}

const formatCell = (item, header) => {
    if (header.key.includes('.')) {
        const keys = header.key.split('.')
        return keys.reduce((acc, key) => acc[key], item)
    }
    return item[header.key]
}

watch(ingredientFilter, (newVal, oldVal) => {
    if (newVal != oldVal) {
        paginator.page = 1
        getPurchasesClient()
        return;
    }
})
getIngredientsClient();
getPurchasesClient();
</script>

<template>
  <main>
    <v-container>
        <v-table
            fixed-header
        >                    
            <thead>
                <tr class="text-left"> 
                    <div>
                        <h3>Purchases</h3> 
                    </div>
                </tr>
                <tr>
                    <v-select
                        v-model="ingredientFilter"
                        :items="ingredients"
                        label="Filter by ingredient"
                        item-title="name"
                        item-value="id"
                        clearable
                    ></v-select>
                </tr>
                <tr>
                    <th 
                        v-for="item, index in headersPurchases"
                        :key="index"
                        class="text-left"
                    >
                        {{ item.name }}
                    </th>
                </tr>
            </thead>
            <tbody v-if="purchases.length">
                <tr
                    v-for="itemPurchase, index in purchases"
                    :key="index"
                >
                    <td v-for="itemHeader, index in headersPurchases" :key="index">
                        {{ formatCell(itemPurchase, itemHeader)}}
                    </td>
                </tr>
            </tbody>
        </v-table>
        <v-container >
            <v-pagination
                v-if="purchases.length"
                v-model="paginator.page"
                :length="paginator.lastPage"
                @input="getPurchasesClient"
            />
            <Loader v-else-if="loader"/>
            <div v-else>
                no records were found
            </div>
        </v-container>
    </v-container>
    <AlertMessage v-model="alert.value" :message="alert.message" :alertType="alert.alertType" />
  </main>
</template>
