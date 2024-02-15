<template>
  <div class="attachments mb-8 grid grid-cols-4 gap-4 border-b border-gray-300 pb-8" :class="displayStyle">
    <menu v-if="categories.length && !hideCategoryExplorer">
      <button class="flex w-full items-center rounded px-4 py-2 hover:bg-gray-200" @click="reset">
        <folder-icon class="mr-2 w-6" />
        {{ $t("media.words.home") }}
      </button>
      <ul class="pl-4">
        <li :class="{ open: activeFolder === 'model' }">
          <button
            class="flex w-full items-center rounded px-4 py-2 hover:bg-gray-200"
            @click="refineCategory(currentCategory, 'model')"
          >
            <folder-open-icon class="mr-2 w-6" v-if="activeFolder === 'model'" />
            <folder-icon class="mr-2 w-6" v-else />
            {{ $t("media.headings.model") }}
          </button>
          <ul class="pl-4">
            <li
              v-for="category in categories"
              :key="category.id"
              class="mb-1"
              :class="{ open: currentCategory === category.id && activeFolder === 'model' }"
            >
              <button
                class="flex w-full items-center rounded px-4 py-2 hover:bg-gray-200"
                @click="refineCategory(category, 'model')"
              >
                <folder-open-icon
                  class="mr-2 w-6"
                  v-if="currentCategory === category.id && activeFolder === 'model'"
                />
                <folder-icon class="mr-2 w-6" v-else />
                {{ category.name }}
              </button>
            </li>
          </ul>
        </li>
        <li v-if="parentModel && parentModelClass" :class="{ open: activeFolder === 'parent' }">
          <button class="flex items-center" @click="refineCategory(currentCategory, 'parent')">
            <folder-open-icon class="mr-2 w-6" v-if="activeFolder === 'parent'" />
            <folder-icon class="mr-2 w-6" v-else />
            {{ $t("media.headings.parent") }}
          </button>
          <ul>
            <li
              v-for="category in categories"
              :key="category.id"
              :class="{ open: currentCategory === category.id && activeFolder === 'parent' }"
            >
              <button class="flex items-center" @click="refineCategory(category, 'parent')">
                <folder-open-icon
                  class="mr-2 w-6"
                  v-if="currentCategory === category.id && activeFolder === 'parent'"
                />
                <folder-icon class="mr-2 w-6" v-else />
                {{ category.name }}
              </button>
            </li>
          </ul>
        </li>
      </ul>
    </menu>
    <model-media
      v-if="activeFolder === 'model'"
      :full-category-options="fullCategoryOptions"
      :categories-endpoint="categoriesEndpoint"
      :model="model"
      :model-class="modelClass"
      :parent-model="parentModel"
      :parent-model-class="parentModelClass"
      :upload-endpoint="uploadEndpoint"
      :add-existing-endpoint="addExistingEndpoint"
      :display-style="displayStyle"
      :permit-delete="permitDelete"
      :permit-edit="permitEdit"
      :show-only="showOnly"
      :current-category="currentCategory"
      :heading-text="headingText"
      :media="media"
      :class="{ 'no-cats': categories.length === 0 }"
    />
    <model-media
      v-if="activeFolder === 'parent'"
      :full-category-options="fullCategoryOptions"
      :categories-endpoint="categoriesEndpoint"
      :model="parentModel"
      :model-class="parentModelClass"
      :upload-endpoint="uploadEndpoint"
      :add-existing-endpoint="addExistingEndpoint"
      :display-style="displayStyle"
      :permit-delete="permitDelete"
      :permit-edit="permitEdit"
      :show-only="showOnly"
      :current-category="currentCategory"
      :heading-text="headingText"
      :media="media"
      :class="{ 'no-cats': categories.length === 0 }"
    />
  </div>
</template>

<script>
import { FolderIcon, FolderOpenIcon } from "@heroicons/vue/solid";
import ModelMedia from "./ModelMedia.vue";
import bytes from "bytes";

/**
 * docs https://lian-yue.github.io/vue-upload-component/#/en/documents
 */
export default {
  name: "Attachments",
  components: {
    ModelMedia,
    FolderIcon,
    FolderOpenIcon,
  },
  filters: {},
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
      default: null,
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
    modelExistingEndpoint: {
      type: String,
      default: "/frontend/admin/media/existing",
    },
    parentExistingEndpoint: {
      type: String,
      default: "/frontend/admin/media/existing",
    },
    categoryOptionsEndpoint: {
      type: String,
      default: "/frontend/admin/category/options",
    },
    categoriesEndpoint: {
      type: String,
      default: "/frontend/admin/category/options",
    },
    addExistingEndpoint: {
      type: String,
      default: "/frontend/admin/media/existing",
    },
    displayStyle: {
      type: String,
      default: "list",
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
      required: false,
    },
    hideCategoryExplorer: {
      type: Boolean,
      default: false,
    },
    compact: {
      type: Boolean,
      default: false,
    },
    showMinimalUpload: {
      type: Boolean,
      default: false,
    },
    displayCategory: {
      type: Boolean,
      default: true,
    },
    preSelectedCategoryId: {
      type: Number,
      default: null,
    },
  },
  data: function () {
    return {
      activeFolder: "model",
      fullCategoryOptions: [],
      media: [],
      categories: [],
      parentCategories: [],
      currentCategory: this.preSelectedCategoryId,
    };
  },
  mounted() {
    this.refineCategory();
    axios
      .get(this.categoryOptionsEndpoint)
      .then(({ data }) => {
        this.fullCategoryOptions = data;
      })
      .catch(console.error);
  },
  methods: {
    formatDate(value) {
      return value == null ? "" : new Date(value).toLocaleDateString();
    },
    formatFileSize(value) {
      return value == null ? "" : bytes(value);
    },
    capitalize(value) {
      return value == null ? "" : _.upperFirst(value);
    },
    reset() {
      this.parentCategories = [];
      this.currentCategory = null;
      this.refineCategory();
    },
    updateParents(category) {
      if (this.parentCategories.length > 0) {
        const lastItem = this.parentCategories[this.parentCategories.length - 1];
        // if last item in the array has the same parent then remove it
        if (lastItem.parent_id === category.parent_id) {
          this.parentCategories.pop();
        }
      }
      if (this.parentCategories.indexOf(category) && category.id !== null) {
        this.parentCategories.push(category);
      }
    },
    refineCategory(category = { id: null, parent_id: null }, folder = "model") {
      this.activeFolder = folder;
      this.currentCategory = category.id;
      this.updateParents(category);
      this.getExisting();
      axios
        .get(this.categoriesEndpoint, {
          params: {
            parent_id: category.id,
          },
        })
        .then(({ data }) => {
          if (data.length) {
            this.categories = data;
          }
        })
        .catch(console.error);
    },
    getExisting() {
      let params = {
        category_id: this.currentCategory,
      };
      if (this.activeFolder === "model") {
        params = {
          ...params,
          modelClass: this.modelClass,
          modelId: this.model.id,
        };
      } else if (this.activeFolder === "parent") {
        if (this.parentModel) {
          params = {
            ...params,
            modelClass: this.parentModelClass,
            modelId: this.parentModel.id,
          };
        } else {
          console.error("activeFolder is parent but missing parentModel");
        }
      }
      axios
        .get(this.activeFolder === "model" ? this.modelExistingEndpoint : this.parentExistingEndpoint, {
          params,
        })
        .then(({ data }) => {
          this.media = [];
          if (data.length) {
            console.log("getExisting media: ", { data });
            this.media = data;
          }
        })
        .catch(console.error);
    },

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
        axios
          .delete("/frontend/admin/media/delete/" + mediaId)
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
