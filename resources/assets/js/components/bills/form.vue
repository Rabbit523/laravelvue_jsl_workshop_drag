<template>

  <div>


    <c-panel :title="'Create Bill ' + form.status">

       <div>

          <c-form-field label="Fuel Suppler">

            <c-multiselect
                v-model="form.selectedSupplier"
                track-by="id"
                label="supplierName"
                :options="supplier"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="form.errors.has('form.selectedSupplier')"></span>
        </c-form-field>

        <c-form-field label="Bill Receiving Date">
            <c-form-input 
                name="receiving date" 
                v-validate="'required|date_format:DD-MM-YYYY'" 
                v-model="form.receivingDate" 
                type="text">
            </c-form-input>
            <small v-if="!errors.first('receiving date')">Format DD-MM-YYYY</small>
            <span class="form-help u-color-danger">{{ errors.first('receiving date') }}</span>
          </c-form-field> 


          <c-form-field label="Starting Date">
            <c-form-input 
                name="starting date" 
                v-validate="'required|date_format:DD-MM-YYYY'" 
                v-model="form.startingDate" 
                type="text">
            </c-form-input>
            <small v-if="!errors.first('starting date')">Format DD-MM-YYYY</small>
            <span class="form-help u-color-danger">{{ errors.first('starting date') }}</span>
          </c-form-field> 
              
          <c-form-field label="Ending Date">
              
              <c-form-input 
                  name="ending date" 
                  v-validate="'required|date_format:DD-MM-YYYY'" 
                  v-model="form.endingDate" 
                  type="text">
              </c-form-input>
              <small v-if="!errors.first('ending date')">Format DD-MM-YYYY</small>
              <span class="form-help u-color-danger">{{ errors.first('ending date') }}</span>
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

          <c-form-field label="Bill Amount">
            <c-form-input 
                v-validate="'required|decimal'"
                v-model="form.billAmount" 
                :status="checkErrors('billAmount')" 
                name="bill amount" 
                value="" 
                type="text">
            </c-form-input>
            <span class="form-help u-color-danger">{{ errors.first('bill amount') }}</span>
          </c-form-field>

          <c-form-field label="Adjustment Amount">
            <c-form-input 
                v-validate="'decimal'"
                v-model="form.adjustmentAmount" 
                name="adjustment amount" 
                value="" 
                type="text">
            </c-form-input>
            <span class="form-help u-color-danger">{{ errors.first('adjusment amount') }}</span>
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
            <c-button type="primary" @click="handleCreateBill()" v-once smart>Save</c-button>
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
          startingDate: undefined,
          endingDate: undefined,
          referenceNumber: undefined,
          billAmount: undefined,

          receivingDate: undefined,
          adjustmentAmount: undefined,
          remarks: undefined,
          status: '',

        }),

        supplier: [],
        
        isLoading: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Create Bill',
        api: '',
        controller: '/bill',
        controllerName: 'Bill',
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

       if(this.$route.params.id){

         this.pageid = this.$route.params.id;
         this.loading('Loading');
         this.getThisBill(this.$route.params.id);
         this.isDisabled = true;       

      } 

    },
    methods: {

        getData(){

          this.getSupplier();

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

        getThisBill(id)
        {

          axios

            .get(this.basePost + '/' + id)

              .then(response => {

                var data = response.data.data;

                for (let field in data ){

                   this.form[field] = data[field];

                }

                this.form.selectedSupplier = data.supplier;

                // this.form.selectedSupplier['label'] = data.supplier.supplierName;

                this.setEdit(id);

                this.loading();
                
            }).catch(error => {

                // callback(error, error.response.data);

                this.errortoast();

            });

        },

        setEdit(pageid){

          this.postUrl = this.api + this.controller + pageid;

          this.pageTitle = this.controllerName + ' > Edit';

          this.isDisabled = true;

        },
        

        handleCreateBill(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.form).then((result) => {
            
              if (result) {

                // no errors submit form
                this.submitFormTrips(button);
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

        submitFormTrips(button = null){

            this.form.submit('post', this.api + this.controller).then(success=>{

                //this.showToast('Bill Created Successfully');

                this.showAlert('New Bill Created, Reference Number: ' + success.id);

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



      },

      watch: {


      }

  }
</script>