<template>
  <div class="inline-field">
    <header
      v-show="showAlert === true"
      class="alert success"
    >
      <div>
        <span><i class="fal fa-check fa-xs" /></span>
        <small :v-text="alertText" />
      </div>
      <button><i class="fal fa-times fa-xs" /></button>
    </header>
    <select
      v-model="form.category_id"
      @change="update"
    >
      <option
        v-for="option in options"
        :key="option.id"
        :value="option.id"
      >
        {{ option.name }}
      </option>
    </select>
  </div>
</template>

<script>

export default {
  name: "InlineCategoryUpdater",
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
      default: null
    }
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
      this.form.category_id = newValue.category_id
    },
  },

  methods: {
    update() {
      this.form
        .patch(this.setEndpoint ? this.setEndpoint : `/api/generic/media/category/${this.model.id}`)
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
