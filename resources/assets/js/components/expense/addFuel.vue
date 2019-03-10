<template>

  <div>


    <c-panel :title="pageTitle">

       <div>

        <br>

          <c-form-field label="Fuel Suppler">

            <c-multiselect
                v-model="selectedSupplier"
                track-by="id"
                label="label"
                :options="supplier"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
        </c-form-field>

        <c-form-field label="Select Bill" v-if="fuel.selectedSupplier != undefined">

            <c-multiselect
                v-model="selectedBill"
                track-by="id"
                label="label"
                :options="filteredBills"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
        </c-form-field>

        <div :hidden="selectedSupplier == undefined || selectedBill == undefined">
        <c-form-field label="Fuel Rate">

          <c-form-input
            type="text"
            v-model="globalRate">
          </c-form-input>

        </c-form-field>

        <c-form-field label="Oil Rate">

          <c-form-input
            type="text"
            v-model="globalOilRate">
          </c-form-input>

        </c-form-field>

        <div v-if="fuel.adjustmentAmount != undefined">

          <c-form-field label="Adjustment Amount">

            <c-form-input
              type="text"
              v-model="fuel.adjustmentAmount">
            </c-form-input>

          </c-form-field>

          <c-form-field label="Remarks">

            <c-form-input
              type="text"
              v-model="fuel.remarks">
            </c-form-input>

          </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button type="primary" @click="handleSubmitExpense()" v-once smart>Save All</c-button>
          </div>

        </div>

        </div>
          

        </div>

    </c-panel>

    <c-panel :hidden="fuel.selectedSupplier == undefined || fuel.selectedBill == undefined" type="ghost">

         <c-panel :title="'Total: Rs ' + calcRate() + '/-'">
          <i v-if="fuel.selectedBill != undefined" slot="control">{{ TotalAmount() }} / {{ fuel.selectedBill.billAmount }}</i>

          <c-row class="u-pb-10 u-mt-10">

            <c-col lg="24" xl :hidden="fuel.selectedEquipment != undefined" >          

               <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Equipment Number</span>
                  <input 
                      type="text" 
                      v-model="fuel.searched" 
                      ref="search"
                      class="form-input">
                </div>
              </div>

            </c-col>

            <c-col lg="24" xl>

            <c-form-field :hidden="fuel.selectedEquipment == undefined" label="Select Equipment">

              <c-multiselect
                v-model="fuel.selectedEquipment"
                track-by="value"
                label="label"
                :options="equipments"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Equipments"></c-multiselect>

            </c-form-field>

            </c-col>

          </c-row>

            
          <c-row class="u-pb-10 u-mt-10" :hidden="fuel.selectedEquipment == undefined">

            <c-col lg="4" xl>


              <div class="form-field">
                <div class="form-group">
                  <span class="form-addon">Date</span>
                  <input 
                      type="text" 
                      v-model="fuel.fuelData['date']"
                      name="date" 
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
                      @keyup.enter="saveInTemp"
                      class="form-input">
                </div>
              </div>


            </c-col>

          </c-row>

         </c-panel>


      <!-- <span v-model="addMore"></span> -->

    </c-panel>    

    <c-panel v-if="temporaryStorage.length > 0 && selectedBill != undefined && selectedSupplier != undefined && saving == false" title="Waiting to Save">
      <i v-if="saving" class="i-spinner" slot="control"></i>
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="">Date</td>
                <td>Equipment</td>
                <td>Slip No.</td>
                <td>Bill No.</td>
                <td width="">Fuel Ltr</td>
                <td width="">Rate</td>
                <td width="">Oil Ltr</td>
                <td width="">Rate</td>
                <td width="">Amount</td>
                <td width="">Action</td>
              </tr>

              <tr v-for="(list, index) in temporaryStorage" :key="index" :class="list.error">
                <td>{{ list.fuelData.date }}</td>
                <td>{{ getEquipment(list.selectedEquipment).equipmentNumber }}</td>
                <td>{{ list.fuelData.slip }}</td>
                <td>
                  <span v-if="selectedBill != undefined">{{ selectedBill.id }}</span>
                </td>
                <td>{{ list.fuelData.qty }}</td>
                <td>{{ list.fuelData.rate }}</td>
                <td>{{ list.fuelData.oQty }}</td>
                <td>{{ list.fuelData.oRate }}</td>
                <td>{{ list.total }}</td>
                <td><c-button-group>
                    
                    <c-button type="primary" @click="editThis(index)">
                        <i class="icon-pencil"></i>
                    </c-button>
                    <c-button @click="killIt(index)" type="primary"><i class="icon-bin"></i></c-button>
                  </c-button-group>
                </td>
              </tr>

            </tbody>

          </table>

      </c-form-field>

    </c-panel>

    <c-panel v-if="waitingApprovalActive.length > 0" title="Fuel Waiting Approval">
            
       <c-form-field >
      
        <table class="table table--striped table--hover">

             <tbody>

              <tr>
                <td width="">Date</td>
                <td width="15%">Supplier</td>
                <td width="15%">Equipment</td>
                <td>Slip No.</td>
                <td width="">Fuel Ltr</td>
                <td width="">Rate</td>
                <td width="">Oil Ltr</td>
                <td width="">Rate</td>
                <td width="">Amount</td>
                <td width="">Created By</td>
                <td width="">Action</td>
                
              </tr>

              <tr v-if="list.isApproved == 0" v-for="(list, index) in waitingApprovalActive">
                <td>{{ list.expenseDate }}</td>
                <td>{{ list.supplier }}</td>
                <td>{{ list.equipment }}</td>
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
                    
                    <c-button @click="saveKillIt(list.id)" type="primary"><i class="icon-bin"></i></c-button>
                  </c-button-group>
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
// import Multiselect from './../../../vendors/cover/vendors/multiselect'
// import style from './../../../vendors/cover/vendors/multiselect/style.css';

// import FlatPickr from './../../../vendors/cover/vendors/flatpickr'
// import style2 from './../../../vendors/cover/vendors/flatpickr/style.css';

  export default {

    data() {
      return {

        fuel: new Form({

          selectedSupplier: undefined,
          selectedEquipment: undefined,
          searched: undefined,
          selectedBill: undefined,
          // runFor: [0],
          fuelData: { 'date': '', 'slip': '', 'qty': 0, 'rate': 0 , 'oQty': 0, 'oRate': 0 },

          submittedData: undefined,

          remarks: '',
          adjustmentAmount: undefined,

        }),

        editing: undefined,
        temporaryStorage: [],

        expense: [],
        reasonModel: false,
        flagid: '',
        reason: '',

        selectedEquipmentError: '',
        selectedSupplier: undefined,
        selectedBill: undefined,
        supplier: [],
        equipments: [],
        bills: [],
        filteredBills: [],
        globalRate: '',
        globalOilRate: '',
        saving: false,
        
        isLoading: false,
        isDisabled: false,
        isHidden: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Add Fuel',
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
        globalSupplierId: undefined,
        globalBillId: undefined,
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

       this.getData();

       if(this.$route.params.supplier_id){
        this.globalSupplierId = this.$route.params.supplier_id;
        this.globalBillId = this.$route.params.bill_id;
        
        // this.selectedSupplier = this.supplier[];
         // this.loading('Loading');
         // this.getData(this.$route.params.id);
         this.isDisabled = true;       
      } 

    },

    mounted(){

      

    },

    methods: {

        getData(){

          this.getSupplier();
          this.getEquipments();
          this.getSupplierBill();

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

        getFuel()
        {

          axios
            .get('/expense/getBillFuel',{

              params: {

                bill_id : this.selectedBill.id

              }

            })

              .then(response => {

                this.pushData(response.data.data);

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getSupplierBill(){

          axios
            .get('/bill')

              .then(response => {

                this.bills = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        pushData(data, saveType){

          if(saveType == null)
          {
            this.expense = [];
          }

          if(data != null){

            var u = 0;

            for (let field in data){

              var run = true;

              var expenseDate = data[field].expenseDate;

              // Only push data belong to id 4 == Petrol
              
              if(data[field].expenseTypes == 'Fuel'){

              var k = this.expense.push({

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
                  equipment: data[field].equipment,
                  slipNumber: data[field].slipNumber,
                  actualAmount: data[field].actualAmount,
                  rate: data[field].rate,
                  qty: data[field].qty,
                  index: u,
                  
                });

              u++;

              } else if(data[field].expenseTypes == 'Oil') {

                let i = this.expense.filter(function(elem){
                    if(elem.id == data[field].parent_id 
                        // && elem.value == data[field].expenseDate
                      ) return elem;
                });

                if(i.length > 0){

                  this.expense[i[0].index].oilRate = data[field].rate;
                  this.expense[i[0].index].oilQty = data[field].qty;

                }

              }

            }

          }

        },

        saveInTemp(){

          var data = this.fuel.fuelData;

          if(this.editing != undefined){
            this.temporaryStorage.splice(this.editing);
          }

          let findError = this.errors.items.map(item => item.field).indexOf('date');
          if(findError != '-1')
          {
            this.errortoast('Incorrect date format', 1000);
            return false;
          }

          var qtyCheck = this.checkforqty();
          if(qtyCheck == true)
          {

            var tempData = {

              'fuelData': data,
              'total': ((data.qty*data.rate)+(data.oQty*data.oRate)),
              'selectedSupplier': this.fuel.selectedSupplier.id,
              'selectedBill': this.fuel.selectedBill.id,
              'selectedEquipment': this.fuel.selectedEquipment.id

            };

            this.temporaryStorage.push(tempData);

            this.fuel.selectedEquipment = undefined;
            this.fuel.searched = undefined;
            this.fuel.date = '';
            this.fuel.fuelData = { date: '', 'slip': '', 'qty': '', 'rate': data.rate , 'oQty': '', 'oRate': data.oRate };

            setTimeout(() => {
              this.$refs.search.focus();
            }, 100);

            this.updateBillAmount();
            this.editing = undefined;
            this.autoSaver();

          }
          

        },

        updateBillAmount(){

          var TotalAmount = this.TotalAmount();
          this.fuel.adjustmentAmount = (this.fuel.selectedBill.billAmount - (TotalAmount) );

        },

        add (message) {
          this.$notify({
            type: 'success',
            title: 'Notification',
            content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi, placeat!',
            closeable: true,
            duration: 5000
          })
        },

        editThis(index)
        {

            var data = this.temporaryStorage[index];
            // let equipmentIndex = this.equipments.map(item => item.id).indexOf(data.selectedEquipment);
            
            var equipment = this.getEquipment(data.selectedEquipment);
            if(equipment != false){
              this.fuel.selectedEquipment = equipment;
            }

            var fuel = this.fuel.fuelData;

            fuel.rate   = data.fuelData.rate;
            fuel.qty    = data.fuelData.qty;
            fuel.oRate  = data.fuelData.oRate;
            fuel.oQty   = data.fuelData.oQty;
            fuel.slip   = data.fuelData.slip;
            fuel.date   = data.fuelData.date;
            this.editing  = index;
          

        },

        killIt(index)
        {

          this.temporaryStorage.splice(index, 1);

        },

        saveKillIt(expense_id, index){

          this.loading('Deleting');

          let i = this.expense.map(item => item.id).indexOf(expense_id);

          axios

            .post(this.api + this.controller + 'deleteIt/' + expense_id).then(success=>{

                if(i != '-1'){
                // this.getData();
                this.$root.$emit('UpdateWaiting');

                this.expense[i].status = 0;
                this.expense[i].updatedBy = this.user;
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

          let i = this.expense.map(item => item.id).indexOf(id);

          this.loading('Approving');

          axios

            .post(this.api + this.controller + 'approveIt/' + id + '/' + this.selectedBill.id).then(success=>{

                // this.getData();
                if(i != '-1'){

                  this.$root.$emit('UpdateWaiting');

                  this.expense[i].isApproved = 1;
                  this.expense[i].approvedBy = this.user;
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

            let i = this.expense.map(item => item.id).indexOf(this.flagid);

            this.loading('Flagging');

            axios

              .post(this.api + this.controller + 'flagIt/' + this.flagid,{

                reason: this.reason

              }).then(success=>{

                  if(i != '-1'){

                    this.$root.$emit('UpdateWaiting');

                    this.expense[i].flag = 1;
                    this.expense[i].flaggedBy = this.user;
                    this.expense[i].reason = this.reason;
                    this.expense[i].reasonToFlag = this.reason;

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

        getEquipment(id)
        {

          var equipment = this.equipments.filter(function(elem){

              if(elem.id == id) return elem;

            });

            if(equipment.length == 1){
              return equipment[0];
            }

            return false;

        },
        
        getEquipments(){

          axios
            .get('/equipment')

            .then(response => {

                this.equipments = response.data.data;

                
            }).catch(error => {

                this.errortoast();

            });

        },

        addDataFields(runFor){

          var i;
          for (i = 0; i < runFor; i++){
              
              if(!this.fuel.fuelData[i]){
              
                  this.fuel.fuelData.push({

                     qty: '',
                     slip: '',
                     rate: '',
                     oRate: '',
                     oQty: '',

                  });

              }

            }

        },

        TotalAmount() {

          var data = 0;

          if(this.temporaryStorage.length > 0)
          {

            for (let index in this.temporaryStorage)
            {

              data = (Number(data) + Number(this.temporaryStorage[index]['total'])).toFixed(2);

            }

          }

          total = 0;
          if(this.expense.length > 0)
          {

            var total = this.waitingApprovalActive.reduce(function(prev, cur) {
              return parseFloat(prev) + parseFloat(cur.actualAmount);
            }, 0);


          }

          return (parseFloat(data) + parseFloat(total));

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

          return data['total'];

        },

        isNumeric: function (n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
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

        checkforqtybeforeSave()
        {

          var flag = false;
          for (let index in this.temporaryStorage)
          {

            flag = this.checkforqty(this.temporaryStorage[index].fuelData);

          }

          return flag;

        },

        checkforqty(data){


          if(data == null){
            var data = this.fuel.fuelData;
          }

          // for (let index in this.fuel.runFor){
          var flag = [];
          var notNumber = [];
            
          var fields = ['qty', 'rate', 'oQty', 'oRate'];

            for (let fIndex in fields)
            {

              flag[fields[fIndex]] = true;
              notNumber[fields[fIndex]] = true;

              if(data[fields[fIndex]] == undefined || data[fields[fIndex]] == '' )
              {

                flag[fields[fIndex]] = false;

              }

              if(this.isNumeric(data[fields[fIndex]]) === false)
              {

                notNumber[fields[fIndex]] = false;

              }

            }

            

          // }

          if(!flag['rate'] && !flag['oRate'])
          {
            this.errortoast('Bote rate fields are empty', 1000);
            return false;            
          }

          if(!flag['qty'] && !flag['oQty'])
          {
            this.errortoast('Both litter fields fields are empty', 1000);
            return false;            
          }

          if(flag['qty'] && !flag['rate'] || flag['oQty'] && !flag['oRate'])
          {
            this.errortoast('Please add rate', 1000);
            return false;             
          }

          if(!notNumber['qty'] || !notNumber['rate'] || !notNumber['oRate'] || !notNumber['oQty'])
          {
            this.errortoast('Field(s) not a valid number', 1000);
            return false;             
          }

          // if(!flag[] && !oFlag){
          //   this.errortoast('Please check litre fields', 1000);            
          //   return false;
          // }

          return true;

        },

        checkDataEntigirity(){

          this.loading('Preparing data to save...')
          var flag = true;
          var data = this.temporaryStorage;
          for (let index in data)
          {

            this.temporaryStorage[index].error = '';
            var field = data[index].fuelData;

            var fields = ['date', 'slip', 'qty', 'rate', 'oQty', 'oRate'];

            for (let fIndex in fields)
            {

              if(field[fields[fIndex]] == undefined || field[fields[fIndex]] < 0 )
              {

                flag = false;
                this.showToast('One or more fields has incorrect or missing data please check!', 100);
                this.temporaryStorage[index].error = 'u-bg-danger u-color-grey-lightest';
                this.saving = false;

              }

            }

          }

          this.loading();

          return flag;

        },

        handleSubmitExpense(button = null){

          this.saving = true;
          var verification = this.checkforqtybeforeSave();
          var checkData = this.checkDataEntigirity();

          if(verification && checkData){

          this.loading('Saving');

          this.$validator.validateAll(this.temporaryStorage).then((result) => {
            
              // no errors submit form
              if(result){
              
                this.fuel.submittedData    =   this.temporaryStorage;

                this.submitAllFuel(button);

                return;

              }

              // Hide Loading Toast
              this.loading();

          });

          } else {

            this.saving = false;

          }

            
        },

        submitAllFuel(savingType){

          this.fuel.submit('post', this.api + this.controller + 'saveAllFuelbySupplier/').then(success=>{

                // this.loading('Saving');

                if(savingType == null){

                  this.showToast('Expense Added Successfully');

                  this.reset();  

                } else if (savingType == 'singleSave' ){

                  console.log(success);
                  this.pushData(success.data, 'singleSave');
                  this.temporaryStorage.splice(0, 1);
                  
                }

              
            }).catch(error=> {

              // Hide Loading Toast
              this.loading();

              this.errortoast();

            });

        },

        autoSaver()
        {

          var flag = true;

          if(this.temporaryStorage.length > 4){

            this.saving = true; // Check starts

                var data = this.temporaryStorage[0];

                flag = this.checkforqty(data.fuelData);
                
                data.error = '';
                var field = data.fuelData;

                var fields = ['date', 'slip', 'qty', 'rate', 'oQty', 'oRate'];

                for (let fIndex in fields)
                {

                  if(field[fields[fIndex]] == undefined || field[fields[fIndex]] < 0 )
                  {

                    flag = false;
                    this.showToast('One or more fields has incorrect or missing data please check!', 100);
                    data.error = 'u-bg-danger u-color-grey-lightest';
                    this.saving = false;

                  }

                }

            this.saving = false; //Checks complete

            if(flag){

            this.saving = true; //Saving starts


            this.$validator.validateAll(data).then((result) => {
              
                // no errors submit form
                if(result){
                
                  var singleSave = [];
                  singleSave.push(data);
                  this.fuel.submittedData    =   singleSave;

                  this.submitAllFuel('singleSave');

                  return;

                }

            });

            this.saving = false; //saving ends;


            } 

          }

        },

        
        reset(){

          // Display success message
          this.showToast();

          // Reset form using form class
          Object.assign(this.$data, this.$options.data.call(this));

          setTimeout(() => {
            this.errors.clear();
            this.getData();
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

          confirmBeforeChange()
          {

            this.$alert({

                type: 'alert',
                title: 'Are you sure?',
                content: 'Are you sure you want to delete all the unsaved fuel?',
                showCancel: true,
                cancelText: 'Update All',
                confirmText: 'Delete All',
                onConfirm: () => {

                  this.temporaryStorage = [];
                  return true;

                },
                onCancel: () => {
                  
                  this.loading('Updating');

                  setTimeout(() => {
                    this.updateAll();
                  }, 500);
                  

                }
              })

          },

          updateAll()
          {

            var supplierid = this.selectedSupplier.id;
            var selectedBill = undefined;

            if(this.selectedBill != undefined)
            {
              var selectedBill = this.selectedBill.id;  
            }
            
            for(let index in this.temporaryStorage)
            {

              this.temporaryStorage[index].selectedSupplier = supplierid;
              this.temporaryStorage[index].selectedBill = selectedBill;
              if(selectedBill != undefined){
                this.updateBillAmount();
              }

            }

            this.loading();

          }

          

      },

      computed: {

        waitingApprovalActive: function (){

           return _.orderBy(this.expense.filter(function(elem){
                if(elem.status == 1 && elem.isApproved == 0) return elem;
            }), 'expenseDate');

        },


      },

      watch: {

        supplier()
        {

          if(this.globalSupplierId != undefined)
          {
            this.selectedSupplier = this.supplier[ this.supplier.map(item => item.id).indexOf(parseInt(this.globalSupplierId) ) ];
          }          

        },

        bills()
        {

          if(this.globalBillId != undefined)
          {
            this.selectedBill = this.bills[this.bills.map(item => item.id).indexOf(parseInt(this.globalBillId) )];
          }

        },

        selectedSupplier(){

            this.selectedBill = undefined;
            if(this.fuel.selectedSupplier != undefined && this.temporaryStorage.length > 0){
              this.confirmBeforeChange();
            } 

            this.pageTitle = 'Add Fuel >';
            this.fuel.selectedSupplier = this.selectedSupplier;
            if(this.selectedSupplier != undefined){
              this.pageTitle = this.pageTitle + ' <strong>' + this.selectedSupplier.label + '</strong>';
            }

            var supplier = this.selectedSupplier;
            this.fuel.selectedBill = undefined;

            if(supplier != undefined){
              var filteredBills = this.bills.filter(function(elem){
                  if(elem.expense_supplier_id == supplier.id) return elem;
              });

            for (let index in filteredBills)
            {

              if(filteredBills[index].status == 13)
              {

                  filteredBills[index].label = filteredBills[index].label + ' (Fuel Added)';

              }

            }

            this.filteredBills = filteredBills;

            } else {

            return [];

            }

        },

        selectedBill(){

          if(this.temporaryStorage.length > 0 && this.selectedBill != undefined)
          {
              this.confirmBeforeChange();
          }

          this.fuel.selectedBill = this.selectedBill;

          if(this.selectedBill != undefined)
          {

            this.pageTitle = 'Add Fuel > ' + this.selectedSupplier.label + ' > <strong> Bill Number: ' + this.fuel.selectedBill.id + '<strong>';

            this.fuel.remarks = this.selectedBill.remarks;

          }

          if(this.selectedBill != undefined && this.selectedBill.status != 4)
          {

            console.log('hit');
            this.getFuel();
            this.updateBillAmount();

          }
          
          

        },

        fuel: {

           handler(form){

              var data = form.fuelData;
              // for (let field in data){

                if(data.rate == ''){

                  data.rate = this.globalRate;
                  data.oRate = this.globalOilRate;

                }

             // }

              var data = form.fuelData.date;
              
              if(data != ''){
                
                if(data.length == 2){
                  data = data + '-';
                }

                if(data.length == 5){
                  data = data + '-';

                }

                this.fuel.fuelData.date = data;

              }

             var searched = form.searched;

             // for(let index in data){

              this.selectedEquipmentError = '';

              // form.selectedEquipment = undefined;
              
              
              if(searched != undefined){

                  var equipment = this.equipments.filter(function(elem){

                    if(
                      elem.equipmentNumber.split('-').join('').toLowerCase() == searched.split('-').join('').toLowerCase() 

                      ) return elem;

                  });

                  if(equipment.length == 1){

                    this.fuel.searched = undefined;
                    form.selectedEquipment = equipment[0];
                    setTimeout(() => {
                      this.$refs.fueldate.focus();
                    }, 200);
                    
                    return true;

                  }

                  form.selectedEquipment = undefined;

                  this.selectedEquipmentError = 'Searching...';

                }

            // }

           },
           deep: true
        }

      }

  }
</script>