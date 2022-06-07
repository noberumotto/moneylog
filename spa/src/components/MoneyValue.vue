<template>
  <div
    class="money-value"
    @click.stop="click"
    :class="{
      'red-text': autoColor && money > 0,
      'green-text': autoColor && money < 0,
    }"
  >
    {{ symobl }} {{ money }}
    <div class="unit" v-show="isShowUnit">
      <div class="text">{{ unit }}</div>
      <label></label>
    </div>
  </div>
</template>

<script>
export default {
  name: "MoneyValue",
  props: {
    money: {
      type: Number | Object,
    },
    symobl: {
      type: String,
    },
    autoColor: {
      type: Boolean,
      default: false,
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
    money: function (val) {
      this.unit = this.$common.getUnit(val);
    },
  },
  mounted() {},

  methods: {
    click: function (e) {
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
.money-value {
  display: inline-block;
  position: relative;
}
.unit {
  position: absolute;
  top: -2rem;
  font-size: 0.8rem;
  background: rgb(255, 232, 27);
  color: #6b6000;
  z-index: 1;
  padding: 0.2rem 0.5rem;
  border-radius: 0.2rem;
  box-shadow: 0 1px 0 2px rgba(218, 197, 15, 0.6);
  label {
    position: absolute;
    top: 1.2rem;
    width: 0;
    height: 0;
    left: 0;
    right: 0;
    margin: 0 auto;
    z-index: -1;
    border-width: 1rem 0.5rem 0.5rem;
    border-style: solid;
    border-color: #ffe81b transparent transparent;
  }
}
</style>
