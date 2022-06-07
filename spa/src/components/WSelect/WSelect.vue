<template>
  <div class="bk" v-show="isShow">
    <div class="masklayer" @click="hide()" :class="{
          hideop: isHide,
        }">
      <div
        class="container"
        :class="{
          hide: isHide,
        }"
      >
        <div class="header-title">
          {{ title ? title : "请选择" }}
        </div>
        <div class="close">❌</div>
        <div class="empty-tip mt" v-if="data.length == 0">🐌 什么都没有</div>
        <div class="list">
          <div
            class="item"
            v-for="(item, index) in data"
            :key="index"
            @click="select(item)"
          >
            <div class="icon" v-if="showIcon && item[showIcon]">
              <img
                class="img"
                :src="
                  require('../../assets/icon/category/' +
                    item[showIcon] +
                    '.png')
                "
              />
            </div>
            {{ item[showName] }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "WSelect",
  props: {
    title: {
      type: String,
    },
    data: {
      type: Array,
      default: [],
    },
    showName: {
      type: String,
      default: "name",
    },
    showIcon: {
      type: String,
      default: "icon_name",
    },
    res: {
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
  watch: {},
  methods: {
    select(item) {
      this.hide();
      if (this.res) {
        this.res(item);
      }
    },
    hide() {
      if (!this.isShow) {
        return;
      }

      this.isHide = true;
      this.timer = setTimeout(() => {
        clearTimeout(this.timer);
        this.timer = null;
        this.isShow = false;
        this.remove(); //  组件销毁
      }, 300);
    },
  },
};
</script>

<style lang="less" scoped>
.hideop{
  animation-name: hide;
    animation-duration: 0.3s;
    animation-fill-mode: forwards;
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
@keyframes hide {
  0% {
    opacity: 1;
  }

  20% {
    opacity: .5;
  }
  100% {
    opacity: 0;
  }
}
@keyframes showmove {
  0% {
    margin-top: 5rem;
    transform: scale(0);
  }

  60% {
    margin-top: 2rem;
    transform: scale(1.05);
  }
  100% {
    margin-top: 2rem;
    transform: scale(1);
  }
}
@keyframes hidemove {
  0% {
  }

  60% {
    margin-top: 1rem;
    transform: scale(0.5);
    opacity: 0.5;
  }
  100% {
    margin-top: 5rem;
    transform: scale(0);
    opacity: 0;
  }
}
.close {
  position: absolute;
  right: 2rem;
  top: 1.5rem;
}
.list {
  margin: 2rem;
  position: absolute;
  overflow: scroll;
  top: 2rem;
  left: 0;
  right: 0;
  bottom: 0;
  .item {
    background: #4170f3;
    margin-top: 1rem;
    padding: 1rem;
    color: #fff;
    display: flex;
    align-items: center;
    border-radius: 1rem;
    .icon {
      background: #fff;
      width: 3rem;
      height: 3rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 1rem;
      margin-right: 1rem;
    }
    .img {
      width: 2rem;
    }
  }
}
.bk {
  .paytype {
    width: 8rem;
    font-size: 1.4rem;
    margin: 2rem 1.5rem;
    border-radius: 2rem;
    background: #ffe81b;
    color: #6b6000;
    padding: 1.5rem 1rem;
    box-shadow: 0 0 15px 15px rgba(255, 238, 3, 0.2);
    margin-right: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;

    .icon {
      background: #fff;
      position: absolute;
      top: -1.8rem;
      padding: 0.5rem;
      border-radius: 1rem;
      z-index: -1;
      .img {
        width: 2rem;
      }
    }
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
      background: #ffe81b;
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
      font-size: 2rem;
    }
  }
  .container {
    z-index: 1000;
    background: #fff;
    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    overflow: scroll;
    animation-name: showmove;
    animation-duration: 0.3s;
    animation-fill-mode: forwards;
    .header-title {
      background: #fff;
      // padding: 1rem 0;
      margin-top: 0.5rem;
    }
  }
  .hide {
    animation-name: hidemove !important;
    animation-duration: 0.2s;
    animation-fill-mode: forwards;
  }
}
</style>
