<template>

  <div>


    <c-panel :title="'General Expense For <strong>' + equipmentNumber + '</strong>'">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
            <c-button @click="$router.push('/tripmanagement/' + pageid + '/' + dispatch.equipment_id)" type="primary" outline>View Trips</c-button>
            <c-button class="u-bg-primary u-color-grey-lightest" type="primary" outline>Add Expense</c-button>
            <c-button @click="$router.push('/dispatchfuel/' + pageid + '/' + dispatch.equipment_id + '/' + dispatch.status + '/' + equipmentNumber)" type="primary" outline>Add Fuel</c-button>

            
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

          <c-form-field label="Dispatch Dates">

          <c-multiselect
            v-model="dispatch.Changed"
            track-by="value"
            label="label"
            accountNumber="accountNumber"
            :options="dispatch.dispatch_dates"
            :searchable="true"
            :close-on-select="false"
            :multiple="true"
            placeholder="Select Expense Dates"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="dispatch.errors.has('selectedClient')"></span>
        </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button v-if="dispatch.dispatch_dates.length > 0" type="primary" @click="selectAll()" v-once smart>Select All Dates</c-button>
            <c-button v-if="verifyMe" type="primary" @click="handleSubmitExpense()" v-once smart>Save All</c-button>
          </div>

        </div>

    </c-panel>

    <c-panel type="ghost">

     <div v-for="(type, i) in expenseType" v-if="type.status == 'Active'">

          <c-panel :title="type.expenseType + ' Total: Rs ' + TotalAmount(i, type.expenseType) + '/-'">
            <i class="icon-floppy-disk" @click="saveThis(i, type.expenseType)" slot="control"></i>
  
           <c-form-field >
          
            <c-row>

              <div v-for="(field, index) in orderedDates">
                
                    <c-col lg="24" xl>
                      
                      <c-form-input
                        :addon-start="field.label" 
                        :class="field.colorclass"
                        type="number"
                        v-model="dispatch.expense[i][type.expenseType][field.position]">
                      </c-form-input>
                    </c-col>
                
              </div>

            </c-row>

          </c-form-field>

        </c-panel>

        </div>

      </c-panel>

 


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


        dispatch: new Form({

          Changed: [],
          Created: [],
          
          dispatch_id: '',
          equipment_id: '',

          dispatch_dates: [],

          expense: [],
          // dates: [],
          status: 0,
          
        }),

        update: false,
        orig_expense: [],

        expenseType: [],
        expenseTypeName: [],

        isLoading: false,
        content: '',
        duration: '',
      

        pageTitle: 'Expense Management',
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
        basePost: '',
        postUrl: '',
        pageid: '',

        toast: '',

        equipmentNumber: 'Loading...',

        
      }
    },

    components: {

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr

    },

    mounted() {

        // this.$on('ChangeView', this.testThis);

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      if(this.$route.params.dispatch_id){

        this.pageid = this.$route.params.dispatch_id;
        this.dispatch.equipment_id = this.$route.params.equipment_id;
        this.equipmentNumber = this.$route.params.equipmentNumber;
        this.dispatch.status = this.$route.params.status;

         this.loading('Loading');
         this.getData();      
         this.isDisabled = true;  
         
      } 

      this.getExpenseType();

    },
    methods: {

       testThis() {
        console.log('hit');
        },

        getData(pageid){

          this.dispatch.Changed = [];
          this.dispatch.Created = [];

          this.dispatch.tripsDetails = [];

          axios

            .get('/dispatch/getTripDates',{

              params: {

                equipment_id : this.dispatch.equipment_id,
                dispatch_id: this.pageid

              }

            })

              .then(response => {

                var data = response.data;
                
                this.createTrips(data);

                this.getExistingExpense();

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

              var value = data[field].tripStartDate;

              if(this.dispatch.dispatch_dates.length > 0){
                let currentIndex = this.dispatch.dispatch_dates.map(item => item.value).indexOf(value);
                if(currentIndex != '-1'){
                  run = false;
                }
              }

              if(run == true){
              
                this.dispatch.dispatch_dates.push(
                {
                  trip_id: data[field].trip_id,
                  position: field,
                  label: format(data[field].tripStartDate, 'DD-MM-YYYY'),
                  value: data[field].tripStartDate,
                
                });
              
              } 
             
            }

          }

        },

        getExistingExpense(){

          axios

            .get('/expense/getExpense',{

              params: {

                equipment_id : this.dispatch.equipment_id,
                dispatch_id: this.pageid

              }

            })

              .then(response => {

                var data = response.data.data;
                
                this.pushData(data);

                this.loading();
                
            }).catch(error => {

                this.loading();

                this.errortoast();

            });

        },

        pushData(data){

          if(data != null){

            for (let field in data){

              var run = true;

              console.log(data[field]);

              var expenseDate = data[field].expenseDate;

              let dispatch_dates_Data = this.dispatch.dispatch_dates.filter(function(elem){

                if(elem.value == expenseDate) return elem;

              });

              if(dispatch_dates_Data){

               var ddd = dispatch_dates_Data[0];
              

              if(data[field].expenseType_id != 1 && data[field].expenseType_id != 2){// Only push data not in petrol type              

                run = true;

              if(this.dispatch.Changed.length > 0){
                let currentIndex = this.dispatch.Changed.map(item => item.value).indexOf(expenseDate);
                if(currentIndex != '-1'){
                  run = false;
                }
              }

              if(run == true){
              
              this.dispatch.Changed.push({
                  trip_id: data[field].trip_id,
                  expenseType: data[field].expenseTypes,
                  // position: data[field].id,
                  position: ddd.position,
                  label: format(data[field].expenseDate, 'DD-MM-YYYY'),
                  value: data[field].expenseDate,
                });

              }

              var expenseTypeID = data[field].expenseType_id;
              let i = this.dispatch.expense.map(item => item.id).indexOf(expenseTypeID);

              var expenseType = data[field].expenseTypes;

              //var expenseDate = data[field].expenseDate;
              // let position_index = this.dispatch.Changed.map(item => item.value).indexOf(expenseDate);
              // var position = this.dispatch.Changed[position_index].position;

              this.setValues(i, data[field].actualAmount, data[field].reportedAmount, expenseType, ddd.position);

              }

              }
              
            }

          }

        },

        setValues(index, actualAmount, reportedAmount, expenseType, position){

            this.dispatch.expense[index][expenseType][position] = actualAmount;
            this.orig_expense[index][expenseType][position] = actualAmount;

        },

        getExpenseType(){

          axios
            .get('/expensetype')

              .then(response => {

                for (let field in response.data.data){

                  var name = response.data.data[field].expenseType;
                  var id = response.data.data[field].id;

                  //Save for show on page
                   this.dispatch.expense.push({

                     [name]: [],
                     id: id,
                     expenseTypeName: name,
                     status: response.data.data[field].status,

                   });

                   //Create copy to check for changes
                   this.orig_expense.push({

                     [name]: [],
                     id: id,
                     expenseTypeName: name,
                     status: response.data.data[field].status,

                   });

                   this.expenseTypeName.push(name);

                }
                
                this.expenseType = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        TotalAmount(index, item) {

          if(this.dispatch.expense[index][item]){
          
            return this.dispatch.expense[index][item].reduce((total, item) => {
              return total + Number(item);
              
            }, 0);
          }

        },

        removeSelectedDate(){

          this.removeClass();

          for (let date in this.dispatch.Changed){
            
            this.dispatch.Created.push({
                trip_id: this.dispatch.Changed[date].trip_id,
                colorclass: 'u-color-danger',
                position: this.dispatch.Changed[date].position,
                label: this.dispatch.Changed[date].label,
                value: this.dispatch.Changed[date].value,
            });

            var thisdate = (this.dispatch.Changed[date].value);
            let i = this.dispatch.dispatch_dates.map(item => item.value).indexOf(thisdate);

            this.dispatch.dispatch_dates.splice(i, 1);

            // this.dispatch.Changed.splice(date, 1);

          }

          for (let date in this.dispatch.Created){

              var thisdate = (this.dispatch.Created[date].value);
              let i = this.dispatch.Changed.map(item => item.value).indexOf(thisdate);

              this.dispatch.Changed.splice(i, 1);              

          }

        },

        selectAll(){

          for (let date in this.dispatch.dispatch_dates){
            
            this.dispatch.Created.push({
                trip_id: this.dispatch.dispatch_dates[date].trip_id,
                colorclass: 'u-color-danger',
                position: this.dispatch.dispatch_dates[date].position,
                label: this.dispatch.dispatch_dates[date].label,
                value: this.dispatch.dispatch_dates[date].value,
            });

          }

          this.dispatch.dispatch_dates = [];

        },

        removeClass(){

          for(let field in this.dispatch.Created){

            this.dispatch.Created[field].colorclass = '';

          }

        },

        saveThis(index, expenseType){

          if(this.update == false){
            this.errortoast('There is nothing to update', 500);
            return false;
          }

          this.loading('Saving');

          var expense_id = this.dispatch.expense[index].id;

          axios
            .post(this.api + this.controller + 'saveExpense_byId/' + expense_id,{

              expense: {

                data : this.dispatch.expense[index],
                dates: this.orderedDates,
                dispatch_id: this.pageid,
                equipment_id: this.dispatch.equipment_id,

              }

            }).then(success=>{

                this.getData();

            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });


        },

        handleSubmitExpense(button = null){

          this.loading('Saving');

          this.$validator.validateAll(this.dispatch).then((result) => {
            
              if(this.orderedDates.length == 0){

                this.errortoast('Please select dates first!', 1000);
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

          
          this.dispatch.submit('post', this.api + this.controller + 'saveAllExpense/' + this.pageid + '/' + this.dispatch.equipment_id).then(success=>{

                this.showToast('Trips Updated Successfully');

                this.getData();

                // this.goBack();
              
            }).catch(error=> {

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

        orderedDates: function () {
          this.removeSelectedDate();
          return _.orderBy(this.dispatch.Created, 'value')
        },

        verifyMe(){

          if(this.dispatch.Created.length == 0){
            return false;
          }

          var expense = this.dispatch.expense;
          var orig_expense = this.orig_expense;

          for (let i in this.expenseTypeName){

            for(let field in expense){

              for ( let value in expense[field][this.expenseTypeName[i]]){

                var expvalue = expense[field][this.expenseTypeName[i]][value];
                var origvalue = orig_expense[field][this.expenseTypeName[i]][value];
                
                if(origvalue == undefined && expvalue == ''){
                  this.dispatch.expense[field][this.expenseTypeName[i]][value] = null;
                  var expvalue = expense[field][this.expenseTypeName[i]][value];
                }

                if(expvalue != origvalue){
                  this.update = true;
                  return true;
                }

              }

            }

          }
          this.update = false;
          return false;

        }
        

      },

  }
</script>