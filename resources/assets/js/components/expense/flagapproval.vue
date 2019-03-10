<template>

  <div>


    <c-panel title="Flagged Fuel">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

          
          <div class="u-text-left" slot="footer">
            
          </div>

        </div>

    </c-panel>

    <c-panel type="ghost" v-if="fuel.editingId != undefined">

         <c-panel :title="'Total: Rs ' + calcRate() + '/-'">

          <c-row class="u-pb-10 u-mt-10">

            <c-col lg="4" xl>


              <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Date</span>
                  <input 
                      type="text" 
                      v-model="fuel.fuelData['date']"
                      name="date" 
                      readonly="" 
                      v-validate="'required|date_format:DD-MM-YYYY'" 
                      ref="fueldate"
                      class="form-input">
                </div>
                  <small v-if="!errors.first('date')">Format DD-MM-YYYY</small>
                  <span class="form-help u-color-danger">{{ errors.first('date') }}</span>
              </div>
                  

            </c-col>

            <c-col lg="4" xl>
              
              <c-form-input
                addon-start="Slip#"
                :class="fuel.colorclass"
                type="text"
                v-model="fuel.fuelData['slip']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Fule Ltr" 
                v-validate="'required'"
                name="litre"
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData['qty']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Fuel Rate" 
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData['rate']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <c-form-input
                addon-start="Oil Ltr" 
                :class="fuel.colorclass"
                type="number"
                v-model="fuel.fuelData['oQty']">
              </c-form-input>

            </c-col>

            <c-col lg="4" xl>

              <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Oil Rates</span>
                  <input 
                      type="text" 
                      v-model="fuel.fuelData['oRate']" 
                      :class="fuel.colorclass"
                      class="form-input">
                </div>
              </div>


            </c-col>

            <c-col lg="24" xl>

            <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Remarks</span>
                  <input 
                      type="text" 
                      v-model="fuel.remarks" 
                      :class="fuel.colorclass"
                      class="form-input">
                </div>
              </div>

            </c-col>

            <c-button type="primary" @click="savenapprove" v-once smart>Save & Approve</c-button>

          </c-row>

         </c-panel>


      <!-- <span v-model="addMore"></span> -->

    </c-panel>  
    
    <c-panel type="ghost">

     

      <c-panel title="Flagged Fuel">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="15%">Fuel Refill Date</td>
                <td width="10%">Equipment</td>
                <td width="10%">Slip Number</td>
                <td width="15%">Amount</td>
                <td width="10%">Created By</td>
                <td width="15%">Action</td>
                <td width="10%" v-if="flagged.length > 0">Flagged By</td>
              </tr>

              <!-- <tr v-if="list.isApproved == 0, list.equipment_id == sequip.id" v-for="(list, index) in dispatch.expense"> -->
                <tr v-if="list.isApproved == 0 && list.status == 1 && list.parent_id == null" v-for="(list, index) in dispatch.expense">
                <td>{{ format(list.expenseDate) }}</td>
                <td>{{ list.equipment }}</td>
                <td>{{ list.slipNumber }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.createdBy.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td>
                  
                  <c-button-group>

                    <c-button v-if="isApprovable(list.createdBy.id, list.flag)" @click="editIt(list.id)" type="primary"><i class="icon-pencil"></i></c-button>

                    <c-button v-if="isApprovable(list.createdBy.id)" type="primary" @click="approveIt(list.id)">
                      <i>
                        <i  v-if="list.isApproved == '1'" class="icon-thumbs-up2"></i>
                        <i  v-if="list.isApproved == '0'" class="icon-thumbs-down2"></i>
                      </i>
                    </c-button>

                    <c-button v-if="isFlaggable(list.createdBy.id, list.flag)" @click="flagModal(list.id)" type="primary"><i class="icon-flag"></i></c-button>

                    <c-button v-tooltip.top-start="'View Reason'" v-if="list.flag == 1" @click="showReason(list.reasonToFlag)" type="primary"><i class="icon-bubble"></i></c-button>
                    
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

     

      </c-panel>

      <c-modal title="Reason to Flag" placement="center" size="small" v-model="reasonModel">
        Reason to flag:

        <c-form-field >

            <textarea class="form-textarea" v-model="reason"
                :disabled="isDisabled" />

        </c-form-field>

        <div class="u-text-right" :hidden="isHidden" slot="footer">
          <c-button type="primary" @click="flagIt()" v-once smart>Confirm</c-button>
        </div>
      </c-modal>

 


  </div>
</template>
<script>

import { format, addDays } from 'date-fns';
import { velocity } from 'velocity-animate';

import {Form, Errors} from "./../common/base.js";

  export default {

    data() {
      return {


        dispatch: new Form({

          expense: [],
        }),

        fuel: new Form({

          editingId: undefined,
          billId: undefined,
          total: 0,
          fuelData: { 'date': '', 'slip': '', 'qty': 0, 'rate': 0 , 'oQty': 0, 'oRate': 0 },
          remarks: '',

        }),

        user: this.$auth.user(),

        reasonModel: false,
        reason: '',
        isDisabled: false,
        isHidden: false,

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
        
      }
    },

    components: {

      // 'c-multiselect': Multiselect,
      // [FlatPickr.name]: FlatPickr

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

        this.getFlaggedData();

    },
    methods: {

        getFlaggedData(){

          this.loading('Loading');

          axios

            .get('/expense/flagged')

            .then(response => {
              
              // this.dispatch.expense = response.data;
              this.dispatch.expense = response.data.data;

              this.loading();

            })

            .catch(error => {

              this.errortoast();

              this.loading();

          });

        },

        calcRate(index) {

          var data = [];

          if(this.fuel.fuelData){

            var data = this.fuel.fuelData;
            var qty = this.fuel.fuelData['qty'];
            var rate = this.fuel.fuelData['rate'];

            var oQty = this.fuel.fuelData['oQty'];
            var oRate = this.fuel.fuelData['oRate'];

            var fuelArray = [];
            var oilArray = [];

              if(qty > 0 && rate > 0){

                 data['total'] = (qty * rate).toFixed(2);
                 fuelArray =  (qty * rate).toFixed(2);
                  
               }


            if(oQty > 0 && oRate > 0){

                var oilAmount =  (oQty * oRate).toFixed(2);

                if(fuelArray > 0){
                  data['total'] = (Number(fuelArray) + Number(oilAmount)).toFixed(2);
                } else {
                  data['total'] = Number(oilAmount).toFixed(2);
                }

            }


          } 

          if(data['total'] == undefined){
            data['total'] = Number(0);
          }

          this.fuel.total = data['total'];
          return data['total'];

        },

        expenses(item){
          
          return this.dispatch.expense.filter(function(elem){
                if(elem.equipment_id == item && elem.isApproved == 0) return elem;
            });

        },

        format(date){

          return format(date, 'DD-MM-YYYY');

        },

        isFlaggable(user, flag){

          if(user == this.user.id || flag == 1){

            return false;

          } else {

            return true;

          }

        },

        isApprovable(user){

          if(user == this.user.id){

            return false;

          } else {

            return true;

          }

        },

        flagModal(id){

          this.reason = '';
          this.isDisabled = false;
          this.isHidden = false;
          this.reasonModel = true;
          this.flagid = id;

        },

        showReason(reason){

          this.reason = reason;
          this.reasonModel = true;
          this.isDisabled = true;
          this.isHidden = true;

        },

        editIt(id)
        {

          var items = this.dispatch.expense.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1 && 
                  ( elem.id == id || elem.parent_id == id )
                  ) return  elem;
            });

          var dt = this.fuel.fuelData;
          for (var i = items.length - 1; i >= 0; i--) {
            
            if(items[i].expenseType_id == 1)
            {
              this.fuel.editingId = items[i].id;
              this.fuel.billId = items[i].billNumber;
              this.fuel.reason = items[i].reasonToFlag;
              dt.date = this.format(items[i].expenseDate);
              dt.slip = items[i].slipNumber;
              dt.qty = items[i].qty;
              dt.rate = items[i].rate;
            }

            if(items[i].expenseType_id == 2)
            {
              dt.oQty = items[i].qty;
              dt.oRate = items[i].rate;
            }

          }

        },

        savenapprove()
        {

          let i = this.dispatch.expense.map(item => item.id).indexOf(this.fuel.editingId);

          this.loading('Saving');

          axios

            .post(this.api + this.controller + 'savenapprove/' ,{

              params: {

                editedData : this.fuel.fuelData,
                id: this.fuel.editingId,
                total: this.fuel.total,
                bill_id: this.fuel.billId,
                orignalReason: this.fuel.reason,
                remarks: this.fuel.remarks

              }

            }).then(success=>{

                // this.getData();
                if(i != '-1'){
                  this.$root.$emit('UpdateWaiting');
                  this.dispatch.expense[i].isApproved = 1;
                  this.dispatch.expense[i].approvedBy = this.user;
                  this.fuel.editingId = undefined;
                  this.fuel.billId = undefined;
                  this.fuel.fuelData = { 'date': '', 'slip': '', 'qty': 0, 'rate': 0 , 'oQty': 0, 'oRate': 0 };
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

        approveIt(id, index){

          let i = this.dispatch.expense.map(item => item.id).indexOf(id);

          this.loading('Approving');

          axios

            .post(this.api + this.controller + 'approveIt/' + id).then(success=>{

                // this.getData();
                if(i != '-1'){
                  this.$root.$emit('UpdateWaiting');
                  this.dispatch.expense[i].isApproved = 1;
                  this.dispatch.expense[i].approvedBy = this.user;
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

        killIt(expense_id, index){

          this.loading('Deleting');

          let i = this.dispatch.expense.map(item => item.id).indexOf(expense_id);

          axios

            .post(this.api + this.controller + 'deleteIt/' + expense_id).then(success=>{

                if(i != '-1'){
                this.$root.$emit('UpdateWaiting');
                this.$root.$children[0].flagged = this.$root.$children[0].flagged -1;
                this.dispatch.expense[i].status = 0;
                this.dispatch.expense[i].updatedBy = this.user;
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

          flagged: function () {

          return this.dispatch.expense.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1) return  elem;
            });
          
        },
        

      },

  }
</script>