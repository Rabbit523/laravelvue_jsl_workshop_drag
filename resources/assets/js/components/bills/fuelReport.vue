<template>

  <div>


    <c-panel :title="pageTitle">

       <div>

          <c-form-field label="Equipment Suppler">

            <c-multiselect
                v-model="selectedEquipmentSupplier"
                track-by="value"
                label="label"
                :options="equipmentSuppliers"
                :searchable="true"
                :close-on-select="true"
                :multiple="true"
                placeholder="Select Equipment Supplier"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>
        </c-form-field>

          <c-form-field label="Fuel Suppler" v-if="selectedEquipmentSupplier != undefined && supplier.length > 0">

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

        <c-form-field label="Select Bill" v-if="fuel.selectedSupplier != undefined">

            <c-multiselect
                v-model="selectedBill"
                track-by="id"
                label="label"
                :options="filteredBills"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Bill"></c-multiselect>

               
         <span class="form-help u-color-danger" v-text="form.errors.get('selectedClient')" v-if="fuel.errors.has('fuel.selectedSupplier')"></span>

        </c-form-field>

        <c-form-field v-if="fuel.selectedSupplier != undefined">
          <c-form-checkbox label="Show Paid?" v-model="showPaid"></c-form-checkbox>
        </c-form-field>

        <c-form-field label="Select Order Sequence" v-if="fuel.selectedBill != undefined">

            <!-- <c-multiselect
                v-model="groupBy"
                track-by="name"
                label="label"
                :options="groupOptions"
                :close-on-select="true"
                placeholder="Select Group By">
                
            </c-multiselect> -->

            <template class="form-inline">
              <div v-for="(group, index) in groupOptions">
                <c-form-radio name="gender" v-model="groupBy" :value="group.name" :label="group.label" />
              </div>
            </template>


        </c-form-field>

         <c-form-field label="Order By" v-if="groupBy != undefined">

            <template v-for="(field, index) in getTypes" v-if="groupBy == 'byEquipment'">
              <c-tag class="u-mr-15 u-fs-24 u-p-15" checkable v-model="orderedBy[index]" type="primary">{{ field.equipmentType }}</c-tag>
            </template>

        </c-form-field>

        <c-form-field v-if="groupBy != undefined">
          <c-form-checkbox v-if="groupBy == 'byEquipment'" label="Header On?" v-model="headerOn"></c-form-checkbox>
          <c-form-checkbox label="Tax On?" v-model="taxOn"></c-form-checkbox>
        </c-form-field>

        <c-form-field label="Tax Rate" v-if="taxOn">

          <c-form-input
            type="text"
            v-model="taxRate">
          </c-form-input>

        </c-form-field>

        <div v-if="groupBy != undefined" class="u-text-left" slot="footer">
            <c-button type="primary" @click="print()" v-once smart>Print</c-button>
          </div>


        </div>

      </c-panel>

    

      <div class="box" ref="print">

          <c-panel v-if="canIrun('bySupplier')" :title="pageTitle" >

              <c-level>
                <template  slot="left" class="phidden">
                  <div class="u-fs-10 pSupplierName phidden">{{ fuel.selectedSupplier.supplierName }}</div>
                </template>

                <c-button-group slot="center" class="u-h3 pCompanyName">
                  {{ selectedEquipmentSupplier[0].fullName }}
                </c-button-group>

                <template slot="right" class="phidden">
                  <div class="phidden">
                    {{ fuel.selectedBill.startingDate }} to {{ fuel.selectedBill.endingDate }}
                  </div>
                </template>
              </c-level>

              <c-level class="u-hidden">

                <template  slot="left">
                  <div class="u-fs-10 pReferenceNumber"></div>
                </template>                
                
                <c-button-group slot="center" class="u-h3 pSupplierName">
                  {{ selectedSupplier.supplierName }}
                </c-button-group>

                <template slot="right">
                  <div class="pDate">
                    {{ fuel.selectedBill.startingDate }} to {{ fuel.selectedBill.endingDate }}  - {{ groupBy.label }}
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
                      <td class="tdLeft">{{ list.equipment }}</td>
                      <td class="tdCenter">{{ list.slipNumber }}</td>
                      <td class="tdRight">{{ list.qty | roundIt }}</td>
                      <td class="tdRight">{{ list.rate }}</td>
                      <td class="tdOilQty" v-if="list.oilQty != undefined && list.oilRate != undefined">{{ list.oilQty | removeDec }} x {{ list.oilRate | removeDec }}</td>
                      <td class="tdOilQty" v-else=""></td>
                      <td class="tdRight">{{ list.oilActualAmount | roundIt | currency }}</td>
                      <!-- <td class="tdRight">{{ ( (list.actualAmount * 25.5) /100 ) }}</td> -->
                      <td class="tdAmount">{{ list.actualAmount | roundIt | currency }}</td>
                      
                    </tr>

                  </tbody>

                  <thead class="pHead">
                      
                      <tr>

                          <th>Date</th>
                          <th>Equipment</th>
                          <th>Slip No.</th>
                          <th>Fuel</th>
                          <th>Rate</th>
                          <th>Oil x Rate</th>
                          <th>Oil Total</th>
                          <!-- <th v-if="taxOn">S.S.T</th> -->
                          <th>Amount</th>

                      </tr>

                  </thead>

                  <thead class="pHead">
                      
                      <tr>

                          <th colspan="3"></th>
                          <th class="tdRight">{{ totalColoumn('qty') | roundIt }} Ltr.</th>
                          <th></th>
                          <th class="tdRight">{{ totalColoumn('oilQty') | removeDec }} Ltr.</th>
                          <th>{{ totalColoumn('oilActualAmount') | roundIt | currency }}</th>
                          <th class="tdRight">{{ totalColoumn('actualAmount') | roundIt | currency }}</th>

                      </tr>

                  </thead>

                </table>

                <br>
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

                </c-row>


            </c-form-field>

          </c-panel>


              <!-- ############################################################# -->
              <!-- START REPORT BY EQUIPMENT -->
              <!-- ############################################################# -->


          <c-panel v-if="canIrun('byEquipment')" class="pTitle" :title="pageTitle" >

               <c-level>
                <template  slot="left" class="phidden">
                  <div class="u-fs-10 pSupplierName phidden">{{ fuel.selectedSupplier.supplierName }}</div>
                </template>

                <c-button-group slot="center" class="u-h3 pCompanyName">
                  {{ selectedEquipmentSupplier[0].fullName }}
                </c-button-group>

                <template slot="right" class="phidden">
                  <div class="phidden">
                    {{ fuel.selectedBill.startingDate }} to {{ fuel.selectedBill.endingDate }}
                  </div>
                </template>
              </c-level>

              <c-level class="u-hidden">

                <template  slot="left">
                  <div class="u-fs-10 pReferenceNumber"></div>
                </template>                
                
                <c-button-group slot="center" class="u-h3 pSupplierName">
                  {{ selectedSupplier.supplierName }}
                </c-button-group>

                <template slot="right">
                  <div class="pDate">
                    {{ fuel.selectedBill.startingDate }} to {{ fuel.selectedBill.endingDate }} - {{ groupBy.label }}
                  </div>
                </template>

                
              </c-level>

              <br>
                  
             <c-form-field >
            
              <div v-for="(field, index) in newOrderByType">

              <h4 class="typeHead">{{ field.equipmentType }}</h4>

              <table class="table table--striped table--hover pTable">

                <thead class="pHead" v-if="!headerOn">
                      
                      <tr>

                          
                          <th width="14%" class="tdCenter">Date</th>
                          <th width="13%" class="tdCenter">Equipment</th>
                          <th width="11%" class="tdCenter">Slip No.</th>
                          <th width="12%" class="tdCenter">Fuel</th>
                          <th width="12%" class="tdCenter">Rate</th>
                          <th width="12%" class="tdCenter">Oil x Rate</th>
                          <th width="12%" class="tdCenter">Oil Total</th>
                          <th width="13%" class="tdCenter">Amount</th>

                      </tr>

                  </thead>

              </table>


              <div  v-for="equip in getEquipmentsByType(field.equipmentType)">

                <table class="table table--striped table--hover pTable">

                     <tbody class="pBody">

                      <tr v-if="list.isApproved == 1" v-for="(list, index) in getByEquipment(equip.equipment)">
                        
                        <td width="14%" class="tdLeft">{{ list.expenseDate }}</td>
                        <td width="13%" class="tdLeft ">{{ list.equipment }}</td>
                        <td width="11%" class="tdCenter">{{ list.slipNumber }}</td>
                        <td width="12%" class="tdRight">{{ list.qty }}</td>
                        <td width="12%"  class="tdRight">{{ list.rate }}</td>
                        
                        <td width="12%"  class="tdOilQty" v-if="list.oilQty != undefined && list.oilRate != undefined">
                        {{ list.oilQty | removeDec }} x {{ list.oilRate | removeDec }}</td>
                        <td width="12%"  class="tdOilQty" v-else=""></td>

                        <td  width="13%" class="tdRight">{{ list.oilActualAmount | roundIt | currency }}</td>
                        <td  width="13%"  class="tdAmount">{{ list.actualAmount | roundIt | currency }}</td>
                        
                      </tr>

                    </tbody>

                    <!-- ###############################
                    DISPLAY ONLY IF HEADER IS ON
                    #################################### -->

                    <thead class="pHead" v-if="headerOn">
                        
                        <tr>

                            
                            <th>Date</th>
                            <th>Equipment</th>
                            <th>Slip No.</th>
                            <th>Fuel</th>
                            <th>Rate</th>
                            <th>Oil x Rate</th>
                            <th>Oil Total</th>
                            <th>Amount</th>

                        </tr>

                    </thead>


                    <thead class="pHead" v-if="headerOn">
                        
                        <tr>

                            <th colspan="3"></th>
                            <th class="tdRight">{{ totalColoumnByEquipment('qty', equip.equipment) | roundIt }} Ltr.</th>
                            <th></th>
                            <th class="tdRight">{{ totalColoumnByEquipment('oilQty', equip.equipment) | removeDec }} Ltr.</th>
                            <th>{{ totalColoumnByEquipment('oilActualAmount', equip.equipment) | roundIt | currency }}</th>
                            <th class="tdRight">{{ totalColoumnByEquipment('actualAmount', equip.equipment) | roundIt | currency }}</th>

                        </tr>

                    </thead>

                    <!-- ###############################
                    SUM OF EACH TYPE DISPLAY ONLY IF HEADER IS ON 
                    #################################### -->

                    

                  </table>

                </div>


                <!-- ###############################
                  SUM OF EACH TYPE DISPLAY ONLY IF HEADER IS OFF 
                #################################### -->
                <table class="table table--striped table--hover pTable" v-if="!headerOn">

                  <thead class="pHead">
                        
                        <tr>

                            <th width="38%"></th>
                            
                            <th width="12%" class="tdRight">
                            {{ totalByTypes('qty', field.id) | roundIt }} Ltr.</th>
                            
                            <th width="12%"></th>

                            <th width="12%" class="tdRight">
                            {{ totalByTypes('oilQty', field.id) | removeDec }} Ltr.</th>

                            <th width="13%">
                            {{ totalByTypes('oilActualAmount', field.id) | roundIt | currency }}</th>

                            <th width="13%" class="tdRight">
                            {{ totalByTypes('actualAmount', field.id) | roundIt | currency }}</th>

                        </tr>

                    </thead>

                  </table>
                  <!-- ###############################
                  SUM OF EACH TYPE DISPLAY ONLY IF HEADER IS OFF 
                #################################### -->

              </div>

              <!-- ###############################
                  GRAND TOTAL OF SELECTED BILL GROUP BY SELECTED EQUIPMENT SUPPLIER
                #################################### -->
                <div v-if="showMe()">
                <h4 class="typeHead">GRAND TOTAL</h4>
                <table class="table table--striped table--hover pTable" v-if="!headerOn">

                  <thead class="pHead">
                        
                        <tr>

                            <th width="38%"></th>
                            <th width="12%" class="tdRight">
                            {{ totalColoumn('qty') | roundIt }} Ltr.</th>
                            <th width="12%"></th>
                            <th width="12%" class="tdRight">
                            {{ totalColoumn('oilQty') | removeDec }} Ltr.</th>
                            <th width="13%">
                            {{ totalColoumn('oilActualAmount') | roundIt | currency }}</th>
                            <th width="13%" class="tdRight">
                            {{ totalColoumn('actualAmount') | roundIt | currency }}</th>

                        </tr>

                    </thead>

                  </table>

                  <!-- ###############################
                  GRAND TOTAL OF SELECTED BILL GROUP BY SELECTED EQUIPMENT SUPPLIER
                #################################### -->

                <br>
                <br>
                </div>


                <c-row justify="center">

                  <c-col md="16" align="center">
        
                  <table class="table table--striped table--hover pSummaryTable">

                     <tbody v-for="(sub, i) in selectedEquipmentSupplier" class="pBody">

                      <tr v-for="(list, index) in newOrderByType" v-if="typeExistsinEqSupplier(list.id, sub.id)">
                       
                        <td class="tdLeft">{{ sub.label }} {{ list.equipmentType }}</td>
                        <td class="tdRight">{{ totalByTypesGroupSup('qty', list.id, sub.id) | roundIt }} Ltr.</td>
                        <td class="tdRight">{{ totalByTypesGroupSup('oilQty', list.id, sub.id) | removeDec  }} Ltr.</td>
                        <td class="tdRight">Rs.{{ totalByTypesGroupSup('oilActualAmount', list.id, sub.id) | roundIt | currency }}/-</td>
                        <td class="tdRight">Rs.{{ subTotalTypeGroupSub(list.id, sub.id, index, i) | makeInt | currency }}/- </td>

                      </tr>

                    </tbody>

                    <tbody>
                       <tr v-if="showMe()" class="subTotal">
                        <td colspan="4" class="tdRight">Sub Total (Inc. Decimal)</td>
                        <td class="tdRight">Rs.{{ totalColoumn('actualAmount') | roundIt | currency }}/-</td>
                      </tr>

                      <tr v-if="showMe()">
                        <td colspan="4" class="tdRight">Decimal Adjustment</td>
                        <td class="tdRight">Rs.{{ decimalAdjustment }}/-</td>
                      </tr>
                    </tbody>

                    <thead class="pHead">
                        
                        <tr >

                            <th>Types</th>
                            <th>Diesel</th>
                            <th>Oil</th>
                            <th>Oil Total</th>
                            <th>Amount</th>

                        </tr>

                    </thead>

                    <thead class="pHead" v-if="showMe()">
                      
                      <tr class="u-color-grey-lightest">
                        
                        <th class="u-bg-grey-light u-text-right tdLeft">Grand Total</th>
                        <th class="u-bg-grey-light tdRight">{{ grandTotal('qty') | roundIt }} Ltr.</th>
                        <th class="u-bg-grey-light tdRight">{{ grandTotal('oilQty') | removeDec }} Ltr.</th>
                        <th class="u-bg-grey-light tdRight">Rs.{{ grandTotal('oilActualAmount') | roundIt | currency }}/-</th>
                        <th class="u-bg-grey-light u-color-grey-lightest">Rs.{{ doAdjustment(grandTotal('actualAmount'), decimalAdjustment ) | currency }}/-</th>

                      </tr>

                    </thead>
                    
                  </table>

                  </c-col>

                </c-row>


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

        headerOn: false,
        taxOn: false,
        taxRate: 25.5,
        expense: [],
        values: [],

        selectedSupplier: undefined,
        selectedBill: undefined,
        supplier: [],
        equipmentSuppliers: [],
        selectedEquipmentSupplier: undefined,
        bills: [],
        showPaid: false,
        filteredBills: [],
        groupOptions: [ 
            { label: 'By Supplier', name: 'bySupplier' }, 
            { label: 'By Equipment', name: 'byEquipment' }
            ],

        groupBy: undefined,
        orderByType: undefined,
        orderedBy: [],
        newOrderByType: [],
        decimalValues: [],
        decimalAdjustment: 0,
        showOptionalValues: false,
        
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

        // const { Printd } = window.printd
          this.d = new Printd()

          // Print dialog events (v0.0.9+)
          const { contentWindow } = this.d.getIFrame()
          // contentWindow.addEventListener(
          //   'beforeprint', () => 
          // )
          // contentWindow.addEventListener(
          //   'afterprint', () => 
          // )      

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


        canIrun(type)
        {

          if(this.groupBy != undefined && this.groupBy == type && this.selectedBill != undefined){
            return true;
          }

          return false;

        },

        showMe()
        {

          if(this.getTypes.length == this.newOrderByType.length)
          {
            return true;
          }
          return false;

        },

        getData(){

          this.getEquipmentSupplier();
          this.getSupplier();
          this.getSupplierBill();
          

        },

        getEquipmentSupplier(){

          axios
            .get('/supplier')

              .then(response => {

                this.equipmentSuppliers = response.data.data;

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

        getFuel()
        {

          this.loading('Fetching data...');

          axios
            .get('/expense/getApprovedFuelBill',{

              params: {

                bill_id : this.selectedBill.id

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

        getSupplierBill(){

          axios
            .get('/getBills/multistatus',{

                  params: {

                    status : [14, 15]

                  }

                })

              .then(response => {

                this.bills = response.data.data;

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

          var allBySelectedSupplier = [];

          if(this.selectedEquipmentSupplier != undefined){

            for (let index in this.selectedEquipmentSupplier)
            {

                 var selectedEquipmentSupplier = this.selectedEquipmentSupplier[index].id;
            
                 var arr = _.orderBy(this.expense.filter(function(elem){
                      if(elem.status == 1 && elem.isApproved == 1 && elem.equipmentSupplier.id == selectedEquipmentSupplier
                        ) return elem;
                  }), 'expenseDate');

                 for (let i in arr)
                 {

                  allBySelectedSupplier.push(arr[i]);

                 }

            }

            


         }

          let ascDesc = 1;
          var sortedApha = allBySelectedSupplier.sort((a, b) => ascDesc * a['expenseDate'].localeCompare(b['expenseDate']));

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

        groupBy()
        {
          this.decimalValues = [];
        },

        selectedEquipmentSupplier(){

            this.orderedBy = [];
            this.newOrderByType = [];

        },

        selectedSupplier(){

            this.selectedBill = undefined;
            this.orderedBy = [];
            this.newOrderByType = [];
            this.groupBy = undefined;

            this.fuel.selectedSupplier = this.selectedSupplier;
            this.pageTitle = 'Supplier Fuel Report > <strong>' + this.selectedSupplier.label + '</strong>';

            var supplier = this.selectedSupplier;
            
            this.fuel.selectedBill = undefined;

            if(supplier != undefined){
              var filteredBills = this.bills.filter(function(elem){
                  if(elem.expense_supplier_id == supplier.id && elem.status == 'Outstanding') return elem;
              });

              this.filteredBills = filteredBills;

              } else {

                return [];

              }

        },

        showPaid()
        {

              var bills = this.bills;
              var supplier = this.selectedSupplier;
              if(this.showPaid == true)
              {
                  var filteredBills = bills.filter(function(elem){
                    if(elem.expense_supplier_id == supplier.id && elem.status == 'Paid') return elem;
                  });

              } 
              else
              {
                  var filteredBills = bills.filter(function(elem){
                    if(elem.expense_supplier_id == supplier.id && elem.status == 'Outstanding') return elem;
                  });                      
              } 

              this.filteredBills = filteredBills;

        },

        orderedBy(){

          for (let i in this.orderedBy)
          {

            if(this.orderedBy[i] && this.orderedBy[i] == true){
              
              if(this.newOrderByType.map(item => item.id).indexOf(this.getTypes[i].id)===-1){
                this.newOrderByType.push(this.getTypes[i]);
              }
            } 
            else  if(!this.orderedBy[i])
            {
              var index = this.newOrderByType.map(item => item.id).indexOf(this.getTypes[i].id)
              if(index != -1){
                this.newOrderByType.splice(index, 1);
              }              
            }


          }

        },

        selectedBill(){ 

          this.fuel.selectedBill = this.selectedBill;
          this.decimalValues = [];
          this.expense = [];
          this.orderedBy = [];
          this.newOrderByType = [];

          if(this.selectedBill != undefined)
          {

            this.pageTitle = 'Supplier Fuel Report > ' + this.selectedSupplier.label + ' > <strong> Bill Number: ' + this.fuel.selectedBill.id + '</strong><br> Supplier Reference Number: ' + this.selectedBill.referenceNumber;

          }

          if(this.selectedBill != undefined && this.selectedBill.status != 4)
          {

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