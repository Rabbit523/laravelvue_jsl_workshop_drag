	<template>
		<table class="table table--striped table--bordered table--hover table--responsive">
		  <thead>
		  	<slot name="heading"></slot>
		  </thead>
		  <tbody>
		  	
		  	<slot name="data"></slot>
		  </tbody>
		  <slot name="pagination"></slot>
		</table>
	</template>

<script type="text/javascript">

	export default {
		data () {
			return {
				
			}
		}
	}
</script>