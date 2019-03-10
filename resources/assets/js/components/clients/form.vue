<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">

        

        <c-form-field label="Client Name">
          <c-form-input 
              v-validate="'required|min:3|alpha_spaces'" 
              v-model="clientName" 
              :status="checkErrors('client name')" 
              name="client name" 
              value="" 
               :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('clientName')">{{ errors.first('client name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('clientName')" v-if="form.errors.has('clientName')"></span>
        </c-form-field>

        <c-form-field label="Account Number">
          <c-form-input 
              v-validate="'required'" 
              v-model="form.accountNumber" 
              :status="checkErrors('account number')" 
              name="account number" 
              disabled="true"
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('accountNumber')">{{ errors.first('account number') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('accountNumber')" v-if="form.errors.has('accountNumber')"></span>
        </c-form-field>

        <c-form-field label="Phone Number">
          <c-form-input 
              v-validate="'required|numeric|min:8'" 
              v-model="form.phone1" 
              :status="checkErrors('phone number')" 
              name="phone number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('phone number') }}</span>
        </c-form-field>        

        <c-form-field label="Secondary Phone Number">
          <c-form-input 
              v-validate="'numeric|min:8'" 
              v-model="form.phone2" 
              :status="checkErrors('secondary phone number')" 
              name="secondary phone number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('secondary phone number') }}</span>
        </c-form-field>        

        <c-form-field label="Contact Person">
          <c-form-input 
              v-validate="'required'" 
              v-model="form.contactPerson" 
              :status="checkErrors('contact person')" 
              name="contact person" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('contact person') }}</span>
        </c-form-field> 

        <c-form-field label="Address">
          <c-form-input 
              v-validate="'required'" 
              v-model="form.address" 
              :status="checkErrors('address')" 
              name="address" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('address') }}</span>
        </c-form-field> 

        <c-form-field label="Email Address">
          <c-form-input 
              name="email address" 
              v-validate="'email'" 
              v-model="form.emailAddress" 
              :status="checkErrors('email address')" 
              type="text">
          </c-form-input>
            <span class="form-help u-color-danger" v-show="errors.has('email address')">{{errors.first('email address')}}</span>
        </c-form-field>

        <c-form-field label="Website">
          <c-form-input 
              v-validate="'url'" 
              v-model="form.website" 
              :status="checkErrors('website')" 
              name="website" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('website') }}</span>
        </c-form-field> 

        <c-form-field label="Credit Limit">
          <c-form-input 
              v-validate="'numeric'" 
              v-model="form.creditLimit" 
              :status="checkErrors('credit limit')" 
              name="credit limit" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('credit limit') }}</span>
        </c-form-field> 

        <c-form-field>
          <c-dropdown>
          <c-button :loading="form.isLoading" :disabled="form.isLoading" type="primary">Save</c-button>
          <c-dropmenu :hidden="form.isLoading" slot="content">
             <c-dropmenu-item  @click="handleSubmit('save')" icon="icon-new" title="Save" />
            <c-dropmenu-item  @click="handleSubmit('save&new')" icon="icon-new" title="Save & Add New" />
            <c-dropmenu-item  @click="handleSubmit('save&close')" icon="icon-list" title="Save & View All" />
          </c-dropmenu>
        </c-dropdown>
        </c-form-field>

      </c-form>

    </c-panel>

  </div>
</template>
<script>

import {Form, Errors} from "./../common/base.js";


  export default {

    data() {
      return {

        form: new Form({
          clientName: '',
          accountNumber: '',
          phone1: '', 
          accountNumber: this.accountNumber,
          phone2: '', 
          contactPerson: '', 
          address: '', 
          emailAddress: '', 
          website: '', 
          creditLimit: '',
          isLoading: false,
          content: '',
          duration: '',
          status: '',
        }),

        isDisabled: false,

        show: false,

        pageTitle: 'Clients > Add New',
        api: '',
        controller: '/clients/',
        controllerName: 'Clients',
        basePost: '',
        postUrl: '',

        onlineStatus: true,

        toast: '',

        // errors: new Errors(),

        
      }
    },

    components: {

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      if(this.$route.params.id){
         this.loading('Loading');
         this.getData(this.$route.params.id);      
         this.isDisabled = true;   
      } 

    },
    methods: {

        

        getData(pageid){

          axios

            .get(this.basePost + pageid)

              .then(response => {

                var data = response.data.data;

                for (let field in data ){

                   this.form[field] = data[field];

                }

                this.setEdit(pageid);

                this.loading();
                
            }).catch(error => {

                callback(error, error.response.data);

                this.errortoast();

            });

        },

        handleSubmit(button = null){

          this.loading('Saving');

          this.$validator.validateAll().then((result) => {
            
            if (result) {

              // no errors submit form
              this.submitForm(button);
              return;

            } else {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            }

          });

            
        },

        submitForm(button = null){

          // this.loading('showtoast');

          this.form.submit('post', this.postUrl).then(success=>{

            if(button == 'save&close'){

                this.showToast();

                this.$router.push(this.controller + 'all');

              }  else if(button == 'save&new') {

                // set url to add new
                this.$router.push(this.controller + 'addnew');

                // clear all and reset to defualt
                this.reset();

                // set url back to original
                this.basePost = this.api + this.controller;
                this.postUrl = this.basePost;


              } else if(button == 'save'){

                this.showToast();

                this.$router.push(this.controller + success.id + '/edit');

                this.setEdit(success.id);

              }
              
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
          }, 100);


        },


        setEdit(pageid){

          this.postUrl = this.api + this.controller + pageid;

          this.pageTitle = this.controllerName + ' > Edit';

          this.isDisabled = true;

        },

        checkErrors(field){

          if(this.errors.first(field)){
            return "danger";
          }

        },

        showToast(){

          // Hide Loading Toast and Loading from Button
          this.loading();

          // Show success toast
          this.$toast.succeed(this.form.content = this.controllerName + ' Data Saved Successfully', this.form.duration = 2000);

        },

        loading(message = null) {

              this.form.isLoading = true;

              const vm = this.toast;

              if(message != null){

                this.toast = this.$toast.loading(message + '...');

              } else {

                this.form.isLoading = false;
                this.toast.hide();

              }

          },

          errortoast(){

              this.$toast.failed('Please correct errors!', 2000)

          }


      },

      computed: {

         clientName: {
            // getter
            get: function () {
              return this.form.clientName;
            },
            // setter
            set: function (newValue) {

              var names = newValue.split(' ')
              this.form.clientName = newValue;
              this.form.accountNumber = this.form.clientName.toLowerCase().split(' ').map((a) => a.charAt(0).toUpperCase()).join('');

            }

          }

        },

  }
</script>