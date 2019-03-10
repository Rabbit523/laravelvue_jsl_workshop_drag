<template>
	<div>
		 
		<c-row gutter="100">
		  
		  <c-col>
		  	<section class="sign">
			  <div class="sign__body">
			    <h1 class="sign__title">Cover</h1>
			    <p class="sign__text">Sign in with your administrator account</p>
			    <c-form  @submit.prevent="register">
			      <c-form-field label="Email">
			        <c-form-input v-model="email" type="email"></c-form-input>
			      </c-form-field>

			      <c-form-field label="name">
			        <c-form-input v-model="name" type="name"></c-form-input>
			      </c-form-field>

			      <c-form-field label="Password">
			        <c-form-input v-model="password" type="password"></c-form-input>
			      </c-form-field>
			      <c-form-field>
			        <c-level>
			          <c-form-checkbox label="Remember me" slot="left"></c-form-checkbox>
			          <a slot="right">Forgot Password?</a>
			        </c-level>
			      </c-form-field>
			      <c-button type="primary" block>Login In</c-button>
			    </c-form>
			    <c-divider>OR</c-divider>
			    <div class="u-text-center">
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-facebook2"></i></a>
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-twitter"></i></a>
			      <a href="#" class="u-color-muted u-ml-10 u-mr-10"><i class="icon-github"></i></a>
			    </div>
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
                name: '',
                email: '',
                password: '',
                error: false,
                success: false
            };
        },
        methods: {
            register(){
                var app = this
                this.$auth.register({
                    params: {
                        name: app.name,
                        email: app.email,
                        password: app.password
                    }, 
                    success: function () {
                        app.success = true
                    },
                    error: function (resp) {
                        app.error = true;
                        app.errors = resp.response.data.errors;
                    },
                    redirect: null
                });                
            }
        }
    }
</script>