<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">
        
        <c-form-field label="Variation Name">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="form.variationName" 
              :status="checkErrors('variation name')" 
              name="variation name" 
              value="" 
              :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('variationName')">{{ errors.first('variation name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('variationName')" v-if="form.errors.has('variationName')"></span>
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
          variationName: '',
          isLoading: false,
          content: '',
          duration: '',
          status: '',
        }),

        isDisabled: false,

        show: false,

        pageTitle: 'Variation > Add New',
        api: '',
        controller: '/variation/',
        controllerName: 'Variations',
        basePost: '',
        postUrl: '',

        toast: '',

        
      }
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

                this.setEdit(success.id, 'save');

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

  }
</script>