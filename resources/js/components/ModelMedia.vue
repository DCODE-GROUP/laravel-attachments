<template>
  <aside class="col-span-3">
    <template v-if="displayStyle === 'list'">
      <header class="mb-4">
        <h2>{{ headingText ? headingText : $t("media.words.title") }}</h2>
        <template v-if="false && addExistingEndpoint">
          <select-existing-media
            :model="model"
            :model-class="modelClass"
            :media-endpoint="addExistingEndpoint"
            @media-selected="addSelectedMedia"
          />
        </template>
      </header>
      <media-list
        :media="media"
        :category-options="fullCategoryOptions"
        :category-id="currentCategory"
        :model="model"
        :model-class="modelClass"
        :parent-model="parentModel"
        :parent-model-class="parentModelClass"
        :upload-endpoint="uploadEndpoint"
        :allow-deleting="permitDelete"
        :allow-uploading="permitUpload"
        :compact="compact"
        :allow-editing="permitEdit"
        :display-category="displayCategory"
        @media-uploaded="appendToMedia"
        @media-deleted="destroy"
      />
    </template>
  </aside>
</template>

<script>
import Upload from "./Upload.vue";
import MediaList from "./MediaList.vue";
import SelectExistingMedia from "./SelectExistingMedia.vue";
import MediaImage from "./MediaImage.vue";
import MediaVideo from "./MediaVideo.vue";
import MediaApplication from "./MediaApplication.vue";
import Default from "./Default.vue";

export default {
  name: "ModelMedia",

  components: {
    Upload,
    MediaList,
    SelectExistingMedia,
    MediaImage,
    MediaVideo,
    MediaApplication,
    Default,
  },

  props: {
    model: {
      type: Object,
      required: true,
    },
    modelClass: {
      type: String,
      required: true,
    },
    parentModel: {
      type: Object,
      default() {
        return {};
      },
      required: false,
    },
    parentModelClass: {
      type: String,
      default: "",
      required: false,
    },
    uploadEndpoint: {
      type: String,
      default: "/frontend/admin/media/upload",
    },
    // existingEndpoint: {
    //   type: String,
    //   default: "/frontend/admin/media/existing",
    // },
    addExistingEndpoint: {
      type: String,
      default: "/frontend/admin/media/existing",
    },
    displayStyle: {
      type: String,
      default: "list",
    },
    fullCategoryOptions: {
      type: Array,
      required: true,
    },
    permitDelete: {
      type: Boolean,
      default: true,
    },
    permitUpload: {
      type: Boolean,
      default: true,
    },
    permitEdit: {
      type: Boolean,
      default: false,
    },
    showOnly: {
      type: Boolean,
      default: false,
    },
    headingText: {
      type: String,
      default: "",
      required: false,
    },
    compact: {
      type: Boolean,
      default: false,
    },
    currentCategory: {
      type: [String, Number],
      required: true,
    },
    media: {
      type: Array,
      required: true,
    },
    displayCategory: {
      type: Boolean,
      default: true,
    },
    // categoriesEndpoint: {
    //   type: String,
    //   default: "/frontend/admin/category/options",
    // },
  },

  data: function () {
    return {
      files: [],
    };
  },

  methods: {
    appendToMedia(item) {
      // console.log("media", item);
      this.media.push(item);
    },

    addSelectedMedia(media) {
      // console.log("media", media);
      this.media.push(media);
    },

    destroy({ id: mediaId, index }) {
      if (confirm("Are you sure you want to remove this item")) {
        const media = this.media[index];
        const endpoint =
          media && media.hasOwnProperty("delete_endpoint")
            ? media.delete_endpoint
            : "/frontend/admin/media/delete/" + mediaId;

        axios
          .delete(endpoint)
          .then(({ data }) => {
            // delete the row
            this.media.splice(index, 1);
          })
          .catch((errors) => {
            console.error(errors);
          });
      }
    },

    getComponent(file) {
      if (_.startsWith(file.mime_type, "image")) {
        return "media-image";
      }

      if (_.startsWith(file.mime_type, "video")) {
        // return "video-" + this.displayStyle;
        return "media-video";
      }

      if (_.startsWith(file.mime_type, "application") || _.startsWith(file.mime_type, "text")) {
        return "media-application";
      }

      return "default";
    },

    formatBytes(bytes, decimals = 2) {
      if (bytes === 0) return "0 Bytes";

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
    },
  },
};
</script>

<style scoped></style>
