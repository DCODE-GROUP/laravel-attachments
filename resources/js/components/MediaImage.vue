<template>
  <arrow-path-icon v-if="error" class="h-6 w-6 animate-spin" />
  <img
    v-else
    ref="img"
    v-bind="$attrs"
    :src="media[displayStyle + '_url']"
    :alt="media.name"
    @error="replaceByDefault"
  />
</template>

<script>
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import MediaElement from "../mixins/MediaElement.js";

export default {
  name: "MediaImage",
  extends: MediaElement,
  components: {
    ArrowPathIcon,
  },
};
</script>
