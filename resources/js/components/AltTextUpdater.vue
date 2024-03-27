<template>
  <div class="inline-field">
    <header v-show="showAlert === true" class="alert success">
      <div>
        <check-icon class="h-4 w-4" />
        <small :v-text="alertText" />
      </div>
      <button>
        <x-mark-icon class="h-4 w-4" />
      </button>
    </header>
    <input type="text" v-model="form.alt_text" @blur="update" />
  </div>
</template>

<script>
import Form from "form-backend-validation";
import { CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";

export default {
  name: "AltTextUpdater",
  components: {
    CheckIcon,
    XMarkIcon,
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
        alt_text: this.model.alt_text,
      }),
      showAlert: false,
      alertText: "",
    };
  },
  watch: {
    model(newValue, oldValue) {
      this.form.alt_text = newValue.alt_text;
    },
  },

  methods: {
    update() {
      this.form
        .patch(
          this.setEndpoint
            ? this.setEndpoint
            : `/frontend/admin/media/alttext/${this.model.id}`,
        )
        .then((data) => {
          this.alertText = data.message;
          this.form.alt_text = data.alt_text;
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
