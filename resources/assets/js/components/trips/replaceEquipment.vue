<template>

  <div>

      <c-panel :title="pageTitle">

      <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">

        <c-form-field label="Client">

          <c-multiselect
            v-model="selectedClient"
            track-by="value"
            label="label"
            accountNumber="accountNumber"
            :options="clients"
            :searchable="true"
            :close-on-select="true"
            :disabled="checkProgress()"
            placeholder="Select Client"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="form.errors.has('selectedClient')"></span>
        </c-form-field>

        <c-form-field label="Starting Date">
          <c-flatpickr 
            v-validate="'required'"
            v-model="dispatchStartingDate" 
            :status="checkErrors('dispatch starting date')"
            name="dispatch starting date"
            value=""
          />
        </c-form-field> 

        <c-form-field label="Dispatch Number">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="form.dispatchNo" 
              :status="checkErrors('dispatch number')" 
              name="dispatch number" 
              value="" 
              disabled="true"
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('dispatchNo')">{{ errors.first('dispatch number') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('dispatchNo')" v-if="form.errors.has('dispatchNo')"></span>
        </c-form-field>

        <c-form-field label="Reference Number">
          <c-form-input 
              v-validate="'required|numeric'" 
              v-model="form.referenceNo" 
              :status="checkErrors('reference number')" 
              name="reference number" 
              :disabled="isDisabled"
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!form.errors.has('referenceNo')">{{ errors.first('reference number') }}</span>
          <span class="form-help u-color-danger" v-text="form.errors.get('referenceNo')" v-if="form.errors.has('referenceNo')"></span>
        </c-form-field>

        <c-form-field label="Invoice Number">
          <c-form-input 
              v-validate="'numeric'" 
              v-model="form.invoiceNo" 
              :status="checkErrors('invoice number')" 
              name="invoice number"
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('invoice number') }}</span>
        </c-form-field> 

        <c-form-field label="Invoice Date">
          <c-flatpickr 
              v-model="form.invoiceDate" 
              :status="checkErrors('invoice date')" 
              name="invoice date" 
              value="" 
          />
          <span class="form-help u-color-danger">{{ errors.first('invoice date') }}</span>
        </c-form-field>        

        <c-form-field label="Total Amount">
          <c-form-input 
              v-validate="'required'" 
              v-model="form.totalAmount" 
              :status="checkErrors('total amount')" 
              name="total amount" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('total amount') }}</span>
        </c-form-field> 

        <c-form-field label="Total Expense">
          <c-form-input 
              v-validate="''" 
              v-model="form.totalExpense" 
              :status="checkErrors('total expense')" 
              name="total expense"
              disabled="true" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('total expense') }}</span>
        </c-form-field> 

       
        <c-form-field label="Remarks">
          <c-form-input 
              v-model="form.remarks" 
              :status="checkErrors('remarks')" 
              name="remarks" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('remarks') }}</span>
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

      <c-button v-if="this.$route.params.id" type="primary" @click="rightOpen = true, tripOpen = false" smart>Dispatch Equipments</c-button>

      <c-button v-if="this.eqForm.selectedEquipments.length > 0" type="primary" @click="tripOpen = true, rightOpen = false" smart>Add Trips</c-button>

    </c-panel>

    <!-- Equipment Model Start -->

    <c-modal title="Add Equipments" placement="right" v-model="rightOpen">

    <c-form layout="horizontal" span="220" @submit.prevent="handleSubmitEquipments">
    
    <!-- <c-form-field label="Select Equipments">

          <c-multiselect
            v-model="eqForm.selectedEquipments"
            track-by="value"
            label="label"
            :options="equipments"
            name="equipments"
            :searchable="true"
            :close-on-select="true"
            :multiple="true"
            :change="equipmentChanged()"
            placeholder="Select Equipments"></c-multiselect>
          
        </c-form-field> -->

        <c-form-field v-if="eqForm.selectedEquipments.length > 0" label="Selected Equipments">

        <table class="table table--striped table--hover">

          <thead>
            <!-- <th colspan="2">Updated {{ variationUpdated }}</th> -->
          </thead>
          <tbody>
            <tr v-for="(vari, index) in eqForm.selectedEquipments">
              <td width="99%">{{ vari.label }}</td>
              <td v-if="vari.trips_id == null" v-tooltip.hover="'No trip created!'">
                  <i class="icon-unlink2"></i>
              </td>
              <td v-if="vari.trips_id != null" v-tooltip.hover="'Already assigned to a trip!'">
                  <i class="icon-link2"></i>
              </td>
            </tr>
          </tbody>
        </table>

        </c-form-field>

      </c-form>

    <div class="u-text-left" slot="footer">
      <c-button type="danger" @click="rightOpen = false" v-once smart>Close</c-button>
      <!-- <c-button v-if="eqForm.selectedEquipments.length > 0" type="primary" @click="handleSubmitEquipments('save')" v-once smart>Confirm</c-button> -->
    </div>
  </c-modal>

  <!-- Equipment Model End -->

  <!-- Trips Model Start -->

  <c-modal title="Add Trips" placement="right" v-model="tripOpen">

    <c-form layout="horizontal" span="220" @submit.prevent="">
    
    
      <div>

          <c-multiselect
            v-model="tripForm.selectedEquipments"
            track-by="value"
            label="label"
            :options="reservedEquipments"
            name="equipments"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select Equipments"></c-multiselect>
          
    </div>

        <div v-if="tripForm.selectedEquipments != undefined">

        <hr>

        <table class="table table--striped table--hover">

          <tbody>
            
            <tr>
              <c-multiselect
                  v-model="tripForm.selectedLocations"
                  v-validate="'required'"
                  track-by="value"
                  label="label"
                  :options="locations"
                  name="locations"
                  :searchable="true"
                  :close-on-select="true"
                  :multiple="true"
                  placeholder="Select Location"></c-multiselect>
                <span class="form-help u-color-danger">{{ errors.first('locations') }}</span>
              </c-form-field> 
            </tr>
          </tbody>

        </table>

        </div>

        <div v-if="tripForm.selectedLocations.length > 0" >

          <hr>

           <table class="table table--striped table--hover">

             <tbody>
            
              <tr v-for="(vari, index) in tripForm.selectedLocations">
                <td width="60%">{{ vari.label }}</td>
                <td width="30%" v-model="setLocationStartEnd">{{ vari.positing }}</td>
                <td> <c-button type="primary" @click="setTripDetails(vari, index)" smart size="sm">Details</c-button></td>
              </tr>

            </tbody>

          </table>

        </div>

      </c-form>

    <div class="u-text-left" slot="footer">
      <c-button type="danger" @click="tripOpen = false" v-once smart>Close</c-button>
      <c-button v-if="tripForm.selectedLocations.length > 1" type="primary" @click="handleSubmitTrips()" v-once smart>Confirm</c-button>
    </div>
  </c-modal>

  <!-- Trips Model End -->

  <!-- Trips Details Start -->

    <c-modal title="<i class='icon-plus-circle u-color-primary u-mr-10'></i>Add Details" v-model="tripDetailsOpen">
      <c-form layout="horizontal" span="100" @submit.prevent="">
        
        <c-form-field label="Location">
          <c-form-input disabled="true" :value="LocationDetails.label"></c-form-input>
        </c-form-field>

        <c-form-field label="Consignment Number">
          <c-form-input v-model="consignmentNumber"></c-form-input>
        </c-form-field>

        <c-form-field label="Trip Starting Date">
          <c-flatpickr v-model="startingDate" />
        </c-form-field>

        <c-form-field label="Trip Ending Date">
          <c-flatpickr v-model="endingDate" />
        </c-form-field>
        
        <c-form-field label="Details of Goods">
          <c-form-input v-model="detailsofGood"></c-form-input>
        </c-form-field>
        
        <c-form-field label="Weight & Dimensions">
          <c-form-input v-model="wieghtdimension"></c-form-input>
        </c-form-field>
        
        <c-form-field>
          <c-button :disabled="isDetailsFilled()" type="primary" @click="saveTripDetails" smart>ADD</c-button>
        </c-form-field>
      </c-form>
    </c-modal>

  <!-- Trips Details End -->


  </div>
</template>
<script>
var moment = require('moment');

import {Form, Errors} from "./../common/base.js";
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
import style2 from './../../../vendors/cover/vendors/flatpickr/style.css';

  export default {

    data() {
      return {

        form: new Form({
          dispatchNo: '',
          referenceNo: '',
          dispatchStartingDate: '',
          invoiceNo: '', 
          invoiceDate: '', 
          totalAmount: '', 
          totalExpense: '', 
          selectedClient: [], 
          selectedEquipments: [],
          trackingIDNo: '', 
          remarks: '',
          isLoading: false,
          content: '',
          duration: '',
          status: '',
        }),

        eqForm: new Form({

          selectedEquipments: [],
          isChanged: false,

        }),

        tripForm: new Form({

          selectedEquipments: undefined,
          selectedLocations: [], 
          selectedLocationsDetails: [],
          isChanged: false,

        }),

        isFilled: this.isitFilled(),

        clients: [],
        isDisabled: false,
        
        equipments: [],
        originalSelectedEquipments: [],

        locations: [],
        originalSelectedTrips: [],

        reservedEquipments: [],

        show: false,
        rightOpen: false,
        tripOpen: false,
        tripDetailsOpen: false,
        LocationDetails: '',
        consignmentNumber: '',
        startingDate: '',
        endingDate: '',
        detailsofGood: '',
        wieghtdimension: '',


        pageTitle: 'Dispatch > Add New',
        api: '',
        controller: '/dispatch/',
        controllerName: 'Dispatch',
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

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      if(this.$route.params.id){

         this.loading('Loading');
         this.getData(this.$route.params.id);      
         this.isDisabled = true;   

      } 

      this.getClients();

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

                this.form.selectedClient = data.client;

                this.form.selectedClient['label'] = data.client.clientName;
                this.form.selectedClient['value'] = data.client.id;

                this.getEquipmentData(data.reservedEquipments);
                this.getEquipmentData(data.reservedEquipments_withTrips, 'withTrips');


                this.setEdit(pageid);

                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        equipmentChanged(){
          
          if(this.originalSelectedEquipments.length == this.eqForm.selectedEquipments.length && this.form.isChanged != true){

            this.eqForm.isChanged = false;

          } else {

            this.eqForm.isChanged = true;

          }

        },

        handleSubmit(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.form).then((result) => {
            
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

                this.getClients();


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

        handleSubmitEquipments(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.eqForm).then((result) => {
            
            if (result) {

              // no errors submit form
              this.submitFormEquipments(button);

              return;

            } else {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            }

          });

            
        },

        submitFormEquipments(button = null){


          this.eqForm.submit('post', this.api + this.controller + 'reserveEquipments/' + this.pageid).then(success=>{

                this.showToast('Equipments Added Successfully');

                this.getEquipmentData(success.reservedEquipments);

                setTimeout(() => {
                  this.rightOpen = false;
                  this.tripOpen = true;
                }, 500);
                
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

         getEquipmentData(response, withTrips)
        {

                var reserved = response;

                if(withTrips == null){
                  this.reservedEquipments = [];
                  this.eqForm.selectedEquipments = [];
                }

                var u = this.eqForm.selectedEquipments.length;

                for (let field in reserved){

                  if(withTrips == null){
                   var i = this.reservedEquipments.push(

                      {
                        value: reserved[field].id, 
                        label: reserved[field].equipmentName,
                      }); 

                    this.originalSelectedEquipments.push(

                    {
                        value: reserved[field].id, 
                        label: reserved[field].equipmentName,
                    }); 

                    this.eqForm.selectedEquipments.push(
                    {
                        value: reserved[field].id, 
                        label: reserved[field].equipmentName,
                    });

                  }

                  if(withTrips != null){

                      let indEx = this.reservedEquipments.map(item => item.value).indexOf(reserved[field].id);

                       this.reservedEquipments[indEx].label = reserved[field].equipmentName + ' - (Linked)';
                       this.eqForm.selectedEquipments[indEx].trips_id = true;
                       this.reservedEquipments[indEx].trips_id = true;
                  }

                  u++;

                }


        },


        handleSubmitTrips(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.tripForm).then((result) => {
            
            if (result && this.tripForm.selectedLocations.length > 1 && this.tripForm.selectedLocations.length == this.tripForm.selectedLocationsDetails.length) {

              // no errors submit form
              this.submitFormTrips(button);

              return;

            } else {

              if(this.tripForm.selectedLocations.length != this.tripForm.selectedLocationsDetails.length){

                this.errortoast('Some of the trips doesn\'t have details!', 2000);

              } else {
                
                this.errortoast();

              }

              // Hide Loading Toast
              this.loading();

              

            }

          });

            
        },

        submitFormTrips(button = null){


          this.tripForm.submit('post', this.api + this.controller + 'storeTrips/' + this.pageid).then(success=>{

                this.showToast('Trips Added Successfully');

                this.removeThisEquipment();

                this.getEquipmentData(success.reservedEquipments);

                this.getEquipments();

                this.tripForm.selectedLocationsDetails = [];
                
              
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

        getClients(){

          axios

            .get('/clients')

              .then(response => {
                
                this.clients = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getEquipments(){

          this.equipments = [];

          axios
            .get('/equipment')

              .then(response => {
                
                // Set all equipmets data from response
                this.equipments = response.data.data;

                // Remove all equipments which assigned to trips
                this.removeAssignedEquipments();

            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        getLocations(){

          axios
            .get('/area')

              .then(response => {
                
                this.locations = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },


        setEdit(pageid){

          this.postUrl = this.api + this.controller + pageid;

          this.pageTitle = this.controllerName + ' > Edit';

          this.isDisabled = true;

          this.pageid = pageid;

          setTimeout(() => {

              this.getEquipments();

              this.getLocations();

          }, 300);


        },

        removeThis(index){

          this.eqForm.selectedEquipments.splice(index, 1);

        },

        removeThisLocation(index){

          this.tripForm.selectedLocations.splice(index, 1);
          this.tripForm.selectedLocationsDetails.splice(index, 1);

        },

        removeThisTrip(index){

          this.tripForm.selectedEquipments = undefined;

        },

        removeAssignedEquipments(){

          var selected = this.eqForm.selectedEquipments;

            for (let field in selected){

              if(selected[field].trips_id != null)
              {
                var equipment_id = selected[field].value;
                let i = this.equipments.map(item => item.id).indexOf(equipment_id);
                this.equipments.splice(i, 1);
              }

            }

        },

        removeThisEquipment(){

          var equipment_id = this.tripForm.selectedEquipments.value;
          let i = this.reservedEquipments.map(item => item.value).indexOf(equipment_id) 
          this.reservedEquipments.splice(i, 1);

          if(this.reservedEquipments.length == 0){
            this.tripOpen = false;
          }
        
          this.tripForm.selectedEquipments = undefined;
          this.tripForm.selectedLocations = [];
          this.selectedLocationsDetails = [];

        },

        checkErrors(field){

          if(this.errors.first(field)){
            return "danger";
          }

        },

        checkDate(){

          return this.form.dispatchStartingDate;

        },

        setTripDetails(location, index){

          this.clearDetailsForm();

          this.LocationDetails = location;

          this.LocationDetails['index'] = index;

          var tripDetailsForm = this.tripForm.selectedLocationsDetails[index];

          for (let ld in tripDetailsForm){

            this[ld] = tripDetailsForm[ld];

          }

          this.tripDetailsOpen = true;

        },

        saveTripDetails(){

          var trip = this.LocationDetails;

          this.tripForm.selectedLocationsDetails[trip.index] = 

              {'consignmentNumber': this.consignmentNumber,
              'startingDate': this.startingDate,
              'endingDate': this.endingDate,
              'detailsofGood': this.detailsofGood,
              'wieghtdimension': this.wieghtdimension
              };

          this.tripDetailsOpen = false;

          this.clearDetailsForm();

        },

        clearDetailsForm(){

          this.consignmentNumber = '';
          this.startingDate = '';
          this.endingDate = '';
          this.detailsofGood = '';
          this.wieghtdimension = '';

        },

        checkProgress(){

            if(this.eqForm.selectedEquipments.length > 0){
              return true;
            } else {
              return false;
            }

        },

        isDetailsFilled(){

          if(this.consignmentNumber == '' || this.startingDate == '' || this.endingDate == ''){
            return true;
          } else {
            return false;
          }

        },

        showToast(message = null){

          // Hide Loading Toast and Loading from Button
          this.loading();

          // Show success toast
          if(message == null){

            this.$toast.succeed(this.form.content = this.controllerName + ' Data Saved Successfully', this.form.duration = 2000);

          } else {

            this.$toast.succeed(this.form.content = message, this.form.duration = 2000);

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

          errortoast(message, delay){

              if(message == null){
                var message = 'Please correct errors!';
                var delay = 2000;
              }
              this.$toast.failed(message, delay)

          },

          isitFilled(value){

            if(value != ''){
              return true;
            }

          },

          createAccountNumber(){

            if(this.form.dispatchStartingDate != '' && this.form.selectedClient != ''){

                var date = this.form.dispatchStartingDate.toLowerCase().split('-').map((a) => a.toUpperCase()).join('');

                this.form.dispatchNo = this.form.selectedClient.accountNumber + '-' + date;
            }

          }


      },

      computed: {

        setLocationStartEnd(){
          
          for (let field in this.tripForm.selectedLocations ){

                  var total = this.tripForm.selectedLocations.length;

                   this.tripForm.selectedLocations[field].positing = '';
                   this.tripForm.selectedLocations[total - 1].positing = 'Ending Point';
                   this.tripForm.selectedLocations[0].positing = 'Starting Point';

          }

        },

        selectedClient: {
            // getter
            get: function () {
              return this.form.selectedClient;
            },
            // setter
            set: function (newValue) {

              this.form.selectedClient = newValue;
              
              this.createAccountNumber();

            }

          },

          dispatchStartingDate: {
            // getter
            get: function () {
              return this.form.dispatchStartingDate;
            },
            // setter
            set: function (newValue) {

              // var names = newValue.split(' ')
              this.form.dispatchStartingDate = newValue;
              
              this.createAccountNumber();

            }

          }


      },

  }
</script>