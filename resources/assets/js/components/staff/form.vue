<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">
        
        <c-form-field label="Staff Name">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="staffName" 
              :status="checkErrors('staff name')" 
              name="staff name" 
              value=""
               :disabled="isDisabled" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('staffName')">{{ errors.first('staff name') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('staffName')" v-if="form.errors.has('staffName')"></span>
        </c-form-field>

        <c-form-field label="Father Name">
          <c-form-input 
              v-validate="'min:3'" 
              v-model="form.fatherName" 
              :status="checkErrors('father name')" 
              name="father name" 
              value=""
              type="text">
          </c-form-input>
        </c-form-field>

        <c-form-field label="Staff Type">

          <c-multiselect
            v-model="form.selectedType"
            v-validate="'required'"
            name="staff type"
            track-by="value"
            label="label"
            :options="type"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Type"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('staff type') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('selectedType')" v-if="form.errors.has('selectedType')"></span>
         
        </c-form-field>

        <c-form-field label="Salary Type">

          <c-multiselect
            v-model="form.selectedSalaryType"
            v-validate="'required'"
            name="staff salary type"
            track-by="value"
            label="label"
            :options="salaryType"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Salary Type"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('staff salary type') }}</span>
         
        </c-form-field>

        <c-form-field v-if="form.selectedSalaryType != undefined && form.selectedSalaryType['value'] == 'Permanent'" label="Employee Code">
          <c-form-input 
              v-model="form.employeeCode" 
              :status="checkErrors('employee code')" 
              name="employee code" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('employee code') }}</span>
        </c-form-field>        

        <c-form-field label="Home Phone">
          <c-form-input 
              v-validate="'min:8|numeric'" 
              v-model="form.homePhone" 
              :status="checkErrors('home phone')" 
              name="home phone" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('home phone') }}</span>
        </c-form-field>

        <c-form-field label="Cell Number">
          <c-form-input 
              v-validate="'numeric|min:8'" 
              v-model="form.cellPhone" 
              :status="checkErrors('cell number')" 
              name="cell number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('cell number') }}</span>
        </c-form-field>        

        <c-form-field label="Emergency Contact Number">
          <c-form-input 
              v-validate="'numeric|min:8'" 
              v-model="form.emergencyNo" 
              :status="checkErrors('emergency contact number')" 
              name="emergency contact number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('emergency contact number') }}</span>
        </c-form-field>        

        <c-form-field label="CNIC Number">
          <c-form-input 
              v-model="form.cnic" 
              :status="checkErrors('cnic')" 
              name="cnic" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('cnic') }}</span>
        </c-form-field> 

        <c-form-field label="Address">
          <c-form-input 
              v-model="form.address" 
              :status="checkErrors('address')" 
              name="address" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('address') }}</span>
        </c-form-field> 

         <c-form-field label="Staff">

          <c-multiselect
            v-model="form.selectedCertifications"
            v-validate="''"
            track-by="value"
            label="label"
            :options="certifications"
            name="certifications"
            :searchable="true"
            :close-on-select="false"
            :multiple="true"
            placeholder="Select Certifications"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('certifications') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('selectedCertifications')" v-if="form.errors.has('selectedCertifications')"></span>
          
        </c-form-field>

        <c-form-field v-if="form.selectedCertifications.length > 0" label="Selected Certifications">
        <table class="table table--striped table--hover">
          <thead>
            <th colspan="5">Certifications</th>
          </thead>
          <tbody>
            <tr v-for="(vari, index) in form.selectedCertifications">
              <td @click="removeThisStaff(index)">{{ vari.label }}</td>
              <td>
                <c-form-field label="Number">

                  <c-form-input 
                      :name="vari.label + 'number'" 
                      v-validate="''" 
                      v-model="form.selectedCertifications[index]['number']" 
                      type="text">
                  </c-form-input>
                    <small v-if="!errors.first(vari.label +'number')">Document Number</small>
                    <span class="form-help u-color-danger">{{errors.first(vari.label + 'number')}}</span>
                </c-form-field>
              </td>
              <td>
                 <c-form-field v-if="vari.expirable" label="Valid From">
                  <c-form-input 
                      name="valid from" 
                      v-validate="'date_format:DD-MM-YYYY'" 
                      v-model="form.selectedCertifications[index]['validFrom']" 
                      type="text">
                  </c-form-input>
                  <small v-if="!errors.first('valid from')">Format DD-MM-YYYY</small>
                    <!-- <c-flatpickr 
                        v-model="form.selectedCertifications[index]['validFrom']" 
                        name="valid from" 
                        value="" 
                    /> -->
                    <span class="form-help u-color-danger">{{ errors.first('valid from') }}</span>
                  </c-form-field> 
              </td>
              <td>
                <c-form-field v-if="vari.expirable" label="Expiry Date">
                  <c-form-input 
                      name="expiry" 
                      v-validate="'date_format:DD-MM-YYYY'" 
                      v-model="form.selectedCertifications[index]['validTo']" 
                      type="text">
                  </c-form-input>
                  <small v-if="!errors.first('expiry')">Format DD-MM-YYYY</small>
                    <!-- <c-flatpickr 
                        v-model="form.selectedCertifications[index]['validTo']" 
                        name="valid to" 
                        value="" 
                    /> -->
                    <span class="form-help u-color-danger">{{ errors.first('expiry') }}</span>
                  </c-form-field>    
              </td>
              
            </tr>
          </tbody>
        </table>

        </c-form-field>

        <c-form-field v-if="previousCertificates.length > 0" label="Previous Certifications">
        <table class="table table--striped table--hover">
          <thead>
            <th>Certificate</th>
            <th>Number</th>
            <th>Valid From</th>
            <th>Expiry</th>
            <th>Status</th>
          </thead>
          <tbody>
            <tr v-for="(vari, index) in previousCertificates">
              <td @click="removeThisStaff(index)">{{ vari.certificateName }}</td>
              <td>
                {{ vari.certificateNumber }}
              </td>
              <td>
                 {{ vari.validFrom }}
              </td>
              <td>
                {{ vari.validTo }}
              </td>
              <td @change="updateStatus(index, vari.id)">
                <c-form-switch :disabled="isLoading" label="Active" v-model="vari.status" />
              </td>
            </tr>
          </tbody>
        </table>

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

import { format, addDays } from 'date-fns';
import {Form, Errors} from "./../common/base.js";
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';
import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
import style2 from './../../../vendors/cover/vendors/flatpickr/style.css';


  export default {

    data() {
      return {

        form: new Form({
          staffName: '',
          fatherName: '',
          cellPhone: '', 
          homePhone: '',
          emergencyNo: '', 
          cnic: '', 
          address: '', 
          driversLicense: '', 
          isLoading: false,
          content: '',
          duration: '',
          status: '',
          selectedType: '',
          selectedCertifications: [],
          selectedSalaryType: undefined,
          employeeCode: undefined,
        }),

        previousCertificates: [],
        show: false,
        isDisabled: false,
        certifications: [],
        isLoading: false,

        type: [ 
            { label: 'Trailer Driver', value: 'Trailer Driver' }, 
            { label: 'Trailer Helper', value: 'Trailer Helper' },

            { label: 'Crane Operator', value: 'Crain Operator' }, 
            { label: 'Crane Helper', value: 'Crane Helper' },

            { label: 'Pickup Driver', value: 'Pickup Driver' },
            { label: 'Operator', value: 'Operator' },

            { label: 'Fork Lifter Operator', value: 'Fork Lifter Operator' },

            ],

        salaryType: [
            { label: 'Contractor', value: 'Contractor' },
            { label: 'Permanent', value: 'Permanent' }
        ],

        pageTitle: 'Staff > Add New',
        api: '',
        controller: '/staff/',
        controllerName: 'Clients',
        basePost: '',
        postUrl: '',

        toast: '',

        
      }
    },

    components: {

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr

    },

    
    created() {

      this.basePost = this.api + this.controller;
      this.postUrl = this.basePost;
      this.getCertification();

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

                this.staffName = data['staffName']; // To use the capitalization computed property.

                var select = [ { label: data.type, value: data.type } ];
                this.form.selectedType = select[0];

                var selectedSalaryType = [ { label: data.salaryType, value: data.salaryType }];
                this.form.selectedSalaryType = selectedSalaryType[0];

                this.getPreviousCertificate(pageid);

                this.setEdit(pageid);

                this.loading();
                
            }).catch(error => {

                this.loading();

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

                this.form.selectedCertifications = [];

                this.getPreviousCertificate(success.id);

              }
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        getCertification(){

          axios

            .get('/certificate')

              .then(response => {

                this.certifications = response.data.data;
               
            }).catch(error => {

                this.errortoast();

            });

        },

        getPreviousCertificate(pageid){

          axios
          
            .get('/certificate_staff/' + pageid)

              .then(response => {

                this.previousCertificates = response.data.data;
                
            }).catch(error => {

                this.errortoast();

            });

        },

        updateStatus(index, id){

          this.loading('Updating');

          axios
          
            .get('/certificate_staff/update/' + id)

              .then(response => {

                this.loading();

                this.showToast('Record Updated Successfully', 500); 
                
            }).catch(error => {

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

        showToast(message, duration){

          // Hide Loading Toast and Loading from Button
          this.loading();

          // Show success toast
          if(message == null){
            
            this.$toast.succeed(this.form.content = this.controllerName + ' Data Saved Successfully', this.form.duration = 2000);
          } else {

            this.$toast.succeed(this.form.content = message, this.form.duration = duration)

          }

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

         staffName: {
            // getter
            get: function () {
              return this.form.staffName;
            },
            // setter
            set: function (newValue) {

              if (!newValue) return ''
              var value = newValue.toString()
              this.form.staffName = value.toLowerCase().split(' ').map((a) => a.charAt(0).toUpperCase() + a.slice(1)).join(' ');


            }

          }

        },

  }
</script>