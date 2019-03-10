<template>

  <div>


    <c-panel title="Trips">

       <div>

           <table class="table table--striped table--hover">

             <tbody>

              <tr v-if="trip.status == 1" v-for="(trip, index) in trip.tripsDetails">
                <td width="60%">{{ trip.tripNumber }}</td>
                <td> <c-button type="primary" @click="settripEquipmentPanel(index)" smart size="sm">Details</c-button></td>
                <td @click="removeThisTrip(index)">
                  <c-svgicon size="xs" class="" name="cross" v-tooltip.hover="'Remove!'" />
                </td>
              </tr>

            </tbody>

          </table>

          <div class="u-text-left" slot="footer">
            <c-button type="primary" @click="handleSubmitTrips()" v-once smart>Save Trips</c-button>
            <c-button type="danger" @click="goBack()" v-once smart>Back to Dispatch</c-button>
            <span class="form-help u-color-danger" v-text="trip.errors.get('Changed')" v-if="trip.errors.has('Changed')"></span>
          </div>

         


        </div>

    </c-panel>

 

  <!-- Trips Model End -->

  <!-- Trips Details Start -->

    <c-modal :closable="isClosable" placement="right" title="<i class='icon-plus-circle u-color-primary u-mr-10'></i>Add Details" v-model="tripEquipmentPanelOpen">
      <c-form layout="horizontal" span="100" @submit.prevent="">
        
        <c-form-field label="Trip Number">
          <c-form-input disabled="true" v-model="trip.tripNumber"></c-form-input>
        </c-form-field>

      <c-form-field label="Trip Locations">
      <c-multiselect
          v-model="trip.tripLocations"
          v-validate="'required'"
          track-by="value"
          label="label"
          :options="locations"
          name="locations"
          :searchable="true"
          :close-on-select="true"
          :multiple="true"
          placeholder="Select Locations"></c-multiselect>
        <span class="form-help u-color-danger">{{ errors.first('locations') }}</span>
      </c-form-field>

        <c-form-field label="Trip Locations">
          <table class="table table--striped table--hover">

             <tbody>

              <tr v-for="(trip, index) in trip.tripLocations">
                <td width="69%">{{ trip.areaName }}</td>
                <!-- <td @click="removeThisTripLocation(index)">
                  <c-svgicon size="xs" class="" name="cross" v-tooltip.hover="'Remove!'" />
                </td> -->
                <td width="30%" v-model="setLocationStartEnd">{{ trip.positing }}</td>
              </tr>

            </tbody>

          </table>
        </c-form-field>

        <c-form-field label="Consignment Number">
          <c-form-input v-model="trip.consignmentNumber"></c-form-input>
        </c-form-field>

        <c-form-field label="Trip Starting Date">
          <c-flatpickr v-model="trip.startingDate" />
        </c-form-field>
        
        <c-form-field label="Details of Goods">
          <c-form-input v-model="trip.detailsofGood"></c-form-input>
        </c-form-field>
        
        <c-form-field label="Weight & Dimensions">
          <c-form-input v-model="trip.wieghtdimension"></c-form-input>
        </c-form-field>
        
        <c-form-field>
          <c-button v-if="trip.tripLocations.length > 1" type="primary" @click="savetripEquipmentPanel" smart>Save</c-button>
        </c-form-field>
      </c-form>
    </c-modal>

  <!-- Trips Details End -->


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


        trip: new Form({

          selectedEquipments: undefined,
          selectedLocations: [], 
          
          Changed: [],
          Deleted: [],
          Created: [],
          index: '',
          dispatch_id: '',
          equipment_id: '',

          //All Trips
          tripsDetails: [],

          //Active Trip Details
          tripNumber: '', 
          consignmentNumber: '', 
          startingDate: '',
          detailsofGood: '',
          wieghtdimension: '',
          trip_id: '',
            //Active Trip Locations
            tripLocations: [],
        }),

        tripEquipmentPanelOpen: false,

        locations: [],

        isClosable: false,

        show: false,
        isLoading: false,
        content: '',
        duration: '',
      

        pageTitle: 'Existing Trips',
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

        this.pageid = this.$route.params.id;
        this.trip.equipment_id = this.$route.params.equipment_id;
         this.loading('show');
         this.getData();      
         this.isDisabled = true;   

         

      } 

      this.getLocations();

    },
    methods: {

        getData(pageid){

          axios
            .get('/dispatch/getTrips',{

              params: {

                equipment_id : this.trip.equipment_id,
                dispatch_id: this.pageid

              }

            })

              .then(response => {

                var data = response.data;
                
                this.createTrips(data);

                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        createTrips(data){


          var i = data.length;
          
          var u = 1;
          if(i > 0)
          {

            for (let field in data)
            {

              var run = true;

              var trip_id = data[field].trip_id;

              if(this.trip.tripsDetails.length > 0){
                let currentIndex = this.trip.tripsDetails.map(item => item.trip_id).indexOf(trip_id);
                if(currentIndex != '-1'){
                  run = false;
                }
              }

              if(run == true){
              
                this.trip.tripsDetails.push(
                {
                trip_id: trip_id,
                tripNumber:  'Trip' + (u) + ' - Date : ' + format(data[field].tripStartDate, 'DD-MM-YYYY'),
                consignmentNumber: data[field].consignmentNo,
                tripLocations: [{
                  id: data[field].area_id,
                  value: data[field].area_id,
                  areaName: data[field].areaName,
                  position: data[field].sequence 
                }],
                startingDate: format(data[field].tripStartDate, 'DD-MM-YYYY'),
                detailsofGood: data[field].detailsofGoods,
                wieghtdimension: data[field].weightandDimmension,
                status: data[field].status,
                });

                u++;
              
              } else if(run == false) {

                let currentIndex = this.trip.tripsDetails.map(item => item.trip_id).indexOf(trip_id);

                 this.trip.tripsDetails[currentIndex].tripLocations.push(
                  {
                        id: data[field].area_id,
                        areaName: data[field].areaName,
                        value: data[field].area_id,
                        position: data[field].sequence
                  });

              }

            }

          }

        },

        handleSubmitTrips(button = null){

          this.loading('show');

          this.$validator.validateAll(this.trip).then((result) => {
            
              if(this.trip.Changed.length == 0 && this.trip.Deleted.length == 0){

                this.errortoast('There is nothing to update', 1000);
                this.loading();
                return false;
              }
              // no errors submit form
              this.submitFormTrips(button);

              return;

              // Hide Loading Toast
              this.loading();

          });

            
        },

        submitFormTrips(button = null){


          this.trip.submit('post', this.api + this.controller + 'updateTrips/' + this.pageid).then(success=>{

                this.showToast('Trips Updated Successfully');

                this.trip.Changed = [];
                this.trip.Deleted = [];

                this.goBack();
              
            }).catch(error=> {

              // Hide Loading Toast
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

              this.getLocations();

          }, 300);


        },

        removeThisTrip(index){

          this.trip.Deleted.push(this.trip.tripsDetails[index]);

          let i = this.trip.Changed.map(item => item.tripNumber).indexOf(this.trip.tripsDetails[index].tripNumber);
          if(i != '-1'){
            this.trip.Changed.splice(i, 1);
          }

          this.trip.tripsDetails.splice(index, 1);
          

        },

        checkErrors(field){

          if(this.errors.first(field)){
            return "danger";
          }

        },

        settripEquipmentPanel(index){

          this.clearDetailsForm();

          this.trip.index = index;

          var tripsDetailsForm = this.trip.tripsDetails[index];

          for (let ld in tripsDetailsForm){

             this.trip[ld] = tripsDetailsForm[ld];

           }

          this.tripEquipmentPanelOpen = true;

        },

        savetripEquipmentPanel(){

          this.trip.tripsDetails[this.trip.index] = 

              {
              trip_id: this.trip.trip_id,
              tripNumber: this.trip.tripNumber,
              consignmentNumber: this.trip.consignmentNumber,
              startingDate: this.trip.startingDate,
              detailsofGood: this.trip.detailsofGood,
              tripLocations: this.trip.tripLocations,
              wieghtdimension: this.trip.wieghtdimension,
              index: this.trip.index,
              status: 1,
              };

          this.trip.Changed.push(this.trip.tripsDetails[this.trip.index]);

          this.tripEquipmentPanelOpen = false;

          this.clearDetailsForm();

        },

        clearDetailsForm(){

          this.trip.consignmentNumber = '';
          this.trip.startingDate = '';
          this.trip.detailsofGood = '';
          this.trip.wieghtdimension = '';
          this.trip.tripLocations = [];

        },

        goBack(){

          this.$router.push(this.controller + this.pageid + '/edit');

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

        loading(show = null) {

              this.isLoading = true;

              const vm = this.toast;

              if(show != null){

                this.toast = this.$toast.loading('Saving...');

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

        setLocationStartEnd(){
          
          for (let field in this.trip.selectedLocations ){

                  var total = this.trip.selectedLocations.length;

                   this.trip.selectedLocations[field].positing = '';
                   this.trip.selectedLocations[total - 1].positing = 'Ending Point';
                   this.trip.selectedLocations[0].positing = 'Starting Point';

          }

          if(this.trip.tripLocations.length > 0){
            for (let field in this.trip.tripLocations){

                  var total = this.trip.tripLocations.length;

                   this.trip.tripLocations[field].positing = '';
                   this.trip.tripLocations[total - 1].positing = 'Ending Point';
                   this.trip.tripLocations[0].positing = 'Starting Point';              

            }
          }

        },

      },

  }
</script>