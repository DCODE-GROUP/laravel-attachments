import Attachments from "./components/Attachments.vue";

const attachmentPlugin = {
  install(app, options) {
    app.component("Attachments", Attachments);

    app.config.globalProperties.$t = (key) => {
      // retrieve a nested property in `options`
      // using `key` as the path
      return key.split(".").reduce((o, i) => {
        if (o) return o[i];
      }, options);
    };
  },
};

export default attachmentPlugin;
