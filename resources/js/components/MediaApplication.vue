<template>
  <a
    v-if="media.mime_type === 'application/pdf'"
    :href="media.url"
    target="_blank"
    :title="!error ? generateName : 'undefined-filename'"
  >
    <i v-if="error" class="fal fa-file-pdf" />
    <img
      v-else
      :src="media[displayStyle + '_url']"
      :alt="generateName"
      :width="displayStyle === 'list' ? '30px' : '100%'"
      @error="replaceByDefault"
    />
  </a>
  <a v-else :href="media.url" target="_blank">
    <i class="fal fa-file-alt fa-3x" />
  </a>
</template>

<script>
import MediaElement from '../mixins/MediaElement';

export default {
  name: 'Application',
  extends: MediaElement,
  computed: {
    generateName() {
      return this.media.name + ' (' + this.media.size + ')';
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
