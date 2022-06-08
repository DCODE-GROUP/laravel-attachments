<template>
  <table class="no-breakpoint">
    <thead v-if="media.length">
      <tr>
        <th colspan="2">
          {{ $t("media.table.headings.details") }}
        </th>
        <th v-if="categoryOptions.length && allowUpdatingCategory">
          {{ $t("media.table.headings.category") }}
        </th>
        <th v-if="!compact">
          {{ $t("media.table.headings.size") }}
        </th>
        <th v-if="!compact">
          {{ $t("media.table.headings.created_at") }}
        </th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody v-if="media.length">
      <template v-for="(item, index) in media">
        <tr
          v-if="item"
          :key="index"
        >
          <td>
            <component
              :is="getComponent(item)"
              :media="item"
              display-style="list"
              class="square-thumb"
            />
          </td>
          <td @click="$emit('click', item)">
            {{ item.custom_properties.original_filename }}
          </td>
          <td v-if="categoryOptions.length && allowUpdatingCategory">
            <inline-category-updater
              :options="categoryOptions"
              :model="item"
              :set-endpoint="item.hasOwnProperty('set_category_endpoint') ? item.set_category_endpoint : null"
            />
          </td>
          <td v-if="!compact">
            {{ item.size | formatFileSize }}
          </td>
          <td v-if="!compact">
            {{ item.created_at | formatDate }}
          </td>
          <td class="actions">
            <menu>
              <button
                v-if="allowEditing"
                class="button -hollow sml"
                @click="fireEditEvent(item)"
              >
                <i class="fal fa-pencil" />
              </button>
              <button
                v-if="allowDeleting"
                class="button -hollow sml"
                type="submit"
                @click="fireDeletedEvent(item, index)"
              >
                <Icon
                  name="cross"
                  :width="10"
                  :height="10"
                />
              </button>
            </menu>
          </td>
        </tr>
      </template>
      <tr>
        <td
          colspan="6"
          class="px-0"
        >
          <upload
            v-if="allowUploading"
            :model="model"
            :model-class="modelClass"
            :category-id="categoryId"
            :upload-endpoint="uploadEndpoint"
            display-style="list"
            :media="media"
            :new-file-uploaded="fireUploadedEvent"
            class="horizontal-uploader"
          />
        </td>
      </tr>
    </tbody>
    <tbody v-else>
      <tr>
        <td
          colspan="6"
        >
          <p class="text-warning">
            No media uploaded...
          </p>
          <upload
            v-if="allowUploading"
            :model="model"
            :model-class="modelClass"
            :category-id="categoryId"
            :upload-endpoint="uploadEndpoint"
            display-style="list"
            :media="media"
            :new-file-uploaded="fireUploadedEvent"
            class="horizontal-uploader"
          />
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import bytes from 'bytes';
import Upload from './Upload.vue';
import MediaImage from './MediaImage.vue';
import MediaVideo from './MediaVideo.vue';
import MediaApplication from './MediaApplication.vue';
import Default from './Default.vue';
import Icon from './Icon.vue';
import InlineCategoryUpdater from './InlineCategoryUpdater.vue';

export default {
  components: {
    MediaImage,
    MediaVideo,
    MediaApplication,
    Default,
    Upload,
    InlineCategoryUpdater,
    Icon,
  },

  filters: {
    formatDate(value) {
      return value == null ? "" : new Date(value).toLocaleDateString();
    },
    formatFileSize(value) {
      return value == null ? "" : bytes(value);
    },
    capitalize(value) {
      return value == null ? "" : _.upperFirst(value);
    },
  },
  props: {
    media: {
      type: Array,
      required: true,
    },
    categoryOptions: {
      type: Array,
      default: () => [],
    },
    categoryId: {
      type: [String,Number],
      default: null,
      required: false,
    },
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
      default: {},
      required: false,
    },
    parentModelClass: {
      type: String,
      default: '',
      required: true,
    },
    uploadEndpoint: {
      type: String,
      default: "/api/admin/media/upload",
    },
    allowUploading: {
      type: Boolean,
      default: true,
    },
    allowDeleting: {
      type: Boolean,
      default: true,
    },
    allowEditing: {
      type: Boolean,
      default: true,
    },
    allowUpdatingCategory: {
      type: Boolean,
      default: true,
    },
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {};
  },
  methods: {
    getComponent(file) {
      if (!file) {
        return "default"
      }
      if (_.startsWith(file.mime_type, "image")) {
        return "media-image";
      }

      if (_.startsWith(file.mime_type, "video")) {
        return "media-video";
      }

      if (
        this.isApplication(file)
      ) {
        return "media-application";
      }

      return "default";
    },
    isApplication(file) {
      return _.startsWith(file.mime_type, "application") ||
                _.startsWith(file.mime_type, "text")
    },
    fireUploadedEvent(item) {
      this.$emit('media-uploaded', item);
    },
    fireDeletedEvent(item, index) {
      this.$emit('media-deleted', {id: item.id, index: index});
    },
    fireEditEvent(item) {
      // console.log("reached fireEditEvent", item)
      this.$root.$emit('openSidePanel', {
        componentName: 'SidePanelImageMarkup',
        componentData: {
          items: this.getMedia(item),
        },
        title: `Edit ${item.custom_properties.original_filename}`,
      });
    },
    getMedia(item) {
      if (this.isApplication(item) && item.children.length) {
        return item.children.map((o) => {
          o.width = 'auto'
          o.height = 'auto'

          return o
        })
      }

      item.width = 'auto'
      item.height = 'auto'

      return [item]
    },
  },
}
</script>
