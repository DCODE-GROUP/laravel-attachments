<template>
  <div class="inline-field">
    <header v-show="showAlert === true" class="alert success">
      <div>
        <check-icon class="h-4 w-4" />
        <small :v-text="alertText" />
      </div>
      <button>
        <x-icon class="h-4 w-4" />
      </button>
    </header>
    <input type="text" v-model="form.title" @blur="update" />
  </div>
</template>

<script>
import Form from "form-backend-validation";
import { CheckIcon, XIcon } from "@heroicons/vue/solid";

export default {
  name: "TitleUpdater",
  components: {
    CheckIcon,
    XIcon,
  },
  props: {
    model: {
      type: Object,
      required: true,
    },
    options: {
      type: Array,
      required: true,
    },
    setEndpoint: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      form: new Form({
        title: this.model.title,
      }),
      showAlert: false,
      alertText: "",
    };
  },
  watch: {
    model(newValue, oldValue) {
      this.form.title = newValue.title;
    },
  },

  methods: {
    update() {
      this.form
        .patch(
          this.setEndpoint
            ? this.setEndpoint
            : `/frontend/admin/media/title/${this.model.id}`,
        )
        .then((data) => {
          this.alertText = data.message;
          this.form.title = data.title;
          this.showAlert = true;
          setTimeout(() => {
            this.showAlert = false;
          }, 1500);
        })
        .catch((errors) => {
          console.error(errors);
        });
    },
  },
};
</script>

<style scoped></style>
