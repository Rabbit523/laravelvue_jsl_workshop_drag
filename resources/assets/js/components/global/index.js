window.Vue = require('vue');

import offline from 'v-offline';
Vue.component('offline', offline);

import Multiselect from './../../../vendors/cover/vendors/multiselect';
import './../../../vendors/cover/vendors/multiselect/style.css';
Vue.component('c-multiselect', Multiselect);

import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
import './../../../vendors/cover/vendors/flatpickr/style.css';
Vue.component('c-flatpickr', FlatPickr);

Vue.component('c-flatpickr'. FlatPickr,)

import basictable from "./../common/basictable.vue";
Vue.component('basictable', basictable);

import tableheader from "./../common/header.vue";
Vue.component('tableheader', tableheader);

import paginations from "./../common/pagination.vue";
Vue.component('paginations', paginations);

import { format, addDays } from 'date-fns';

import Vue2Storage from 'vue2-storage'

Vue.use(Vue2Storage, {
  prefix: 'app_',
  driver: 'local',
  ttl: 60 * 60 * 24 * 1000
})

import axios from 'axios';

import tableExport from 'tableexport';
