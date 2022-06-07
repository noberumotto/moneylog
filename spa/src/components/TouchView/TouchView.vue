<template>
  <div
    class="touch-view"
    @touchstart="touchstart"
    @touchend="touchend"
    @touchmove="touchmove"
    :class="{ touch: isTouch }"
  >
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "TouchView",
  props: {
    time: {
      type: Number,
      default: 1,
    },
  },

  data() {
    return {
      timer: null,
      isTouch: false,
      isLongTap: false,
      lastTouchEnd: 0,
    };
  },
  methods: {
    touchstart() {
      this.isLongTap = false;

      this.clearTimer();
      this.isTouch = true;
      let seconds = 0;
      this.timer = setInterval(() => {
        seconds++;
        if (seconds >= this.time) {
          this.isLongTap = true;
          this.$emit("longtap");
          this.clearTimer();
        }
      }, 500);
    },
    touchmove() {
      this.clearTimer();
    },
    touchend(e) {
      this.isTouch = false;

      this.clearTimer();
      if (!this.isLongTap) {
        this.$emit("click");
      }

      let now = new Date().getTime();
      if (now - this.lastTouchEnd <= 300) {
        this.$emit("doubleclick");
        e.preventDefault();
      }
      this.lastTouchEnd = now;
    },
    clearTimer() {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    },
  },
  mounted() {},
};
</script>

<style lang="less" scoped>
.touch {
  border: 0.2rem solid rgb(255, 232, 27);
  box-shadow: 0 5px 0px 2px rgba(216, 210, 210, 0.4);
}
</style>
