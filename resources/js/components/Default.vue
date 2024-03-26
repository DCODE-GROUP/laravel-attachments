<template>
  <div>
    <p class="text-warning">
      No preview was found for this file type {{ media.mime_type }}
    </p>
    {{ media }}
  </div>
</template>

<script>
import MediaElement from "../mixins/MediaElement";

export default {
  name: "Default",
  extends: MediaElement,
};
</script>

<style scoped></style>
