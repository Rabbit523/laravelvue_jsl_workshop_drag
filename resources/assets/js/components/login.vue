<template>
	<div>
		 
		<c-row gutter="100">
		  
		  <c-col>
		  	<section class="sign">
			  <div class="sign__body">
			    <h1 class="sign__title">Cover</h1>
			    <p class="sign__text">Sign in with your administrator account</p>
			    
			    <c-form  @submit.prevent="login" method="post">

			      <div v-if="!codeSent">
			      <c-form-field label="Email">
			        <c-form-input v-validate="'required|email'" name="email" v-model="email" type="email" required /></c-form-input>
			      </c-form-field>
			      <c-form-field label="Password">
			        <c-form-input v-validate="'required'" name="password" v-model="password" type="password"></c-form-input>
			      </c-form-field>
			  	 </div>
			  	 <div v-if="codeSent">
			  	 	<c-form-field label="Pin Number">
			        <c-form-input v-validate="'required'" v-model="pinCode" type="text"></c-form-input>
			      </c-form-field>
			  	 </div>
			      <c-form-field>
			        <c-level>
			          <c-form-checkbox label="Remember me" slot="left"></c-form-checkbox>
			          <a slot="right">Forgot Password?</a>
			        </c-level>
			      </c-form-field>
			      <c-button type="primary" block>Login In</c-button>
			      <span class="form-help u-color-danger" v-if="error" v-text="error"></span>
			    </c-form>
			    <!-- <c-divider>OR</c-divider>
			    <div class="u-text-center">
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-facebook2"></i></a>
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-twitter"></i></a>
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-github"></i></a>
			    </div> -->
			  </div>
			</section>
		  </c-col>
		 
		</c-row>
		
	</div>
</template>

<script>
  export default {

    data(){

      return {

        email: null,
        password: null,
        error: false,
        isLogOut: true,
        codeSent: false,
        pinCode: undefined,

      }

    },

    methods: {

      login(){

      	this.error = false;
        var app = this

        // this.$auth.login({
        //     params: {
        //       email: app.email,
        //       password: app.password
        //     }, 
        //     success: function () {
        //     	this.isLogOut = false;
        //     },
        //     error: function () {
        //     	this.error = 'Please check your user name or password and try again!'
        //     },
        //     rememberMe: true,
        //     redirect: '/',
        //     fetchUser: true,
        // });       

        this.$auth.login( {

            params: {
              email: app.email,
              password: app.password,
              pinCode: app.pinCode,
            }

        }).then(response =>{ 

          	
            this.isLogOut = false;
            // rememberMe: true,
            // redirect = '/',
            // fetchUser: true,

        })

        .catch(error => {

        	if(error.response.data.status == 'pinCode'){
        		this.codeSent = true;
        	}
        	this.error = error.response.data.msg;

        });

      },
    }
  } 
</script>