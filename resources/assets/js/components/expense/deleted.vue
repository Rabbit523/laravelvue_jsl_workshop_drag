<template>

  <div>


    <c-panel title="Deleted Fuel">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
            
            <!-- <c-button @click="$router.push('/dispatchfuel/' + pageid + '/' + dispatch.equipment_id)" type="primary" outline>Add Fuel</c-button> -->
            
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

          
          <div class="u-text-left" slot="footer">
            
          </div>

        </div>

    </c-panel>

    
    
    <c-panel type="ghost">

     

      <c-panel title="Trash">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="30%">Fuel Refill Date</td>
                <td width="20%">Amount</td>
                <td width="20%">Created By</td>
                <td width="20%">Deleted By</td>
              </tr>

              <!-- <tr v-if="list.isApproved == 0, list.equipment_id == sequip.id" v-for="(list, index) in dispatch.expense"> -->
                <tr v-if="list.status == 0" v-for="(list, index) in dispatch.expense">

                <td>{{ format(list.expenseDate) }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.createdBy.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td><c-badge :type="list.updatedBy.tagColor">{{ list.updatedBy.name }}</c-badge></td>
              </tr>

            </tbody>

          </table>

      </c-form-field>

    </c-panel>

     

      </c-panel>

      <c-modal title="Reason to Flag" placement="center" size="small" v-model="reasonModel">
        Reason to flag:

        <c-form-field >

            <textarea class="form-textarea" v-model="reason"
                :disabled="isDisabled" />

        </c-form-field>

        <div class="u-text-right" :hidden="isHidden" slot="footer">
          <c-button type="primary" @click="flagIt()" v-once smart>Confirm</c-button>
        </div>
      </c-modal>

 


  </div>
</template>
<script>
var moment = require('moment');

import { format, addDays } from 'date-fns';
import { velocity } from 'velocity-animate';

import {Form, Errors} from "./../common/base.js";
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
import style2 from './../../../vendors/cover/vendors/flatpickr/style.css';

  export default {

    data() {
      return {


        dispatch: new Form({

          expense: [],
        }),

        user: this.$auth.user(),

        reasonModel: false,
        reason: '',
        isDisabled: false,
        isHidden: false,

        isLoading: false,
        content: '',
        duration: '',
      

        pageTitle: 'Expense Management',
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
        basePost: '',
        postUrl: '',
        pageid: '',

        toast: '',
        
      }
    },

    components: {

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr

    },
    
    created() {

      this.$root.$emit('UpdateWaiting');
       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

        this.getDeletedData();

    },
    methods: {

        getDeletedData(){

          this.loading('show');

          axios

            .get('/expense/deleted')

            .then(response => {
              
              // this.dispatch.expense = response.data;
              this.dispatch.expense = response.data.data;

              this.loading();

            })

            .catch(error => {

              this.errortoast();

              this.loading();

          });

        },

        expenses(item){
          
          return this.dispatch.expense.filter(function(elem){
                if(elem.equipment_id == item && elem.isApproved == 0) return elem;
            });

        },

        format(date){

          return format(date, 'DD-MM-YYYY');

        },

        setEdit(pageid){

          this.postUrl = this.api + this.controller + pageid;

          this.pageTitle = this.controllerName + ' > Edit';

          this.isDisabled = true;

          this.pageid = pageid;

        },

        showToast(message = null){

          // Hide Loading Toast and Loading from Button
          this.loading();

          // Show success toast
          if(message == null){

            this.$toast.succeed(this.content = this.controllerName + ' Data Saved Successfully', this.duration = 2000);

          } else {

            this.$toast.succeed(this.content = message, this.duration = 2000);

          }

        },

        loading(show = null) {

              this.isLoading = true;

              const vm = this.toast;

              if(show != null){

                this.toast = this.$toast.loading('Saving...');

              } else {

                this.isLoading = false;
                this.toast.hide();

              }

          },

          errortoast(message, delay){

              if(message == null){
                var message = 'Please correct errors!';
                var delay = 2000;
              }
              this.$toast.failed(message, delay)

          },

          

      },

      computed: {

          flagged: function () {

          return this.dispatch.expense.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1) return  elem;
            });
          
        },
        

      },

  }
</script>