<template>
  <div class="modal" v-show="isShow">
    <div class="masklayer">
      <div
        class="container"
        :class="{
          hide: isHide,
        }"
      >
        <div class="title">✨ {{ title }}</div>
        <div class="content" v-if="content">{{ content }}</div>
        <div class="slot"><slot></slot></div>
      </div>
      <div class="action">
        <div class="btn" @click="action(1)">🙅‍</div>

        <div class="btn done" @click="action(0)">👌</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "Modal",
  props: {
    title: {
      type: String,
      default: "",
    },
    content: {
      type: String,
      default: "",
    },
    confirm: {
      type: Function,
    },
    cancel: {
      type: Function,
    },
  },
  computed: {
    ...mapState(["user"]),
  },
  data() {
    return {
      isShow: false,
      isHide: false,
      timer: null,
    };
  },
  mounted() {
    this.isShow = true;
  },

  methods: {
    action(i) {
      this.isHide = true;
      this.timer = setTimeout(() => {
        clearTimeout(this.timer);
        this.timer = null;
        this.hide();

        if (i == 0 && this.confirm) {
          this.confirm();
        }

        if (i == 1 && this.cancel) {
          this.cancel();
        }
      }, 450);
    },
    hide() {
      if (!this.isShow) {
        return;
      }
      this.isShow = false;
      this.remove(); //  组件销毁
    },
  },
};
</script>

<style lang="less" scoped>
.slot {
  margin-top: 1rem;
}
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999999;
  animation-name: show;
  animation-duration: 1s;
  animation-fill-mode: forwards;
  opacity: 0;
  .title {
    font-weight: bold;
  }
  .masklayer {
    background: rgba(0, 0, 0, 0.8);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999;
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
      box-shadow: 0 1px 0px 2px rgba(210, 210, 211, 0.1);
    }
    .done {
      padding: 0 3rem;
      background: #4170f3;
      color: #fff;
      // font-size: 2rem;
      box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.2);
    }
  }
  .container {
    z-index: 1000;
    background: #fff;
    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 0 15px 15px rgba(255, 232, 27, 0.1);
    animation-name: showmove;
    animation-duration: 0.5s;
    animation-fill-mode: forwards;

    .content {
      background: rgb(255, 232, 27);
      color: #4b4300;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-top: 1rem;
      box-shadow: 0 1px 0px 2px rgba(255, 232, 27, 0.2);
    }
  }
  .hide {
    animation-name: hide !important;
  }
}

@keyframes show {
  0% {
    opacity: 0;
  }

  20% {
    opacity: 1;
  }
  100% {
    opacity: 1;
  }
}
@keyframes showmove {
  0% {
    margin-top: -100px;
    transform: scale(0);
  }

  60% {
    margin-top: 2rem;
    transform: scale(1);
  }
  100% {
    margin-top: 2rem;
    transform: scale(1);
  }
}
@keyframes hide {
  0% {
  }

  60% {
    margin-top: 1rem;
    transform: scale(0.5);
    opacity: 0.8;
  }
  100% {
    margin-top: -20rem;
    transform: scale(0);
    opacity: 0;
  }
}
</style>
