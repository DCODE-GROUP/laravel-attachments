<template>
  <span ref="icon" :class="display" v-html="svg" />
</template>

<script>
export default {
  name: "Icon",
  props: {
    name: {
      type: String,
      required: true,
    },
    display: {
      type: String,
    },
    strokeColor: {
      type: String,
      default: "",
    },
    width: {
      type: Number,
      default: 5,
    },
  },
  mounted() {
    const svg = this.$refs.icon.querySelector("svg");
    svg.style.width = `${this.width}px`;
    if (this.strokeColor) {
      const paths = this.$refs.icon.getElementsByTagName("path");

      Object.keys(paths).forEach(
        function (key) {
          paths[`${key}`].style.stroke = this.strokeColor;
        }.bind(this),
      );
    }
  },
  computed: {
    svg() {
      return "<svg></svg>";
    },
  },
};
</script>
