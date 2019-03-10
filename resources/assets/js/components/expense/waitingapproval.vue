<template>

  <div>


    <c-panel title="Waiting Approval">

       <div>

        <c-level>

          <template  slot="left">
         
            <c-button type="danger" @click="$router.go(-1)" v-once smart>Back</c-button>

          </template>

          <c-button-group slot="center">
            
            
            <c-button @click="$router.push('/expense/flagged')" type="primary" outline>Flagged Fuel</c-button>

            <c-button @click="$router.push('/expense/deleted')" type="primary" outline>Deleted Fuel</c-button>
            
          </c-button-group>

          <template slot="right">
            
            <p></p>

          </template>

        </c-level>

        <br>

          <c-form-field label="Equipments">

          <c-multiselect
            v-model="selectedEquipments"
            track-by="value"
            label="label"
            accountNumber="accountNumber"
            :options="equipments"
            :searchable="true"
            :close-on-select="false"
            :multiple="true"
            placeholder="Select Equipments"></c-multiselect>
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedEquipments')" v-if="dispatch.errors.has('selectedEquipments')"></span>

        </c-form-field>

        <!-- <c-form-field>

          <c-row>
            <span v-for="(equip, index) in selectedEquipments">
              <c-col xs="24" sm="24" md="24" lg="24" xl="24">
                <c-tag 
                  checkable 
                  v-model="filtered[equip.id]" 
                  closable v-show="visible" 
                  type="primary" 
                  @close="onClose(index)">{{ equip.label }}</c-tag>
              </c-col>
            </span>
          </c-row>

        </c-form-field> -->

        <c-form-field v-if="selectedEquipments.length > 0" label="Search By Date">
            <c-flatpickr 
              v-validate="'required'"
              v-model="dateRange"
              
              name="dispatch starting date"
              range
            />
          </c-form-field> 

          <div class="u-text-left" slot="footer">
            <c-button type="primary" @click="getSearchData()" v-once smart>Search</c-button>
          </div>

        </div>

    </c-panel>

    
    
    <c-panel type="ghost">

     

     <!--  <c-panel title="Waiting Approval">
            
       <c-form-field >
      
        <table v-for="(sequip, index) in selectedEquipments" v-if="filtered[sequip.id] == true" class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="20%">Fuel Refill Date</td>
                <td width="14%">Litre</td>
                <td width="14%">Rate</td>
                <td width="14%">Amount</td>
                <td width="14%">Created By</td>
                <td width="14%">Action</td>
                <td width="14%" v-if="flagged.length > 0">Flagged By</td>
              </tr>

                <tr v-if="list.equipment_id == sequip.id, list.status == 1" v-for="(list, index) in expenses(sequip.id)">

                <td>{{ format(list.expenseDate) }}</td>
                <td>{{ list.qty }}</td>
                <td>{{ (list.actualAmount / list.qty).toFixed(2) }}</td>
                <td>{{ list.actualAmount }}</td>
                <td><c-badge :type="list.createdBy.tagColor">{{ list.createdBy.name }}</c-badge></td>
                <td>
                  
                  <c-button-group>

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

      
    </c-panel> -->

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

      </c-panel>

      <c-modal title="Reason to Flag" placement="center" size="small" v-model="reasonModel">
        Reason to flag:

        <c-form-field >

            <textarea class="form-textarea" v-model="reason"
                :disabled="isDisabled" />

        </c-form-field>

        <div class="u-text-right" :hidden="isHidden" slot="footer">
          <c-button type="primary"  @click="flagIt()" v-once smart>Confirm</c-button>
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
          selectedEquipments: [],
          selectedEquipments_id: [],

          dateRange: [],
          startingDate: undefined,
          endingDate: undefined,
          expense: [],
          waitingApproval: [],
          // dates: [],
          
        }),

        update: false,
        orig_expense: [],
        
        equipments: [],
        visible: true,
        filtered: [],
        expenseType: [],
        expenseTypeName: [],
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

      'c-multiselect': Multiselect,
      [FlatPickr.name]: FlatPickr

    },
    
    created() {

      this.$root.$emit('UpdateWaiting');
       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

      this.getEquipments();

    },
    mounted() {

        // this.$parent.$on('ChangeView', this.testThis);

    },
    methods: {

      testThis() {
        console.log('hit');
      },

        getEquipments(){

            this.equipments = [];

            this.loading('Loading');

            axios

              .get('/equipment')

                .then(response => {
                  
                  // Set all equipmets data from response
                  this.equipments = response.data.data;

                  for (let field in this.equipments){

                    var id = this.equipments[field].id;

                    this.filtered[id] = false;

                  }
                    
                  this.loading();

              }).catch(error => {

                  this.loading();

                  this.errortoast();

              });

          },

        onClose(index){

          this.dispatch.selectedEquipments.splice(index, 1);
          if(this.dispatch.startingDate != undefined && this.dispatch.endingDate != undefined){
              this.getSearchData();
          }

        },

        getSearchData(){

          this.loading('Searching');

          axios

            .get('/searchExpense',{

              params: {

                startDate : this.dispatch.startingDate,
                endDate: this.dispatch.endingDate,
                equipment_id: this.dispatch.selectedEquipments_id,

              }

            })

            .then(response => {
              
              this.loading();

              // this.dispatch.expense = response.data;
              // this.dispatch.expense = response.data.data;

              this.pushData(response.data.data);

            })

            .catch(error => {

              this.errortoast();

              this.loading();

          });

        },

        pushData(data){

          this.dispatch.expense = [];

          if(data != null){

            var u = 0;

            for (let field in data){

              var run = true;

              var expenseDate = data[field].expenseDate;

              // Only push data belong to id 4 == Petrol
              
              if(data[field].expenseTypes == 'Fuel'){

              var k = this.dispatch.expense.push({

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

                let i = this.dispatch.expense.filter(function(elem){
                    if(elem.id == data[field].parent_id 
                        // && elem.value == data[field].expenseDate
                      ) return elem;
                });

                if(i.length > 0){

                  this.dispatch.expense[i[0].index].oilRate = data[field].rate;
                  this.dispatch.expense[i[0].index].oilQty = data[field].qty;

                }

              }

            }

          }

        },



        expenses(item){
          
          return this.dispatch.expense.filter(function(elem){
                if(elem.equipment_id == item && elem.isApproved == 0) return elem;
            });

        },

        format(date){

          return format(date, 'DD-MM-YYYY');

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

        flagModal(id){

          this.reason = '';
          this.isDisabled = false;
          this.reasonModel = true;
          this.flagid = id;
          this.isHidden = false;

        },

        showReason(reason){

          this.reason = reason;
          this.reasonModel = true;
          this.isDisabled = true;
          this.isHidden = true;

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

        flagIt(){

            let i = this.dispatch.expense.map(item => item.id).indexOf(this.flagid);

            this.loading('Flagging');

            axios

              .post(this.api + this.controller + 'flagIt/' + this.flagid,{

                reason: this.reason

              }).then(success=>{

                  if(i != '-1'){

                    this.$root.$emit('UpdateWaiting');

                    this.dispatch.expense[i].flag = 1;
                    this.dispatch.expense[i].flaggedBy = this.user;
                    this.dispatch.expense[i].reasonToFlag = this.reason;

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

          let i = this.dispatch.expense.map(item => item.id).indexOf(expense_id);

          axios

            .post(this.api + this.controller + 'deleteIt/' + expense_id).then(success=>{

                if(i != '-1'){
                // this.getData();
                this.$root.$emit('UpdateWaiting');

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

        dateRange: {
            // getter
            get: function () {
              return this.dispatch.dateRange;
            },
            // setter
            set: function (newValue) {

              // var names = newValue.split(' ')
              this.dispatch.dateRange = newValue;

              var dates = newValue.split(' → ');
              
              if(dates.length > 1){
                this.dispatch.startingDate = dates[0];
                this.dispatch.endingDate = dates[1];
                this.button = true;
              }

            }

          },

          selectedEquipments: {

            get: function () {
              return this.dispatch.selectedEquipments;
            },
            
            set: function (newValue){

              this.dispatch.selectedEquipments = newValue;

              this.dispatch.selectedEquipments_id = [];

              for (let item in newValue){
                this.dispatch.selectedEquipments_id.push(newValue[item].id);
              }

              // if(this.dispatch.startingDate != undefined && this.dispatch.endingDate != undefined){
              //   this.getSearchData();
              // }

            }

          },

          flagged: function () {

            return this.dispatch.expense.filter(function(elem){
                if(elem.flag == 1 && elem.status == 1) return  elem;
            });
          
          },

          waitingApprovalActive: function (){

           return _.orderBy(this.dispatch.expense.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 0) return elem;
            }), 'expenseDate');

        },
        

      },

  }
</script>