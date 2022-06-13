import bytes from "bytes";

export default {
  props: {
    media: {
      type: Object,
      required: true,
    },

    displayStyle: {
      type: String,
      default: "list",
    },
  },

  data: () => {
    return {
      error: false,
    };
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
    replaceByDefault(e) {
      this.error = true;

      setTimeout(() => {
        this.error = false;
      }, 1000);
    },
  },
};
