<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">
        
        <c-form-field label="Equipment Name">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="form.equipmentName" 
              :status="checkErrors('equipment name')" 
              name="equipment name" 
              value="" 
              :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('equipmentName')">{{ errors.first('equipment name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('equipmentName')" v-if="form.errors.has('equipmentName')"></span>
        </c-form-field>

        <c-form-field label="Equipment Number">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="form.equipmentNumber" 
              :status="checkErrors('equipment number')" 
              name="equipment number" 
              value="" 
              :disabled="isDisabled"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('equipment number') }}</span>
        </c-form-field>

       <c-form-field label="Supplier">

          <c-multiselect
            v-model="form.selectedSupplier"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="supplier"
            name="supplier"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Supplier"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('supplier') }}</span>

        </c-form-field>

         <c-form-field label="Type">

          <c-multiselect
            v-model="form.selectedType"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="type"
            name="type"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Type"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('type') }}</span>
          
        </c-form-field>

        <c-form-field label="Variation">

          <c-multiselect
            v-model="form.selectedVariations"
            track-by="value"
            label="label"
            :options="variations"
            name="variations"
            :searchable="true"
            :close-on-select="true"
            :multiple="true"
            :change="variationChanged()"
            placeholder="Select Variation"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('variations') }}</span>
          
        </c-form-field>

        <c-form-field v-if="form.selectedVariations.length > 0" label="Selected Variations">
        <table class="table table--striped table--hover">
          <thead>
            <th colspan="2">Updated {{ variationUpdated }}</th>
          </thead>
          <tbody>
            <tr v-for="(vari, index) in form.selectedVariations">
              <td width="99%" @click="removeThis(index)">{{ vari.label }}</td>
              <td @click="removeThis(index)"><c-svgicon size="xs" class="" name="cross" /></td>
            </tr>
          </tbody>
        </table>

        </c-form-field>

        <c-form-field label="Staff">

          <c-multiselect
            v-model="form.selectedStaff"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="staff"
            name="staff"
            :searchable="true"
            :close-on-select="true"
            :multiple="true"
            :max="3"
            :min="1"
            placeholder="Select Staff"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('staff') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('selectedStaff')" v-if="form.errors.has('selectedStaff')"></span>
          
        </c-form-field>

        <c-form-field v-if="form.selectedStaff.length > 0" label="Selected Staff">
        <table class="table table--striped table--hover">
          <thead>
            <th colspan="2">Updated {{ staffUpdated }}</th>
          </thead>
          <tbody>
            <tr v-for="(vari, index) in form.selectedStaff">
              <td width="99%" @click="removeThisStaff(index)">{{ vari.label }}</td>
              <td @click="removeThisStaff(index)"><c-svgicon size="xs" class="" name="cross" /></td>
            </tr>
          </tbody>
        </table>

        </c-form-field>

        <hr>

        <c-form-field>
          <c-dropdown>
          <c-button :loading="form.isLoading" :disabled="form.isLoading" type="primary">Save</c-button>
          <c-dropmenu :hidden="form.isLoading" slot="content">
             <c-dropmenu-item @click="handleSubmit('save')" icon="icon-new" title="Save" />
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

var moment = require('moment');

import {Form, Errors} from "./../common/base.js";

  export default {

    data() {
      return {

        form: new Form({
          equipmentName: '',
          equipmentNumber: '',
          selectedSupplier: '',
          selectedType: '',
          selectedVariations: [],
          isChanged: false,
          isLoading: false,
          content: '',
          duration: '',
          status: '',
          selectedStaff: [],
        }),

        originalSelectedVariations: [],
        originalSelectedStaff: [],

        visible: true,

        variationUpdated: '',
        staffUpdated: '',

        supplier: [],
        type: [],
        variations: [],
        staff:[],

        isDisabled: false,

        pageTitle: 'Equipments > Add New',
        api: '',
        controller: '/equipment/',
        controllerName: 'Equipments',
        basePost: '',
        postUrl: '',
        pageid: '',

        show: false,

        toast: '',

       

        
      }
    },

    components: {


    },
    
    created() {

      this.basePost = this.api + this.controller;
      this.postUrl = this.basePost;

      if(this.$route.params.id){
          
          this.getData();      
      } 

      this.getSelectData();

    },

    methods: {

        getData(){

          this.basePost = this.api + this.controller;
          this.postUrl = this.basePost;
          this.pageid = this.$route.params.id;
          this.loading('Loading');
          this.isDisabled = true;   

          axios

            .get(this.basePost + this.pageid)

              .then(response => {

                var data = response.data.data;

                for (let field in data ){

                   this.form[field] = data[field];

                }

                //Set Selected Supplier
                this.form.selectedSupplier = data.supplierData;
                this.form.selectedSupplier['label'] = data.supplierData.supplierName;

                //Set Selected Type
                this.form.selectedType = data.typeData;
                this.form.selectedType['label'] = data.typeData.equipmentType;

                //Set Selected Variation
                for (let field in data.variation){

                  this.originalSelectedVariations.push(

                    {
                      value: data.variation[field].id, 
                      addedOn: moment(data.variation[field].created_at).fromNow(),
                      label: data.variation[field].variationName,
                    }); 

                  this.form.selectedVariations.push(

                    {
                      value: data.variation[field].id, 
                      label: data.variation[field].variationName,
                    }); 

                  this.variationUpdated = moment(data.variation[field].pivot.created_at).fromNow();

                }

                //Set Selected Staff
                for (let field in data.staff){

                  this.originalSelectedStaff.push(

                    {
                      value: data.staff[field].id, 
                      addedOn: moment(data.staff[field].created_at).fromNow(),
                      label: data.staff[field].staffName,
                    }); 

                  this.form.selectedStaff.push(

                    {
                      value: data.staff[field].id, 
                      label: data.staff[field].staffName,
                    }); 

                  this.staffUpdated = moment(data.staff[field].pivot.created_at).fromNow();

                }

                this.setEdit(this.pageid);

                this.loading();

                this.form.isChanged = false;
                
            }).catch(error => {

                //callback(error, error.response.data);
                this.loading();
                this.errortoast();

            });

        },

        variationChanged(){
          
          if(this.originalSelectedVariations.length == this.form.selectedVariations.length && this.form.isChanged != true){

            this.form.isChanged = false;

          } else {

            this.form.isChanged = true;

          }

        },

        // variationChanged(){

        //   if(this.originalSelectedVariations.length == this.form.selectedVariations.length && this.form.isChanged != true){

        //     this.form.isChanged = false;

        //   } else {

        //     this.form.isChanged = true;

        //   }

        // },

        removeThis(index){

          this.form.selectedVariations.splice(index, 1);

        },

        removeThisStaff(index){

          this.form.selectedStaff.splice(index, 1);

        },


        getSelectData(){

          axios

            .get(this.basePost + 'getdata')

              .then(response => {
                
                this.supplier = response.data.Supplier;
                this.type = response.data.Type;
                this.variations = response.data.Variations;
                this.staff = response.data.Staff;

            }).catch(error => {


              this.errortoast();
              this.loading();
                //callback(error, error.response.data);

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

                // Get option data
                this.getSelectData();


              } else if(button == 'save'){

                this.showToast();

                this.$router.push(this.controller + success.id + '/edit');

                this.reset();

                //set page for edit
                this.setEdit(success.id);

                // get data
                this.getData();

                // Get option data
                this.getSelectData();


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