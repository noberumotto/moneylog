import Vue from 'vue'
import Loading from './Loading.vue'
const loading = Vue.extend(Loading)

let instanceList = [];

function showLoading(options) {
    let t = new loading({
        propsData: options
    });
    t.$mount();

    instanceList.push(t);

    document.body.appendChild(t.$el);
    t.remove = function () {
        document.body.removeChild(t.$el);
        t.$destroy()
    }
    return t;
}
showLoading.closeAll = function () {
    instanceList.forEach(element => {
        element.hide();
    });
}
export default showLoading