<template>

  <div>


    <c-panel title="Trips">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
            <c-button @click="$router.push('/tripmanagement/' + pageid + '/' + expense.equipment_id)" type="primary" outline>View Trips</c-button>
            <c-button @click="$router.push('/dispatchexpense/' + pageid + '/' + expense.equipment_id)" type="primary" outline>Add Expense</c-button>
            <c-button class="u-bg-primary u-color-grey-lightest" type="primary" outline>Add Fuel</c-button>
            
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

        <c-form-field label="Equipment">

          <c-multiselect
            v-model="expense.selectedEquipment"
            track-by="value"
            label="label"
            accountNumber="accountNumber"
            :options="equipments"
            :searchable="true"
            :close-on-select="false"
            :multiple="true"
            placeholder="Select Expense Dates"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="expense.errors.has('selectedClient')"></span>
        </c-form-field>


          <c-form-field label="Dispatch Dates">

          <c-multiselect
            v-model="expense.Changed"
            track-by="value"
            label="label"
            accountNumber="accountNumber"
            :options="expense.dispatch_dates"
            :searchable="true"
            :close-on-select="false"
            :multiple="true"
            placeholder="Select Expense Dates"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="expense.errors.has('selectedClient')"></span>
        </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button type="primary" @click="selectAll()" v-once smart>Select All Dates</c-button>
            <c-button v-if="verifyMe" type="primary" @click="handleSubmitExpense()" v-once smart>Save All</c-button>
          </div>

        </div>

    </c-panel>

    <c-panel v-if="orderedDates.length > 0" type="ghost">

     <div v-for="(type, i) in expenseType" v-if="type.status == 'Special'">

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
                        v-model="expense.expense[i][type.expenseType][field.position]">
                      </c-form-input>
                    </c-col>
                
              </div>

            </c-row>

          </c-form-field>

        </c-panel>

        </div>

      </c-panel>

    <c-panel v-if="waitingApprovalActive.length > 0" title="Fuel Waiting Approval">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="30%">Fuel Refill Date</td>
                <td width="20%">Amount</td>
                <td width="20%">Created By</td>
                <td width="20%">Action</td>
                <td width="20%" v-if="flagged.length > 0">Flagged By</td>
              </tr>

              <tr v-if="list.isApproved == 0" v-for="(list, index) in waitingApprovalActive">
                <td>{{ list.expenseDate }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td><c-button-group>
                    
                    <c-button v-if="isApprovable(list.createdBy.id)" type="primary" @click="approveIt(list.id)">
                      <i>
                        <i  v-if="list.isApproved == '1'" class="icon-thumbs-up2"></i>
                        <i  v-if="list.isApproved == '0'" class="icon-thumbs-down2"></i>
                      </i>
                    </c-button>
                    <c-button v-if="isFlaggable(list.createdBy.id, list.flag)" @click="flagModal(list.id)" type="primary"><i class="icon-flag"></i></c-button>
                    <c-button v-tooltip.top-start="'View Reason'" v-if="list.flag == 1" @click="showReason(list.reason)" type="primary"><i class="icon-bubble"></i></c-button>
                    
                    <c-button @click="killIt(list.id)" type="primary"><i class="icon-bin"></i></c-button>
                  </c-button-group>
                </td>
                <td v-if="flagged.length > 0">
                    <c-badge v-if="list.flag == 1" :type="list.flaggedBy.tagColor">{{ list.flaggedBy.name }}</c-badge>
                </td>
              </tr>

            </tbody>

          </table>

      </c-form-field>

    </c-panel>

    <c-panel v-if="approvedActive.length > 0" title="Fuel Approved">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="30%">Fuel Refill Date</td>
                <td width="15%">Amount</td>
                <td width="15%">Created By</td>
                <td width="15%">Approved By</td>
                <td width="15%">Action</td>
              </tr>

              <tr v-if="list.isApproved == 1" v-for="(list, index) in approvedActive">
                <td>{{ list.expenseDate }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.createdBy.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td><c-badge :type="list.approvedBy.tagColor">{{ list.approvedBy.name }}</c-badge></td>
                <td><c-button-group>
                    <c-button @click="killIt(list.id)" type="primary"><i class="icon-bin"></i></c-button>
                  </c-button-group>
                </td>
              </tr>

            </tbody>

          </table>

      </c-form-field>

    </c-panel>

    <c-panel v-if="deleted.length > 0" title="Fuel Deleted">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="30%">Fuel Refill Date</td>
                <td width="20%">Amount</td>
                <td width="20%">Created By</td>
                <td width="20%">Deleted By</td>
                <!-- <td width="20%">Action</td> -->
              </tr>

              <tr v-for="(list, index) in deleted">
                <td>{{ list.expenseDate }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.createdBy.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td><c-badge :type="list.updatedBy.tagColor">{{ list.updatedBy.name }}</c-badge></td>
                <!-- <td><c-button-group>
                    <c-button @click="killIt(list.id)" type="primary"><i class="icon-bin"></i></c-button>
                  </c-button-group>
                </td> -->
              </tr>

            </tbody>

          </table>

      </c-form-field>

    </c-panel>

      <c-modal title="Reason to Flag" placement="center" size="small" v-model="reasonModel">
        Reason to flag:

        <c-form-field >

            <textarea class="form-textarea" v-model="reason"
                :disabled="isDisabled" />

        </c-form-field>

        <div class="u-text-right" slot="footer">
          <c-button type="primary" @click="flagIt()" v-once smart>Confirm</c-button>
        </div>
      </c-modal>
 


  </div>
</template>
<script>

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
          selectedDates: [],

          expense: [],
          waitingApproval: [],
          
        }),

        update: false,
        orig_expense: [],
        flagid: '',
        reason: '',
        reasonModel: false,
        isDisabled: false,

        expenseType: [],
        expenseTypeName: [],

        isLoading: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Expense Management',
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
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

      if(this.$route.params.dispatch_id){

        this.pageid = this.$route.params.dispatch_id;
        this.expense.equipment_id = this.$route.params.equipment_id;
        this.getData();      
         
      } 

    },
    methods: {

        getData(){

          this.loading('Loading');
          
          axios

            .get('/dispatch/getTrips',{

              params: {

                equipment_id : this.expense.equipment_id,
                dispatch_id: this.pageid

              }

            })

              .then(response => {

                var data = response.data;

                this.getExpenseType();
                
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

              if(this.expense.dispatch_dates.length > 0){
                let currentIndex = this.expense.dispatch_dates.map(item => item.value).indexOf(value);
                if(currentIndex != '-1'){
                  run = false;
                }
              }

              if(run == true){
              
                this.expense.dispatch_dates.push(
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

            .get('/expense/getFuel',{

              params: {

                equipment_id : this.expense.equipment_id,
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

              var expenseDate = data[field].expenseDate;

              // Only push data belong to id 4 == Petrol
              

              var k = this.expense.waitingApproval.push({
                  id: data[field].id,
                  trip_id: data[field].trip_id,
                  tagColor: data[field].createdBy.tagColor,
                  isApproved: data[field].isApproved,
                  approvedBy: data[field].approvedBy,
                  status: data[field].status,
                  createdBy: data[field].createdBy,
                  updatedBy: data[field].updatedBy,
                  flag: data[field].flag,
                  flaggedBy: data[field].flaggedBy,
                  reason: data[field].reasonToFlag,
                  expenseType: data[field].expenseTypes,
                  position: data[field].id,
                  label: format(data[field].expenseDate, 'DD-MM-YYYY'),
                  value: data[field].expenseDate,
                  expenseDate: format(data[field].expenseDate, 'DD-MM-YYYY'),
                  actualAmount: data[field].actualAmount,
                  index: field,
                });


            }

          }

        },

        setValues(index, actualAmount, reportedAmount, expenseType, position){

            this.expense.expense[index][expenseType][position] = actualAmount;
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
                   this.expense.expense.push({

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

        isApprovable(user){

          if(user == this.user.id){

            return false;

          } else {

            return true;

          }

        },

        isFlaggable(user, flag){

          if(user == this.user.id || flag == 1){

            return false;

          } else {

            return true;

          }

        },

        approveIt(id, index){

          let i = this.expense.waitingApproval.map(item => item.id).indexOf(id);

          this.loading('Approving');

          axios

            .post(this.api + this.controller + 'approveIt/' + id).then(success=>{

                // this.getData();
                if(i != '-1'){
                  this.expense.waitingApproval[i].isApproved = 1;
                  this.expense.waitingApproval[i].approvedBy = this.user;
                } else {
                  this.errortoast('Something went wrong, please refresh page and try again', 1000);
                }

                this.loading();

            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        flagModal(id){

          this.reason = '';
          this.isDisabled = false;
          this.reasonModel = true;
          this.flagid = id;

        },

        showReason(reason){

          this.reason = reason;
          this.reasonModel = true;
          this.isDisabled = true;

        },

        flagIt(){

            let i = this.expense.waitingApproval.map(item => item.id).indexOf(this.flagid);

            this.loading('Flagging');

            axios

              .post(this.api + this.controller + 'flagIt/' + this.flagid,{
                reason: this.reason
              }).then(success=>{

                  if(i != '-1'){
                    this.expense.waitingApproval[i].flag = 1;
                    this.expense.waitingApproval[i].flaggedBy = this.user;
                    this.expense.waitingApproval[i].reason = this.reason;
                  } else {
                    this.errortoast('Something went wrong, please refresh page and try again', 1000);
                  }

                  this.reasonModel = false;
                  this.flagid = '';
                  this.reason = '';
                  this.loading();

              }).catch(error=> {

                // Hide Loading Toast
                this.loading();

                this.errortoast();

              });

        },

        killIt(expense_id, index){

          this.loading('Deleting');

          let i = this.expense.waitingApproval.map(item => item.id).indexOf(expense_id);

          axios

            .post(this.api + this.controller + 'deleteIt/' + expense_id).then(success=>{

                if(i != '-1'){
                // this.getData();
                this.expense.waitingApproval[i].status = 0;
                this.expense.waitingApproval[i].updatedBy = this.user;
                } else {
                  this.errortoast('Something went wrong, please refresh page and try again', 1000);
                }

                this.loading();

            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        TotalAmount(index, item) {

          if(this.expense.expense[index][item] && this.expense.expense.length > 0){
          
            return this.expense.expense[index][item].reduce((total, item) => {
              return total + Number(item);
              
            }, 0);
          }

        },

        removeSelectedDate(){

          this.removeClass();

          for (let date in this.expense.Changed){
            
            this.expense.Created.push({
                trip_id: this.expense.Changed[date].trip_id,
                colorclass: 'u-color-danger',
                position: this.expense.Changed[date].position,
                label: this.expense.Changed[date].label,
                value: this.expense.Changed[date].value,
            });

            var thisdate = (this.expense.Changed[date].value);
            let i = this.expense.dispatch_dates.map(item => item.value).indexOf(thisdate);

            this.expense.dispatch_dates.splice(i, 1);

            // this.expense.Changed.splice(date, 1);

          }

          for (let date in this.expense.Created){

              var thisdate = (this.expense.Created[date].value);
              let i = this.expense.Changed.map(item => item.value).indexOf(thisdate);

              this.expense.Changed.splice(i, 1);              

          }

        },

        selectAll(){

          for (let date in this.expense.dispatch_dates){
            
            this.expense.Created.push({
                trip_id: this.expense.dispatch_dates[date].trip_id,
                colorclass: 'u-color-danger',
                position: this.expense.dispatch_dates[date].position,
                label: this.expense.dispatch_dates[date].label,
                value: this.expense.dispatch_dates[date].value,
            });

          }

          this.expense.dispatch_dates = [];

        },

        removeClass(){

          for(let field in this.expense.Created){

            this.expense.Created[field].colorclass = '';

          }

        },

        saveThis(index, expenseType){

          if(this.update == false){
            this.errortoast('There is nothing to update', 500);
            return false;
          }

          this.handleSubmitExpense();

        },

        handleSubmitExpense(button = null){

          if(this.update == false){
            this.errortoast('There is nothing to update', 500);
            return false;
          }

          this.loading('Saving Expense');

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

          this.loading('Saving Expense');
          
          this.expense.submit('post', this.api + this.controller + 'saveAllFuel/' + this.pageid + '/' + this.expense.equipment_id).then(success=>{

                this.showToast('Expense Added Successfully');

                // this.loading('show'); moved before post
                this.loading();

                this.reset();

                 setTimeout(() => {
                    this.pageid = this.$route.params.dispatch_id;
                    this.expense.equipment_id = this.$route.params.equipment_id;
                    this.getData();
                  }, 100);
                
              
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

        reset(){

          // Display success message
          this.showToast();

          // Reset form using form class
          Object.assign(this.$data, this.$options.data.call(this));

          setTimeout(() => {
            this.errors.clear();
          }, 100);


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

        waitingApprovalActive: function (){

           return this.expense.waitingApproval.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 0) return elem;
            });

        },

        approvedActive: function (){

           return this.expense.waitingApproval.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 1) return elem;
            });

        },

        deleted: function (){

           return this.expense.waitingApproval.filter(function(elem){
                if(elem.status == 0) return elem;
            });

        },

        flagged: function () {

          return this.expense.waitingApproval.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1) return elem;
            });
          
        },

        orderedDates: function () {
          this.removeSelectedDate();
          return _.orderBy(this.expense.Created, 'value')
        },

        verifyMe(){

          if(this.expense.Created.length == 0){
            return false;
          }

          var expense = this.expense.expense;
          var orig_expense = this.orig_expense;

          for (let i in this.expenseTypeName){

            for(let field in expense){

              for ( let value in expense[field][this.expenseTypeName[i]]){

                var expvalue = expense[field][this.expenseTypeName[i]][value];
                var origvalue = orig_expense[field][this.expenseTypeName[i]][value];
                
                if(origvalue == undefined && expvalue == ''){
                  this.expense.expense[field][this.expenseTypeName[i]][value] = null;
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