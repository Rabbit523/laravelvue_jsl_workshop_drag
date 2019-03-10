<template>

  <div>


    <c-panel :title="pageTitle">

       <div>

          <c-form-field label="Fuel Suppler">

            <c-multiselect
                v-model="selectedSupplier"
                track-by="value"
                label="label"
                :options="supplier"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Fuel Supplier"></c-multiselect>

               
          <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
          </c-form-field>

           <c-form-field label="Equipments">

            <c-multiselect
                v-model="selectedEquipment"
                track-by="value"
                label="label"
                :options="equipments"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Equipment"></c-multiselect>

               
          <span class="form-help u-color-danger" v-text="form.errors.get('selectedEquipment')" v-if="fuel.errors.has('selectedEquipment')"></span>
          </c-form-field>

          <c-form-field label="Date Range" v-if="selectedSupplier != undefined || selectedEquipment != undefined">
            <c-flatpickr 
              v-validate="'required'"
              v-model="dateRange"
              
              name="dispatch starting date"
              value="" range
            />
          </c-form-field> 

          <div class="u-text-left" slot="footer">
            <c-button v-if="dateRange != undefined"  type="warning" @click="getFuel()" v-once smart>Run</c-button>
            <c-button  v-if="expense.length > 0" type="primary" @click="print()" v-once smart>Print</c-button>
          </div>


        </div>

      </c-panel>

    

      <div class="box" ref="print">

        <!-- v-if="canIrun('bySupplier')" -->
          <c-panel v-if="expense.length > 0"  :title="pageTitle" >

              <c-level>
                <template  slot="left" class="phidden">
                  <div class="u-fs-10 pSupplierName phidden">{{ getTitle() }}</div>
                </template>

              </c-level>

              <c-level class="u-hidden">

                <template  slot="left">
                  <div class="u-fs-10 pReferenceNumber"></div>
                </template>                
                
                <c-button-group slot="center" class="u-h3 pSupplierName">
                  {{ getTitle() }}
                </c-button-group>

                <template slot="right">
                  <div class="pDate">
                    {{ startingDate }} to {{ endingDate }}
                  </div>
                </template>

                
              </c-level>

              <br>

              <!-- ############################################################# -->
              <!-- START REPORT BY SUPPLIER -->
              <!-- ############################################################# -->
                  
             <c-form-field >
            
              <table class="table table--striped table--hover pTable">

                   <tbody class="pBody">

                    <tr v-if="list.isApproved == 1" v-for="(list, index) in approved">
                      
                      <td class="tdLeft">{{ list.expenseDate }}</td>
                      <td class="tdLeft" v-if="selectedEquipment == undefined">{{ list.equipment }}</td>
                      <td class="tdLeft" v-if="selectedSupplier == undefined">{{ list.supplier }}</td>
                      <td class="tdCenter">{{ list.slipNumber }}</td>
                      <td class="tdRight">{{ list.qty | roundIt }}</td>
                      <td class="tdRight">{{ list.rate }}</td>
                      <td class="tdOilQty" v-if="list.oilQty != undefined && list.oilRate != undefined">{{ list.oilQty | removeDec }} x {{ list.oilRate | removeDec }}</td>
                      <td class="tdOilQty" v-else=""></td>
                      <td class="tdRight">{{ list.oilActualAmount | roundIt | currency }}</td>
                      <td class="tdAmount">{{ list.actualAmount | roundIt | currency }}</td>
                      
                    </tr>

                  </tbody>

                  <thead class="pHead">
                      
                      <tr>

                          <th>Date</th>
                          <th v-if="selectedEquipment == undefined">Equipment</th>
                          <th v-if="selectedSupplier == undefined">Supplier</th>
                          <th>Slip No.</th>
                          <th>Fuel</th>
                          <th>Rate</th>
                          <th>Oil x Rate</th>
                          <th>Oil Total</th>
                          <th>Amount</th>

                      </tr>

                  </thead>

                  <thead class="pHead">
                      
                      <tr>

                          <th v-if="selectedEquipment != undefined && selectedSupplier != undefined" colspan="2"></th>
                          <th v-else colspan="3"></th>
                          <th class="tdRight">{{ totalColoumn('qty') | roundIt }} Ltr.</th>
                          <th></th>
                          <th class="tdRight">{{ totalColoumn('oilQty') | removeDec }} Ltr.</th>
                          <th>{{ totalColoumn('oilActualAmount') | roundIt | currency }}</th>
                          <th class="tdRight">{{ totalColoumn('actualAmount') | roundIt | currency }}</th>

                      </tr>

                  </thead>

                </table>

                <!-- <br>
                <br>

                <c-row justify="center">

                  <c-col md="16" align="center">
        
                  <table class="table table--striped table--hover pSummaryTable">

                     <tbody class="pBody">

                      <tr v-for="(list, index) in getTypes">
                       
                        <td class="tdLeft">{{ list.equipmentType }}</td>
                        <td class="tdRight">{{ totalByTypes('qty', list.id) | roundIt }} Ltr.</td>
                        <td class="tdRight">{{ totalByTypes('oilQty', list.id) | removeDec }} Ltr.</td>
                        <td class="tdRight">Rs.{{ totalByTypes('oilActualAmount', list.id) | roundIt | currency }}/-</td>
                        <td class="tdRight">Rs.{{ subTotalType(list.id, index) | makeInt | currency  }}/- </td>

                      </tr>

                      <tr class="subTotal">
                        <td colspan="4" class="tdRight">Sub Total</td>
                        <td class="tdRight">Rs.{{ totalColoumn('actualAmount') | roundIt | currency }}/-</td>
                      </tr>

                      <tr v-if="approved.length > 0 && decimalValues.length > 0">
                        <td colspan="4" class="tdRight">Decimal Adjustment</td>
                        <td class="tdRight">Rs.{{ decimalAdjustment }}/-</td>
                      </tr>


                    </tbody>

                    <thead class="pHead">
                        
                        <tr class="u-bg-warning">

                            <th width="">Types</th>
                            <th width="20%">Diesel</th>
                            <th>Oil</th>
                            <th>Oil Total</th>
                            <th>Line Amount</th>

                        </tr>

                    </thead>

                    <thead class="pHead">
                      
                      <tr class="u-color-grey-lightest">
                        
                        <th class="u-bg-grey-light tdLeft">Grand Total</th>
                        <th class="u-bg-grey-light tdRight">{{ totalColoumn('qty') | roundIt }} Ltr.</th>
                        <th class="u-bg-grey-light tdRight">{{ totalColoumn('oilQty') | removeDec }} Ltr.</th>
                        <th class="u-bg-grey-light tdRight">
                        Rs.{{ totalColoumn('oilActualAmount') | roundIt | currency }}/-</th>
                        <th class="u-bg-grey-light tdRight">
                        Rs.{{ doAdjustment(grandTotal('actualAmount'), decimalAdjustment ) | currency }}/-</th>

                      </tr>

                    </thead>
                    
                  </table>

                  </c-col>

                </c-row> -->


            </c-form-field>

          </c-panel>


        </div>

      </c-panel>


  </div>
</template>
<script>

import { format, addDays } from 'date-fns';

import {Form, Errors} from "./../common/base.js";

import { Printd } from 'printd';
import numeral from 'numeral';

  export default {

    data() {
      return {

        fuel: new Form({

          selectedSupplier: undefined,
          selectedEquipment: undefined,
          searched: undefined,
          selectedBill: undefined,
          adjustmentAmount: undefined,

        }),

        dateRange: undefined,
        startingDate: undefined,
        endingDate: undefined,

        expense: [],
        values: [],

        selectedSupplier: undefined,
        selectedEquipment: undefined,
        supplier: [],
        equipments: [],
        decimalValues: [],
        decimalAdjustment: 0,
        
        isLoading: false,
        isDisabled: false,
        isHidden: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Supplier Fuel Report',
        printPageTitle: undefined,
        api: '',
        controller: '/expense/',
        controllerName: 'Expense',
        basePost: '',
        postUrl: '',
        pageid: '',

        toast: '',

        cssText: `
            .box {
              font-family: sans-serif;
              width: 100%;
              text-align: center;
            }

            .phidden{
              display: none;
            }

            .pTitle {
              font-size: 12px;
              float: left;
            }


            .pCompanyName{
              font-size: 20px;
              font-weight: bold;
            }

            .pSupplierName{
              margin-top: 15px;
              font-weight: 500;
              font-size: 14px;
            }

            .pBillDetails{
              margin-top: 15px;
              font-size: 14px;
            }

            .pDate {
              margin-top: 15px;
              font-size: 12px;
              float: right;
            }

            .pReferenceNumber {
              margin-top: 15px;
              font-size: 12px;
              float: left;
            }

            .pTable{
              width: 100%;
              margin-top: 0px;
              margin-bottom: 10px;
              border-collapse: collapse;
            }

            .pAmount {
              
            }

            .pBody td {
              border-left: solid 1px #ccc;
              border-right: solid 1px #ccc;
            }

            .tdLeft {
              text-align: left;
              border-right: solid 1px #ccc;
              padding: 5px 5px 5px 10px;
            }

            .tdCenter {
              text-align: center;
              border-right: solid 1px #ccc;
              padding: 5px 5px 5px 10px;
            }

            .tdRight {
              text-align: right;
              border-right: solid 1px #ccc;
              padding: 5px 10px 5px 10px;
            }

            .tdOilQty {
              text-align: right;
              border-right: solid 1px #ccc;
              padding: 5px 5px 5px 5px;
              width: 30px;
            }

            .tdAmount {
              text-align: right;
              border-right: solid 1px #ccc;
              padding: 5px 10px 5px 10px;
            }

            .pHead{
              text-align: center;
              border: solid 1px #ccc;
              border-left: solid 1px #ccc;
              background-color: #000;
              color: #000;
            }

            .pHead th {
                border-left: solid 1px #ccc;
                background-color: #000;
                padding: 10px 5px 10px 5px;
            }

            .pHead tr {
              background-color: #000;
              // padding: 10px 10px 10px 10px;
            }

            .pSummaryTable {
              margin-top: 20px;
              width: 90%;
              border-collapse: collapse;
            }

            .typeHead{
              text-align: left;
              padding-top: 10px;
              margin-bottom: 0px;
            }

            .subTotal{
              border-top: solid 1px #ccc;
            }
            
          `,
        
      }


    },

    components: {

    

    },
    
    created() {

       this.basePost = this.api + this.controller;
       this.postUrl = this.basePost;

       this.getData();

    },

    mounted(){

          this.d = new Printd()
          const { contentWindow } = this.d.getIFrame()

    },

    methods: {

       print () {

          var pageTitle = this.pageTitle;
          this.pageTitle = '';

          setTimeout(() => {
            this.d.print( this.$refs.print, this.cssText)
          }, 100);

          setTimeout(() => {

            this.pageTitle = pageTitle;

          }, 300);
            
        },

        getTitle()
        {

          if(this.selectedSupplier != undefined && this.selectedEquipment != undefined)
          {
            return 'Report for ' + this.selectedEquipment.equipmentNumber + ' from ' + this.selectedSupplier.supplierName;
          } 
          else if (this.selectedSupplier != undefined)
          {
            return 'Report for ' + this.selectedSupplier.supplierName;
          }
          else if (this.selectedEquipment != undefined)
          {
            return 'Report for ' + this.selectedEquipment.equipmentNumber;
          }


        },

        getData(){

          this.getSupplier();
          this.fetchEquipments();          

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

        fetchEquipments(){

          axios
            .get('/equipment')

              .then(response => {

                this.equipments = response.data.data;

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        getFuel()
        {

          this.loading('Fetching data...');
          var supplier_id = null;
          var equipment_id = null;
          if(this.selectedSupplier != undefined)
          {
            supplier_id = this.selectedSupplier.id;
          }
          if(this.selectedEquipment != undefined)
          {

            equipment_id = this.selectedEquipment.id;  
          }
          
          
          axios
            .get( '/expense/getFuelByDate', {

              params: {

                startDate : this.startingDate,
                endDate: this.endingDate,
                supplier_id: supplier_id,
                equipment_id: equipment_id

              }

            })

              .then(response => {

                this.pushData(response.data.data);

                this.loading();

            }).catch(error => {

                  // Hide Loading Toast
                this.loading();

                this.errortoast();

            });

        },

        

        pushData(data){

          this.expense = [];

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
                  equipmentType: data[field].equipmentType,
                  eqTypes: data[field].eqTypes,
                  equipmentSupplier: data[field].equipmentSupplier,
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
                  this.expense[i[0].index].oilActualAmount = data[field].actualAmount;

                }

              }

            }

          }

        },
        
        getByEquipment(equipment)
        {

          var arr = _.orderBy(this.approved.filter(function(elem){
                      if(elem.equipment == equipment
                        ) return elem;
                  }) );

          return arr;
        },

        //Get equipments by select types in sequence
        getEquipmentsByType(type){

            var arr = _.orderBy(this.getEquipments.filter(function(elem){
                      if(elem.equipmenType == type) return elem;
                  }) );
          return arr;

        },

        getOilTotal(rate, qty)
        {

          if(rate != undefined && rate > 0 && qty != undefined && qty > 0){
            return (rate*qty);  
          }

          return '';
          

        },

        //Total by supplier of each column
        totalColoumn(col) {

          total = 0;
          if(this.approved.length > 0)
          {

            var total = this.approved.reduce(function(prev, cur) {

              if(cur[col]){
                return parseFloat(prev) + parseFloat(cur[col]);
              } 

              return parseFloat(prev);
              
            }, 0);


          }

          return parseFloat(total).toFixed(2);

        },

        //total by equipment number of each coloumn
        totalColoumnByEquipment(col, equip) {

          total = 0;
          var data = this.getByEquipment(equip);
          if(data.length > 0)
          {

            var total = data.reduce(function(prev, cur) {

              if(cur[col]){
                return parseFloat(prev) + parseFloat(cur[col]);
              } 

              return parseFloat(prev);
              
            }, 0);


          }

          return parseFloat(total).toFixed(2);

        },

        //Total by supplier of each type
        totalByTypes(col, type) {
          
            var total=[];
            this.approved.forEach(function(element){
               if(element.equipmentType == type){
                    if(element[col]){
                        total.push(element[col]);
                    }
               }
            })

            var returnTotal = total.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);
            this.values[col] = returnTotal;
            
            return returnTotal;


        },


        //Total of each equipment type
        subTotalType(type, index)
        {

            var total=[];
            this.approved.forEach(function(element){
               if(element.equipmentType == type){
                   total.push(element.actualAmount);
               }
            })

            var totalAmount = total.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);
            this.decimalValues[index] =  Math.round(totalAmount) - totalAmount;
            
            this.decimalAdjustment = this.decimalValues.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);
            return totalAmount;

        },

          //Total by supplier of each type group by selected equipment supplier
        totalByTypesGroupSup(col, type, sup_id) {
          
            var total=[];
            this.approved.forEach(function(element){
               if(element.equipmentType == type && element.equipmentSupplier.id == sup_id){
                    if(element[col]){
                        total.push(element[col]);
                    }
               }
            })

            return total.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);


        },

        typeExistsinEqSupplier(type, sup_id) {
          
            var arr = _.orderBy(this.approved.filter(function(elem){
                      if(elem.equipmentType == type && elem.equipmentSupplier.id == sup_id) return elem;
                  } ) );

            if(arr.length > 0){
              return true;
            }

            return false;

          
        },

        //Total of each equipment type group by selected quipment supplier
        subTotalTypeGroupSub(type, sup_id, index, i)
        {

            var total=[];
            this.approved.forEach(function(element){
               if(element.equipmentType == type && element.equipmentSupplier.id == sup_id){
                   total.push(element.actualAmount);
               }
            })

            var totalAmount = total.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);
            if(!this.decimalValues[i])
            {
              this.decimalValues[i] = [];
            }

            this.decimalValues[i][index] =  Math.round(totalAmount) - totalAmount;
            
            var myNewArray = this.decimalValues.reduce(function(prev, curr) {
            return prev.concat(curr);
            });

          this.decimalAdjustment = myNewArray.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);

            return totalAmount;

        },

        //Grand total of bill
        grandTotal(col)
        {

          var total = this.totalColoumn(col);

          return (parseFloat(total).toFixed(2) );

        },

        doAdjustment(val, adjustment)
        {

          var finalAmount = (parseFloat(val) + parseFloat(adjustment));
          return finalAmount

        },


        getAdjustment(billAmount)
        {

          var grandTotal = this.grandTotal('actualAmount');
          return  (parseFloat(this.selectedBill.billAmount) - parseFloat(grandTotal) ).toFixed(2);

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

  
          

      },

      computed: {

        decimalVal: function ()
        {

          return this.decimalValues.reduce(function(total, num){ return (parseFloat(total) + parseFloat(num)).toFixed(2) }, 0);

        },

        approved: function (){

          var arr = [];

          if(this.selectedSupplier != undefined || this.selectedEquipment != undefined){

                 var arr = _.orderBy(this.expense.filter(function(elem){
                      if(elem.status == 1 && elem.isApproved == 1) return elem;
                  }), 'expenseDate');

         }

          let ascDesc = 1;
          var sortedApha = arr.sort((a, b) => ascDesc * a['expenseDate'].localeCompare(b['expenseDate']));

          return sortedApha;

        },
 

        getEquipments: function(){

             var uniqueArray=[];
              this.approved.forEach(function(element){
                 if(uniqueArray.map(item => item.equipment).indexOf(element.equipment)===-1){
                     uniqueArray.push({'equipment': element.equipment, 'equipmenType': element.eqTypes.equipmentType});
                 }
             })

            let ascDesc = 1;
            var sortedApha = uniqueArray.sort((a, b) => ascDesc * a['equipment'].localeCompare(b['equipment']));

             return sortedApha;

        },        

        getTypes: function(){

             var uniqueArray=[];
              this.approved.forEach(function(element){
                 if(uniqueArray.map(item => item.id).indexOf(element.eqTypes.id)===-1){
                     uniqueArray.push(element.eqTypes);
                 }
             })

             return uniqueArray;

        },


      },

      watch: {

        dateRange() {

            this.pageTitle = this.dateRange;

            var dates = this.dateRange.split(' → ');
            
            if(dates.length > 1){

              this.startingDate  = dates[0];
              this.endingDate    = dates[1];
              this.getFuel();

          }

        },


      },

      filters: {
        format: function (date) {
          return format(date, 'DD MMMM YYYY');
        },

        roundIt: function (val) {

          if(val != undefined && val != ''){
              // return parseInt(val);  
          
              return parseFloat(val).toFixed(2);
          }
          
        },

        makeInt: function (val) {

          if(val != undefined && val != ''){
              return Math.round(val);      
          }
          
        },

        removeDec: function (val) {

          if(val != undefined && val != '')
          {
            return parseInt(val);
          }

        },

        currency: function (val) {

          if(val != undefined){
              return numeral(val).format("0,0[.]00");
          }
          
        }

      }

  }
</script>