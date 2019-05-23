require('./bootstrap');
window.Vue = require('vue')
import {Loading,Message, Form, FormItem, Input, Button} from 'element-ui';
import Vuex from 'vuex'
Vue.prototype.$message = Message;
Vue.use(Form)
Vue.use(Vuex)
Vue.use(FormItem)
Vue.use(Input)
Vue.use(Button)
Vue.use(Loading)
const store = new Vuex.Store({
    state: {
        app: 'Gallery',
        validate: {
            required: [
                {required: true}
            ]
        },
    },

    getters:{
        APP_NAME(state){
            return state.app
        },
        validate(state){
            return state.validate
        }
    }
})
new Vue({
    store,
    render: h => h(require('./components/Auth/Register.vue').default)
}).$mount('#register');