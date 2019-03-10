<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">

        
        <c-form-field label="User Name">
          <c-form-input 
              v-validate="'required|min:3|alpha_spaces'" 
              v-model="name" 
              :status="checkErrors('name')" 
              name="user name" 
              value="" 
               :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('name')">{{ errors.first('name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('name')" v-if="form.errors.has('name')"></span>
        </c-form-field>

        <c-form-field label="Email Address">
          <c-form-input 
              name="email" 
              v-validate="'email'" 
              v-model="form.email" 
              :status="checkErrors('email')" 
              type="text">
          </c-form-input>
            <span class="form-help u-color-danger" v-show="errors.has('email')">{{errors.first('email')}}</span>
        </c-form-field>

        <c-form-field label="Mobile Number">
          <c-form-input 
              v-validate="'required|numeric|min:8'" 
              v-model="form.phoneNumber" 
              :status="checkErrors('mobile number')" 
              name="mobile number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('mobile number') }}</span>
        </c-form-field>  

        <c-form-field label="Password">
          <c-form-input 
              v-model="form.password" 
              :status="checkErrors('password')" 
              name="password" 
              value="" 
              type="password">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('password')">{{ errors.first('password') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('password')" v-if="form.errors.has('password')"></span>
        </c-form-field>

        <c-form-field label="Select Color Code">

          <c-multiselect
            v-model="form.tagColor"
            v-validate="'required'" 
            track-by="value"
            label="label"
            :options="colors"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Color"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('tagColor')" v-if="form.errors.has('tagColor')"></span>
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
import offline from 'v-offline';
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

  export default {

    data() {
      return {

        form: new Form({
          name: '',
          email: '', 
          tagColor: '',
          password: '',
          isLoading: false,
          content: '',
          duration: '',
          status: '',
          phoneNumber: '',
        }),

        colors: [ 
            { label: 'Primary', value: 'primary' }, 
            { label: 'Success', value: 'success' }, 
            { label: 'Info', value: 'info' },
            { label: 'Danger', value: 'danger' },
            { label: 'Warning', value: 'warning' },
            { label: 'Ghost', value: 'ghost' },
            
            ],
        isDisabled: false,

        show: false,

        pageTitle: 'User > Add New',
        api: '',
        controller: '/getusers/',
        controllerName: 'Users',
        basePost: '',
        postUrl: '',

        toast: '',

        // errors: new Errors(),

        
      }
    },

    components: {

      offline,
      'c-multiselect': Multiselect

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

        handleConnectivityChange(status) {
          console.log(status);
        },

        getData(pageid){

          axios

            .get(this.basePost + pageid)

              .then(response => {

                var data = response.data.data;

                for (let field in data ){

                  if(field != 'password'){
                   this.form[field] = data[field];
                  }

                }

                this.form.tagColor = { 'value': data.tagColor };

                this.form.tagColor['label'] = data.tagColor;

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

         name: {
            // getter
            get: function () {
              return this.form.name;
            },
            // setter
            set: function (newValue) {

              var names = newValue.split(' ')
              this.form.name = newValue;
              this.form.accountNumber = this.form.name.toLowerCase().split(' ').map((a) => a.charAt(0).toUpperCase()).join('');

            }

          }

        },

  }
</script>