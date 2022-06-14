<template>
  <refresh-icon v-if="error" class="h-6 w-6 animate-spin" />
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
import { RefreshIcon } from "@heroicons/vue/solid";
import MediaElement from "../mixins/MediaElement";

export default {
  name: "MediaImage",
  extends: MediaElement,
  components: {
    RefreshIcon,
  },
};
</script>
