<template>
  <c-pagination class="u-mb-15" v-model="paged" :range="5" :pager="false" :total="50" />
</template>

<script>
  export default {
    data() {
      return {
        paged: 1,
        total: 50
      }
    }
  }
</script>