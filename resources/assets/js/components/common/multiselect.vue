<template>
  <c-multiselect
    v-model="singleSelected"
    track-by="value"
    label="label"
    :options="colors"
    :searchable="false"
    :close-on-select="false"
    placeholder="Pick a color"></c-multiselect>
</template>

<script>
  import Multiselect from './../../../vendors/cover/vendors/multiselect'
  export default {
    components: {
      'c-multiselect': Multiselect
    },
    data() {
      return {
        singleSelected: '',
        colors: [
          { value: 'red', label: 'Red' },
          { value: 'blue', label: 'Blue' },
          // ...
        ]
      }
    }
  }
</script>