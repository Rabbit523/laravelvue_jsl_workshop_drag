<template>
    

        <c-app  :sider-open="$root.siderOpen" v-if="$auth.ready()">

            <c-sider v-if="$auth.check()">
                <c-brand class="u-bg-primary" slot="start">

                  <img src="http://www.airproducts.com/~/media/Images/inline/Company/Gasin-logo-PNG.png?la=en" width="100" >
                    
                </c-brand>

                 <c-menu :activeIndex="path" @select="onSelect" theme="dark">
                    <c-menu-heading label="General" />
                        <c-menu-item index="home" icon="icon-grid" label="Dashboard" />

                        <c-submenu icon="icon-quill2" label="Users">
                            <c-menu-item index="getusers/all" label="All"></c-menu-item>
                            <c-menu-item index="getusers/addnew" label="Add New"></c-menu-item>
                        </c-submenu>

                        <c-submenu icon="icon-quill2" label="Clients">
                            <c-menu-item index="clients/all" label="All"></c-menu-item>
                            <c-menu-item index="clients/addnew" label="Add New"></c-menu-item>
                        </c-submenu>

                        <!-- 

                        <c-submenu icon="icon-quill2" label="Staff">

                            <c-menu-item index="staff/all" label="All"></c-menu-item>
                            <c-menu-item index="staff/addnew" label="Add New"></c-menu-item>

                        </c-submenu> -->

                        <c-submenu icon="icon-quill2" label="Locations">

                            <c-menu-item index="city/all" label="City > View All"></c-menu-item>
                            <c-menu-item index="city/addnew" label="City > Add New"></c-menu-item>

                            <c-menu-divider />

                            <c-menu-item index="area/all" label="Area > View All"></c-menu-item>
                            <c-menu-item index="area/addnew" label="Area > Add New"></c-menu-item>

                        </c-submenu>

                         <!-- <c-submenu icon="icon-quill2" label="Equipments">

                            <c-menu-item index="supplier/all" label="Supplier > View All"></c-menu-item>
                            <c-menu-item index="supplier/addnew" label="Supplier > Add New"></c-menu-item>

                            <c-menu-divider />

                            <c-menu-item index="variation/all" label="Variation > View All"></c-menu-item>
                            <c-menu-item index="variation/addnew" label="Variation > Add New"></c-menu-item>

                            <c-menu-divider />

                            <c-menu-item index="equipmenttype/all" label="Type > View All"></c-menu-item>
                            <c-menu-item index="equipmenttype/addnew" label="Type > Add New"></c-menu-item>

                            <c-menu-divider />

                            <c-menu-item index="equipment/all" label="Equipments > View All"></c-menu-item>
                            <c-menu-item index="equipment/addnew" label="Equipments > Add New"></c-menu-item>

                        </c-submenu> -->

                        

                         <c-submenu icon="icon-quill2" label="Bookings">

<!--                             <c-menu-item index="bookings/all" label="All"></c-menu-item> -->
                            <c-menu-item index="booking/addnew" label="Add New"></c-menu-item>

                        </c-submenu>


                        <c-submenu v-if="checkthis()" icon="icon-quill2" label="Account">

                            <c-menu-item @click.prevent="logout()" index="tripmanagement/all" label="Logout"></c-menu-item>

                        </c-submenu>

                         

                </c-menu>

            </c-sider>

            <c-content>

                <c-header v-if="$auth.check()">

                    <nav class="navbar navbar--light u-bg-primary">
                      <div class="navbar__start">
                        <a role="button" class="navbar__item u-hidden-up@lg" @click="toggleSider">
                            <i class="i-menu i-menu--light"></i>
                        </a>
                        <a class="navbar__item">Admin</a>
                        <a @click="onSelect('dispatch/all')" class="navbar__item">Workshop</a>
                        <a class="navbar__item">Expense</a>
                        <a class="navbar__item">Management</a>
                      </div>
                      <div class="navbar__end">
                        <div class="navbar__item">
                          ...
                        </div>
                      </div>
                    </nav>
                    
                    

                </c-header>


                <c-body>

                    <c-main>
                        <offline @detected-condition="handleConnectivityChange"></offline>
                        <div v-if="!$root.onlineStatus" >
                          <c-flash @detected-condition="handleConnectivityChange" type="danger">Check your internet before saving data!</c-flash>
                        </div>
                       <router-view></router-view>
                    </c-main>
                    
                </c-body>

                <c-footer>
                    


                </c-footer>

            </c-content>
        </c-app>
    
</template>

<script type="text/javascript">
  
  export default {
    data () {
      return {
        name: "MMS Services",
        path: "Dashbord",
        isLogOut: false,
        waiting: 0,
        flagged: 0,
        deleted: 0,
        tested: 999,
      }
    },
    created() {

      // this.waitingapproval();

    },
    mounted() {

        // this.$root.$on('UpdateWaiting', this.waitingapproval)

    },
    methods: {
      onSelect(index) {
        this.$router.push({
            name: index
        })
      },
      toggleSider() {
        this.$root.siderOpen = !this.$root.siderOpen;
      },
      checkthis(){
        if(this.$auth){
            return true;
            this.toggleSider();
        }
      },
      logout(){
        
        var app = this
        this.$auth.logout({
            params: {
              
            }, 
            success: function () {
                this.isLogOut = true;
            },
            error: function () {},
            redirect: '/login',
            
        });  
      },

      // updateWaiting: function (value){
      //   var v = 1;
      //   if(value == '-1'){
      //      v = -1;
      //   }
      //   this.waiting = this.waiting + v;
      // },

      // waitingapproval(){

      //     axios

      //       .get('/expense/getExpenseCount')

      //       .then(response => {
              
      //         // this.dispatch.expense = response.data;
      //         this.waiting = response.data.Waiting;
      //         this.flagged = response.data.Flagged;
      //         this.deleted = response.data.Deleted;

      //       })

      //       .catch(error => {


      //     });

      // },

        handleConnectivityChange(status) {
            console.log(status);
            this.$root.onlineStatus = status;
        },

    }

  }
</script>