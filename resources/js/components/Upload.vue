<template>
  <file-upload
    ref="upload"
    v-model="files"
    name="file"
    class="justify-center flex w-full !cursor-pointer items-center rounded border border-dashed border-gray-500 p-4 hover:bg-gray-50"
    :post-action="uploadEndpoint"
    :headers="{
      'X-CSRF-TOKEN': $root.csrf_token,
    }"
    accept="image/*,video/mp4,video/x-m4v,video/*,audio/mpeg,audio/wav,audio/x-wav,audio/mp4,audio/mp3,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/rtf,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/pdf"
    :multiple="true"
    :data="{
      modelClass: modelClass,
      modelId: model.id,
      category_id: categoryId,
    }"
    @input-file="inputFile"
    @input-filter="inputFilter"
  >
    <div class="text-center">
      <plus-icon class="mx-auto mb-4 h-12 w-12" />
      <p>{{ $t("media.phrase.upload_area") }}</p>
      <button
        v-show="$refs.upload && $refs.upload.active"
        type="button"
        class="button secondary"
        @click.prevent="$refs.upload.active = false"
      >
        {{ $t("media.buttons.cancel") }}
      </button>
    </div>
  </file-upload>
</template>

<script>
import { PlusIcon } from "@heroicons/vue/solid";
import VueUploadComponent from "vue-upload-component";
import Icon from "./Icon.vue";
export default {
  name: "Upload",

  components: {
    FileUpload: VueUploadComponent,
    Icon,
    PlusIcon,
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
    categoryId: {
      type: [String, Number],
      default: null,
      required: false,
    },
    uploadEndpoint: {
      type: String,
      default: "/frontend/admin/media/upload",
    },
    existingEndpoint: {
      type: String,
      default: "/frontend/admin/media/existing",
    },
    displayStyle: {
      type: String,
      default: "list",
    },
    newFileUploaded: {
      type: Function,
      default: () => {
        return () => {};
      },
    },
    iconName: {
      type: String,
      default: "plus",
    },
    iconWidth: {
      type: Number,
      default: 15,
    },
    iconHeight: {
      type: Number,
      default: 15,
    },
  },

  data: function () {
    return {
      files: [],
    };
  },

  methods: {
    /**
     * Has changed
     * @param  Object|undefined   newFile   Read only
     * @param  Object|undefined   oldFile   Read only
     * @return undefined
     */
    inputFile: function (newFile, oldFile) {
      if (newFile && !oldFile) {
        // Add file
        // console.log("new file", newFile);
      }

      if (newFile && oldFile) {
        // Update file

        // Start upload
        if (newFile.active !== oldFile.active) {
          // console.log("Start upload", newFile.active, newFile);
          // min size
          // if (newFile.size >= 0 && newFile.size < 100 * 1024) {
          //     newFile = this.$refs.upload.update(newFile, {
          //         error: "size",
          //     });
          // }
          // this.newFileUploaded(newFile);
        }

        // Upload progress
        if (newFile.progress !== oldFile.progress) {
          // console.log("progress", newFile.progress, newFile);
        }

        // Upload error
        if (newFile.error !== oldFile.error) {
          // console.log("error", newFile.error, newFile);
        }

        // Uploaded successfully
        if (newFile.success !== oldFile.success) {
          // console.log("success", newFile.success, newFile);
          // console.log("response", newFile.response);
          this.newFileUploaded(newFile.response.media);
        }
      }

      if (!newFile && oldFile) {
        // Remove file

        // Automatically delete files on the server
        if (oldFile.success && oldFile.response.id) {
          // $.ajax({
          //   type: 'DELETE',
          //   url: '/file/delete?id=' + oldFile.response.id,
          // });
        }
      }

      // Automatic upload
      // if (
      //     Boolean(newFile) !== Boolean(oldFile) ||
      //     oldFile.error !== newFile.error
      // ) {
      if (!this.$refs.upload.active) {
        this.$refs.upload.active = true;
      }
      // }
    },
    /**
     * Pretreatment
     * @param  Object|undefined   newFile   Read and write
     * @param  Object|undefined   oldFile   Read only
     * @param  Function           prevent   Prevent changing
     * @return undefined
     */

    inputFilter: function (newFile, oldFile, prevent) {
      // Filters system files or hide files
      if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
        return prevent();
      }
      // Filters php html js file
      if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
        return prevent();
      }

      // Create a blob field
      // newFile.blob = "";
      // let URL = window.URL || window.webkitURL;
      // if (URL && URL.createObjectURL) {
      //     newFile.blob = URL.createObjectURL(newFile.file);
      // }
      if (newFile && newFile.error === "" && newFile.file && (!oldFile || newFile.file !== oldFile.file)) {
        // Create a blob field
        newFile.blob = "";
        const URL = window.URL || window.webkitURL;
        if (URL) {
          newFile.blob = URL.createObjectURL(newFile.file);
        }

        // console.log("blob", newFile.blob);

        // Thumbnails
        newFile.thumb = "";
        if (newFile.blob) {
          newFile.thumb = newFile.blob;
        }
        //     newFile.blob = ''
        //     let URL = (window.URL || window.webkitURL)
        //     if (URL) {
        //         newFile.blob = URL.createObjectURL(newFile.file)
        //     }
        //     // Thumbnails
        //     // 缩略图
        //     newFile.thumb = ''
        //     if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
        //         newFile.thumb = newFile.blob
        //     }
      }
    },
  },
};
</script>

