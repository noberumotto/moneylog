<template>
  <div class="bk" v-if="isShow">
    <div class="masklayer">
      <div class="container">
        <div class="main">
          <div class="icon">
            <div class="text">
              {{ icon[iconIndex] }}
            </div>
          </div>
          <div class="content" v-if="content">{{ content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "Loading",
  props: {
    content: {
      type: String,
      default: "",
    },
    duration: {
      type: Number,
      default: 2000,
    },
  },
  computed: {
    ...mapState(["user"]),
  },
  data() {
    return {
      icon: ["☕", "✨", "😴", "🍹", "👻", "😎", "🐢", "🦕", "🤳", "🎈", "⌛"],
      isShow: false,
      timer: null,
      iconIndex: 0,
    };
  },
  methods: {
    hide() {
      if (this.isShow) {
        this.isShow = false;
        if (this.timer != null) {
          clearInterval(this.timer);
          this.timer = null;
        }
        this.remove(); //  组件销毁
      }
    },
  },
  mounted() {
    this.isShow = true;
    this.timer = setInterval(() => {
      let index = this.iconIndex + 1;
      if (index == this.icon.length) {
        index = 0;
      }
      this.iconIndex = index;
    }, 1500);
  },
};
</script>

<style lang="less" scoped>
.bk {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  .masklayer {
    background: rgba(0, 0, 0, 0.8);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-right: 2rem;
    .btn {
      padding: 0 2rem;
      background: #fff;
      border-radius: 0.5rem;
      margin-left: 2rem;
      height: 4rem;
      display: flex;
      align-items: center;
    }
    .done {
      padding: 0 3rem;
      background: #4170f3;
      color: #fff;
      // font-size: 2rem;
    }
  }
  .container {
    z-index: 1000;
    background: #ffe81b;
    color: #6b6000;
    margin: 2rem;
    min-width: 10rem;
    min-height: 10rem;
    padding: 2rem;
    // width: 2rem;
    // height: 2rem;
    border-radius: 2.6rem;
    box-shadow: 0 0 15px 15px rgba(255, 232, 27, 0.1);
    // .content {
    //   background: #ffe81b;
    //   padding: 1rem;
    //   border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    // }
    .icon {
      position: relative;
      font-size: 3rem;
      height: 5rem;
      width: 5rem;
      margin: 0 auto;
      .text {
        position: absolute;
        left: 0;
        right: 0;
        width: 3rem;
        margin: auto;
        text-align: center;
        animation: flash 1.5s infinite;
      }
    }
    .content {
      text-align: center;
    }
  }
}

@keyframes flash {
  0% {
    opacity: 0.2;
    margin-top: 0.4rem;
    transform: scale(0.7);
  }

  50% {
    opacity: 1;
    margin-top: 0rem;
    transform: scale(1);
  }
  100% {
    opacity: 0.2;
    margin-top: 0.4rem;
    transform: scale(0.7);
  }
}
</style>
