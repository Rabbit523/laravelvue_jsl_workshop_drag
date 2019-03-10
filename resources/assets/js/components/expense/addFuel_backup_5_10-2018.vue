<template>

  <div>


    <c-panel :title="'Fuel for <strong>' + equipmentNumber + '</strong>'">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          
          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

          <c-form-field label="Fuel Suppler">

            <c-multiselect
                v-model="selectedSupplier"
                track-by="value"
                label="label"
                :options="supplier"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
        </c-form-field>

        <c-form-field label="Select Bill" v-if="fuel.selectedSupplier != undefined">

            <c-multiselect
                v-model="fuel.selectedBill"
                track-by="id"
                label="label"
                :options="filteredBills"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
        </c-form-field>

        <c-form-field label="Fuel Rate">

          <c-form-input
            type="text"
            v-validate="'required'"
            v-model="globalRate">
          </c-form-input>

        </c-form-field>

        <c-form-field label="Oil Rate">

          <c-form-input
            type="text"
            v-validate="'required'"
            v-model="globalOilRate">
          </c-form-input>

        </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button v-if="checkforqty()" type="primary" @click="handleSubmitExpense()" v-once smart>Save All</c-button>
          </div>

        </div>

    </c-panel>

    <c-panel v-if="fuel.selectedSupplier != undefined" type="ghost">

      <div v-for="(i, index) in fuel.runFor">

         <c-panel :title="'Total: Rs ' + calcRate(index) + '/-'">

          <c-row class="u-pb-10 u-mt-10">

            <c-col lg="12" xl>          

              <!-- <c-form-field label="Equipment">
              <c-form-input
                  addon-start="Equipment Number"
                  type="text"
                  ref="search"
                  v-model="fuel.searched[index]">
                </c-form-input>
                <span>{{ selectedEquipmentError[index] }}</span>
              </c-form-field> -->

               <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Equipment Number</span>
                  <input 
                      type="text" 
                      v-model="fuel.searched[index]" 
                      ref="search"
                      class="form-input">
                </div>
              </div>

            </c-col>

            <c-col lg="12" xl>

            <c-form-field v-if="fuel.selectedEquipment[index] != undefined" label="Select Equipment">
              <c-multiselect
                v-model="fuel.selectedEquipment[index]"
                track-by="value"
                label="label"
                :options="equipments"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Equipments"></c-multiselect>
              </c-form-field>

            </c-col>

          </c-row>

            
          <c-row class="u-pb-10 u-mt-10" v-if="fuel.selectedEquipment[i] != undefined">

            <c-col lg="4" xl>


                  <c-form-input 
                      name="date"
                      addon-start="Date" 
                      v-validate="'required|date_format:DD-MM-YYYY'" 
                      v-model="fuel.date[index]" 
                      type="text">
                  </c-form-input>
                  <small v-if="!errors.first('date')">Format DD-MM-YYYY</small>
                  <span class="form-help u-color-danger">{{ errors.first('date') }}</span>

            </c-col>

            <c-col lg="4" xl>
              
              <c-form-input
                addon-start="Slip#"
                :class="fuel.colorclass"
                type="text"
                v-model="fuel.fuelData[index]['slip']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Fule Ltr" 
                v-validate="'required'"
                name="litre"
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData[index]['qty']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Fuel Rate" 
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData[index]['rate']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Oil Ltr" 
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData[index]['oQty']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <!-- <c-form-input
                addon-start="Oil Rate" 
                :class="fuel.colorclass"
                type="number"
                @keyup.enter="saveInTemp"
                v-model="fuel.fuelData[index]['oRate']">
              </c-form-input> -->

              <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Oil Rates</span>
                  <input 
                      type="text" 
                      v-model="fuel.fuelData[index]['oRate']" 
                      :class="fuel.colorclass"
                      @keyup.enter="saveInTemp"
                      class="form-input">
                </div>
              </div>


            </c-col>

          </c-row>

         </c-panel>

      </div>

      <span v-model="addMore"></span>

    </c-panel>    


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

        fuel: new Form({


          selectedSupplier: undefined,
          selectedEquipment: [],
          searched: [],
          selectedBill: [],
          date: [],
          runFor: [0],
          fuelData: [],

        }),

        temporaryStorage: [],

        selectedEquipmentError: [],
        selectedSupplier: undefined,
        supplier: [],
        equipments: [],
        bills: [],
        filteredBills: [],
        globalRate: '',
        globalOilRate: '',
        
        isLoading: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Fuel Management',
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
        basePost: '',
        postUrl: '',
        pageid: '',

        toast: '',

        equipmentNumber: 'Loading...',
        
      }
    },

    components: {

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

       this.getData();

    },
    methods: {

        getData(){

          this.getSupplier();
          this.getEquipments();
          this.getSupplierBill();

        },

        getSupplier(){

          axios
            .get('/expensesupplier')

              .then(response => {

                this.supplier = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getSupplierBill(){

          axios
            .get('/bill')

              .then(response => {

                this.bills = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        saveInTemp(){

          console.log('hit');
          console.log(this.fuel.fuelData);
          this.$refs.search.focus();

        },
        
        getEquipments(){

          axios
            .get('/equipment')

            .then(response => {

                this.equipments = response.data.data;

                
            }).catch(error => {

                this.errortoast();

            });

        },

        addDataFields(runFor){

          var i;
          for (i = 0; i < runFor; i++){
              
              if(!this.fuel.fuelData[i]){
              
                  this.fuel.fuelData.push({

                     qty: '',
                     slip: '',
                     rate: '',
                     oRate: '',
                     oQty: '',

                  });

              }

            }

        },

        TotalAmount(index) {

          if(this.fuel.fuelData[index]){
            console.log(this.calcRate(index));
          }

          // if(this.fuel.fuelData[index]){
          
          //   return this.fuel.fuelData[index].qty.reduce((total, item) => {
          //     return total + Number(item);
              
          //   }, 0);
          // }

        },

        calcRate(index) {

          var data = [];

          if(this.fuel.fuelData[index]){

            var data = this.fuel.fuelData[index];
            var qty = this.fuel.fuelData[index]['qty'];
            var rate = this.fuel.fuelData[index]['rate'];

            var oQty = this.fuel.fuelData[index]['oQty'];
            var oRate = this.fuel.fuelData[index]['oRate'];

            var fuelArray = [];
            var oilArray = [];

              if(qty > 0 && rate > 0){

                 data['total'] = (qty * rate).toFixed(2);
                 fuelArray =  (qty * rate).toFixed(2);
                  
               }


            if(oQty > 0 && oRate > 0){

                var oilAmount =  (oQty * oRate).toFixed(2);

                if(fuelArray > 0){
                  data['total'] = (Number(fuelArray) + Number(oilAmount)).toFixed(2);
                } else {
                  data['total'] = Number(oilAmount).toFixed(2);
                }

            }


          } 

          if(data['total'] == undefined){
            data['total'] = Number(0);
          }

          return data['total'];

        },

        checkforqty(beforeSave){

          var data = this.fuel.fuelData;
          
          for (let index in this.fuel.runFor){
            
            if(this.fuel.selectedEquipment[index] != undefined){

              if(data[index].qty == 0 || data[index].qty == undefined || data[index].qty == ''){

                if(beforeSave != null){
                  this.errortoast('Please check litre fields', 1000);
                }
                return false;

              } 

            }

          }

          return true;

        },

        handleSubmitExpense(button = null){

          var verification = this.checkforqty('beforeSave');

          if(verification){

          this.loading('Saving');

          this.$validator.validateAll(this.fuel).then((result) => {
            
              // no errors submit form
              this.submitFormTrips(button);

              return;

              // Hide Loading Toast
              this.loading();

          });

          }

            
        },

        submitFormTrips(button = null){

          this.fuel.submit('post', this.api + this.controller + 'saveAllFuelbySupplier/').then(success=>{

                this.showToast('Expense Added Successfully');

                this.loading('Saving');

                this.reset();
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        
        reset(){

          // Display success message
          this.showToast();

          // Reset form using form class
          Object.assign(this.$data, this.$options.data.call(this));

          setTimeout(() => {
            this.errors.clear();
            this.getData();
          }, 100);


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

        loading(message = null) {

              this.isLoading = true;

              const vm = this.toast;

              if(message != null){

                this.toast = this.$toast.loading(message + '...');

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

        
        addMore: function () {

            var l = this.fuel.runFor.length;
            var i;
            var needMore = true;

            this.addDataFields(l);


            for (i = 0; i < l; i++){

              if(this.fuel.selectedEquipment[i] == undefined){
                
                needMore = false;

              } 

            }

            if(needMore == true){
              this.addDataFields(l+1);
              this.fuel.runFor.push((l));
            }
          
        },


      },

      watch: {

        selectedSupplier(){

            this.fuel.selectedSupplier = this.selectedSupplier;
            var supplier = this.selectedSupplier;
            this.fuel.selectedBill = undefined;

            if(supplier != undefined){
              this.filteredBills = this.bills.filter(function(elem){
                  if(elem.expense_supplier_id == supplier.id) return elem;
              });
            } else {

            return [];

            }

        },

        fuel: {

           handler(form){

              var data = form.fuelData;
              for (let field in data){

                if(data[field].rate == ''){

                  data[field].rate = this.globalRate;
                  data[field].oRate = this.globalOilRate;

                }

             }

             var data = form.date;
             for (let index in data){

                if(data[index] != ''){
                  
                  if(data[index].length == 2){
                    data[index] = data[index] + '-';
                  }

                  if(data[index].length == 5){
                    data[index] = data[index] + '-';
                  }

                }

             }

             data = form.searched;

             for(let index in data){

              this.selectedEquipmentError[index] = '';
              
              if(data.length > 0 && data[index] != ''){

                  var searched = data[index];
                  var equipment = this.equipments.filter(function(elem){
                    if(elem.equipmentNumber.toLowerCase() == searched.toLowerCase()) return elem;
                  });

                  if(equipment.length == 1){

                    form.selectedEquipment[index] = equipment;
                    return true;

                  }

                  form.selectedEquipment[index] = undefined;

                  this.selectedEquipmentError[index] = 'Searching...';

                }

            }

           },
           deep: true
        }

      }

  }
</script>