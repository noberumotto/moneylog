import Vue from 'vue'
import Toast from './Toast.vue'
const v = Vue.extend(Toast)
function toast(options) {
    let t = new v({
        propsData: options
    });
    t.$mount();
    document.body.appendChild(t.$el);
    t.remove = function () {
        document.body.removeChild(t.$el);
        t.$destroy()
    }
    return t;
}

export default toast