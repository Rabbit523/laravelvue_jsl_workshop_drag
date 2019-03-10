<template>
	<div>
		<c-row>
		  <c-col xs="24" sm="6" md="4" lg="3" xl="2">
		  	<!-- <c-panel title="Panel footer"> -->
					  <!-- place your body content here.

					  <div class="u-text-right" slot="footer">
					    <c-button type="ghost">Cancel</c-button>
					    <c-button type="primary" smart>Submit</c-button>
					  </div>

					</c-panel>
		  </c-col>
		  <c-col xs="24" sm="12" md="16" lg="18" xl="20"> 
		  	<c-panel>
			  <div class="u-mb-15">
			    <c-level>
			      <span slot="left">Google</span>
			      <span slot="right" class="u-color-muted">65</span>
			    </c-level>
			    <c-progress-bar type="#397AF2" value="65" />
			  </div>
			  <div class="u-mb-15">
			    <c-level>
			      <span slot="left">Facebook</span>
			      <span slot="right" class="u-color-muted">80</span>
			    </c-level>
			    <c-progress-bar type="#395CA9" value="80" />
			  </div>
			 
			</c-panel>
		  </c-col>
		  <c-col xs="24" sm="6" md="4" lg="3" xl="2">
		  	
		  	<c-panel>
				  <article class="cell cell--lg">
				    <figure class="cell__media">
				      <img src="" class="avatar avatar--lg" />
				    </figure>
				    <div class="cell__content">
				      <h4 class="u-m-0">John Doe</h4>
				      <div class="u-text-muted u-mb-10">Project Manager</div>
				      <c-nav type="gap">
				        <c-nav-item>
				          <i class="icon-twitter u-color-light-blue"></i>
				        </c-nav-item>

				      </c-nav>
				    </div>
				    <div class="cell__after">
				      <i class="icon-pencil u-text-muted"></i>
				    </div>
				  </article> -->
				<!-- </c-panel> -->

		  </c-col>
		</c-row>
	</div>
</template>

<script type="text/javascript">
  
  export default {
    data () {
      return {
        
      }
    },
    created() {

      this.$root.$emit('UpdateWaiting');

    },
    mounted() {

        // this.$root.$on('UpdateWaiting', this.waitingapproval)

    },

	}

    </script>