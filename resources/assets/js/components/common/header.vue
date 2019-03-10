<template>

			<c-level>
			  <template slot="left">

			  	<slot name="status"></slot>

			  </template>

			  <template slot="right">
			    <c-level-item>
			      <c-form-group>
			        <slot name="navright"></slot>
			       	<slot name="newbutton"></slot>
			      </c-form-group>
			    </c-level-item>
			  </template>
			</c-level>


	</template>

	<script type="text/javascript">

		export default {
			data () {
				return {
					
				}
			}
		}
	</script>