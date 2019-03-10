<template>
	<div>

		<c-panel title="Dispatch">

			  <c-form-field label="Select Equipment">

	        	 <c-multiselect
		            v-model="selectedEquipments"
		            track-by="value"
		            label="label"
		            :options="equipments"
		            name="equipments"
		            :searchable="true"
		            :close-on-select="true"
		            placeholder="Select Equipments"></c-multiselect>

	       		</c-form-field>

			 <c-form-field v-if="form.selectedEquipments != undefined" label="Trip Search By Date">
	          <c-flatpickr 
	            v-validate="'required'"
	            v-model="dateRange"
	            
	            name="dispatch starting date"
	            value="" range
	          />
	        </c-form-field> 

	      

			<tableheader>
				
				<h1 slot="pageheading" class="toolbar__title">Dashboard</h1> 

				<c-level-item slot="status">
			      <c-nav type="pills">
			      <c-nav-item @click="statusfilter = undefined, searchItem = ''" title="All" :active="statusfilter == undefined" />	
				  <c-nav-item @click="statusfilter = 'Active', searchItem = ''" title="Active" :active="statusfilter == 'Active'" />
				  <c-nav-item @click="statusfilter = 'InActive', searchItem = ''" title="InActive" :active="statusfilter == 'InActive'"  />
				</c-nav>
			    </c-level-item>

				<div slot="navright">
					<c-form-input placeholder="Search ..." v-model="searchItem"  @keyup.native="searchInTheList(searchItem), statusfilter = undefined" type="text" size="sm" flat icon-start="icon-search" />
				</div>

				<button :disabled="!checkSelect()" slot="newbutton" @click="createVoucher()" class="btn btn--info">
					<span class="i-text"><i class="icon-plus-circle"></i><span>Create Voucher</span></span>
				</button>

			</tableheader>
  				
		<c-row>
		  <c-col xs="24" sm="24" md="24" lg="24" xl="24" >

		  	<basictable>
		  		
		  			<th v-for="heading in headings" slot="heading" @click="invertSort(), selected = heading.name" >
		  				{{ heading.label }}
		  				<i v-if="currentSortDir == 1" :class="{ 'is-down':selected == heading.name }" class="i-sort"></i>
		  				<i v-if="currentSortDir == 0" :class="{ 'is-up':selected == heading.name }" class="i-sort"></i>
		  			</th>

		  		<tr slot="data" v-for="(item, index) in sortedItems">
		  			
		  			<td>
		  				<c-form-checkbox 
		                    :disabled="false" 
		                    
		                    :label="item.dispatchNo" 
		                    v-model="select[index]" />
					</td>
		  			<td>{{ item.referenceNo }}</td>
		  			<td>{{ item.clientName }}</td>
		  			<td>{{ item.statusName }}</td>
		  			<td><c-button-group>
						  <c-button :disabled="isDiabled" @click="editThis(item.id, item.equipment_id)" type="primary"><i class="icon-pencil"></i></c-button>
						  
						  <c-button :hidden="isHidden" :disabled="isDiabled" @click="killIt(item.id, index)" type="primary"><i class="icon-bin"></i></c-button>
						</c-button-group>
					</td>
		  		</tr>
				
		  	</basictable>
		  	
		  	<c-pagination :value="pagination.currentPage" @input="selectPage" :pager="true" :jumper="true" :total="total" />

		  </c-col>
		</c-row>

		</c-panel>
		
	</div>
</template>

<script type="text/javascript">

import {Form, Errors} from "./../common/base.js";

import axios from 'axios';


	export default {
		data () {

			return {

				form: new Form({
		          statusid: '',
		          status: '',
		          startingDate: undefined,
		          endingDate: undefined,
		          selectedEquipments: undefined,
		        }),
		        equipments: [],
				toast: '',
				dispatch: [],
				headings: [ 
						{ label: 'Dispatch No', name: 'dispatchNo' }, 
						{ label: 'Reference No', name: 'referenceNo' }, 
						{ label: 'Client', name: 'client' }, 
						{ label: 'Status', name: 'status' }, 
						{ label: 'Action', name: 'status' }
						],
				currentSort:'dispatchNo',
  				currentSortDir: true,
  				isdown: true,
  				isDiabled: false,
  				isHidden: true,
				selected: undefined,
				statusfilter: undefined,
        		total: undefined,
        		searchItem: '',
			    items: [],
			    filteredItems: [],
			    paginatedItems: [],
			    pagination: {
			      range: 5,
			      currentPage: 1,
			      itemPerPage: 10,
			      items: [],
			      filteredItems: [],
			    },

			    select: [],
			    selectedDispatch: [],
			}


		},
		mounted() {

		     
		},

		components: {

        	

    	},

    	created() {

    		this.getEquipments();

    	},

    	methods: {

    		editThis(dispatch_id, equipment_id){

      			this.$router.push('/tripmanagement/'+dispatch_id+'/'+equipment_id);

      		},
      		
      		createNew(){

  				this.$router.push('/staff/addnew');

  			},

  			getEquipments(){

	          this.equipments = [];

	          this.loading('show');

	          axios
	            .get('/equipment')

	              .then(response => {
	                
	                // Set all equipmets data from response
	                this.equipments = response.data.data;

	                this.loading();

	            }).catch(error => {

	                this.loading();

	                this.errortoast();

	            });

	        },

          ActiveorInActive: function (id, index){

          	this.loading('showtoast');

          	let i = this.items.map(item => item.id).indexOf(id);

          	this.form.statusid = id;
          	this.form.status = this.items[i].status;

          	this.handleSubmit('status', 'post', '/dispatch/status/', this.form.statusid, this.form.status, i, index);

          },

          killIt: function (id, index){

          	let i = this.items.map(item => item.id).indexOf(id);
          	let p = this.paginatedItems.map(item => item.id).indexOf(id);
          	
          	this.form.statusid = id;

          	this.handleSubmit('kill', 'get', '/dispatch/destroy/', this.form.statusid, this.form.status, i, index, p);

          },

          handleSubmit(action, method, url, id, status, i, index, p){

	          this.form.submit(method, url + id).then(success=>{

	          		if(success.status){


	          			if(action == 'kill'){

		          			this.items.splice(i, 1);
	          				this.sortedItems.splice(index, 1);
	          				this.paginatedItems.splice(p, 1);

							this.onSuccessKill(success.message);	              	
							

						} else {

							this.onSuccessStatus(status, i, index, success.message);	              	

						}

					} else {
						this.errortoast();
					}

	            }).catch(error=> {

	              this.loading();
	              this.errortoast();	              

	            });
	            
	      },

          invertSort() {

            this.currentSortDir = !this.currentSortDir;

          },

          onSuccessKill(message){

          	this.loading();

          	this.$toast.succeed(this.form.content = message, this.form.duration = 2000);

          },

          onSuccessStatus(status, i, index, message){

          	this.loading();


          	if(status == 'Active'){

          		status = 'InActive';

          	} else {

          		status = 'Active';
          	}

          	this.items[i].status = status;
  			this.sortedItems[index].status = status;

          	this.$toast.succeed(this.form.content = message, this.form.duration = 2000);

          },

          buildPagination(){

		      let numberOfPage = Math.ceil(this.filteredItems.length/this.pagination.itemPerPage)
		      this.pagination.items = []
		      for(var i=0; i<numberOfPage; i++){
		        this.pagination.items.push(i+1)
		      }

		  },

		  loading(show = null) {


		  	  this.isDiabled = true;

              const vm = this.toast;

              if(show != null){

                this.toast = this.$toast.loading('Loading...');

              } else {

                this.toast.hide();

                this.isDiabled = false;

              }

          },

          clearDates(){

          	alert('hit');

          },

          getSearchData(){

          		this.loading('show');

          		axios
        		.get('/searchDispatch',{

        			params: {

        				startDate : this.form.startingDate,
        				endDate: this.form.endingDate,
        				equipment_id: this.form.selectedEquipments.value,

        			}

        		})
        			.then(response => {
            		
            		this.items = response.data;
            		this.total = Math.ceil((this.items.length / this.pagination.itemPerPage), 0);

            		this.filteredItems = this.items;
		    		this.buildPagination();
		    		this.selectPage(1);   

            		this.loading();

        		}).catch(error => {

            		this.errortoast();

            		this.loading();

        		});

          },

          errortoast(){

              this.$toast.failed('Operation failed!', 2000)

          },

          searchInTheList(searchText, currentPage, statusfilter){


          	  if(_.isUndefined(searchText)){
		        this.filteredItems = _.filter(this.items, function(v, k){
		          return !v.selected
		        })
		      }
		      else{
		        this.filteredItems = _.filter(this.items, function(v, k){

		          var name = !v.selected && v.clientName.toLowerCase().indexOf(searchText.toLowerCase()) > -1;
		          var email = !v.selected && v.emailAddress.toLowerCase().indexOf(searchText.toLowerCase()) > -1;
		          var sts = !v.selected && v.status.toLowerCase().indexOf(searchText.toLowerCase()) > -1;

		          var finalResult =  name + email + sts;

		          return finalResult;

		        })

		      }

		      this.total = Math.ceil((this.filteredItems.length / this.pagination.itemPerPage), 0);
		      this.filteredItems.forEach(function(v, k){
		        v.key = k+1
		      })  

		      this.buildPagination()
		      
		      if(_.isUndefined(currentPage)){
		        this.selectPage(1) 
		      }
		      else{
		        this.selectPage(currentPage)
		      }

		    },

		    sort:function(s) {
    			
    			if(s === this.currentSort) {
     			 this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
    			}
    			
    			this.currentSort = s;
  			},

		    selectPage(item) {

			      this.pagination.currentPage = item
			      
			      let start = 0
			      let end = 0
			      
			      if(this.pagination.currentPage < this.pagination.range-2){
			        start = 1
			        end = start+this.pagination.range-1
			      }
			      else if(this.pagination.currentPage <= this.pagination.items.length && this.pagination.currentPage > this.pagination.items.length - this.pagination.range + 2){
			        start = this.pagination.items.length-this.pagination.range+1
			        end = this.pagination.items.length
			      }
			      else {
			        start = this.pagination.currentPage-2
			        end = this.pagination.currentPage+2
			      }
			      
			      
			      this.pagination.filteredItems = []
			      for(var i=start; i<=end; i++){
			        this.pagination.filteredItems.push(i);
			      }
			      
			      this.paginatedItems = this.filteredItems.filter((v, k) => {
			        return Math.ceil((k+1) / this.pagination.itemPerPage) == this.pagination.currentPage
			      })

		    },

		checkselectall(){

          var set = true
          var i = this.trip.tripsDetails.length;
          var u;

          for (u = 0; u < i; u++){
            if(this.select[u] == undefined){
              set = false;
            }
            if(this.select[u] != undefined && this.select[u] == false){
              set = false;
            }

          }

          this.selectall = set;

        },

        checkSelect(){

        	var setAny = false;
        	var i = this.sortedItems.length;
        	var u;

        	for(u = 0; u < i; u++ )
        	{
        		if(this.select[u] != undefined && this.select[u] == true)
        		{
        			setAny = true;
        			this.selectedDispatch[u] = this.sortedItems[u].id;
        		} else {
        			this.selectedDispatch[u] = undefined;
        		}
        	}

        	return setAny;

        },

        createVoucher(){

          	for (let i in this.selectedDispatch){
          		
          		if(this.selectedDispatch[i] == undefined){
          			this.selectedDispatch.splice(i, 1);
          		}
          		
          	}

          	this.$router.push('/createVoucher/' + this.selectedDispatch + '/' + this.form.selectedEquipments.id);

          },

    	},



    	watch:{

    		statusfilter: function(val){

    			if(val != ''){

    				this.searchInTheList(this.statusfilter, this.currentPage);

    			}

    		}

    	},
    	computed:{
		  	
		  	
		  sortedItems() {

            let result = this.paginatedItems;
            
            let ascDesc = this.currentSortDir ? 1 : -1;

            var newSort = result.sort((a, b) => ascDesc * a['dispatchNo'].localeCompare(b['dispatchNo']));

            for (let head in this.headings){

            	var heading = this.headings[head].name;

            	if(this.selected == heading){

            	newSort = result.sort((a, b) => ascDesc * a[heading].localeCompare(b[heading]));

	        	}
            }

            return newSort;

          },

          dateRange: {
            // getter
            get: function () {
              return this.form.dateRange;
            },
            // setter
            set: function (newValue) {

              // var names = newValue.split(' ')
              this.form.dateRange = newValue;

              var dates = newValue.split(' → ');
              
              if(dates.length > 1){
              	this.form.startingDate = dates[0];
              	this.form.endingDate = dates[1];
              	this.getSearchData();
              }

            }

          },

          selectedEquipments: {

          	get: function () {
              return this.form.selectedEquipments;
            },
          	
          	set: function (newValue){

          		this.form.selectedEquipments = newValue;
          		if(this.form.startingDate != undefined && this.form.endingDate != undefined){
          			this.getSearchData();
          		}
          	}

          },


		}
	}
</script>