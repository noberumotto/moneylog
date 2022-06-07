import Vue from 'vue'
import Modal from './WSelect.vue'
const v = Vue.extend(Modal)
function wselect(options) {
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

export default wselect