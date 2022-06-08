<template>
  <div
    id="v-image-markup"
    :class="{ 'no-pages' : plans.length === 1 }"
  >
    <div
      v-if="plans.length > 1"
      class="side-bar plans"
    >
      <h4>Pages</h4>
      <ul class="menu vertical">
        <li
          v-for="(item, index) in plans"
          :key="index"
        >
          <a
            class="thumbnail"
            :class="{ 'active' : index === selected }"
            :style="{ backgroundImage: `url(${item.url})` }"
            @click="selected = index"
          />
        </li>
      </ul>
    </div>
    <div class="side-bar annotation-layers">
      <h4>Layers</h4>
      <ul
        v-if="loaded"
        class="menu vertical"
      >
        <li
          v-for="(annotation, id) in plans[selected].annotations"
          :key="id"
        >
          <a
            class="thumbnail"
            :class="{ hidden: annotation.hidden }"
            :style="{ backgroundImage: `url(${annotation.content})` }"
          >
            <button
              class="delete"
              @click="deleteLayer(annotation.id)"
            >
              <i class="fal fa-trash-alt" />
            </button>
            <button
              class="remove"
              @click="annotation.hidden = !annotation.hidden"
            >
              <i
                class="fal"
                :class="annotation.hidden ? 'fa-eye-slash' : 'fa-eye'"
              />
            </button>
          </a>
        </li>
      </ul>
    </div>
    <div
      ref="plansContainer"
      class="plans-container"
    >
      <div
        v-if="plansLoaded"
        class="plan"
      >
        <div
          v-if="plans[selected].annotations"
          class="annotations"
        >
          <img
            v-for="annotation in plans[selected].annotations"
            :key="annotation.id"
            :src="annotation.content"
            :width="canvas.width + 'px'"
            :height="canvas.height + 'px'"
            :class="{ hide: annotation.hidden }"
          >
        </div>
        <img
          ref="planImg"
          class="plan-image"
          :src="plans[selected].url"
          @load="onPlanImgLoaded"
        >
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
          <button
            ref="drawBtn"
            class="drawBtn"
            :class="{ 'active' : toolSelected === 'draw' }"
            @click="drawMode"
          >
            <i class="fal fa-pencil" />
          </button>
          <button
            :class="{ 'active' : toolSelected === 'text' }"
            @click="textMode"
          >
            <i class="fal fa-text-size" />
          </button>
          <button
            :class="{ 'active' : toolSelected === 'move' }"
            @click="moveMode"
          >
            <i class="fal fa-expand-arrows-alt" />
          </button>
          <button
            :disabled="stroke === 8"
            @click="increaseStroke"
          >
            <i class="fal fa-plus" />
          </button>
          <button
            :disabled="stroke === 1"
            @click="decreaseStroke"
          >
            <i class="fal fa-minus" />
          </button>
          <div class="colour-picker">
            <input
              v-model="colourVal"
              type="color"
              list="presetColours"
              @change="updateColour"
            >
            <datalist id="presetColours">
              <option
                v-for="colour in colourOptions"
                :key="colour"
              >
                {{ colour }}
              </option>
            </datalist>
            <div
              class="stroke-container"
              :style="{ borderColor:`${colourVal}` }"
            >
              <div
                class="stroke"
                :style="{ width:`${stroke}px`, height:`${stroke}px`, backgroundColor:`${colourVal}` }"
              />
            </div>
          </div>
          <button
            ref="clear"
            @click="clearMode"
          >
            <i class="fal fa-undo" />
          </button>
        </nav>
      </div>
      <button
        class="button success"
        @click="saveImg"
      >
        <i class="fal fa-save" />
        <span>Save</span>
      </button>
    </div>
  </div>
</template>
<script>
import Editor from 'vue-image-markup';
export default {
  components: {
    "Editor": Editor,
  },
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
  data() {
    return{
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
      colourOptions: [
        "#000000",
        "#ff0000",
        "#00ff00",
        "#0000ff",
        "#ffee00",
        "#ff8800",
      ],
      stroke: 2,
      saving: false,
      planImgHasLoaded: false,
      windowResizeTimeout: null,
    }
  },
  mounted() {
    this.plans = this.items;
    this.plansLoaded = true;
    this.scaleCanvasAndPlan();
  },
  methods: {
    scaleCanvasAndPlan() {
      if (! this.planImgHasLoaded) {
        setTimeout(()=>{ this.scaleCanvasAndPlan() }, 1000);
        return false;
      } else {
        const windowWidth = this.$refs.plansContainer.clientWidth,
          windowHeight = this.$refs.plansContainer.clientHeight,
          windowRatio = windowWidth / windowHeight,
          fitFactor = 0.9,
          plan = this.plans[0];
        let planScaleFactor;
        if (plan.aspectRatio > windowRatio) {
          let planWidth = windowWidth * fitFactor;
          this.$set(this.plans[0], 'width', planWidth + 'px');
          planScaleFactor = planWidth / plan.initWidth;
          let planHeight = plan.initHeight * planScaleFactor;
          this.$set(this.plans[0], 'height', planHeight + 'px');
          this.canvas.width = windowWidth;
          this.canvas.height = planHeight / fitFactor;
        } else {
          let planHeight = windowHeight * fitFactor;
          this.$set(this.plans[0], 'height', planHeight + 'px');
          planScaleFactor = planHeight / plan.initHeight;
          let planWidth = plan.initWidth * planScaleFactor;
          this.$set(this.plans[0], 'width', planWidth + 'px');
          this.canvas.height = windowHeight;
          this.canvas.width = planWidth / fitFactor;
        }
        this.$nextTick(()=>{
          this.loaded = true;
          setTimeout(()=>{
            this.clearMode();
          }, 500);
        });
      }
    },
    onPlanImgLoaded() {
      if (! this.planImgHasLoaded) {
        this.planImgHasLoaded = true;
        const plan = this.plans[0];
        plan.initWidth = this.$refs.planImg.clientWidth;
        plan.initHeight = this.$refs.planImg.clientHeight;
        plan.aspectRatio = plan.initWidth / plan.initHeight;
      }
    },
    increaseStroke(){
      this.stroke++;
      this.drawMode();
    },
    decreaseStroke(){
      this.stroke--;
      this.drawMode();
    },
    hideLayer(id){
      console.log('reached hideLayer', this.plans[this.selected].annotations[id])
      this.plans[this.selected].annotations = this.plans[this.selected].annotations.map(annotation => {
        if(annotation.id === annotationId){
          annotation.hidden = !annotation.hidden;
        }
        return annotation;
      })
    },
    deleteLayer(annotationId){
      axios.delete(`/api/generic/media/${this.plans[this.selected].id}/annotations/${annotationId}`)
        .then(resp => {
          this.plans[this.selected].annotations = this.plans[this.selected].annotations.filter(plan => plan.id !== annotationId )
        })
        .catch(err => console.error(err))
    },
    drawMode(){
      this.toolSelected = 'draw';
      this.$refs.editor.set('freeDrawing', { stroke: this.colourVal, strokeWidth: this.stroke });
      this.$refs.drawBtn.focus();
    },
    textMode(){
      this.toolSelected = 'text';
      this.$refs.editor.set('text', { fill: this.colourVal});
    },
    moveMode(){
      this.toolSelected = 'move';
      this.$refs.editor.set('selectMode');
    },
    clearMode(){
      this.$refs.editor.clear();
      this.drawMode();
    },
    updateColour(ev){
      this.colourVal = ev.target.value;
      this.drawMode();
    },
    saveImg(){
      this.$refs.drawBtn.focus();
      this.plans[this.selected].annotations.push(
        {
          content: this.$refs.editor.saveImage(),
          hidden: false,
        }
      );
      this.clearMode();
      this.textMode();

      const media = this.plans[this.selected]
      axios.post(media.add_annotation_endpoint, {annotations: media.annotations})
        .then(resp => {
          this.plans[this.selected].annotations = resp.data.annotations;
        })
        .finally(() => {
          this.clearMode();
        })
    },
  },
}
</script>
