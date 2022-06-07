<template>
  <div class="tooltip">
    <div class="container">
      <div class="text">{{ content }}</div>
      <label></label>
    </div>
  </div>
</template>

<script>
export default {
  name: "ToolTip",
  props: {
    content: {
      type: String | Object,
    },
  },
  data() {
    return {
      unit: "",
      isShowUnit: false,
      timer: null,
    };
  },
  watch: {
    // money: function (val) {
    //   this.unit = this.$common.getUnit(val);
    // },
  },
  mounted() {},

  methods: {
    click: function () {
      return;
      if (!this.unit) {
        this.unit = this.$common.getUnit(this.money);
      }
      if (!this.unit) {
        return;
      }
      if (this.timer != null) {
        clearTimeout(this.timer);
        this.timer = null;
      }

      if (this.isShowUnit) {
        this.isShowUnit = false;
        return;
      }
      this.isShowUnit = true;
      this.timer = setTimeout(() => {
        this.isShowUnit = false;
        clearTimeout(this.timer);
        this.timer = null;
      }, 1500);
    },
  },
};
</script>

<style lang="less" scoped>
.tooltip {
  // display: inline-block;
  position: absolute;
  z-index: 999;
}
.container {
  top: -2rem;
  font-size: 0.8rem;
  background: rgb(255, 232, 27);
  color: #6b6000;
  padding: 0.2rem 0.5rem;
  border-radius: 0.2rem;
  max-width: 6rem;
  box-shadow: 0 1px 0 2px rgba(218, 197, 15, 0.6);

  label {
    position: absolute;
    bottom: -1rem;
    width: 0;
    height: 0;
    left: 0.1rem;
    margin: 0 auto;
    border-width: 0.8rem 0.4rem 0.4rem;
    border-style: solid;
    border-color: #ffe81b transparent transparent;
  }
}
</style>
