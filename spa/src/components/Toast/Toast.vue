<template>
  <div
    class="bk"
    v-if="isShow"
    @click="tap"
    :style="{
      'animation-duration': this.duration / 1000 + 's',
      'pointer-events': light ? 'none' : 'auto',
    }"
  >
    <div
      class="masklayer"
      :style="{
        background: light ? '' : 'rgba(0, 0, 0, 0.8)',
      }"
    >
      <div
        class="container"
        :style="{
          'animation-duration': this.duration / 1000 + 's',
        }"
      >
        <div class="border">
          <div class="content">{{ content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "Toast",
  props: {
    light: {
      type: Boolean,
      default: false,
    },
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
      isShow: false,
    };
  },
  methods: {
    tap() {
      if (this.light) {
        return;
      }
      this.hide();
    },
    hide() {
      if (!this.isShow) {
        return;
      }
      this.isShow = false;
      this.remove(); //  组件销毁
    },
  },
  mounted() {
    this.isShow = true;
    setTimeout(this.hide, this.duration);
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
  opacity: 0;
  animation-name: show;

  .masklayer {
    // background: rgba(0, 0, 0, 0.8);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999;
    display: flex;
    align-items: flex-start;
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
  .border {
    box-shadow: 0 0 15px 15px rgba(255, 232, 27, 0.1);
  }
  .container {
    flex: 1;
    z-index: 1000;
    background: rgb(255, 232, 27);
    color: #6b6000;
    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
    // box-shadow: 0 0 15px 15px rgba(255, 232, 27, 0.1);
    margin-top: 5rem;
    text-align: center;
    box-shadow: 0 2px 0px 2px rgba(255, 232, 27, 0.1);
    // .content {
    //   padding: 0.5rem;
    //   border-radius: 1rem;

    //   border: 1px dotted rgb(223, 200, 1);
    // }
    // border: 2px solid rgba(233, 132, 17, 0.1);
    // .content {
    //   background: #ffe81b;
    //   padding: 1rem;
    //   border-radius: 0.5rem;
    // }
    animation-name: showmove;
  }
}

@keyframes show {
  0% {
    opacity: 0;
  }

  20% {
    opacity: 1;
  }
  80% {
    opacity: 1;
  }
  99% {
    opacity: 0;
  }
}
@keyframes showmove {
  0% {
    margin-top: -100px;
    transform: scale(0);
  }

  20% {
    margin-top: 2rem;
    transform: scale(1);
  }
  80% {
    margin-top: 2rem;
    transform: scale(1);
  }
  99% {
    margin-top: -100px;
    transform: scale(0);
  }
}
</style>
