<template>

  <div>

    <c-panel title="Trips">
      
       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            <c-button type="primary" outline>Add Missing Trip</c-button>
            <c-button type="primary" outline>Add Expense</c-button>
            <c-button type="primary" outline>Add Fuel</c-button>
            <c-button type="primary" outline>Replace Equipment</c-button>
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

      </div>

    </c-panel>


      <c-panel>

          <c-tabs type="underline" size="lg">

            <div v-for="did in dispatch_ids">

              <c-tabs-pane :label="getDispatchNumber(did)" :name="did">    

              <c-tabs class="u-mt-20" type="pills" size="md">

                <c-tabs-pane label="Trips" name="trips">

                      <table  class="table table--striped">
                        <thead>
                          <tr>
                            <td width="10%">Staff Name</td>
                            <td width="8%">Type</td>
                            <td width="5%">Total Trips</td>
                            <td width="8%">From - To</td>
                            <td width="2%">Calculate</td>
                            <td width="2%">Total (Inc Driver)</td> 
                            <td width="4%">Self Total</td>
                          </tr>
                        </thead>
                        <tbody>
                         

                        <tr v-for="st, index in staff">
                          <td>{{ st.staffName }}</td>
                          <td>{{ st.type }} - {{ st.salaryType }}</td>
                          <td>{{ getTrips(st.id, did).length }}</td>
                          <td>{{ findDates(st.id, did) }}</td>
                          <td><c-button-group>
                                <c-button 
                                  type="primary" 
                                  @click="setStaffTrip(st, did)" 
                                  smart 
                                  class="icon-file-stats2"
                                  size="sm">
                                </c-button>
                              </c-button-group>
                          </td>
                          <td>{{ grabStaffTotal(st.id, did) }}</td>
                          <td>{{ grabSelfTotals(st.id) }}</td>
                        </tr>

                        </tbody>

                      </table>

                </c-tabs-pane>

                <c-tabs-pane label="Expenses" name="expenses">

                      <table  class="table table--striped">
                        <thead>
                          <tr>
                            <td width="10%">Expense Name</td>
                            <td width="8%">Amount</td>
                            <td width="8%">Date</td>
                          </tr>
                        </thead>
                        <tbody>
                         

                        <tr v-for="exp, index in expenses" v-if="exp.dispatch_id == did">
                          <td>{{ exp.expenseTypes }}</td>
                          <td>Rs {{ exp.actualAmount }}/-</td>
                          <td>{{ exp.expenseDate }}</td>
                        </tr>

                          

                        </tbody>

                      </table>
                  
                </c-tabs-pane>

                <c-tabs-pane label="Fuel & Oil" name="fuelnoil">

                      <table  class="table table--striped">
                        <thead>
                          <tr>
                            <td width="10%">Expense Name</td>
                            <td width="8%">Amount</td>
                            <td width="8%">Date</td>
                          </tr>
                        </thead>
                        <tbody>
                         
                        <tr v-for="exp, index in oilnFuel" v-if="exp.dispatch_id == did && exp.actualAmount > 0">
                          <td>{{ exp.expenseTypes }}</td>
                          <td>Rs {{ exp.actualAmount }}/-</td>
                          <td>{{ exp.expenseDate }}</td>
                        </tr>

                        </tbody>
                      </table>

                </c-tabs-pane>
        
              </c-tabs>    

              </c-tabs-pane>

            </div>

            <div>

              <c-tabs-pane label="Trips" name="trips">    

                 <table  class="table table--striped">
                        <thead>
                          <tr>
                            <td width="50%">Trip</td>
                            <td width="50%">Kilometers</td>
                          </tr>
                        </thead>
                        <tbody>
                         
                        <tr v-for="trip, index in unique_trip_ids">
                          <!-- <td>{{ trip.area.areaName }}</td> -->
                          <td><span v-for="areas in getTripAreas(trip.id)">{{ areas.area.areaName }} > </span></td>
                          <td>
                            <c-form-input 
                              v-model="unique_trip_ids[index]['km']" 
                              name="kilometers" 
                              type="text">
                            </c-form-input>
                          </td>
                        </tr>

                        </tbody>
                      </table>

              </c-tabs-pane>              

            </div>

            <c-tabs-pane label="Summary" name="summary">    

              <div v-for="st, index in staff" >

                <h5>{{ st.staffName + ' - ' + st.type }}</h5>

              <table  class="table table--striped">

                  <thead>
                    <tr>
                      <td width="10%">Title</td>
                      <td width="8%">Amount</td>
                    </tr>
                  </thead>
                  <tbody>
                   

                  <tr>
                    <td>Self Total</td>
                    <td>Rs {{ grabSelfTotals(st.id) }}/-</td>
                  </tr>

                  <tr v-if="st.type == 'Driver'">
                    <td>Other Staff Total</td>
                    <td>Rs {{ grabStaffTotals(st.id) }}/-</td>
                  </tr>

                  <tr v-if="st.type == 'Driver'">
                    <td>Expense</td>
                    <td>Rs {{ getMegaExpenseTotal(st.id) }}/-</td>
                  </tr>

                  <tr v-if="st.type == 'Driver'">
                    <td>Advance</td>
                    <td>{{ getMegaAdvance(st.id) }}</td>
                  </tr>

                  <tr>
                    <td>Grand Total</td>
                    <td>Rs {{ getMegaSummary(st.id) }}/-</td>
                  </tr>

                  <tr>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr v-if="st.type == 'Driver'" class="u-bg-grey-light u-color-grey-lightest">
                    <td>Fuel & Oil</td>
                    <!-- <td>{{ TotalAmount(st.id) }}</td> -->
                    <td>Rs {{ getMegaOilTotal(st.id) }}/-</td>
                  </tr>

                  </tbody>

                </table>

              </div>

            </c-tabs-pane>

        </c-tabs>

      </c-panel>

      <span v-model="expenseStaff"></span>

   <!-- Bill T Number Model -->

      <c-modal title="Staff Trips" :closable="false" placement="right" v-model="staffTrips">

        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td @change="selectallchecks()">
                  <c-form-checkbox 
                    v-model="selectallTrips"
                    label="Date" />
                </td>
                <td>Days</td>
                <td>Trip Type</td>
                <td>Total</td>
              </tr>

              <tr v-for="trip, index in selectedStaffTrips(selectedStaff, selectedDispatch_id)">
                <td @change="checkselectall()">
                  <c-form-checkbox 
                  :disabled="false" 
                  :label="getTripLabel(trip)" 
                  v-model="trip.selected" />
                </td>
                <td>{{ trip.days }}</td>
                <td>{{ trip.tripType.label }}</td>
                <td>{{ grabTotal(trip) }}</td>
              </tr>

            </tbody>

        </table>

        

        <div class="u-text-left" slot="footer">
          <c-button type="primary" @click="variationModel = true, getSavedVariations()" v-if="checkForSelection()" v-once smart>Select Variations</c-button>
          <c-button type="warning" @click="closeStaffTripModel()" v-if="variationModel == false" v-once smart>Close</c-button>
        </div>
      </c-modal>
  <!-- Bill T Number Model -->

<!-- Bill T Number Model -->

      <c-modal title="Select Variation" :closable="false" placement="left" v-model="variationModel">

        <c-level>
          <template slot="left">
            <c-badge type="success" class="u-text-left u-h5">Rs. {{ total }}</c-badge>    
          </template>

          <template slot="right">
            <c-badge type="warning" class="u-text-right u-h5">{{ days }} Days</c-badge>    
          </template>
        </c-level>

        
        

        <table class="table table--striped table--hover">

             <tbody>

              
              <tr v-for="vari, index in salaryVariations">
                
                <td width="50%">
                  <c-form-checkbox 
                  :disabled="false" 
                  :click="updateSelected(vari.variationType)"
                  :value="vari[selectedStaffType]"
                  :label="vari.variation" 
                  v-model="selectVari[index]" />

                </td>
                <td width="30%">

                  {{ 'Rs ' + vari[selectedStaffType] }} - {{ vari.variationType }}

                </td>
                <td width="20%">
                  <span v-if="selectVari[index] && vari.variationType == 'Per Day'">Rs {{ vari[selectedStaffType] * days }}/-</span>
                  <span v-if="selectVari[index] && vari.variationType == 'Per Trip'">Rs {{ vari[selectedStaffType] * selectedTripsCount }}/-</span>
                </td>
              </tr>

              <tr>
                <td width="50%"></td>
                <td width="30%"></td>
                <td width="20%"><span v-if="(total*days) > 0"> Rs {{ total }}/-</span></td>
              </tr>

            </tbody>

        </table>

        <div class="u-text-left" slot="footer">
          <c-button type="primary" @click="saveVariations()" v-once smart>Apply Variations</c-button>
          <c-button type="warning" @click="closeVariationsModel()" v-once smart>Close</c-button>
        </div>
      </c-modal>
  <!-- Bill T Number Model -->

  </div>

  
  
</template>

<!-- Ask Date End -->

<script>
var moment = require('moment');

import { format, addDays } from 'date-fns';

import {Form, Errors} from "./../common/base.js";
import Multiselect from './../../../vendors/cover/vendors/multiselect'
import style from './../../../vendors/cover/vendors/multiselect/style.css';

import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
import style2 from './../../../vendors/cover/vendors/flatpickr/style.css';

  export default {

    data() {

      return {

        trip: new Form({

          tripsDetails: [],
          
        }),

        staff: [],
        staff_ids: [],
        allTrips: [],
        filteredResult: [],
        filteredByDispatch: [],
        staffTrips: false,
        variationModel: false,

        selectedStaff: undefined,
        selectedStaffType: undefined,
        selectedDispatch_id: undefined,
        selectedStaffTripsArray: [],
        select: [],
        selectVari: [],
        total: [],
        isTotal: [],
        staffTotal: [],
        selectallTrips: undefined,
        
        days: 0, // count of number of days of selected staff trips
        selectedTripsCount: 0, //count of selected staff trips

        salaryVariations: [],

        advancepayments: [],

        allExpenses: [],

        grandStaffTotal: 0,
        grandExpense: 0,
        grandAdvance: 0,

        dispatch_ids: [],
        controller: '/voucher/',
        controllerName: 'Voucher',
        basePost: '',
        api: '',
        postUrl: '',
        pageid: '',
        equipment_id: '',

        trip_ids: [],
        unique_trip_ids: [],
        trip_coordinates: [],
        isCoordinates: false,
        km: [],

      }

    },

    components: {

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr,

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      if(this.$route.params.dispatch_ids){

          var ids = this.$route.params.dispatch_ids.split(',');
          
          for(let field in ids){
            if(Number(ids[field])){
              this.dispatch_ids.push(ids[field]);
            }
          }

          this.equipment_id = this.$route.params.equipment_id;
          this.loading('Loading');
          this.getData();      

      } 


    },

    methods: {

        getData(pageid){

          this.trip.tripsDetails = [];

          axios

            .post('/trip/getAllTrips',{

                equipment_id : this.equipment_id,
                dispatch_id: this.dispatch_ids

            })

            .then(response => {

                this.setStaff(response.data.data);

                this.allTrips = response.data.data;

                this.getVariation();

                this.getExpenses();

                this.getAdvances();

                this.getPostedVariations();
                
                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        getVariation(){

          axios

            .get('/getSalaryVariation')

            .then(response => {

                this.salaryVariations = response.data.data;
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        getExpenses(){


          axios

            .get('/expense/getMultiExpense',{

              params: {

                equipment_id : this.equipment_id,
                dispatch_id: this.dispatch_ids

              }

            })

            .then(response => {

                this.allExpenses = response.data.data;
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });


        },

         getAdvances(){

          var staff_ids = [];

          for(let i in this.staff){

            this.staff_ids.push(this.staff[i].id);

            if(this.staff[i].type == "Driver"){
              staff_ids.push(this.staff[i].id);
            }

          }

          axios

            .get('/advancepayments/getByStaff',{

              params: {

                staff_ids : staff_ids,
                dispatch_ids: this.dispatch_ids

              }

            })

            .then(response => {

                this.advancepayments = response.data.data;
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });


        },

        setStaff(data){
          
          for (let i in data){

            var subData = data[i].staffs;
            
            for (let u in subData){

              var run = true;

              if(subData[u] != null){
                          
                  if(this.staff.length > 0){
    
                    var index = this.staff.map(item => item.id).indexOf(subData[u].id);
                    if(index != '-1'){
                      run = false;
                    }
    
                  }
    
                  if(run == true){
                    this.staff.push(subData[u])
                  }

              } else {

                subData.splice(u, 1);

              }


            }

          }

        },

        getTrips(staff_id, dispatch_id){

          var trips = [];

          var trip_coordinates = [];

          for (let i in this.allTrips){

            var run = true;

            if(this.allTrips[i].area.coordinates != ''){
              trip_coordinates.push(this.allTrips[i].area.coordinates);
            }

            if(trips.length > 0){
              
              var index = trips.map(item => item.trip_id).indexOf(this.allTrips[i].trip_id);
              
              if(index >= 0){
                run = false;
              }

            }

            if(run == true){

              trips.push(this.allTrips[i]);

            }

            this.pushUniqueId(this.allTrips[i].trip_id);

          }

          this.staff;
          var that = this;

          var array = _.filter(trips, function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id && group.dispatch_id == dispatch_id; //that.filter
              });
              return childArray.length > 0;
          });

          var allArray = _.filter(trips, function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id; //that.filter
              });
              return childArray.length > 0;
          });

          this.filteredResult[staff_id] = allArray;
          this.trip_coordinates = trip_coordinates;
          // if(this.isCoordinates == false){
          //   this.getDistance();
          // }

          return array;

        },

        pushUniqueId(id){

          var run = true;

          if(this.unique_trip_ids.length > 0){
              
            var index = this.unique_trip_ids.map(item => item.id).indexOf(id);
            
            if(index >= 0){
              run = false;
            }

          }

          if(run == true){

            this.unique_trip_ids.push({'id': id});
            

          }

        },

        getTripAreas(id){

          var trips = this.allTrips.filter(function(elem){
                if(elem.trip_id == id) return elem;
          });

          return trips;

        },

        getDistance(){

          var destinations = undefined;
          var trip_coordinates = this.trip_coordinates;

          this.isCoordinates = true;          

          if(this.trip_coordinates.length  > 0){

            for (let i in this.trip_coordinates){
              if(i > 0){
                if(destinations == undefined){
                  destinations = this.trip_coordinates[i];
                } else {
                  destinations = destinations + '|' + this.trip_coordinates[i];
                }
              }
            }

          axios

            .get('/getDistance',{

              params: {

                origin : this.trip_coordinates[0],
                destinations: destinations,
                key: 'AIzaSyCZJde2FpPnqpSudJ576bCJmWkUF5E-Vpk'

              }

            })

            .then(response => {

                // console.log(response);
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

          }

        },

        findDates(staff_id, dispatch_id){

          var dates = [];
          var data = this.filteredResult[staff_id];

          for (let i in data){

            dates.push({
              dispatch_id: data[i].dispatch_id,
              startingDate: data[i].tripStartDate,
              endingDate: data[i].tripEndDate
            })

          }

          var refined = dates.filter(function(elem){
                if(elem.dispatch_id == dispatch_id) return elem;
          });

          if(refined.length == 0) return;

          var result = refined[0].startingDate;

          if(refined.length > 1){

            var result = result + ' to ';

            if(refined[refined.length-1].endingDate == null){
                var result = result + refined[refined.length -1].startingDate;
            } else {
                var result = result + refined[refined.length -1].endingDate;
            }

          }

          return result;

        },

        selectedStaffTrips(staff_id, dispatch_id){

          var data = _.filter(this.filteredResult[staff_id], function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id && group.dispatch_id == dispatch_id; //that.filter
              });
              return childArray.length > 0;
          });

          this.selectedStaffTripsArray = data;

          return data;

        },

        getDispatchNumber(did){

          if(this.allTrips.length > 0){

            var data = this.allTrips.filter(function(elem){
                if(elem.dispatch_id == did) return elem;
            });

            return data[0].dispatchNo;

          } else {

            return 'loading';

          }

        },

        setStaffTrip(staff, dispatch_id){

          this.staffTrips = true;
          this.selectedStaff = staff.id;
          this.selectedStaffType = staff.type;
          this.selectedDispatch_id = dispatch_id;

        },

        getTripLabel(trip){

          var label = trip.tripStartDate;

          trip.days = 1;

          if(trip.tripEndDate != null){

            var a = moment(trip.tripStartDate);
            var b = moment(trip.tripEndDate);
            trip.days = b.diff(a, 'days')+1;
            // trip.days = moment.diff(trip.tripStartDate).fromNow();
          
            label = label + ' - ' + trip.tripEndDate;

          }

          return label;

        },

        grabTotal(trip){

          var u = trip.staffs.map(item => item.id).indexOf(this.selectedStaff);
          var total = [];

          if(u != '-1' && trip.staffs[u].variations){

            var variation = trip.staffs[u].variations;

            for ( let i in variation){
                if(variation[i].variType == 'Per Day'){
                  total.push(variation[i].amount * trip.days);
                } else {
                  total.push(variation[i].amount);
                }
            }

            var t = total.reduce((acc, item) => acc + Number(item), 0);

            trip['total'] = t;
            return t;

          } else {

            return 0;

          }

        },

        grabStaffTotal(staff_id, dispatch_id){


          var array = _.filter(this.allTrips, function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id && group.dispatch_id == dispatch_id; //that.filter
              });
              return childArray.length > 0;
          });

          var total = [];
          var grandTotal = [];

          for (let i in array){

              var u = array[i].staffs.map(item => item.id).indexOf(staff_id);

              if(array[i].staffs[u].variations){

                var variation = array[i].staffs[u].variations;
                
                for ( let k in variation){
                    if(variation[k].variType == 'Per Day'){
                      total.push( (variation[k].amount * array[i].days) );
                    } else{
                      total.push( (variation[k].amount) );
                    }
                }                

              }

          }

          return total.reduce((acc, item) => acc + Number(item), 0);


        },

        grabStaffTotals(staff_id){

          var array = _.filter(this.allTrips, function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id; //that.filter
              });
              return childArray.length > 0;
          });

          var total = [];
          var grandTotal = [];

          for (let i in array){

              var u = array[i].staffs.map(item => item.id).indexOf(staff_id);

              if(array[i].staffs[u].helperAmount){

                var variation = array[i].staffs[u].helperAmount;

                for (let k in variation){
                  
                  var vari = variation[k];

                  for (let n in vari){
                    total.push(vari[n]);
                  }

                }

              }

          }
          
          return total.reduce((acc, item) => acc + Number(item), 0);      

        },

        grabSelfTotals(staff_id){

          var array = _.filter(this.allTrips, function (group) {
              var childArray = _.filter(group.staffs, function (childGroup) {
                  return childGroup.id === staff_id; //that.filter
              });
              return childArray.length > 0;
          });

          var total = [];
          var grandTotal = [];

          for (let i in array){

              var u = array[i].staffs.map(item => item.id).indexOf(staff_id);

              if(array[i].staffs[u].variations){

                var variation = array[i].staffs[u].variations;
                
                for ( let k in variation){

                    if( variation[k].variType == 'Per Day' && variation[k].paidTo == 'Self'){
                      total.push( (variation[k].amount * array[i].days) );

                    } else if(variation[k].paidTo == 'Self') {

                      total.push( (variation[k].amount) );

                    } else if(variation[k].paidTo == 'Driver'){

                      var driverIndex = array[i].staffs.map(item => item.type).indexOf('Driver');

                      if(!array[i].staffs[driverIndex].helperAmount){
                        array[i].staffs[driverIndex]['helperAmount'] = [];
                      }

                      if(!array[i].staffs[driverIndex].helperAmount[staff_id]){
                        array[i].staffs[driverIndex].helperAmount[staff_id] = [];
                      }

                      if(variation[k].variType == 'Per Day'){
                        array[i].staffs[driverIndex].helperAmount[staff_id][k] = (variation[k].amount * array[i].days);
                      } else {
                        array[i].staffs[driverIndex].helperAmount[staff_id][k] = (variation[k].amount);
                      }

                    }

                }                

              }

          }

          return total.reduce((acc, item) => acc + Number(item), 0);

        },

        updateSelected(data){

          var total = [];
          var multiplier = 1;

          for (let i in this.selectVari){

            if(this.selectVari[i] == true){

              var amount = ((this.salaryVariations[i][this.selectedStaffType]));

              var type = this.salaryVariations[i].variationType;

              var days = 1;

              if(type == 'Per Day'){
                days = this.days;
              }
              if(type == 'Per Trip'){
                days = this.selectedStaffTrips(this.selectedStaff, this.selectedDispatch_id).length;
                this.selectedTripsCount = days;
              }
              
              total.push({
                          subTotal:  amount,
                          day: days
                        });

            }

          }

          this.isTotal = total;
          this.total = total.reduce((acc, item) => acc + Number(item.subTotal * item.day), 0);

        },

        selectallchecks(){

          for (let field in this.selectedStaffTripsArray){

            if(this.selectallTrips == true){

              this.allTrips[this.allTrips.map(item => item.trip_id).indexOf(this.selectedStaffTripsArray[field].trip_id)]['selected'] = true;

              // this.selectedStaffTripsArray[field]['selected'] = true;
            } else {

              this.allTrips[this.allTrips.map(item => item.trip_id).indexOf(this.selectedStaffTripsArray[field].trip_id)]['selected'] = false;

            }
          }


        },

        checkselectall(){

          var set = true

          for (let u in this.selectedStaffTripsArray){

            var i = this.allTrips.map(item => item.trip_id).indexOf(this.selectedStaffTripsArray[u].trip_id);

            if(this.allTrips[i].selected == undefined){
              set = false;
            }

            if(this.allTrips[i].selected != undefined && this.allTrips[i].selected == false){
              set = false;
            }

          }

          this.selectallTrips = set;

          this.checkForSelection();

        },

        checkForSelection: function(){

          var flag = false;
          var days = [];
          
          for (let i in this.allTrips){
           
            if(this.allTrips[i].selected == true){

              days.push(this.allTrips[i].days);
              flag = true;
            }

          }

          this.days = days.reduce((days, item) => {
                      return days + Number(item);              
                    }, 0);

          return flag;

        },

        saveVariations(){

          var selectedVari = [];

          for (let k in this.selectVari){

            if(this.selectVari[k] == true){

              var thisvari = this.salaryVariations[k];

              var id = thisvari['id'];
              var variationName   = thisvari['variation'];
              var variationAmount = thisvari[this.selectedStaffType];
              var variType        = thisvari['variationType'];
              var paidTo          = thisvari['paidTo'];
              
              if(this.selectedStaffType == 'Driver'){
                paidTo = 'Self';
              }

              selectedVari.push({
                vari_id: id,
                variIndex: k,
                paidTo: paidTo,
                amount: variationAmount,
                variName: variationName,
                variType: variType,
              })


            }

            this.selectVari[k] = false;
            this.selectallTrips = false;

          }

          var trip_ids = [];

          for (let i in this.selectedStaffTripsArray){
            
            var i = this.allTrips.map(item => item.trip_id).indexOf(this.selectedStaffTripsArray[i].trip_id);

            if(this.allTrips[i].selected == true){

              var u = this.allTrips[i].staffs.map(item => item.id).indexOf(this.selectedStaff);

              this.allTrips[i].staffs[u].variations = selectedVari;

              trip_ids.push({
                trip_id: this.allTrips[i].trip_id,
                multiplier: this.allTrips[i].days,
                dispatch_id: this.allTrips[i].dispatch_id
              });

            }

            this.allTrips[i].selected = false;

          }

          this.submitVariation(selectedVari, trip_ids);

          this.variationModel = false;

        },

        submitVariation(Variation, trip_id){

          this.loading('Saving');

           axios

            .post('/salaryVariation/saveVariations',{

                data : Variation,
                trip_ids: trip_id,
                staff_id: this.selectedStaff,

            })

            .then(response => {

                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        getPostedVariations(){

           axios

            .get('/salaryVariation/getSavedVariations',{

              params: {

                staff_id : this.staff_ids,
                dispatch_id: this.dispatch_ids

              }

            })

            .then(response => {

                this.pushtoTrips(response.data.data);

                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        pushtoTrips(data){


          for (let i in data){

            var index = this.allTrips.map(item => item.trip_id).indexOf(data[i].trip_id);
            
            if(index != '-1'){

              var staffIndex = this.allTrips[index].staffs.map(item => item.id).indexOf(data[i].staff_id);
              
              if(staffIndex != '-1'){

                if(!this.allTrips[index].staffs[staffIndex].variations){
                  this.allTrips[index].staffs[staffIndex]['variations'] = [];
                  this.allTrips[index]['days'] = data[i].multiplier;
                } 

                // data[i]['variType'] = data[i].type;
                data[i]['variType'] = data[i].type;
                data[i]['variIndex'] = this.salaryVariations.map(item => item.id).indexOf(data[i].variation_id);

                this.allTrips[index].staffs[staffIndex].variations.push(data[i]);

              }

            }

          }

          for (let i in this.allTrips){

            for (let u in this.staff_ids){

              this.selectedStaff = this.staff_ids[u];

              this.grabTotal(this.allTrips[i]);

            }

          }

        },

        getSavedVariations(){

          for (let i in this.selectedStaffTripsArray){

            var i = this.allTrips.map(item => item.trip_id).indexOf(this.selectedStaffTripsArray[i].trip_id);

            var u = this.allTrips[i].staffs.map(item => item.id).indexOf(this.selectedStaff);

            if(this.allTrips[i].selected == true && this.allTrips[i].staffs[u].variations){

              var variations = this.allTrips[i].staffs[u].variations;
              
              for (let k in variations){
                
                  this.selectVari[variations[k].variIndex] = true;

              }

            }

          }

        },

        getMegaStaffTotal(staff_id){

          var grandTotal = [];

          for (let i in this.staff){
            if(this.staff[i].id == staff_id){
              var total = this.grabSelfTotals(this.staff[i].id);
              grandTotal.push(total);
            }
          }

          if(grandTotal.length > 0){
            this.grandStaffTotal = grandTotal.reduce((acc, item) => acc + Number(item), 0);
          } else {
            this.grandStaffTotal = 0;
          }

           return this.grandStaffTotal;

        },

        getMegaExpenseTotal(staff_id){

          var grandTotal = [];

          for( let i in this.expenses){
            if(this.expenses[i].staff_id && this.expenses[i].staff_id == staff_id){
              grandTotal.push(this.expenses[i].actualAmount);
            }
          }

          if(grandTotal.length > 0){
            return grandTotal.reduce((acc, item) => acc + Number(item), 0);
          }

          return 0;

        },

        getMegaOilTotal(staff_id){

          var grandTotal = [];

          for( let i in this.oilnFuel){
            if(this.oilnFuel[i].staff_id && this.oilnFuel[i].staff_id == staff_id){
              grandTotal.push(this.oilnFuel[i].actualAmount);
            }
          }


          if(grandTotal.length > 0){
           return grandTotal.reduce((acc, item) => acc + Number(item), 0);
          }

          return 0;

        },

        getMegaAdvance(staff_id){

          var grandTotal = [];

          for (let i in this.advancepayments){

            if(this.advancepayments[i].staff_id == staff_id){
              grandTotal.push(this.advancepayments[i].actualAmount)
            }

          }

          if(grandTotal.length > 0){
            return grandTotal.reduce((acc, item) => acc + Number(item), 0);
          }

          return 0;

        },

        getMegaSummary(staff_id){

          return ((this.grabSelfTotals(staff_id) + this.getMegaExpenseTotal(staff_id) + this.grabStaffTotals(staff_id)) - this.getMegaAdvance(staff_id));

        },

        closeStaffTripModel(){

          for (let i in this.allTrips){
            this.allTrips[i].selected = false;
            this.selectallTrips = false;
          }

          this.staffTrips = false;

        },

        closeVariationsModel(){

          for (let i in this.selectVari){
            this.selectVari[i] = false;
          }

          this.variationModel = false;

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

       oilnFuel: function () {
          return this.allExpenses.filter(function(elem){
                if(elem.expenseType_id == 1 || elem.expenseType_id == 2) return elem;
          });
        },

        expenses: function () {

          return this.allExpenses.filter(function(elem){
                if(elem.expenseType_id != 1 && elem.expenseType_id != 2) return elem;
          });

        },

        expenseStaff: function(){

          for (let i in this.allExpenses){

            var index = this.allTrips.map(item => item.trip_id).indexOf(this.allExpenses[i].trip_id);
            
            if(index != '-1'){
            
              var staffIndex = this.allTrips[index].staffs.map(item => item.type).indexOf('Driver');

              if(staffIndex != '-1'){
                
                this.allExpenses[i].staff_id = this.allTrips[index].staffs[staffIndex].id;

              }

            }



          }

        },
        
       
        
      },

  }
</script>