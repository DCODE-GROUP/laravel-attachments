<template>
  <i
    v-if="error"
    class="fal fa-spinner fa-spin"
  />
  <img
    v-else
    ref="img"
    v-bind="$attrs"
    :src="media[displayStyle + '_url']"
    :alt="media.name"
    @error="replaceByDefault"
  >
</template>

<script>
import MediaElement from "../mixins/MediaElement";

export default {
  name: "MediaImage",
  extends: MediaElement,
};
</script>
