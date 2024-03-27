<template>
  <a
    v-if="media.mime_type === 'application/pdf'"
    :href="media.url"
    target="_blank"
    :title="!error ? generateName : 'undefined-filename'"
  >
    <arrow-path-icon v-if="error" class="h-6 w-6 animate-spin" />
    <img
      v-else
      :src="media[displayStyle + '_url']"
      :alt="generateName"
      :width="displayStyle === 'list' ? '30px' : '100%'"
      @error="replaceByDefault"
    />
  </a>
  <a v-else :href="media.url" target="_blank">
    <rectangle-group-icon class="h-6 w-6" />
  </a>
</template>

<script>
import { RectangleGroupIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import MediaElement from "../mixins/MediaElement.js";

export default {
  name: "Application",
  extends: MediaElement,
  components: {
    RectangleGroupIcon,
    ArrowPathIcon,
  },
  computed: {
    generateName() {
      return this.media.name + " (" + this.media.size + ")";
    },
  },
};
</script>

<style scoped>
a {
  display: block;
  text-align: center;
}
</style>
