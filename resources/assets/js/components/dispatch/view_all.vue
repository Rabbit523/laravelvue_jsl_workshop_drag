<template>
	<div>

		<c-panel title="Dispatch">

			<!-- <input class="input" v-model="searchItem" v-on:keyup="searchInTheList(searchItem)" type="text" placeholder="Find a person"> -->

			<tableheader>
				
				<h1 slot="pageheading" class="toolbar__title">Dashboard</h1> 

				<c-level-item slot="status">
			      
			      <c-nav type="pills">
				      <c-nav-item @click="statusfilter = undefined, searchItem = ''" title="All" :active="statusfilter == undefined" />	
					  <c-nav-item @click="statusfilter = 'Active', searchItem = ''" title="Active" :active="statusfilter == 'Active'" />
					  <c-nav-item @click="statusfilter = 'InActive', searchItem = ''" title="InActive" :active="statusfilter == 'InActive'" />
				  </c-nav>

			    </c-level-item>

				<div slot="navright">
					<c-form-input placeholder="Search ..." v-model="searchItem"  @keyup.native="searchInTheList(searchItem), statusfilter = undefined" type="text" size="sm" flat icon-start="icon-search" />
				</div>

				<button slot="newbutton" @click="createNew" class="btn btn--info">
					<span class="i-text"><i class="icon-plus-circle"></i><span>New Post</span></span>
				</button>

			</tableheader>

			<c-form-field label="Select Client">

        	 <c-multiselect
	            v-model="selectedClient"
	            track-by="value"
	            label="label"
	            :options="clients"
	            name="client"
	            :searchable="true"
	            :close-on-select="true"
	            placeholder="Select Client"></c-multiselect>

       		</c-form-field>

       		<c-form-field label="Dispatch Search By Date">
	          <c-flatpickr 
	            v-model="dateR"
	            name="dispatch starting date"
	            value=""
	            :clearable="true"
	          />
	        </c-form-field> 
  				
		<c-row>
		  <c-col xs="24" sm="24" md="24" lg="24" xl="24" >

		  	<basictable>
		  		
		  			<th v-for="heading in headings" slot="heading" @click="invertSort(), selected = heading.name" >
		  				{{ heading.label }}
		  				<i v-if="currentSortDir == 1" :class="{ 'is-down':selected == heading.name }" class="i-sort"></i>
		  				<i v-if="currentSortDir == 0" :class="{ 'is-up':selected == heading.name }" class="i-sort"></i>
		  			</th>

		  		<tr slot="data" v-for="(item, index) in sortedItems">
		  			<td>{{ item.dispatchStartingDate }}</td>
		  			<td>{{ item.dispatchNo }}</td>
		  			<td>{{ item.referenceNo }}</td>
		  			<td>{{ item.clientName }}</td>
		  			<td>{{ item.status }}</td>
		  			<td><c-button-group>
						  <c-button :disabled="isDiabled" @click="editThis(item.value)" type="primary"><i class="icon-pencil"></i></c-button>
						  
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

	export default {
		data () {

			return {

				form: new Form({
		          statusid: '',
		          status: '',
		        }),
		        
		        dateR: undefined,
		        startingDate:undefined,
		        endingDate: undefined,

				toast: '',
				dispatch: [],
				headings: [ 
						{ label: 'Date', name: 'dispatchStartingDate' }, 
						{ label: 'Dispatch No', name: 'dispatchNo' }, 
						{ label: 'Refeence No', name: 'referenceNo' }, 
						{ label: 'Client Name', name: 'client' }, 
						{ label: 'Status', name: 'status' }, 
						{ label: 'Action', name: 'status' }
						],
				currentSort:'dispatchNo',
  				currentSortDir: true,
  				selectedClient: undefined,
  				clients: [],
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
			    }
			}


		},
		mounted() {

		     
		},
		
    	created() {

    		this.loading('show');

    		axios
        		.get('/dispatch')
        			.then(response => {
            		// this.dispatch = response.data.data;
            		this.items = response.data.data;
            		this.total = Math.ceil(this.items.length / this.pagination.itemPerPage);

            		this.filteredItems = this.items;
		    		this.buildPagination();
		    		this.selectPage(1);   

            		this.loading();

        		}).catch(error => {

            		callback(error, error.response.data);

            		this.loading();

        		});

        	axios

        		.get('/clients')

        			.then(response => {
            		// this.dispatch = response.data.data;
            		this.clients = response.data.data;
            		this.loading();

        		}).catch(error => {

            		callback(error, error.response.data);

            		this.loading();

        		});

    	},
    	methods: {

    		editThis(pageid){

      			this.$router.push('/dispatch/'+pageid+'/edit');

      		},
      		
      		createNew(){

  				this.$router.push('/staff/addnew');

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

		          var name 			= !v.selected && v.clientName.toLowerCase().indexOf(searchText.toLowerCase()) > -1;
		          var referenceNo 	= !v.selected && v.referenceNo.indexOf(searchText.toLowerCase()) > -1;
		          var dispatchNo 	= !v.selected && v.dispatchNo.toLowerCase().indexOf(searchText.toLowerCase()) > -1;	
		          var sts 			= !v.selected && v.status.toLowerCase().indexOf(searchText.toLowerCase()) > -1;

		          var finalResult =  name + referenceNo + sts + dispatchNo;

		          return finalResult;

		        })

		      }

		      this.total = Math.ceil(this.filteredItems.length / this.pagination.itemPerPage);
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

		    dispatchBySelecClient(){

		    	var selectedId = this.selectedClient.id;
		    	 var arr = this.paginatedItems.filter(function(elem){
                      if(elem.client_id == selectedId) return elem;
                  });

		    	 return arr;


		    },

		    dispatchByDate(){

		    	var arr = this.paginatedItems;
		    	if(this.selectedClient != undefined)
		    	{
		    		arr = this.dispatchBySelecClient();
		    	}

		    	var startingDate = this.dateR;
		    	//var endingDate = this.endingDate;

		    	 var filteredItems = this.paginatedItems.filter(function(elem){
                      if( elem.dispatchStartingDate == startingDate ) return elem;
                  });

		    	 return filteredItems;

		    }

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

            if(this.selectedClient != undefined)
            {
            	result = this.dispatchBySelecClient();
            }

            if(this.dateR != undefined && this.dateR != '')
            {
            	result = this.dispatchByDate();
            }

           	var newSort = result.sort((a, b) => ascDesc * a['dispatchStartingDate'].localeCompare( b['dispatchStartingDate'] ));

            for (let head in this.headings){

            	var heading = this.headings[head].name;

            	if(this.selected == heading){

            	newSort = result.sort((a, b) => ascDesc * a[heading].localeCompare( b[heading] ));

	        	}
            }

            return newSort;

          },

          dateRange: {

            // getter
            get: function () {

              return this.dateR;

            },
            // setter
            set: function (newValue) {

              // var names = newValue.split(' ')
              this.dateR = newValue;

              var dates = newValue.split(' → ');
              
              if(dates.length > 1){

              	this.startingDate = dates[0];
              	this.endingDate = dates[1];

              }

            }

          },

		}
	}
</script>