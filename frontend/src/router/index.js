import { createRouter, createWebHistory } from 'vue-router'
import HomeLayout from '../views/HomeLayout.vue'
import OrdersView from '../views/OrdersView.vue'
import OrderView from '../views/OrderView.vue'
import IngredientsView from '../views/IngredientsView.vue'
import RecipesView from '../views/RecipesView.vue'
import PurchasesView from '../views/PurchasesView.vue'
import AddOrderView from '../views/AddOrder.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeLayout,
      children: [
        {
          path: 'orders',
          name: 'orders',
          component: OrdersView
        },
        {
          path: 'orders/:id',
          name: 'order',
          component: OrderView
        },
        {
          path: 'ingredients',
          name: 'ingredients',
          component: IngredientsView
        },
        {
          path: 'recipes',
          name: 'recipes',
          component: RecipesView
        },
        {
          path: 'purchases',
          name: 'purchases',
          component: PurchasesView
        },
        {
          path: 'create',
          name: 'createOrder',
          component: AddOrderView
        }
      ],
    }
  ]
})

export default router
