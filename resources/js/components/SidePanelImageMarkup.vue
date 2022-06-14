<template>
  <div class="side-panel-image-markup">
    <v-image-markup :items="items" />
  </div>
</template>
<script>
import VImageMarkup from "./VImageMarkup.vue";
export default {
  components: { VImageMarkup },
  props: {
    items: Array,
  },
  data() {
    return {};
  },
  created() {},
  methods: {},
};
</script>
