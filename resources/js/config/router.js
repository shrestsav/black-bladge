import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import dashboard from '../components/dashboard.vue'
import appDefaults from '../components/appDefaults/index.vue'

import drivers from '../components/drivers/index.vue'
import createDriver from '../components/drivers/create.vue'

import customers from '../components/customers/index.vue'
import unverifiedCustomers from '../components/customers/unverifiedCustomers.vue'

import vehicles from '../components/vehicles/index.vue'
import createVehicle from '../components/vehicles/create.vue'








import orders from '../components/orders/index.vue'
import orderDetails from '../components/orders/details.vue'

import createOrder from '../components/orders/create.vue'

import reports from '../components/reports/index.vue'
import promoCoupons from '../components/coupon/promoCoupons.vue'
import referralCoupons from '../components/coupon/referralCoupons.vue'
import pushNotification from '../components/pushNotification/index.vue'

const routes = [
  {name:'dashboard',  path: '/', component: dashboard },

  {name:'appDefaults',  path: '/v/appDefaults', component: appDefaults },

  {name:'orders',  path: '/v/orders', component: orders },
  {name:'createOrder',  path: '/v/orders/create', component: createOrder },
  {name:'orderDetails',  path: '/v/orders/details', component: orderDetails },

  {name:'customers',  path: '/v/customers', component: customers },
  {name:'unverifiedCustomers',  path: '/v/unverifiedCustomers', component: unverifiedCustomers },
  
  {name:'drivers',  path: '/v/drivers', component: drivers },
  {name:'createDriver',  path: '/v/drivers/create', component: createDriver },

  {name:'vehicles',  path: '/v/vehicles', component: vehicles },
  {name:'createVehicle',  path: '/v/vehicles/create', component: createVehicle },

  {name:'reports',  path: '/v/reports', component: reports },

  {name:'promoCoupons',  path: '/v/promoCoupons', component: promoCoupons },
  {name:'referralCoupons',  path: '/v/referralCoupons', component: referralCoupons },

  {name:'pushNotification',  path: '/v/push-notification', component: pushNotification },

]

export const router = new VueRouter({
	mode: 'history',
  	routes // short for `routes: routes`
})
