<template>
  <div class="titlebar">
    <div class="main">
      <div class="btn" @click="back">←</div>
      <div class="title">
        {{ subtitle }}<span>{{ title }}</span>
      </div>
    </div>

    <div class="minibar" v-if="isFixed">
      <div class="btn" @click="back">←</div>
      <div class="title">
        {{ subtitle }}<span>{{ title }}</span>
      </div>
    </div>
    <div v-if="!isFixed">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  name: "TitleBar",
  props: {
    title: {
      type: String,
    },
    subtitle: {
      type: String,
    },
  },
  data: () => {
    return {
      isFixed: false,
    };
  },
  mounted() {
    window.addEventListener("scroll", this.scroll);
  },
  destroyed() {
    window.removeEventListener("scroll", this.scroll);
  },
  methods: {
    scroll(e) {
      let scrollTop =
        document.body.scrollTop || document.documentElement.scrollTop;

      let titlebarHeight =
        this.$el.getBoundingClientRect().height + window.$fontSize * 2;

      if (scrollTop > titlebarHeight) {
        //  开始隐藏
        this.isFixed = true;
      } else {
        this.isFixed = false;
      }
    },
    back() {
      this.$emit("back")
      this.$router.go(-1);
    },
  },
};
</script>

<style lang="less" scoped>
.minibar {
  position: fixed;
  background: #fff;
  left: 0;
  right: 0;
  z-index: 1999;
  top: 0;
  padding: 0.7rem 2rem;
  border-bottom: 1px solid rgb(231, 231, 231);
  box-shadow: 0 2px 2px 2px rgba(216, 210, 210, 0.1);

  .btn {
    z-index: 2;
    background: #fff;
    border-radius: 0.5rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    color: rgb(94, 94, 94);
    justify-content: center;
    box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.2);
  }
  .title {
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    font-size: 1.2rem;
    font-weight: bold;
    justify-content: center;
  }
}
.titlebar {
  display: flex;
  align-items: center;
  margin: 1rem 2rem;
  justify-content: space-between;
  // position: fixed;
  .main {
    display: flex;
    align-items: center;
    .btn {
      background: #fff;
      border-radius: 0.5rem;
      width: 3rem;
      height: 3rem;
      display: flex;
      align-items: center;
      color: rgb(94, 94, 94);
      justify-content: center;
      box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.1);
    }
    .title {
      font-size: 1.1rem;
      font-weight: bold;
      margin-left: 1rem;
      span {
        font-size: 1.4rem;
        color: #4170f3;
        margin-left: 0.4rem;
      }
    }
  }
}
</style>