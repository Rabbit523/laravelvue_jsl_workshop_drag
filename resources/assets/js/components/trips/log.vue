<template>

  <div>


    <c-panel title="Trips Log">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            <c-button class="u-bg-primary u-color-grey-lightest" type="primary" outline>Log</c-button>
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

           <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td>Trip Number</td>
                <td>Created By</td>
                <td>Details</td>
                
              </tr>

              <tr v-for="(trip, index) in trip.tripsDetails">
                <td width="40%">{{ trip.tripNumber }}</td>
                <td>
                  <c-badge :type="trip.tagColor">{{ trip.createdBy }}</c-badge>
                  <c-badge v-if="trip.status == 1" type="success">Active</c-badge>
                  <c-badge v-if="trip.status == 0" type="success">Last Active</c-badge>
                </td>
                <td> <c-button type="primary" @click="settripEquipmentPanel(index)" smart size="sm">View</c-button></td>
                
              </tr>

            </tbody>

          </table>

          <div class="u-text-left" slot="footer">
          
            
          </div>

        </div>

    </c-panel>

 

  <!-- Trips Model End -->

  <!-- Trips Details Start -->

    <c-modal :closable="isClosable" placement="right" title="<i class='icon-info u-color-primary u-mr-10'></i>View Details" v-model="tripEquipmentPanelOpen">
      <c-form layout="horizontal" span="100" @submit.prevent="">
        
        <c-form-field label="Trip Number">
          <c-form-input disabled="true" v-model="trip.tripNumber"></c-form-input>
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
          <c-form-input disabled="true" v-model="trip.consignmentNumber"></c-form-input>
        </c-form-field>

        <c-form-field label="Trip Starting Date">
          <c-flatpickr disabled="true" v-model="trip.startingDate" />
        </c-form-field>
        
        <c-form-field label="Details of Goods">
          <c-form-input disabled="true" v-model="trip.detailsofGood"></c-form-input>
        </c-form-field>
        
        <c-form-field label="Weight & Dimensions">
          <c-form-input disabled="true" v-model="trip.wieghtdimension"></c-form-input>
        </c-form-field>
        
        <c-form-field>
          
          <c-button @click="tripEquipmentPanelOpen = false" type="danger"  smart>Close</c-button>

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

          selectedLocations: [], 
          
          index: undefined,
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

      if(this.$route.params.trip_id){

        this.pageid = this.$route.params.trip_id;
         this.loading('Loading');
         this.getData();      
         this.isDisabled = true;   
      } 

      

    },
    methods: {

        getData(pageid){

          this.trip.tripsDetails = [];

          axios
            .get('/dispatch/getTripsByID',{

              params: {

                trip_id: this.pageid

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
              var status = data[field].status;

              if(this.trip.tripsDetails.length > 0){
                let currentIndex = this.trip.tripsDetails.map(item => item.status).indexOf(status);
                if(currentIndex != '-1'){
                  run = false;
                }
              }

              if(run == true){
              
                this.trip.tripsDetails.push(
                {
                trip_id: trip_id,
                tripNumber:  'Trip - Date : ' + format(data[field].tripStartDate, 'DD-MM-YYYY'),
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
                createdBy: data[field].name,
                tagColor: data[field].tagColor,
                status: data[field].status
                });

                // u++;
              
              } else if(run == false) {

                let currentIndex = this.trip.tripsDetails.map(item => item.status).indexOf(status);

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

        

        setEdit(pageid){

          this.postUrl = this.api + this.controller + pageid;

          this.pageTitle = this.controllerName + ' > Edit';

          this.isDisabled = true;

          this.pageid = pageid;


        },

        settripEquipmentPanel(index){

          this.clearDetailsForm();

          if(index != null){
          
          this.trip.index = index;

          var tripsDetailsForm = this.trip.tripsDetails[index];

          for (let ld in tripsDetailsForm){

             this.trip[ld] = tripsDetailsForm[ld];

           }

         } else {

          this.trip.index = this.trip.tripsDetails.length;

          this.trip.tripsDetails.push({

            trip_id: this.trip.trip_id,
            tripNumber: this.trip.tripNumber,
            consignmentNumber: this.trip.consignmentNumber,
            startingDate: this.trip.startingDate,
            detailsofGood: this.trip.detailsofGood,
            tripLocations: this.trip.tripLocations,
            wieghtdimension: this.trip.wieghtdimension,
            index: this.trip.index

          })

         }

          this.tripEquipmentPanelOpen = true;

        },

        clearDetailsForm(){

          this.trip.tripNumber = '';
          this.trip.index = undefined;
          this.trip.consignmentNumber = '';
          this.trip.startingDate = '';
          this.trip.detailsofGood = '';
          this.trip.wieghtdimension = '';
          this.trip.tripLocations = [];
          this.trip.index = undefined;

        },


        //Log Functions

        showlog(trip_id){

          this.$router.push('tripmanagement/' + trip_id + '/log');

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