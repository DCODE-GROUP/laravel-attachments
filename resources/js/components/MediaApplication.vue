<template>
  <a
    v-if="media.mime_type === 'application/pdf'"
    :href="media.url"
    target="_blank"
    :title="!error ? generateName : 'undefined-filename'"
  >
    <refresh-icon v-if="error" class="h-6 w-6 animate-spin" />
    <img
      v-else
      :src="media[displayStyle + '_url']"
      :alt="generateName"
      :width="displayStyle === 'list' ? '30px' : '100%'"
      @error="replaceByDefault"
    />
  </a>
  <a v-else :href="media.url" target="_blank">
    <template-icon class="h-6 w-6" />
  </a>
</template>

<script>
import { TemplateIcon, RefreshIcon } from "@heroicons/vue/solid";
import MediaElement from "../mixins/MediaElement";

export default {
  name: "Application",
  extends: MediaElement,
  components: {
    TemplateIcon,
    RefreshIcon,
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
