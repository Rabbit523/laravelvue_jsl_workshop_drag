<template>

  <div>


    <c-panel :title="'Advance Slip'">

       <div>

          <c-form-field label="Fuel Suppler">

            <c-multiselect
                v-model="form.selectedSupplier"
                track-by="value"
                label="label"
                :options="supplier"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="form.errors.has('form.selectedSupplier')"></span>
        </c-form-field>

        <c-form-field label="Select Bill">

            <c-multiselect
                v-model="form.selectedBill"
                track-by="value"
                label="label"
                :options="sortedBills"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Bill"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('bill')" v-if="form.errors.has('form.bill')"></span>
        </c-form-field>

        <c-form-field label="Select Staff">

            <c-multiselect
                v-model="form.selectedStaff"
                track-by="value"
                label="label"
                :options="staff"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Staff"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('staff')" v-if="form.errors.has('form.staff')"></span>
        </c-form-field>

         <c-form-field label="Select Equipment">

            <c-multiselect
                v-model="form.selectedEquipment"
                track-by="value"
                label="label"
                :options="equipment"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Equipment"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('equipment')" v-if="form.errors.has('form.equipment')"></span>
        </c-form-field>


          <c-form-field label="Voucher Date">
            <c-form-input 
                name="voucher date" 
                v-validate="'required|date_format:DD-MM-YYYY'" 
                v-model="form.voucherDate" 
                type="text">
            </c-form-input>
            <small v-if="!errors.first('voucher date')">Format DD-MM-YYYY</small>
            <span class="form-help u-color-danger">{{ errors.first('voucher date') }}</span>
          </c-form-field> 

          <c-form-field label="Reference Number">
            <c-form-input 
                v-model="form.referenceNumber" 
                :status="checkErrors('reference number')" 
                name="reference number" 
                value="" 
                type="text">
            </c-form-input>
          </c-form-field>

          <c-form-field label="Amount">
            <c-form-input 
                v-validate="'required|decimal'"
                v-model="form.voucherAmount" 
                :status="checkErrors('voucherAmount')" 
                name="voucher amount" 
                value="" 
                type="text">
            </c-form-input>
            <span class="form-help u-color-danger">{{ errors.first('voucher amount') }}</span>
          </c-form-field>


          <c-form-field label="Remarks">
            <c-form-input 
                v-model="form.remarks" 
                name="remarks" 
                value="" 
                type="text">
            </c-form-input>
          </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button type="primary" @click="handleCreateAdvanceSlip()" v-once smart>Save</c-button>
          </div>

        </div>

    </c-panel>

  </div>
</template>
<script>

import { format, addDays } from 'date-fns';
import { velocity } from 'velocity-animate';

import {Form, Errors} from "./../common/base.js";
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

  export default {

    data() {
      return {

        form: new Form({

          selectedSupplier: undefined,
          selectedStaff: undefined,
          selectedEquipment: undefined,
          selectedBill: undefined,

          referenceNumber: undefined,
          voucherAmount: undefined,

          voucherDate: undefined,
          remarks: undefined,
          status: '',

        }),

        supplier: [],
        staff: [],
        equipment: [],
        allBills: [],
        
        isLoading: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Advance Slip',
        api: '',
        controller: '/advanceSlip',
        controllerName: 'advanceSlip',
        basePost: '',
        postUrl: '',
        pageid: '',

        toast: '',
        
      }
    },

    components: {

      'c-multiselect': Multiselect,

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

       this.getData();

    },
    methods: {

        getData(){

          this.getSupplier();

          this.getBills();

          this.getStaff();

          this.getEquipmnet();

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

        getStaff(){

          axios

            .get('/staff')

              .then(response => {

                this.staff = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getEquipmnet(){

          axios

            .get('/equipment')

              .then(response => {

                this.equipment = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

         getBills(){

          axios

            .get('/bill')

              .then(response => {

                this.allBills = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },
        

        handleCreateAdvanceSlip(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.form).then((result) => {
            
              if (result) {

                // no errors submit form
                this.submitFormSlip(button);
                return;

              } else {

                // Hide Loading Toast
                this.loading();

                this.errortoast();

              }

              // Hide Loading Toast
              this.loading();

          });
            
        },

        submitFormSlip(button = null){

            this.form.submit('post', this.api + this.controller).then(success=>{

                //this.showToast('Bill Created Successfully');

                this.showAlert('New Advance Slip Created, Reference Number: ' + success.id);

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

            this.$toast.succeed(this.content = this.controllerName + ' Data Saved Successfully', this.duration = 500);

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

          showAlert(message) {

              this.$alert({

                type: 'success',
                title: 'Success',
                content: message,

                onConfirm: () => {
                  // do something..
                  // if you want to stop closing alert, just return false
                }

              })

          },

          errortoast(message, delay){

              if(message == null){
                var message = 'Please correct errors!';
                var delay = 2000;
              }
              this.$toast.failed(message, delay)

          },

          checkErrors(field){

            if(this.errors.first(field)){
              return "danger";
            }

          },

          

      },

      computed: {

          sortedBills: function ()
          {

              if(this.form.selectedSupplier != undefined)
              {
                return this.allBills.filter(function(elem){
                    if(elem.status != 'Paid' && elem.expense_supplier_id == this.form.selectedSupplier.id) return elem;
                }.bind(this));
              }

              return [];
                
          }

      },

      watch: {


      }

  }
</script>