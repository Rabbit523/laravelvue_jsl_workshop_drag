<template>

  <div>


    <c-panel :title="'Trips of <strong>' + equipmentNumber + '</strong>'">
      
       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            <c-button @click="settripEquipmentPanel()"  type="primary" outline>Add Missing Trip</c-button>
            <c-button @click="addExpense()" type="primary" outline>Add Expense</c-button>
            <c-button @click="addFuel()" type="primary" outline>Add Fuel</c-button>
            <c-button @click="openReplace()" type="primary" outline>Replace Equipment</c-button>
          </c-button-group>

          <template slot="right">
            
            <p></p>
            <c-dropdown>
              <c-button type="primary">Options</c-button>
              <c-dropmenu slot="content">
                <c-dropmenu-item v-if="dispatch != undefined && dispatch.status == 11" @click="markComplete()" icon="icon-loop2" title="Mark Complete" />
                <c-dropmenu-item icon="icon-bin" v-if="deletedTrips.length > 0" type="danger" @click="goDeleted()" title="Deleted Trips" />
              </c-dropmenu>
            </c-dropdown>

          </template>

        </c-level>

<br>

           <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td @change="selectallchecks()"><c-form-checkbox v-model="selectall" /></td>
                <td class="u-text-center">Type</td>
                <td class="u-text-center">Created By</td>
                <td class="u-text-center">Locations</td>
                <td class="u-text-center">Actions</td>
                <td class="u-text-center">Log</td>
              </tr>

              <tr :class="checkforLog(trip.hasLog, trip.missing)" v-if="trip.status == 1" 
                  v-for="(trip, index) in trip.tripsDetails">
                <td>
                  <c-form-checkbox 
                    :disabled="false" 
                    :click="checkselectall()" 
                    :label="trip.tripNumber" 
                    v-model="select[index]" />
                </td>

                <td>

                  {{ trip.tripType.label }}

                </td>

                <td class="u-text-center">
                  <c-badge 
                    :type="trip.tagColor">{{ trip.createdBy }}
                  </c-badge>
                </td>

                <td class="u-text-center">
                  <c-badge 
                    type="success">{{ trip.tripLocations.length }}
                  </c-badge>
                </td>

                <td  class="u-text-center">
                  <c-button-group>
                    
                    <c-button 
                     
                      type="primary" 
                      @click="settripEquipmentPanel(index)" 
                      smart 
                      class="icon-tree3"
                      size="sm">
                    </c-button>

                    <c-button width="100px" type="primary" @click="setStaff(index)" size="sm">Staff</c-button>

                    <c-button 
                      v-if="trip.billTNumber == undefined" 
                      type="primary" 
                      class="icon-file-empty"
                      @click="setBillT(index)" 
                      smart size="sm">
                    </c-button>
                    
                    <c-button 
                      v-if="trip.billTNumber != undefined" 
                      type="primary" 
                      class="icon-file-text"
                      @click="getBillT(index)" 
                      smart size="sm">
                    </c-button>

                    <c-button 
                      type="primary" 
                      class="icon-bin"
                      @click="removeThisTrip(index)"
                      smart size="sm">
                    </c-button>

                  </c-button-group>
                  
                </td>

                <td class="u-text-center">
                  </c-button-group>

                    <c-button 
                      v-if="trip.hasLog" 
                      type="primary" 
                      @click="showlog(trip.trip_id)" 
                      smart 
                      class="icon-list2"
                      size="sm">
                    </c-button>

                  </c-button-group>
                </td>

              </tr>

            </tbody>

          </table>

          <div class="u-text-left" slot="footer">
            <c-button v-if="trip.Changed.length > 0 || trip.Deleted.length > 0" type="primary" @click="handleSubmitTrips()" v-once smart>Save Trips</c-button>
            
            
            <span class="form-help u-color-danger" v-text="trip.errors.get('Changed')" v-if="trip.errors.has('Changed')"></span>
            <span class="form-help u-color-danger" v-text="trip.errors.get('Created')" v-if="trip.errors.has('Created')"></span>
          </div>

        </div>

    </c-panel>

  <!-- Trips Model End -->

  <!-- Start Equipment replaceData Model -->
      
      <c-modal title="Replace Equipment" placement="left" v-model="replaceEquipmentModel">

        <table class="table table--striped table--hover">

             <tbody>

              <tr>

                <td>

                  <c-form-field label="Select Date(s)">
                    <c-flatpickr 
                      v-validate="'required'"
                      v-model="replaceEquipment.dates"
                      format="d-m-Y"
                      :config="enable"
                      name="dates"
                      value="" multiple
                    />
                  </c-form-field> 
                  <span class="form-help u-color-danger" v-if="!replaceEquipment.errors.has('dates')">{{ errors.first('dates') }}</span>
                </td>

              </tr>
              

              <tr>
                
                <td>

                    <c-multiselect
                      v-model="replaceEquipment.equipment"
                      v-validate="'required'"
                      track-by="value"
                      label="label"
                      name="equipment"
                      :options="equipments"
                      :searchable="true"
                      :close-on-select="true"
                      placeholder="Replaced By"></c-multiselect>
                      <span class="form-help u-color-danger" v-if="!replaceEquipment.errors.has('equipment')">{{ errors.first('equipment') }}</span>
                </td>

              </tr>

                <tr>

                  <td>

                     <c-form-field label="Reason">
                        <c-form-input 
                            v-validate="'required|min:3'" 
                            v-model="replaceEquipment.reason" 
                            name="reason" 
                            value="" 
                            type="text">
                        </c-form-input>
                        <span class="form-help u-color-danger" v-if="!replaceEquipment.errors.has('reason')">{{ errors.first('reason') }}</span>
                        <span class="form-help u-color-danger" v-text="replaceEquipment.errors.get('reason')" v-if="replaceEquipment.errors.has('reason')"></span>
                      </c-form-field>

                  </td>

                </tr>

                <tr v-if="verifyModel" v-for="equip, index in replaceEquipment.replaceData">

                  <td>
                    <c-form-checkbox 
                      :disabled="false" 
                      :label="equip.trip.tripNumber + ' - ' + equip.trip.tripType.label" 
                      v-model="equip.trip.selected" />
                  </td>

                  <td>
                    <c-button 
                     
                      type="primary" 
                      @click="settripEquipmentPanelFromreplaceData(equip.trip_id)" 
                      smart 
                      class="icon-tree3"
                      size="sm">
                    </c-button>
                  </td>

                </tr>

                <td>
                   <c-button v-if="!verifyModel"  type="danger" @click="replaceThisEquipment()" smart>Replace</c-button>
                   <c-button v-if="verifyModel"  type="danger" @click="replaceSelectedEquipment()" smart>Replace</c-button>
                </td>
              </tr>

            </tbody>

          </table>

          <span v-model="findIdsEquipment"></span>

          <div class="u-text-left" slot="footer">
            <c-button type="danger" @click="replaceEquipmentModel = false" smart>Close</c-button>
          </div>

      </c-modal>

       <!-- End Equipment replaceData Model -->

  <!-- Trips Details Start -->

    <c-modal :closable="isClosable" placement="right" title="<i class='icon-plus-circle u-color-primary u-mr-10'></i>Add Details" v-model="tripEquipmentPanelOpen">
      <c-form layout="horizontal" span="100" @submit.prevent="">
        
        <c-form-field v-if="trip.tripNumber != null" label="Trip Number">
          <c-form-input disabled="true" v-model="trip.tripNumber"></c-form-input>
        </c-form-field>

      <c-form-field label="Trip Type" :hidden="trip.trip_id != undefined">
      <c-multiselect
          v-model="trip.tripType"
          v-validate="'required'"
          track-by="value"
          label="label"
          :options="tripType"
          name="tripType"
          :searchable="true"
          :close-on-select="true"
          :multiple="false"

          placeholder="Select Type"></c-multiselect>
        <span class="form-help u-color-danger">{{ errors.first('tripType') }}</span>
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
          :close-on-select="false"
          :multiple="true"
          placeholder="Select Locations"></c-multiselect>
        <span class="form-help u-color-danger">{{ errors.first('locations') }}</span>
      </c-form-field>

      <c-form-field>
        <c-button type="primary" @click="areaModel = true" v-once smart>Add New Area</c-button>
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
          <c-flatpickr v-model="trip.startingDate" format="d-m-Y" />
        </c-form-field>

        <c-form-field label="Trip Ending Date">
          <c-flatpickr v-model="trip.endingDate" format="d-m-Y" />
        </c-form-field>
        
        <c-form-field label="Details of Goods">
          <c-form-input v-model="trip.detailsofGood"></c-form-input>
        </c-form-field>
        
        <c-form-field label="Weight & Dimensions">
          <c-form-input v-model="trip.wieghtdimension"></c-form-input>
        </c-form-field>
        
        <!-- Mark Missing -->

        <c-form-checkbox 
          label="Mark Missing" 
          v-model="trip.missing" />

        <c-form-field>
          <c-button v-if="verifyMe" type="primary" @click="savetripEquipmentPanel" smart>Save</c-button>
          <c-button @click="cancelMe" type="danger"  smart>Cancel</c-button>

        </c-form-field>
      </c-form>
    </c-modal>

  <!-- Trips Details End -->

  <!-- Bill T Number Model -->

      <c-modal title="Receive Document" placement="center" size="small" v-model="billTModel">
        
      <c-form-field :hidden="isHidden">

          <c-row>
            <span v-for="(sel, index) in select" v-if="sel == true && trip.tripsDetails[index].billTNumber == undefined">
              <c-col xs="24" sm="24" md="24" lg="24" xl="24">
                <c-tag 
                  closable 
                  v-show="visible" 
                  type="primary" 
                  @close="onClose(index)">{{ trip.tripsDetails[index].tripNumber }}</c-tag>
              </c-col>
            </span>
          </c-row>

        </c-form-field>

        <c-form-field  label="Receiving Date">
          <c-flatpickr 
            v-validate="'required'"
            v-model="billTReceiving" 
            :disabled="isHidden"
            name="document receiving date"
            value=""
            format="d-m-Y"
          />
        </c-form-field> 

        <c-form-field label="Doc Ref Number">
            <!-- <textarea :disabled="isHidden" class="form-textarea" v-model="billTNumber"/> -->
            <c-form-input :disabled="isHidden" v-model="billTNumber"></c-form-input>
        </c-form-field>

        <!-- <c-form-field label="Release Order">
          <c-form-input v-model="releaseOrder"></c-form-input>
        </c-form-field>

        <c-form-field label="D. Note">
          <c-form-input v-model="dNote"></c-form-input>
        </c-form-field>
 -->
        <div class="u-text-right" slot="footer" :hidden="isHidden">
          <c-button type="primary" @click="setIt()" v-once smart>Confirm</c-button>
        </div>
      </c-modal>
  <!-- Bill T Number Model -->

  <!-- Start Staff Model -->

    <template>
      
      <c-modal title="Trip Staff" placement="right" v-model="staffOpen">

        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td>Name</td>
                <td>Type</td>
              </tr>

              <template  v-for="(staff, index) in tripStaffs" v-if="staff != null">
              <tr >
                
                <td @click="replacedWith = undefined">
                  <c-form-radio 
                    name="selectedStaff"
                    :value="staff.staffName + '|' + staff.id + '|' + staff.type"
                    :label="staff.staffName"
                    v-model="selectedStaff" />
                </td>

                <td>
                  {{ staff.type }}
                </td>

              </tr>

            
              <tr  :hidden="selectedStaff != staff.staffName + '|' + staff.id + '|' + staff.type">

                <td colspan="2">

                  <c-form-field label="Select Date Range">
                    <c-flatpickr 
                      v-validate="'required'"
                      v-model="tripStaffs[index]['replaceDates']"
                      :config="enable"
                      format="d-m-Y"
                      name="Select Date Range"
                      value="" multiple
                    />
                  </c-form-field> 

                </td>

              </tr>
              

              <tr :hidden="selectedStaff != staff.staffName + '|' + staff.id + '|' + staff.type">
                <td :name="staff.staffName" :key="index">

                    <c-multiselect

                      v-model="tripStaffs[index]['replacedWith']"
                      track-by="value"
                      label="label"
                      accountNumber="accountNumber"
                      :options="fileterdstaffs"
                      :searchable="true"
                      :close-on-select="true"
                      placeholder="Replace with"></c-multiselect>

                </td>
                <td>
                   <c-button @click="replaceIt(index)" type="danger" smart>Replace</c-button>
                </td>
              </tr>

              <tr>
                <td colspan="2">
                  <hr>
                </td>
              </tr>

            
            </template>

            </tbody>

          </table>

            <span v-model="findIds"></span>
              

          <div class="u-text-left" slot="footer">
            <c-button type="danger" @click="staffOpen = false" smart>Close</c-button>
          </div>

      </c-modal>

      <span v-model="enabledDates"></span>

       <!-- End Staff Model -->

       <!-- Start Area Addition Model -->

      <c-modal title="Add New Area" placement="left" v-model="areaModel">
        
       <c-form-field label="Area Name">
          <c-form-input 
              v-validate="'required|min:3'" 
              v-model="areaForm.areaName" 
              :status="checkErrors('area name')" 
              name="area name" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger" v-if="!areaForm.errors.has('areaName')">{{ errors.first('area name') }}</span>
          <span class="form-help u-color-danger" v-text="areaForm.errors.get('areaName')" v-if="areaForm.errors.has('areaName')"></span>
        </c-form-field>

       <c-form-field label="City">

          <c-multiselect
            v-model="areaForm.selectedCity"
            track-by="value"
            label="label"
            :options="city"
            :searchable="true"
            :close-on-select="true"
            placeholder="Select City"></c-multiselect>
         <span class="form-help u-color-danger" v-text="areaForm.errors.get('selectedCity')" v-if="areaForm.errors.has('selectedCity')"></span>
        </c-form-field>
        

        <div class="u-text-left" slot="footer">
          <c-button type="primary" @click="addArea()" v-once smart>Save</c-button>
        </div>
      </c-modal>
  <!-- End Area Addition Model -->

  
    </template>

  <!-- Ask Date End -->





  </div>
</template>
<script>
var moment = require('moment');

import { format, addDays } from 'date-fns';

import {Form, Errors} from "./../common/base.js";

  export default {

    data() {
      return {


        trip: new Form({

          selectedEquipments: undefined,
          selectedLocations: [], 
          
          Changed: [],
          Deleted: [],
          Created: [],
          index: undefined,
          dispatch_id: '',
          equipment_id: '',

          //All Trips
          tripsDetails: [],

          //Active Trip Details
          tripNumber: '', 
          consignmentNumber: '', 
          startingDate: '',
          endingDate: '',
          tripType: undefined,
          detailsofGood: '',
          wieghtdimension: '',
          missing: false,
          trip_id: undefined,
            //Active Trip Locations
            tripLocations: [],
            replaceStartingDate: undefined,
            replaceEndingDate: undefined,
            dateRange: undefined,
        }),

        replace: new Form({

          replacement: [],

        }),

        replaceEquipment: new Form({

          equipment: undefined,
          dates: [],
          replaceData: [],
          replacement: [],
          reason: undefined,

        }),

        areaForm: new Form({

          areaName: '',
          selectedCity: '',

        }),

        tripType: [],
        city: [],
        areaModel: false,



        replaceEquipmentModel: false,
        verifyModel: false,

        selectall: undefined,
        select: [],
        billTNumber: undefined,
        billTReceiving: undefined,
        releaseOrder: undefined,
        dNote: undefined,

        billTModel: false,
        isHidden: false,
        visible: true,
        disabled: [],
        runthis: [],
        runthis2: [],
        enable: undefined,
        dispatch: undefined,
        

        selectedStaff: undefined,
        staffOpen: false,
        staffs: [], // 
        tripStaffs: [],
        replacedWith: undefined,
        
        replaceStartingDate: undefined,
        replaceEndingDate: undefined,
        

        tripEquipmentPanelOpen: false,

        locations: [],
        equipments: [],

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

        equipmentNumber: 'Loading...',
        
      }
    },

    components: {

   
    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      if(this.$route.params.id){

          this.pageid = this.$route.params.id;
          this.trip.equipment_id = this.$route.params.equipment_id;
          this.loading('Loading');
          this.getData();      
          this.isDisabled = true;   
      } 

      this.getLocations();

    },
    methods: {

        beforeEnter: function (el) {
          el.style.opacity = 0
        },
        enter: function (el, done) {
          Velocity(el, { height: 0, opacity: 1, fontSize: '0.8em' }, { duration: 300 })
          Velocity(el, { height: '100%', fontSize: '1em' }, { complete: done })
        },
        leave: function (el, done) {
          Velocity(el, { translateX: '15px', rotateZ: '50deg' }, { duration: 600 })
          Velocity(el, { rotateZ: '100deg' }, { loop: 2 })
          Velocity(el, {
            rotateZ: '45deg',
            translateY: '30px',
            translateX: '30px',
            opacity: 0
          }, { complete: done })
        },

        getData(pageid){

          this.trip.tripsDetails = [];

          axios
            .get('/trip/getTrips',{

              params: {

                equipment_id : this.trip.equipment_id,
                dispatch_id: this.pageid

              }

            })

            .then(response => {

                var data = response.data.data;
                
                this.createTrips(data);

                this.getDispatchStatus();

                this.getStaff();

                this.getTripType();

                this.getCity();

                this.getEquipments();

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
                if(currentIndex != '-1'){ //Index found
                  run = false; // run false so details does not push as new trip
                }
              }

              if(run == true){

                // Check if ending date isn't null
                if(data[field].tripEndDate != null){
                  var endDate = format(data[field].tripEndDate, 'DD-MM-YYYY');
                  var tripNumberEndDate = ' to ' + endDate;
                } else {
                  var endDate = '';
                  var tripNumberEndDate = '';
                }

                if(data[field].trips.flag == 1){
                  var flag = true;
                } else {
                  var flag = false;
                }
              
                this.trip.tripsDetails.push(
                {
                trip_id: trip_id,
                tripNumber:  'Trip: ' + format(data[field].tripStartDate, 'DD-MM-YYYY') + tripNumberEndDate,
                consignmentNumber: data[field].consignmentNo,
                tripLocations: [{
                  id: data[field].area.id,
                  value: data[field].area.id,
                  areaName: data[field].area.areaName,
                  position: data[field].sequence 
                }],
                startingDate: format(data[field].tripStartDate, 'DD-MM-YYYY'),
                endingDate: endDate,
                detailsofGood: data[field].detailsofGoods,
                wieghtdimension: data[field].weightandDimmension,
                createdBy: data[field].created_by.name,
                tagColor: data[field].created_by.tagColor,
                hasLog: data[field].trips.hasLog,
                tripType: data[field].tripType,
                status: data[field].status,
                billTNumber: data[field].trips.billT,
                billTReceiving: format(data[field].trips.billTReceiving, 'DD-MM-YYYY'),
                staff: data[field].staffs,
                replaced_trip_ids: [],
                replacedBy_id: data[field].equipment_trip.replacedby_id,
                missing: flag,
                });



                u++;
              
              } 

              else if(run == false) {

                let currentIndex = this.trip.tripsDetails.map(item => item.trip_id).indexOf(trip_id);

                 this.trip.tripsDetails[currentIndex].tripLocations.push(
                  {
                        id: data[field].area.id,
                        areaName: data[field].area.areaName,
                        value: data[field].area.id,
                        position: data[field].sequence
                  });

              }

            }

          }

        },

        getStaff(){

          axios
            .get('/staff')
              .then(response => {
                // this.staff = response.data.data;
                this.staffs = response.data.data;

            }).catch(error => {

                callback(error, error.response.data);

                this.errortoast('Something went wrong, refresh page to try again!', 1000);

            });


        },

        getDispatchStatus(){

          axios
            .get('/getEquipmentStatus/' + this.pageid + '/' + this.trip.equipment_id)

              .then(response => {
                // this.staff = response.data.data;
                this.dispatch = response.data.data;

            }).catch(error => {

                callback(error, error.response.data);

                this.errortoast('Something went wrong, refresh page to try again!', 1000);

            });

        },

        setEquipmentName(){

            let i = this.equipments.map(item => item.id).indexOf(parseInt(this.trip.equipment_id));
            console.log(i);
            if(i != '-1'){
              this.equipmentNumber = this.equipments[i].label;
            }
          

        },

        getTripType(){

          axios
            .get('/getTripType')
              .then(response => {
                // this.staff = response.data.data;
                this.tripType = response.data.data;

            }).catch(error => {

                callback(error, error.response.data);

                this.errortoast('Something went wrong, refresh page to try again!', 1000);

            });

        },

        getCity(){

          axios
            .get('/city')

              .then(response => {
                
                this.city = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        handleSubmitTrips(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.trip).then((result) => {
            
              if(this.trip.Changed.length == 0 && this.trip.Deleted.length == 0 && this.trip.Created.length == 0){

                this.errortoast('All trips are up to date!', 1000);
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


          this.trip.submit('post', this.api + this.controller + 'updateTrips/' + this.pageid,
            ).then(success=>{

                this.showToast('Trips Updated Successfully');

                this.trip.Changed = [];
                this.trip.Deleted = [];
                this.trip.Created = [];

                this.getData();

                // this.goBack();
              
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
            endingDate: this.trip.endingDate,
            detailsofGood: this.trip.detailsofGood,
            tripLocations: this.trip.tripLocations,
            wieghtdimension: this.trip.wieghtdimension,
            missing: this.trip.missing,
            index: this.trip.index

          })

         }

          this.tripEquipmentPanelOpen = true;

        },

        settripEquipmentPanelFromreplaceData(trip_id){

          var index = this.trip.tripsDetails.map(item => item.trip_id).indexOf(trip_id);

          this.settripEquipmentPanel(index);

        },

        savetripEquipmentPanel(){

          this.trip.Created = [];

          this.trip.tripsDetails[this.trip.index] = 

              {
              trip_id: this.trip.trip_id,
              tripNumber: 'Trip' + this.trip.tripsDetails.length + ' - Date : ' + this.trip.startingDate,
              consignmentNumber: this.trip.consignmentNumber,
              tripType: this.trip.tripType.id,
              startingDate: this.trip.startingDate,
              endingDate: this.trip.endingDate,
              detailsofGood: this.trip.detailsofGood,
              tripLocations: this.trip.tripLocations,
              wieghtdimension: this.trip.wieghtdimension,
              missing: this.trip.missing,
              index: this.trip.index
              };

          if(this.trip.trip_id !=  undefined){

            this.trip.Changed.push(this.trip.tripsDetails[this.trip.index]);

          } else {

            this.trip.Created.push(this.trip.tripsDetails[this.trip.index]);

          }

          this.tripEquipmentPanelOpen = false;

          this.clearDetailsForm();

          this.handleSubmitTrips();

        },

        clearDetailsForm(){

          this.trip.trip_id = undefined;
          this.trip.tripNumber = '';
          this.trip.index = undefined;
          this.trip.consignmentNumber = '';
          this.trip.startingDate = '';
          this.trip.endingDate = '';
          this.trip.detailsofGood = '';
          this.trip.wieghtdimension = '';
          this.trip.missing = false;
          this.trip.tripLocations = [];
          this.trip.index = undefined;

        },

        showConfirm() {

            this.$alert({
            type: 'info',
            title: 'Info',
            content: 'Are you sure you want to do this?',
            showCancel: true,
            onConfirm: () => {
            // do something..
            // if you want to stop closing alert, just return false
            },
            onCancel: () => {
            // do something..
            // if you want to stop closing alert, just return false
            }
          })
        },

        setIt(){

          var billT = [];
            for (let i in this.select){

              if(this.select[i] == true && this.trip.tripsDetails[i].billTNumber == undefined){

                billT.push({
                 trip_id: this.trip.tripsDetails[i].trip_id,
                 billT: this.billTNumber,
                 releaseOrder: this.releaseOrder,
                 dNote: this.dNote,
                 billTReceiving: this.billTReceiving,
                 curIndex: i,
                });

              }

            }

            this.loading('Setting Bill-T');

            axios

              .post(this.api + 'trip/setBillT/',{

                data: billT

              }).then(success=>{

                  this.billTModel = false;
                  this.setDisable(billT);
                  this.billTNumber = undefined;
                  this.select = [];
                  this.loading();

              }).catch(error=> {

                // Hide Loading Toast
                this.loading();

                this.errortoast();

              });

        },

        setStaff(index){

          this.tripStaffs = this.trip.tripsDetails[index].staff;
          this.staffOpen = true;

        },

        replaceIt(index){

          if(this.selectedStaff != undefined && this.tripStaffs[index].replacedWith != undefined && this.replace.replacement.length > 0){
            
            this.submitReplaceForm(index);

          } else {
            this.errortoast('Please select staff & dates to replace!', 600);
          }

        },

        replaceThisEquipment(){

          this.loading('Replacing Equipment');

            this.$validator.validateAll(this.replaceEquipment).then((result) => {
            

              if(result){

                var verify = this.verifyTripsMoving();

                if(verify){

                  // no errors submit form
                  this.submitFormReplaceEquipment();

                }

              } else {

                // Hide Loading Toast
                this.loading();

                return false;

              }


          });

        },

        verifyTripsMoving(){

          if(this.replaceEquipment.replaceData.length > 1){

            this.verifyModel = true;
            this.loading();
            return false;

          } else {

            this.replaceEquipment.replacement = this.replaceEquipment.replaceData;
            return true;

          }

        },

        replaceSelectedEquipment(){

          var replaceData = this.replaceEquipment.replaceData;

          for(let field in replaceData){

            if(replaceData[field].trip.selected){

              this.replaceEquipment.replacement.push({

                trip_id: replaceData[field].trip_id

              })

            }

          }

          this.submitFormReplaceEquipment();

        },

        submitFormReplaceEquipment(){

          this.replaceEquipment.submit('post', this.api + '/trip/replaceEquipment/' + this.trip.equipment_id + '/' + this.pageid
  
              ).then(success=>{

                this.showToast('Trips Updated Successfully');
                // var replaced = this.tripStaffs[index].replacedWith;
                // this.tripStaffs[index] = replaced;
                // this.selectedStaff = undefined;

                this.getData();
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        openReplace(index){

          if(this.equipments.length > 0){
              this.runthis2.push(1);
          } 
          this.replaceEquipmentModel = true;

        },

        getEquipments(){

          axios
            .get('/equipment')

            .then(response => {

                this.equipments = response.data.data;

                this.setEquipmentName(response.data.data);
                
            }).catch(error => {

                this.errortoast();

            });

        },

        submitReplaceForm(index){


          this.replace.submit('post', this.api + '/trip/replaceStaff/'
  
              ).then(success=>{

                this.showToast('Trips Updated Successfully');
                var replaced = this.tripStaffs[index].replacedWith;
                this.tripStaffs[index] = replaced;
                this.selectedStaff = undefined;

                this.getData();
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        addArea(){

          this.loading('Adding Area');

          this.$validator.validateAll(this.areaForm).then((result) => {
            
            if (result) {

              // no errors submit form
              this.submitFormArea();
              return;

            } else {

              alert('hit');
              // Hide Loading Toast
              this.loading();

              this.errortoast();

            }

          });

        },

        submitFormArea(){


          this.areaForm.submit('post', '/area/').then(success=>{

                this.showToast('Area Added Successfully');

                this.getLocations();

                this.areaModel = false;                
                
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        checkforLog(Log, missing){

          if(missing){
           return 'u-color-grey-lightest u-bg-grey'; 
          } else if(Log) {
            return 'u-color-grey-lightest u-bg-warning';
          }

        },

        onClose(index){

          this.select[index] = false;
          this.select.splice(index, 1);
          this.selectall = false;

        },

        setBillT(index){

          this.isHidden = false;
          this.billTNumber = undefined;
          this.billTReceiving = undefined;
          this.select[index] = true;
          this.billTModel = true;

        },

        getBillT(index){

          // this.select[index] = true;
          this.isHidden = true;
          this.billTNumber = this.trip.tripsDetails[index].billTNumber;
          this.billTReceiving = this.trip.tripsDetails[index].billTReceiving;
          this.billTModel = true;

        },

        selectallchecks(){

          for (let field in this.trip.tripsDetails){
            
            if(this.selectall == true){
              this.select[field] = true;
            } else {
              this.select[field] = false;
            }
          }

        },

        checkselectall(){

          var set = true
          var i = this.trip.tripsDetails.length;
          var u;

          for (u = 0; u < i; u++){
            if(this.select[u] == undefined){
              set = false;
            }
            if(this.select[u] != undefined && this.select[u] == false){
              set = false;
            }

          }

          this.selectall = set;

        },

        setDisable(billTarray){

          for (let i in billTarray){
          
              this.trip.tripsDetails[billTarray[i].curIndex].billTNumber    = billTarray[i].billT;
              this.trip.tripsDetails[billTarray[i].curIndex].releaseOrder   = billTarray[i].releaseOrder;
              this.trip.tripsDetails[billTarray[i].curIndex].dNote          = billTarray[i].dNote;
              this.trip.tripsDetails[billTarray[i].curIndex].billTReceiving = billTarray[i].billTReceiving;
          
          }

        },

        goDeleted(){

          this.$router.push(/tripmanagement/ + this.pageid + '/' + this.trip.equipment_id + '/deleted');

        },

        addExpense(){

          if(this.dispatch.status == 1){

            this.showAlert('warning', 'Warning', 'You want to change status to arrived?', 'letGo');
            

          } else {

           this.$router.push('/dispatchexpense/' + this.pageid + '/' + this.trip.equipment_id + '/' + this.dispatch.status + '/' + this.equipmentNumber);

         }

        },

        showAlert(type, title, message, stay) {

          this.$alert({

            type: type,
            title: title,
            content: message,
            showCancel: true,
            onConfirm: () => {

              this.changeStatus(this.dispatch.status, stay);

            },
            onCancel: () => {
              
            }
          })

        },

        changeStatus(status, stay){

          this.areaForm.submit('post', '/dispatchEquipment/ChangeEquipmentStatus/' + this.pageid  + '/' + this.trip.equipment_id + '/' + status).then(success=>{

                this.showToast('Record Updated Successfully');

                this.dispatch.status = success.status;

                if(stay != null){
                  this.$router.push('/dispatchexpense/' + this.pageid + '/' + this.trip.equipment_id + '/' + this.dispatch.status);
                }
              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

         markComplete(){

          this.showAlert('warning', 'Warning', 'You want to change status to completed?');

        },

        addFuel(){

          this.$router.push('/dispatchfuel/' + this.pageid + '/' + this.trip.equipment_id + '/' + this.dispatch.status + '/' + this.equipmentNumber);

        },

        replaceEquipmentPanel(){

          //this.$router.push('/replace_equipment/' + this.pageid + '/' + this.trip.equipment_id);

        },

        //Log Functions
        showlog(trip_id){

          this.$router.push('/tripmanagement/' + trip_id + '/log');

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

          cancelMe(){

            if(this.trip.trip_id == undefined){
              this.trip.tripsDetails.splice(this.trip.index, 1);
            }

            this.tripEquipmentPanelOpen = false;

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

        checkDates(){

          var flag = true;
          if(this.replaceStartingDate != undefined && this.replaceEndingDate != undefined){
            flag = false;
          }

          return flag;

        },

        maxEndingDate: function (){

          if(this.trip.tripsDetails.length > 0){
            return this.trip.tripsDetails[this.trip.tripsDetails.length-1].startingDate;
          }

        },

        maxStartingDate: function (){

          if(this.trip.tripsDetails.length > 0){
            return this.trip.tripsDetails[0].startingDate;
          }

        },

        fileterdstaffs: function (){

          if(this.selectedStaff != undefined){

            var selectable = this.staffs;

            for(let i in this.tripStaffs){

              if(this.tripStaffs[i] != null){

                var id = this.tripStaffs[i].id;

                let index = selectable.map(item => item.id).indexOf(id);

                if(index != '-1'){
                  selectable.splice(index, 1);
                }

              }

            }

            var type = this.selectedStaff.split('|')[2];

            return selectable.filter(function(elem){
                if(elem.type == type) return elem;
            });

            // return selectable;

            } else {
              return [];
            }

        },

        tripDates: function (){

          var tripNumber = [];

          var u = 1;
          var data = this.trip.tripsDetails;

          for (let i in data){
            tripNumber.push({

              label: 'Trip' + (u) + ' - Date : ' + data[i].startingDate,
              value: data[i].trip_id

            });
            u++;
          }

          return tripNumber;

        },

        deletedTrips: function(){

           return this.trip.tripsDetails.filter(function(elem){
                if(elem.status == 9) return elem;
            });

        },

        verifyMe(){

          if(this.trip.tripLocations.length < 2 || this.trip.startingDate == ''){
            return false;
          }

          var index = this.trip.index;

          if(index != undefined){

          var tripsDetailsForm = this.trip.tripsDetails[index];

          for (let ld in tripsDetailsForm){

             if(this.trip[ld] != tripsDetailsForm[ld]){
                return true;
             }

           }

         } else {

            return true;

         }

          return false;

        },

        enabledDates: function (){

          var data = this.trip.tripsDetails;          
          var options = {
            enable: [],
          };
          
          for (let i in data){

            options.enable.push(data[i].startingDate);

          }
            
            this.enable = options;
            if(options.enable.length > 0){
              this.runthis.push(1);
            } 

        },

        findIds: function (){

          var self = this.tripStaffs;

          if(this.selectedStaff != undefined){

            var selected = this.selectedStaff.split('|');
            var selected_id = selected[1];

            this.replace.replacement = [];

            for (let i in self){
              
              if(self[i] != null && self[i].replaceDates && self[i].replacedWith){
                var arr = []
                var broken = self[i].replaceDates.split(', ');
                for (var k = 0; k < broken.length; k++){
                  
                  // var index = this.trip.tripsDetails.map(item => item.startingDate).indexOf(broken[k]);
                  var trip = this.trip.tripsDetails.filter(function(elem){
                    if(elem.startingDate == broken[k]) return elem;
                  });

                  

                  if(selected_id == self[i].id){

                    for (let t in trip ){

                      this.replace.replacement.push({

                        trip_id:  trip[t].trip_id,
                        originalStaff: self[i].id,
                        replaceBy: self[i].replacedWith.id

                      });

                    }

                  }
                  
                }

                
              } else if(self[i] == null) {
                self.splice(i, 1);
              }

            }

          }

        },

        findIdsEquipment: function (){

          var self = this.replaceEquipment.dates;

          if(this.replaceEquipment.equipment != undefined && this.replaceEquipment.dates.length > 0){

            this.replaceEquipment.replaceData = [];

                var arr = []
                var broken = self.split(', ');

                for (var k = 0; k < broken.length; k++){
                  
                  var trip = this.trip.tripsDetails.filter(function(elem){
                    if(elem.startingDate == broken[k]) return elem;
                  });

                    for (let t in trip ){

                      this.replaceEquipment.replaceData.push({

                        trip_id:  trip[t].trip_id,
                        trip: trip[t],

                      });

                    }
                  
                }

            

          }

        },

        dateRange: {

            // getter
            get: function () {
              return this.trip.dateRange;
            },
            // setter
            set: function (newValue) {

              var arr = []
              for (let i in newValue.split(',')){
                arr.push(i);
              }
              arr.push(newValue)
              console.log(arr);

              this.trip.dateRange = newValue;

              var dates = newValue.split(' to ');
              
              if(dates.length > 1){
                this.trip.replaceStartingDate =  dates[0];
                this.trip.replaceEndingDate = dates[1];
              }

            }

          },

      },

  }
</script>