require('./bootstrap');

window.Vue = require('vue')
import {Loading,Message, Form, FormItem, Input, Button} from 'element-ui';
Vue.prototype.$message = Message;
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Input)
Vue.use(Button)
Vue.use(Loading)
import Vuex from 'vuex'
Vue.use(Vuex)
const store = new Vuex.Store({
    state: {
        app: 'Gallery',
    },
    getters: {
        APP_NAME(state){
            return state.app
        }
    }
})
new Vue({
    store,
    render: h => h(require('./components/Auth/Login.vue').default)
}).$mount('#login');