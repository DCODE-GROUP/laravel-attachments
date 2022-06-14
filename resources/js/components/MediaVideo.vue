<template>
  <div>
    <video :width="width" :height="height" controls>
      <source :src="media.url" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
  </div>
</template>

<script>
import MediaElement from "../mixins/MediaElement";

export default {
  name: "MediaVideo",
  extends: MediaElement,
  computed: {
    height() {
      return this.displayStyle === "list" ? 43 : 192;
    },
    width() {
      return this.displayStyle === "list" ? 43 : 160;
    },
  },
};
</script>

<style scoped></style>
