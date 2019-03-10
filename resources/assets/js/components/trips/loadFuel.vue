<template>

  <div>


    <c-panel :title="'Fuel for <strong>' + equipmentNumber + '</strong>'">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
            <c-button @click="$router.push('/tripmanagement/' + pageid + '/' + dispatch.equipment_id)" type="primary" outline>View Trips</c-button>
            <c-button @click="$router.push('/dispatchexpense/' + pageid + '/' + dispatch.equipment_id + '/' + dispatch.status + '/' + equipmentNumber)" type="primary" outline>Add Expense</c-button>
            <c-button class="u-bg-primary u-color-grey-lightest" type="primary" outline>Add Fuel</c-button>
            
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
            <c-button type="primary" @click="selectAll()" v-once smart>Select All Dates</c-button>
            <c-button v-if="verifyMe" type="primary" @click="handleSubmitExpense()" v-once smart>Save All</c-button>
          </div>

        </div>

    </c-panel>

    <c-panel v-if="orderedDates.length > 0" type="ghost">

     <div v-for="(type, i) in expenseType" v-if="type.status == 'Special' && type.expenseType == 'Fuel'">

          <c-panel :title="type.expenseType + ' Total: Rs ' + TotalAmount(i, type.expenseType) + '/-'">
            <i class="icon-floppy-disk" @click="saveThis(i, type.expenseType)" slot="control"></i>
  
           <c-form-field >

            <table class="table table--striped">
            
             <tbody>
          
              <tr v-for="(field, index) in orderedDates">

                  <td>

                      <c-row class="u-mt-10">

                        <c-col lg="24" xl>
                          
                          <c-multiselect
                              v-model="dispatch.expense[i]['supplier'][field.position]"
                              track-by="value"
                              label="label"
                              :options="supplier"
                              :searchable="true"
                              :close-on-select="true"
                              placeholder="Select Supplier"></c-multiselect>
                              <span class="form-help u-color-danger" 
                                    v-text="form.errors.get('supplier')" 
                                    v-if="dispatch.errors.has('supplier')">
                              </span>

                        </c-col>

                      </c-row>

                      <c-row class="u-pb-10 u-mt-10">

                        <c-col lg="4" xl>
                          
                          <c-form-input
                            addon-start="Slip No."
                            :class="field.colorclass"
                            type="text"
                            v-model="dispatch.expense[i]['slip'][field.position]">
                          </c-form-input>

                        </c-col>

                        <c-col lg="5" xl>

                          <c-form-input
                            addon-start="Fuel Litre" 
                            v-validate="'required'"
                            name="litre"
                            :class="field.colorclass"
                            type="number"
                            v-model="dispatch.expense[i]['qty'][field.position]">
                          </c-form-input>

                        </c-col>

                        <c-col lg="5" xl>

                          <c-form-input
                            addon-start="Fuel Rate" 
                            :class="field.colorclass"
                            type="number"
                            v-model="dispatch.expense[i]['rate'][field.position]">
                          </c-form-input>

                        </c-col>

                        <c-col lg="5" xl>

                          <c-form-input
                            addon-start="Oil Litre" 
                            :class="field.colorclass"
                            type="number"
                            v-model="dispatch.expense[i]['oQty'][field.position]">
                          </c-form-input>

                        </c-col>

                        <c-col lg="5" xl>

                          <c-form-input
                            addon-start="Oil Rate" 
                            :class="field.colorclass"
                            type="number"
                            v-model="dispatch.expense[i]['oRate'][field.position]">
                          </c-form-input>

                        </c-col>

                      </c-row>

                      <c-row class="u-mb-20">

                        <c-col lg="24" xl>
                          
                          <c-form-input
                            addon-end="Amount" 
                            :addon-start="field.label"
                            :class="field.colorclass"
                            class="u-color-info"
                            type="number"
                            :readonly="true"
                            v-model="dispatch.expense[i][type.expenseType][field.position]">
                          </c-form-input>

                        </c-col>

                      </c-row>

                      </td>

                </tr>

            </tbody>

            </table>

          </c-form-field>

        </c-panel>

        </div>

      </c-panel>

    <c-panel v-if="waitingApprovalActive.length > 0" title="Fuel Waiting Approval">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="">Date</td>
                <td width="15%">Supplier</td>
                <td>Slip No.</td>
                <td width="">Fuel Ltr</td>
                <td width="">Rate</td>
                <td width="">Oil Ltr</td>
                <td width="">Rate</td>
                <td width="">Amount</td>
                <td width="">Created By</td>
                <td width="">Action</td>
                <td width="" v-if="flagged.length > 0">Flagged By</td>
              </tr>

              <tr v-if="list.isApproved == 0" v-for="(list, index) in waitingApprovalActive">
                <td>{{ list.expenseDate }}</td>
                <td>{{ list.supplier }}</td>
                <td>{{ list.slipNumber }}</td>
                <td>{{ list.qty }}</td>
                <td>{{ list.rate }}</td>
                <td>{{ list.oilQty }}</td>
                <td>{{ list.oilRate }}</td>
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
          selectedDates: [],

          expense: [],
          waitingApproval: [],
          status: 0,
          
        }),

        supplier: [],
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

        equipmentNumber: 'Loading...',
        
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
        this.dispatch.equipment_id = this.$route.params.equipment_id;
        this.dispatch.status = this.$route.params.status;
        this.equipmentNumber = this.$route.params.equipmentNumber;
        this.getData();      
         
      } 

    },
    methods: {

        getData(){

          this.loading('Loading');
          
          axios

            .get('/dispatch/getTrips',{

              params: {

                equipment_id : this.dispatch.equipment_id,
                dispatch_id: this.pageid

              }

            })

              .then(response => {

                var data = response.data;

                this.getExpenseType();
                
                this.createTrips(data);

                this.getSupplier();

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

            .get('/expense/getFuel',{

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

            var u = 0;

            for (let field in data){

              var run = true;

              var expenseDate = data[field].expenseDate;

              // Only push data belong to id 4 == Petrol
              
              if(data[field].expenseTypes == 'Fuel'){

              var k = this.dispatch.waitingApproval.push({

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
                  supplier: data[field].supplier,
                  slipNumber: data[field].slipNumber,
                  actualAmount: data[field].actualAmount,
                  rate: data[field].rate,
                  qty: data[field].qty,
                  index: u,
                  
                });

              u++;

              } else if(data[field].expenseTypes == 'Oil') {

                let i = this.dispatch.waitingApproval.filter(function(elem){
                    if(elem.slipNumber == data[field].slipNumber 
                        && elem.value == data[field].expenseDate
                      ) return elem;
                });

                if(i.length > 0){

                  this.dispatch.waitingApproval[i[0].index].oilRate = data[field].rate;
                  this.dispatch.waitingApproval[i[0].index].oilQty = data[field].qty;

                }

              }

            }

          }

        },

        getExpenseType(){

          axios
            .get('/expensetype')

              .then(response => {

                for (let field in response.data.data){

                  var name = response.data.data[field].expenseType;
                  var id = response.data.data[field].id;


                  if(name == 'Fuel'){

                    //Save for show on page
                     this.dispatch.expense.push({

                       [name]: [],
                       id: id,
                       expenseTypeName: name,
                       status: response.data.data[field].status,
                       supplier: [],
                       qty: [],
                       slip: [],
                       rate: [],
                       oRate: [],
                       oQty: [],

                     });

                     //Create copy to check for changes
                     this.orig_expense.push({

                       [name]: [],
                       id: id,
                       expenseTypeName: name,
                       status: response.data.data[field].status,
                       supplier: [],
                       qty: [],
                       slip: [],
                       rate: [],
                       oRate: [],
                       oQty: [],

                     });

                     this.expenseTypeName.push(name);

                  }

                }
                
                this.expenseType = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getSupplier(){

          axios
            .get('/expensesupplier')

              .then(response => {

                this.supplier = response.data.data;

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

          let i = this.dispatch.waitingApproval.map(item => item.id).indexOf(id);

          this.loading('Approving');

          axios

            .post(this.api + this.controller + 'approveIt/' + id).then(success=>{

                // this.getData();
                if(i != '-1'){
                  this.dispatch.waitingApproval[i].isApproved = 1;
                  this.dispatch.waitingApproval[i].approvedBy = this.user;
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

            let i = this.dispatch.waitingApproval.map(item => item.id).indexOf(this.flagid);

            this.loading('Flagging');

            axios

              .post(this.api + this.controller + 'flagIt/' + this.flagid,{
                reason: this.reason
              }).then(success=>{

                  if(i != '-1'){
                    this.dispatch.waitingApproval[i].flag = 1;
                    this.dispatch.waitingApproval[i].flaggedBy = this.user;
                    this.dispatch.waitingApproval[i].reason = this.reason;
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

          let i = this.dispatch.waitingApproval.map(item => item.id).indexOf(expense_id);

          axios

            .post(this.api + this.controller + 'deleteIt/' + expense_id).then(success=>{

                if(i != '-1'){
                // this.getData();
                this.dispatch.waitingApproval[i].status = 0;
                this.dispatch.waitingApproval[i].updatedBy = this.user;
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

          this.calcRate(index, item);

          if(this.dispatch.expense[index][item] && this.dispatch.expense.length > 0){
          
            return this.dispatch.expense[index][item].reduce((total, item) => {
              return total + Number(item);
              
            }, 0);
          }

        },

        calcRate(index, item) {

          
          var data = this.dispatch.expense[index][item];
          var qty = this.dispatch.expense[index]['qty'];
          var rate = this.dispatch.expense[index]['rate'];

          var oQty = this.dispatch.expense[index]['oQty'];
          var oRate = this.dispatch.expense[index]['oRate'];

          var fuelArray = [];
          var oilArray = [];

          for (let i in qty){

            if(qty[i] > 0 && rate[i] > 0){

               data[i] = (qty[i] * rate[i]).toFixed(2);
               fuelArray[i] =  (qty[i] * rate[i]).toFixed(2);
                
             }

          }

          for(let i in oQty){

            if(oQty[i] > 0 && oRate[i] > 0){

                var oilAmount =  (oQty[i] * oRate[i]).toFixed(2);

                if(fuelArray[i] > 0){
                  data[i] = (Number(fuelArray[i]) + Number(oilAmount)).toFixed(2);
                } else {
                  data[i] = Number(oilAmount).toFixed(2);
                }

            }

          }

        },

        checkforqty(){

          var data = this.dispatch.expense[0]['Fuel'];
          var qty = this.dispatch.expense[0]['qty'];
          for (let i in data){
            
            if(data[i] > 0 && qty[i] == 0 || qty[i] == undefined || qty[i] == ''){
              this.errortoast('Please check litre fields', 1000);
              return false;
            }

          }

          return true;

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

          this.handleSubmitExpense();

        },

        verifyMe(){

          if(this.dispatch.Created.length == 0){
            return false;
          }

          var expense = this.dispatch.expense;
          var orig_expense = this.orig_expense;

            for(let field in expense){

              for ( let value in expense[field][this.expenseTypeName[0]] ){

                var expvalue = expense[field][this.expenseTypeName[0]][value];
                var origvalue = orig_expense[field][this.expenseTypeName[0]][value];
                
                if(origvalue == undefined && expvalue == ''){
                  this.dispatch.expense[field][this.expenseTypeName[0]][value] = null;
                  var expvalue = expense[field][this.expenseTypeName[0]][value];
                }

                if(expvalue != origvalue){
                  this.update = true;
                  return true;
                }

              }

            }

          this.update = false;
          return false;

        },

        handleSubmitExpense(button = null){

          this.verifyMe();

          if(this.update == false){
            this.errortoast('There is nothing to update', 500);
            return false;
          }

          if(this.checkforqty()){

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

          }

            
        },

        submitFormTrips(button = null){

          
          this.dispatch.submit('post', this.api + this.controller + 'saveAllFuel/' + this.pageid + '/' + this.dispatch.equipment_id).then(success=>{

                this.showToast('Expense Added Successfully');

                this.loading('Saving');

                this.reset();

                 setTimeout(() => {
                    this.pageid = this.$route.params.dispatch_id;
                    this.dispatch.equipment_id = this.$route.params.equipment_id;
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

           return _.orderBy(this.dispatch.waitingApproval.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 0) return elem;
            }), 'expenseDate');

        },

        approvedActive: function (){

           return _.orderBy(this.dispatch.waitingApproval.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 1) return elem;
            }), 'expenseDate');

        },

        deleted: function (){

           return _.orderBy(this.dispatch.waitingApproval.filter(function(elem){
                if(elem.status == 0) return elem;
            }), 'expenseDate');

        },

        flagged: function () {

          return this.dispatch.waitingApproval.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1) return elem;
            });
          
        },

        orderedDates: function () {
          this.removeSelectedDate();
          return _.orderBy(this.dispatch.Created, 'value')
        },


        

      },

  }
</script>