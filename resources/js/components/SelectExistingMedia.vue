<template>
  <div>
    <a
      class="button -hollow"
      @click="show = true"
    >
      {{ $t('media.words.select_existing') }}
    </a>
    <transition name="fade">
      <div
        v-if="show"
        class="modal-wrapper"
        @click="show = false"
      >
        <div
          class="modal"
          @click.stop
        >
          <h2>{{ $t('media.table.headings.select_existing') }}</h2>
          <div
            class="close"
            @click="show = false"
          >
            <i class="fal fa-times" />
          </div>
          <div class="content">
            <media-list
              v-if="existingMedia.length > 0"
              :media="existingMedia"
              :model="model"
              :model-class="modelClass"
              :allow-deleting="false"
              :allow-uploading="false"
              :allow-updating-category="false"
              @click="onMediaSelected"
            />
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import MediaList from './MediaList.vue';

export default {
  components: {MediaList},
  props: {
    model: {
      type: Object,
      required: true,
    },
    modelClass: {
      type: String,
      required: true,
    },
    mediaEndpoint: {
      type: String,
      required: true,
    },
    attachMediaEndpoint: {
      type: String,
      default: '/frontend/admin/media/attach',
    },
  },
  data() {
    return {
      show: false,
      existingMedia: [],
    }
  },
  watch: {
    show(newValue, oldValue) {
      if (newValue && !oldValue) {
        this.loadMedia();
      }
    },
  },
  methods: {
    loadMedia() {
      axios.get(this.mediaEndpoint)
        .then(({ data }) => {
          // console.log(data);
          if (data.length) {
            this.existingMedia = data;
          }
        })
        .catch(console.error);
    },
    onMediaSelected(media) {
      axios
        .post(this.attachMediaEndpoint, {
          modelId: this.model.id,
          modelClass: this.modelClass,
          media: media.id,
        })
        .then(() => {
          // If succeeded, emit event.
          this.$emit('media-selected', media);
        })
        .catch(console.error);
    },
  },
}
</script>
