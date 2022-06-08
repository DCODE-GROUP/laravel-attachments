<template>
  <aside>
    <template v-if="displayStyle === 'list'">
      <header>
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
        :category-id="currentCategory.id"
        :model="model"
        :model-class="modelClass"
        :parent-model="parentModel"
        :parent-model-class="parentModelClass"
        :upload-endpoint="uploadEndpoint"
        :allow-deleting="permitDelete"
        :allow-uploading="permitUpload"
        :compact="compact"
        :allow-editing="permitEdit"
        @media-uploaded="appendToMedia"
        @media-deleted="destroy"
      />
    </template>
    <!--
    <template v-if="displayStyle === 'grid'">
      <h2>{{ headingText ? headingText : $t("media.words.title") }}</h2>
      <section>
        <aside
          v-if="!showOnly"
          class="card"
        >
          <upload
            :model="model"
            :model-class="modelClass"
            :parent-model="parentModel"
            :category-id="currentCategory.id"
            :display-style="displayStyle"
            :upload-endpoint="uploadEndpoint"
            :media="media"
            :options="fullCategoryOptions"
            :new-file-uploaded="appendToMedia"
            icon-name="file-upload"
            :icon-width="50"
          />
        </aside>
        <div
          v-for="(item, index) in media"
          :key="index"
          class="card"
        >
          <figure>
            <a
              target="_blank"
              :href="item.url"
            >
              <component
                :is="getComponent(item)"
                :media="item"
                :display-style="displayStyle"
              />
            </a>
            <button
              v-if="!showOnly"
              type="submit"
              class="button -hollow sml"
              @click="destroy(item.id, index)"
            >
              <icon
                name="cross"
                :width="8"
              />
            </button>
          </figure>
          <footer>
            <h5>
              <a
                target="_blank"
                :href="item.url"
              >{{ item.custom_properties.original_filename }}</a>
            </h5>
            <h6>{{ formatBytes(item.size) }}</h6>
            <inline-category-updater
              v-if="fullCategoryOptions && !showOnly"
              :options="fullCategoryOptions"
              :model="item"
            />
          </footer>
        </div>
      </section>
    </template> -->
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
        return {}
      },
      required: false,
    },
    parentModelClass: {
      type: String,
      default: '',
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
      default: '',
      required: false,
    },
    compact: {
      type: Boolean,
      default: false,
    },
    currentCategory: {
      type: Object,
      required: true,
    },
    media: {
      type: Array,
      required: true,
    },
    // categoriesEndpoint: {
    //   type: String,
    //   default: "/frontend/admin/category/options",
    // },
  },

  data: function () {
    return {
      files: [],
      // media: [],

      // categories: [],
      // parentCategories: [],
      // currentCategory: {
      //   id: null,
      //   parent_id: null,
      // },
    };
  },

  // mounted() {
  //   // this.getExisting();
  //   this.refineCategory();
  // },

  methods: {
    // reset() {
    //   this.parentCategories = [];
    //   this.currentCategory = {
    //     id: null,
    //     parent_id: null,
    //   };

    //   this.refineCategory();
    // },

    // updateParents(category) {
    //   if (this.parentCategories.length > 0) {
    //     let lastItem = this.parentCategories[this.parentCategories.length - 1];
    //     // if last item in the array has the same parent then remove it
    //     if (lastItem.parent_id === category.parent_id) {
    //       this.parentCategories.pop();
    //     }
    //   }
    //   if (this.parentCategories.indexOf(category) && category.id !== null) {
    //     this.parentCategories.push(category);
    //   }
    // },

    // refineCategory(category = {id: null, parent_id: null}) {
    //   this.currentCategory = category;

    //   this.updateParents(category);

    //   this.getExisting();

    //   axios
    //     .get(this.categoriesEndpoint, {
    //       params: {
    //         parent_id: category.id,
    //       },
    //     })
    //     .then(({data}) => {
    //       if (data.length) {
    //         this.categories = data;
    //       }
    //     })
    //     .catch(console.error);
    // },

    // getExisting() {
    //   axios
    //     .get(this.existingEndpoint, {
    //       params: {
    //         category_id: this.currentCategory.id,
    //       },
    //     })
    //     .then(({data}) => {
    //       this.media = [];
    //       if (data.length) {
    //         this.media = data;
    //       }
    //     })
    //     .catch(console.error);
    // },

    appendToMedia(item) {
      // console.log("media", item);
      this.media.push(item);
    },

    addSelectedMedia(media) {
      // console.log("media", media);
      this.media.push(media);
    },

    destroy({id: mediaId, index}) {
      if (confirm("Are you sure you want to remove this item")) {
          const media = this.media[index]
          const endpoint = media && media.hasOwnProperty('delete_endpoint') ? media.delete_endpoint: "/frontend/admin/media/delete/" + id

        axios
          .delete(endpoint)
          .then(({data}) => {
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

      if (
        _.startsWith(file.mime_type, "application") ||
              _.startsWith(file.mime_type, "text")
      ) {
        return "media-application";
      }

      return "default";
    },

    formatBytes(bytes, decimals = 2) {
      if (bytes === 0) return '0 Bytes';

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },

  },
}
</script>

<style scoped>

</style>
