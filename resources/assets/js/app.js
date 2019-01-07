require('./bootstrap');

window.Vue = require('vue')
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)

Vue.component('chat-component', require('./components/Chat.vue'))
Vue.component('private-chat', require('./components/PrivateChat.vue'))


const app = new Vue({
    el: '#app'
});
