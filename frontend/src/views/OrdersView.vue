<script setup>
import { reactive, ref, watch } from 'vue';
import { getOrders } from '@/services/api/backend.js'
import AlertMessage from '@/components/AlertMessage.vue'
import Menu from '@/components/MenuComponent.vue'
import Loader from '@/components/LoaderComponent.vue'

const alert = reactive({
  message: '',
  alertType: '',
  value: false
})
const paginator = reactive({
  page: 1,
  lastPage: 1
})
const loader = ref(true);
const orders = ref([]);
const headersOrders = [
    { name: 'Id', key: 'id' },
    { name: 'Status', key: 'status' },
    { name: 'Dishes', key: 'items'},
    { name: 'Creation Date', key: 'created_at'},
    { name: 'Update Date', key: 'updated_at' },
    { name: 'Actions', key: 'actions' }
];
const getOrdersClient = async () => {
    loader.value = true
    try {
        const { data } = await getOrders(paginator.page)
        orders.value = data.data.map(order => ({
            id: order.id,
            status: order.status,
            items: order.items,
            created_at: (new Date(order.created_at)).toLocaleString(),
            updated_at: (new Date(order.updated_at)).toLocaleString(),
        }))
        paginator.lastPage = data.last_page
        paginator.page = data.current_page
    } catch (error) {
        alert.message = 'An error occurred while trying to fetch the orders'
        alert.alertType = 'error'
        alert.value = true
    } finally {
        loader.value = false
    }
}
watch(paginator, () => {
    getOrdersClient()
})
getOrdersClient()
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
                        <h3>Orders</h3> 
                    </div>
                </tr>
                <tr>
                    <th 
                        v-for="item, index in headersOrders"
                        :key="index"
                        class="text-left"
                    >
                        {{ item.name }}
                    </th>
                </tr>
            </thead>
            <tbody v-if="orders.length">
                <tr
                    v-for="itemOrder, index in orders"
                    :key="index"
                >
                    <td v-for="itemHeader, index in headersOrders" :key="index">
                        <div v-if="itemHeader.key === 'items'">
                            <small v-for="dish in itemOrder.items" :key="dish.id">
                                <strong>{{ dish.name }}</strong> &times; {{ dish.pivot.quantity }} <br>
                            </small>
                        </div>
                        <div v-else-if="itemHeader.key === 'actions'">
                            <Menu
                                :items="[
                                    { title: 'View details', link: `/orders/${itemOrder.id}` },
                                ]"
                            />
                        </div>
                        <div v-else>
                            {{ itemOrder[itemHeader.key] }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </v-table>
        <v-container>
            <v-pagination
                v-if="orders.length"
                v-model="paginator.page"
                :length="paginator.lastPage"
            />
            <Loader v-else-if="loader" />
            <div v-else>
                no records were found
            </div>
        </v-container>
    </v-container>
    <AlertMessage v-model="alert.value" :message="alert.message" :alertType="alert.alertType" />
  </main>
</template>
