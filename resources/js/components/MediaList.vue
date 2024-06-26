<template>
  <table class="no-breakpoint">
    <thead v-if="media.length">
      <tr>
        <th colspan="2">
          {{ $t("media.table.headings.details") }}
        </th>
        <th v-if="displayCategory">
          {{ $t("media.table.headings.category") }}
        </th>
        <th v-if="showTitle">
          {{ $t("media.table.headings.title") }}
        </th>
        <th v-if="showAltText">
          {{ $t("media.table.headings.alt_text") }}
        </th>
        <th v-if="categoryOptions.length && allowUpdatingCategory">
          {{ $t("media.table.headings.category") }}
        </th>
        <th v-if="showFileSize">
          {{ $t("media.table.headings.size") }}
        </th>
        <th v-if="showUploadedBy">
          {{ $t("media.table.headings.uploaded_by") }}
        </th>
        <th v-if="showCreatedAt">
          {{ $t("media.table.headings.created_at") }}
        </th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody v-if="media.length">
      <template v-for="(item, index) in media">
        <tr v-if="item" :key="index">
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
          <td v-if="showTitle">
            <title-updater
              :model="item"
              :set-endpoint="
                item.hasOwnProperty('set_title_endpoint')
                  ? item.set_title_endpoint
                  : null
              "
            ></title-updater>
          </td>
          <td v-if="showAltText">
            <alt-text-updater
              :model="item"
              :set-endpoint="
                item.hasOwnProperty('set_alt_text_endpoint')
                  ? item.set_alt_text_endpoint
                  : null
              "
            ></alt-text-updater>
          </td>
          <td v-if="categoryOptions.length && allowUpdatingCategory">
            <inline-category-updater
              :options="categoryOptions"
              :model="item"
              :set-endpoint="
                item.hasOwnProperty('set_category_endpoint')
                  ? item.set_category_endpoint
                  : null
              "
            />
          </td>
          <td v-if="showFileSize">
            {{ formatFileSize(item.size) }}
          </td>
          <td v-if="showUploadedBy">
            {{ item.custom_properties.uploaded_by }}
          </td>
          <td v-if="showCreatedAt">
            {{ formatDate(item.created_at) }}
          </td>
          <td class="actions">
            <menu>
              <button
                v-if="allowEditing"
                class="button -hollow sml"
                @click="fireEditEvent(item)"
              >
                <pencil-icon class="h-6 w-6" />
              </button>
              <a
                :href="item.url"
                target="_blank"
                v-if="showDownload"
                class="button -hollow sml"
                :title="item.custom_properties.original_filename"
              >
                <arrow-down-icon class="h-6 w-6" />
              </a>
              <button
                v-if="allowDeleting"
                class="button -hollow sml"
                type="submit"
                @click="fireDeletedEvent(item, index)"
              >
                <trash-icon class="h-6 w-6" />
              </button>
            </menu>
          </td>
        </tr>
      </template>
      <tr>
        <td colspan="6">
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
        <td colspan="6">
          <p class="text-warning mb-2 text-center">No media uploaded...</p>
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
import { PencilIcon, TrashIcon, ArrowDownIcon } from "@heroicons/vue/24/solid";
import bytes from "bytes";
import Upload from "./Upload.vue";
import MediaImage from "./MediaImage.vue";
import MediaVideo from "./MediaVideo.vue";
import MediaApplication from "./MediaApplication.vue";
import Default from "./Default.vue";
import Icon from "./Icon.vue";
import InlineCategoryUpdater from "./InlineCategoryUpdater.vue";
import TitleUpdater from "./TitleUpdater.vue";
import AltTextUpdater from "./AltTextUpdater.vue";

export default {
  components: {
    MediaImage,
    MediaVideo,
    MediaApplication,
    Default,
    Upload,
    InlineCategoryUpdater,
    TitleUpdater,
    AltTextUpdater,
    Icon,
    PencilIcon,
    TrashIcon,
    ArrowDownIcon,
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
      type: [String, Number],
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
      default: "",
      required: true,
    },
    uploadEndpoint: {
      type: String,
      default: "/frontend/admin/media/upload",
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
    showTitle: {
      type: Boolean,
      default: true,
    },
    showAltText: {
      type: Boolean,
      default: true,
    },
    showFileSize: {
      type: Boolean,
      default: true,
    },
    showUploadedBy: {
      type: Boolean,
      default: true,
    },
    showCreatedAt: {
      type: Boolean,
      default: true,
    },
    showDownload: {
      type: Boolean,
      default: true,
    },
    displayCategory: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {};
  },
  methods: {
    formatDate(value) {
      return value == null ? "" : new Date(value).toLocaleDateString();
    },
    formatFileSize(value) {
      return value == null ? "" : bytes(value);
    },
    capitalize(value) {
      return value == null
        ? ""
        : value.charAt(0).toUpperCase() + value.slice(1);
    },
    getComponent(file) {
      if (!file) {
        return "default";
      }
      if (file.mime_type.startsWith("image")) {
        return "media-image";
      }

      if (file.mime_type.startsWith("video")) {
        return "media-video";
      }

      if (this.isApplication(file)) {
        return "media-application";
      }

      return "default";
    },
    isApplication(file) {
      return (
        file.mime_type.startsWith("application") ||
        file.mime_type.startsWith("text")
      );
    },
    fireUploadedEvent(item) {
      this.$emit("media-uploaded", item);
    },
    fireDeletedEvent(item, index) {
      this.$emit("media-deleted", { id: item.id, index: index });
    },
    fireEditEvent(item) {
      // console.log("reached fireEditEvent", item)
      // this.$root.$emit("openSidePanel", {
      //   componentName: "SidePanelImageMarkup",
      //   componentData: {
      //     items: this.getMedia(item),
      //   },
      //   title: `Edit ${item.custom_properties.original_filename}`,
      // });
    },
    getMedia(item) {
      if (this.isApplication(item) && item.children.length) {
        return item.children.map((o) => {
          o.width = "auto";
          o.height = "auto";

          return o;
        });
      }

      item.width = "auto";
      item.height = "auto";

      return [item];
    },
  },
};
</script>
