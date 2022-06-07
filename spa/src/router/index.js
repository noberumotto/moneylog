import Vue from 'vue'
import VueRouter from 'vue-router'
import Index from '../views/Index.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Bank from '../views/Bank.vue'
import Category from '../views/Category.vue'
import Chart from '../views/Chart.vue'
import List from '../views/List.vue'
import Setting from '../views/Setting.vue'
import Password from '../views/Password.vue'
import Waitsync from '../views/Waitsync.vue'
import Import from '../views/Import.vue'
import Edit from '../views/Edit.vue'


import store from '../store'
Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Index',
    component: Index
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/bank',
    name: 'Bank',
    component: Bank
  },
  {
    path: '/category',
    name: 'Category',
    component: Category
  },
  {
    path: '/chart',
    name: 'Chart',
    component: Chart
  }
  , {
    path: '/list',
    name: 'List',
    component: List
  },
  {
    path: '/setting',
    name: 'Setting',
    component: Setting
  },
  {
    path: '/password',
    name: 'Password',
    component: Password
  },
  {
    path: '/waitsync',
    name: 'Waitsync',
    component: Waitsync
  },
  {
    path: '/import',
    name: 'Import',
    component: Import
  },
  {
    path: '/edit',
    name: 'Edit',
    component: Edit
  },
]

const router = new VueRouter({
  routes
})

const noLoginUrl = [
  'Login',
  'Register'
];
router.beforeEach((to, from, next) => {

  if (!store.state.user.isLogin && !noLoginUrl.includes(to.name) && !localStorage.getItem('Token')) {
    // 无权访问
    next({ name: 'Login' });
  }
  else {
    next();
  }


})
export default router
