<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">
        
        <c-form-field label="Area Name">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="form.areaName" 
              :status="checkErrors('area name')" 
              name="area name" 
              value="" 
              :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('areaName')">{{ errors.first('area name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('areaName')" v-if="form.errors.has('areaName')"></span>
        </c-form-field>

        <c-form-field label="Coordinates">
          <c-form-input 
              v-validate="'required|min:10'" 
              v-model="form.coordinates" 
              :status="checkErrors('coordinates')" 
              name="Coordinates" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('coordinates')">{{ errors.first('Coordinates') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('coordinates')" v-if="form.errors.has('coordinates')"></span>
        </c-form-field>

       <c-form-field label="City">

          <c-multiselect
            v-model="form.selectedCity"
            track-by="value"
            label="label"
            :options="city"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select City"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedCity')" v-if="form.errors.has('selectedCity')"></span>
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
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

  export default {

    data() {
      return {

        form: new Form({
          areaName: '',
          selectedCity: '',
          isLoading: false,
          coordinates: '',
          content: '',
          duration: '',
          status: '',
        }),

        city: [],

        isDisabled: false,

        show: false,

        pageTitle: 'Area > Add New',
        api: '',
        controller: '/area/',
        controllerName: 'Area',
        basePost: '',
        postUrl: '',

        toast: '',

        
      }
    },

    components: {

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

      this.getCities();

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

                this.form.selectedCity = data.cityid;

                this.form.selectedCity['label'] = data.cityid.cityName;

                this.setEdit(pageid);

                this.loading();
                
            }).catch(error => {

                callback(error, error.response.data);

                this.errortoast();

            });

        },

        getCities(){

          axios
            .get('/city')

              .then(response => {
                
                this.city = response.data.data;

            }).catch(error => {

                callback(error, error.response.data);

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

              this.getCities();

          },

          errortoast(){

              this.$toast.failed('Please correct errors!', 2000)

          }


      },

  }
</script>