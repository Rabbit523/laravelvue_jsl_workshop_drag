<template>

  <div>


    <c-panel :title="pageTitle">

       <div>

        <c-form-field v-if="clients.length > 0" label="Select Equipment">

             <c-multiselect
                v-model="selectedClient"
                track-by="value"
                label="label"
                :options="clients"
                name="equipments"
                :searchable="true"
                :close-on-select="true"
                placeholder="Select Client"></c-multiselect>

        </c-form-field>

        <c-form-field v-if="equipments.length > 0" label="Select Equipment">

             <c-multiselect
                v-model="selectedEquipment"
                track-by="value"
                label="label"
                :options="equipments"
                name="equipments"
                :searchable="true"
                :close-on-select="false"
                :multiple="true"
                placeholder="Select Equipment"></c-multiselect>

        </c-form-field>

        <c-form-field label="Document Number">
          <c-form-input 
              v-validate="'min:2'" 
              v-model="docNumber" 
              name="document number" 
              value="" 
              type="text">
          </c-form-input>
          
        </c-form-field>
        

          <c-form-field label="Trip Search By Date">
            <c-flatpickr 
              v-validate="'required'"
              v-model="dateRange"
              
              name="dispatch starting date"
              value="" range
            />
          </c-form-field> 

          <c-form-field label="Columns" v-if="trips.length > 0">

            <template v-for="i in datafields">
              <c-tag class="u-mr-10 u-fs-12 u-p-15" checkable v-model="i.status" type="primary">{{ i.label }}</c-tag>
            </template>

          </c-form-field>

          <div class="u-text-left" slot="footer">
            <c-button v-if="trips.length > 0" type="primary" @click="print()" v-once smart>Print</c-button>
            <c-button type="primary" @click="getDispatchData()" v-once smart>Run</c-button>
          </div>




        </div>

      </c-panel>

    

      <div class="box" ref="print">

          <c-panel :title="pageTitle" >

              
              <!-- ############################################################# -->
              <!-- START REPORT  -->
              <!-- ############################################################# -->
                  
             <c-form-field >
            
                <table class="table table--striped table--hover pTable">

                     <tbody class="pBody">

                      <tr v-if="list.status == 1 && tripCreated == true" v-for="(list, index) in uniqueTrips"
                      :class="amIMissing(list.trips.flag)"
                      >
                        
                        <td v-if="datafields[0].status"  class="tdLeft">
                        {{ (index+1) }}</td>
                        <!-- <td>{{ list.id }}</td> -->
                        <td v-if="datafields[1].status"  class="tdCenter">
                        {{ list.tripStartDate | format }}</td>

                        <td v-if="datafields[2].status"  class="tdCenter">
                        {{ list.accountNumber }}</td>

                        <td v-if="datafields[3].status"  class="tdCenter">
                        {{ list.EquipmentData.equipmentNumber }}</td>
                        
                        <td v-if="datafields[4].status" class="tdCenter">
                        {{ list.EquipmentData.typeData.equipmentType }}</td>

                        <td v-if="datafields[5].status" class="tdLeft">
                        {{ getRoute(list.trip_id, 'first') }}</td>

                        <td v-if="datafields[6].status" class="tdLeft">
                        {{ getRoute(list.trip_id) }}</td>

                        <td v-if="datafields[7].status" class="tdCenter">
                        {{ list.consignmentNo }}</td>
                        
                        <td v-if="datafields[8].status" class="tdCenter">
                        {{ list.trips.billT }}</td>
                        
                        <td v-if="datafields[9].status" class="tdCenter">
                        {{ list.trips.billTReceiving | format }}</td>

                        <td v-if="datafields[10].status" class="tdCenter">
                        {{ list.invoiceNumber }}</td>

                        <td v-if="datafields[11].status" class="tdLeft">
                        {{ list.invoiceDate }}</td>
                        
                      </tr>

                    </tbody>

                    <thead class="pHead">
                        
                        <tr>

                            <th v-if="i.status" v-for="i in datafields">{{ i.label }}</th>
                            
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
import { Form, Errors } from "./../common/base.js";
import { Printd } from 'printd';
import numeral from 'numeral';

  export default {

    data() {
      
      return {

        clients: [],
        selectedClient: undefined,
        docNumber: undefined,
        equipments: [],
        selectedEquipment: undefined,

        date: undefined,
        dateRange: undefined,
        startingDate: undefined,
        endingDate: undefined,
        trips: [],
        uniqueTrips: [],
        tripRoute: [],
        tripCreated: false,

        filteredTrips: [],

        
        datafields: [ 
            { label: '#', status: true }, 
            { label: 'Date', status: true }, 
            { label: 'Client', status: true }, 
            { label: 'Equipment', status: true }, 
            { label: 'From', status: true }, 
            { label: 'To', status: true }, 
            { label: 'Type', status: true }, 
            { label: 'Shipment#', status: true }, 
            { label: 'Doc#', status: false }, 
            { label: 'Rec. Date', status: false },
            { label: 'Inv#', status: false },
            { label: 'Inv. Date', status: false },
            ],

        isLoading: false,
        isDisabled: false,
        isHidden: false,
        content: '',
        duration: '',

        user: this.$auth.user(),

        pageTitle: 'Dispatch Report',
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
              border-bottom: solid 1px #ccc;
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
              width: 80%;
              border-collapse: collapse;
            }

            .typeHead{
              text-align: left;
              padding-top: 10px;
              margin-bottom: 0px;
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

          // Print dialog events (v0.0.9+)
          const { contentWindow } = this.d.getIFrame()

    },

    methods: {

       print () {

          setTimeout(() => {
            this.d.print( this.$refs.print, this.cssText)
          }, 100);
            
        },


        canIrun(type)
        {

          if(this.groupBy != undefined && this.groupBy == type && this.selectedBill != undefined){
            return true;
          }

          return false;

        },

        getData(){

          this.getEquipmentsData();
          this.getClients();

        },

        getEquipmentsData()
        {

          const hasEquipments = this.$storage.has('equipments');

          if(hasEquipments)
          {
            var equipments = this.$storage.get('equipments');
            if(equipments != null)
            {
              this.equipments = equipments;
              return;  
            }
            
          }

          // this.loading('Fetching Equipment Data...');

          axios
          .get('/equipment')

              .then(response => {

                this.equipments = response.data.data;

                // this.loading();

                this.$storage.set('equipments', response.data.data, { ttl: 60 * 1000 });

            }).catch(error => {

                  // Hide Loading Toast
                // this.loading();

                this.errortoast();

            });


        },

        getClients()
        {

          const hasClients = this.$storage.has('clients');

          if(hasClients)
          {
            var client = this.$storage.get('clients');
            if(client != null){
              this.clients = client
              return;
            }
          }

          // this.loading('Fetching Clients Data...');

          axios
          .get('/clients')

              .then(response => {

                this.clients = response.data.data;

                // this.loading();

                this.$storage.set('clients', response.data.data, { ttl: 600 * 1000 });

            }).catch(error => {

                  // Hide Loading Toast
                // this.loading();

                this.errortoast();

            });

        },


        tripEquipment(trip_eq, index)
        {

          if(this.equipments && this.equipments.length > 0 && trip_eq){
         
            let i = this.equipments.filter(function(elem){
                if(elem.id == trip_eq.equipment_id) return elem;
            });

            this.uniqueTrips[index]['EquipmentData'] = i[0];

            return i[0];

          }

          return []
          

        },

        createTrips()
        {


          if(this.equipments && this.equipments.length > 0)
          {

            for (let i in this.uniqueTrips)
              {
                this.tripCreated = false;
                var equipment = this.tripEquipment(this.uniqueTrips[i].equipment_trip, i);

              }

              this.tripCreated = true;

          }

        },

        getRoute(trip_id, what)
        {

            let byTripID = this.trips.filter(function(elem){
                if(elem.trip_id == trip_id) return elem;
            });

            var Route = '';

            if(what == 'first'){
              return byTripID[0].area.areaName;
            }
            for (let i in byTripID)
            {

                if(i > 0){

                  Route += byTripID[i].area.areaName + ' ';
                  if(i < (byTripID.length-1)){
                    Route += ' to ';
                  }

                }

            }

            return Route;

        },

        getDispatchData()
        {

            this.loading('Fetching...');

            var selectedEquipment = [];
            for (let i in this.selectedEquipment)
            {
              selectedEquipment.push(this.selectedEquipment[i].id);
            }

            var selectedClient = null;
            if(this.selectedClient != undefined)
            {
              selectedClient = this.selectedClient.id;
            }

            axios
              .get('/searchTripByFields',{

                params: {

                  client:       selectedClient,
                  equipment:    selectedEquipment,
                  docNumber:    this.docNumber,
                  startingDate: this.startingDate,
                  endingDate:   this.endingDate

                }

              })

                .then(response => {

                  this.loading();

                  this.trips = response.data.data;
                  

              }).catch(error => {

                    // Hide Loading Toast
                  this.loading();

                  this.errortoast();

              });

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

          amIMissing(item)
          {

            if(item == 1){
             return 'u-color-grey-lightest u-bg-grey'; 
            } 

          }

          

      },

      computed: {


      },

      watch: {

        date()
        {

          this.pageTitle = 'Dispatch Report for ' + this.date;

          this.getDispatchData();

        },

        dateRange() {

            this.pageTitle = 'Dispatch Report for ' + this.dateRange;

            var dates = this.dateRange.split(' → ');
            
            if(dates.length > 1){

              this.startingDate  = dates[0];
              this.endingDate    = dates[1];
              this.getDispatchData();

          }

        },

        trips()
        {


            var uniqueArray=[];
            var tripRoute = [];
              this.trips.forEach(function(element){
                 if(uniqueArray.map(item => item.trip_id).indexOf(element.trip_id)===-1){
                     uniqueArray.push(element);
                 } else {
                    tripRoute.push(element);
                 }
             })

            
            let ascDesc = 1;

            // var sortedApha = uniqueArray.sort((a, b) => ascDesc * a['client_id'].localeCompare(b['client_id']));

            this.uniqueTrips  = uniqueArray;
            this.tripRoute    = tripRoute;
            this.createTrips();


        },

        clients()
        {

          if(this.clients == null){
            this.getClients();
          }

        },

        equipments()
        {


          if(this.equipments == null){
            this.tripCreated = false;
            this.getEquipmentsData();
          }

          this.createTrips();

        },

        docNumber()
        {

          var docNumber = this.docNumber;
          let i = this.uniqueTrips.filter(function(elem){
                if(elem.trips.billT == docNumber) return elem;
            });

          this.filteredTrips = i;

        },

      },

      filters: {

        format: function (date) {
          if(date != null){
            return format(date, 'DD-MM-YY');
          }
        },

      }

  }
</script>