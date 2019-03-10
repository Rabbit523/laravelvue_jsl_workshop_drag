
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Cover = require('./../vendors/cover/cover.js');

import './../vendors/cover/cover.css';
import VueRouter from 'vue-router';
import routes from './router';
import axios from 'axios';
import VueAxios from 'vue-axios';

import './components/global'

Vue.use(VueRouter);
Vue.use(VueAxios, axios);



/**
 * Custom Global Vue Helper for drag and drop.
 */
import Helpers from './Support/Helpers';
Vue.use(Helpers);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import App from './components/App.vue';

Vue.use(VeeValidate, config);
import VeeValidate from 'vee-validate';

const config = {

  aria: true,
  classNames: {},
  classes: false,
  delay: 0,
  dictionary: null,
  errorBagName: 'verrors', // change if property conflicts
  events: 'input|blur',
  fieldsBagName: 'fields',
  i18n: null, // the vue-i18n plugin instance
  i18nRootKey: 'validations', // the nested key under which the validation messsages will be located
  inject: true,
  locale: 'en',
  strict: true,
  validity: false,

};

axios.defaults.baseURL = '/api';

const router = new VueRouter({
  routes // short for `routes: routes`
});

Vue.router = router

Vue.use(require('@websanova/vue-auth'), {
   authRedirect: { name: 'auth.login' },
   auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
   http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
   router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
});

var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

if(getUrl.hash == '#/'){

  window.location.href = baseUrl + '#/login';
  
}

Vue.axios.interceptors.response.use((response) => { // intercept the global error
  
    return response
  
  }, function (error) {

    let originalRequest = error.config

    if (error.response.status === 401 && !originalRequest._retry) { // if the error is 401 and hasent already been retried

      originalRequest._retry = true // now it can be retried 


      return Vue.axios.post('/users/token', null).then((data) => {

        store.dispatch('authfalse') 
        store.dispatch('authtruth', data.data)
        originalRequest.headers['Authorization'] = 'Bearer ' + store.state.token // new header new token
        return Vue.axios(originalRequest) // retry the request that errored out

      }).catch((error) => {

        for (let i = 0; i < error.response.data.errors.length; i++) {
          if (error.response.data.errors[i] === 'TOKEN-EXPIRED') {
            auth.logout()
            return
          }

        }

      })

    }
    if (error.response.status === 405 || error.response.status == 401 && !originalRequest._retry) {
      originalRequest._retry = true
      window.location.href = baseURL + '#/login'
      //window.location.href = 'http://52.59.53.155/#/login'
      return
    }
    // Do something with response error
    return Promise.reject(error)
  })

const app = new Vue({
    el: '#app',
    data: {
      siderOpen: true,
      onlineStatus: true,
    },
    created(){

      this.$storage.setOptions({
        prefix: 'app_',
        driver: 'local',
        ttl: 60 * 60 * 24 * 1000
      })

    },
    // mounted() {

      // this.$on('UpdateWaiting', this.updateWaiting)

    // },
    
    router: router,
    VeeValidate: VeeValidate,
    render: t => t(App),
    
});
