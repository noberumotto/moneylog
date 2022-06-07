import Vue from 'vue'
import Modal from './DatePicker.vue'
const v = Vue.extend(Modal)
function datepicker(options) {
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

export default datepicker