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
    <select v-model="form.category_id" @change="update">
      <option v-for="option in options" :key="option.id" :value="option.id">
        {{ option.name }}
      </option>
    </select>
  </div>
</template>

<script>
import Form from "form-backend-validation";
import { CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";

export default {
  name: "InlineCategoryUpdater",
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
        category_id: this.model.category_id,
      }),
      showAlert: false,
      alertText: "",
    };
  },
  watch: {
    model(newValue, oldValue) {
      this.form.category_id = newValue.category_id;
    },
  },

  methods: {
    update() {
      this.form
        .patch(
          this.setEndpoint
            ? this.setEndpoint
            : `/frontend/admin/media/category/${this.model.id}`,
        )
        .then((data) => {
          this.alertText = data.message;
          this.form.category_id = data.category_id;
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
