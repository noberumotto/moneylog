import Vue from 'vue'
import Modal from './Modal.vue'
const v = Vue.extend(Modal)
function modal(options) {
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

export default modal