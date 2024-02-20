<template>
  <div id="v-image-markup" :class="{ 'no-pages': plans.length === 1 }">
    <div v-if="plans.length > 1" class="side-bar plans">
      <h4>Pages</h4>
      <ul class="menu vertical">
        <li v-for="(item, index) in plans" :key="index">
          <a
            class="thumbnail"
            :class="{ active: index === selected }"
            :style="{ backgroundImage: `url(${item.url})` }"
            @click="selected = index"
          />
        </li>
      </ul>
    </div>
    <div class="side-bar annotation-layers">
      <h4>Layers</h4>
      <ul v-if="loaded" class="menu vertical">
        <li v-for="(annotation, id) in plans[selected].annotations" :key="id">
          <a
            class="thumbnail"
            :class="{ hidden: annotation.hidden }"
            :style="{ backgroundImage: `url(${annotation.content})` }"
          >
            <button class="delete" @click="deleteLayer(annotation.id)">
              <trash-icon class="h-6 w-6" />
            </button>
            <button class="remove" @click="annotation.hidden = !annotation.hidden">
              <i class="fal" :class="annotation.hidden ? 'fa-eye-slash' : 'fa-eye'" />
            </button>
          </a>
        </li>
      </ul>
    </div>
    <div ref="plansContainer" class="plans-container">
      <div v-if="plansLoaded" class="plan">
        <div v-if="plans[selected].annotations" class="annotations">
          <img
            v-for="annotation in plans[selected].annotations"
            :key="annotation.id"
            :src="annotation.content"
            :width="canvas.width + 'px'"
            :height="canvas.height + 'px'"
            :class="{ hide: annotation.hidden }"
          />
        </div>
        <img ref="planImg" class="plan-image" :src="plans[selected].url" @load="onPlanImgLoaded" />
      </div>
      <Editor
        v-if="loaded"
        ref="editor"
        :canvas-width="canvas.width"
        :canvas-height="canvas.height"
        :overlay-opacity="0"
      />
    </div>
    <div class="side-bar tool-bar">
      <div>
        <h4>Tools</h4>
        <nav class="menu vertical">
          <button ref="drawBtn" class="drawBtn" :class="{ active: toolSelected === 'draw' }" @click="drawMode">
            <pencil-icon class="h-6 w-6" />
          </button>
          <button :class="{ active: toolSelected === 'text' }" @click="textMode">
            <adjustments-icon class="h-6 w-6" />
          </button>
          <button :class="{ active: toolSelected === 'move' }" @click="moveMode">
            <arrows-expand-icon class="h-6 w-6" />
          </button>
          <button :disabled="stroke === 8" @click="increaseStroke">
            <plus-icon class="h-6 w-6" />
          </button>
          <button :disabled="stroke === 1" @click="decreaseStroke">
            <minus-icon class="h-6 w-6" />
          </button>
          <div class="colour-picker">
            <input v-model="colourVal" type="color" list="presetColours" @change="updateColour" />
            <datalist id="presetColours">
              <option v-for="colour in colourOptions" :key="colour">
                {{ colour }}
              </option>
            </datalist>
            <div class="stroke-container" :style="{ borderColor: `${colourVal}` }">
              <div
                class="stroke"
                :style="{ width: `${stroke}px`, height: `${stroke}px`, backgroundColor: `${colourVal}` }"
              />
            </div>
          </div>
          <button ref="clear" @click="clearMode">
            <rewind-icon class="h-6 w-6" />
          </button>
        </nav>
      </div>
      <button class="button success" @click="saveImg">
        <save-icon class="h-6 w-6" />
        <span>Save</span>
      </button>
    </div>
  </div>
</template>

<script>
import {
  TrashIcon,
  PencilIcon,
  AdjustmentsIcon,
  ArrowsExpandIcon,
  PlusIcon,
  MinusIcon,
  RewindIcon,
  SaveIcon,
} from "@heroicons/vue/solid";
import Editor from "vue-image-markup";
export default {
  components: {
    Editor: Editor,
    TrashIcon,
    PencilIcon,
    ArrowsExpandIcon,
    PlusIcon,
    MinusIcon,
    RewindIcon,
    SaveIcon,
  },
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      canvas: {
        width: 0,
        height: 0,
      },
      loaded: false,
      plans: [],
      plansLoaded: false,
      selected: 0,
      toolSelected: null,
      colourVal: "#000000",
      colourOptions: ["#000000", "#ff0000", "#00ff00", "#0000ff", "#ffee00", "#ff8800"],
      stroke: 2,
      saving: false,
      planImgHasLoaded: false,
      windowResizeTimeout: null,
    };
  },
  mounted() {
    this.plans = this.items;
    this.plansLoaded = true;
    this.scaleCanvasAndPlan();
  },
  methods: {
    scaleCanvasAndPlan() {
      if (!this.planImgHasLoaded) {
        setTimeout(() => {
          this.scaleCanvasAndPlan();
        }, 1000);
        return false;
      } else {
        const windowWidth = this.$refs.plansContainer.clientWidth;
        const windowHeight = this.$refs.plansContainer.clientHeight;
        const windowRatio = windowWidth / windowHeight;
        const fitFactor = 0.9;
        const plan = this.plans[0];
        let planScaleFactor;
        if (plan.aspectRatio > windowRatio) {
          const planWidth = windowWidth * fitFactor;
          this.$set(this.plans[0], "width", planWidth + "px");
          planScaleFactor = planWidth / plan.initWidth;
          const planHeight = plan.initHeight * planScaleFactor;
          this.$set(this.plans[0], "height", planHeight + "px");
          this.canvas.width = windowWidth;
          this.canvas.height = planHeight / fitFactor;
        } else {
          const planHeight = windowHeight * fitFactor;
          this.$set(this.plans[0], "height", planHeight + "px");
          planScaleFactor = planHeight / plan.initHeight;
          const planWidth = plan.initWidth * planScaleFactor;
          this.$set(this.plans[0], "width", planWidth + "px");
          this.canvas.height = windowHeight;
          this.canvas.width = planWidth / fitFactor;
        }
        this.$nextTick(() => {
          this.loaded = true;
          setTimeout(() => {
            this.clearMode();
          }, 500);
        });
      }
    },
    onPlanImgLoaded() {
      if (!this.planImgHasLoaded) {
        this.planImgHasLoaded = true;
        const plan = this.plans[0];
        plan.initWidth = this.$refs.planImg.clientWidth;
        plan.initHeight = this.$refs.planImg.clientHeight;
        plan.aspectRatio = plan.initWidth / plan.initHeight;
      }
    },
    increaseStroke() {
      this.stroke++;
      this.drawMode();
    },
    decreaseStroke() {
      this.stroke--;
      this.drawMode();
    },
    hideLayer(id) {
      this.plans[this.selected].annotations = this.plans[this.selected].annotations.map((annotation) => {
        if (annotation.id === annotationId) {
          annotation.hidden = !annotation.hidden;
        }
        return annotation;
      });
    },
    deleteLayer(annotationId) {
      axios
        .delete(`/frontend/admin/media/${this.plans[this.selected].id}/annotations/${annotationId}`)
        .then((resp) => {
          this.plans[this.selected].annotations = this.plans[this.selected].annotations.filter(
            (plan) => plan.id !== annotationId,
          );
        })
        .catch((err) => console.error(err));
    },
    drawMode() {
      this.toolSelected = "draw";
      this.$refs.editor.set("freeDrawing", { stroke: this.colourVal, strokeWidth: this.stroke });
      this.$refs.drawBtn.focus();
    },
    textMode() {
      this.toolSelected = "text";
      this.$refs.editor.set("text", { fill: this.colourVal });
    },
    moveMode() {
      this.toolSelected = "move";
      this.$refs.editor.set("selectMode");
    },
    clearMode() {
      this.$refs.editor.clear();
      this.drawMode();
    },
    updateColour(ev) {
      this.colourVal = ev.target.value;
      this.drawMode();
    },
    saveImg() {
      this.$refs.drawBtn.focus();
      this.plans[this.selected].annotations.push({
        content: this.$refs.editor.saveImage(),
        hidden: false,
      });
      this.clearMode();
      this.textMode();

      const media = this.plans[this.selected];
      axios
        .post(media.add_annotation_endpoint, { annotations: media.annotations })
        .then((resp) => {
          this.plans[this.selected].annotations = resp.data.annotations;
        })
        .finally(() => {
          this.clearMode();
        });
    },
  },
};
</script>
