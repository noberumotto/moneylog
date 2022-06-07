import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store/index';
import toast from "./components/Toast/index";
import showLoading from "./components/Loading/index";
import request from "./library/request";
import modal from "./components/Modal/index";
import common from "./library/common";
import datepicker from "./components/DatePicker/index";
import wselect from "./components/WSelect/index"

Vue.config.productionTip = false
Vue.prototype.$toast = toast
Vue.prototype.$loading = showLoading
Vue.prototype.$request = request
Vue.prototype.$modal = modal
Vue.prototype.$common = common
Vue.prototype.$datepicker = datepicker
Vue.prototype.$wselect = wselect

window.$store = store;
window.$router = router;
window.$request = request;
window.$toast = toast;

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
