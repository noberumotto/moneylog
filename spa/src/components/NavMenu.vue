<template>
  <div class="bk">
    <div class="menu" @click="show">
      <img class="img" :src="require('../assets/icon/menu.png')" />
    </div>
    <div class="masklayer" v-show="isShowList" @click="hide">
      <div class="container">
        <div
          class="list"
          :class="{
            hide: !isShow,
          }"
        >
          <div
            class="item"
            :class="{
              open: openIndex == index,
              close: isOpen && openIndex != index,
            }"
            @click="onSelect(index)"
            v-for="(item, index) in data"
            :key="index"
            :style="{
              'animation-duration': 0.1 + index * 0.1 + 's',
              background: item.bg ? item.bg : '',
              color: item.color ? item.color : '',
            }"
          >
            <div class="left">
              <div class="icon">
                {{ item.icon }}
                <!-- <img
                  class="img"
                  :src="
                    require('../assets/icon/category/' + item.icon + '.png')
                  "
                /> -->
              </div>
              {{ item.name }}
            </div>
            <div class="right">→</div>
          </div>
        </div>
      </div>

      <!-- <div class="action">
        <div class="btn done">→</div>
      </div> -->
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "NavMenu",
  props: {},
  computed: {
    ...mapState(["user"]),
  },
  data() {
    return {
      openIndex: -1,
      isOpen: false,
      data: [
        {
          name: "账户",
          icon: "💳",
          url: "/bank",
        },
        {
          name: "分类",
          icon: "🏷",
          url: "/category",
        },
        {
          name: "统计",
          icon: "📊",
          bg: "#ffe81b",
          color: "#6b6000",
          url: "/chart",
        },
        {
          name: "明细",
          icon: "📃",
          url: "/list",
          bg: "#fff",
          color: "#161616",
        },
        {
          name: "设置",
          icon: "⚙",
          bg: "#fff",
          url: "/setting",
          color: "#161616",
        },
        {
          name: "退出",
          icon: "👋",
          bg: "#fa4545",
        },
      ],
      isShowList: false,
      timer: null,
      isShow: false,
      isTapItem: false,
    };
  },
  mounted() {},

  methods: {
    show() {
      this.isShow = true;
      this.isShowList = true;
      this.isTapItem = false;
    },
    hide() {
      if (!this.isTapItem) {
        this.isShow = false;
        this.timer = setTimeout(() => {
          this.isShowList = false;
          clearTimeout(this.timer);
          this.timer = null;
        }, 400);
      }
    },
    onSelect(index) {
      this.isTapItem = true;

      let item = this.data[index];
      this.openIndex = index;
      this.isOpen = true;
      this.timer = setTimeout(() => {
        clearTimeout(this.timer);
        this.timer = null;
        if (item.url) {
          this.$router.push(item.url);
        } else if (item.name == "退出") {
          this.$modal({
            title: "提示",
            content: "是否确认退出？",
            confirm: () => {
              this.$loading();
              this.$request.post({
                url: "user/logOut",
                complete: () => {
                  this.$loading.closeAll();
                  this.$store.commit("user/logOut");
                },
              });
            },
            cancel: () => {
              this.isOpen = false;
              this.openIndex = -1;
              this.isTapItem = false;
            },
          });
        }
      }, 500);
      return;
    },
  },
};
</script>

<style lang="less" scoped>
@keyframes close {
  0% {
    opacity: 1;
  }
  30% {
    opacity: 0.5;
  }
  60% {
    opacity: 0.7;
  }
  100% {
    opacity: 0;
  }
}
@keyframes open {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  30% {
    transform: scale(1.2);
    opacity: 1;
  }
  60% {
    transform: scale(2);
    opacity: 1;
  }
  100% {
    transform: scale(26);
    opacity: 0.3;
  }
}
@keyframes showscale {
  0% {
    transform: scale(0);
  }
  30% {
    transform: scale(0.5);
  }
  60% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes showmove {
  0% {
    margin-top: -100px;
  }

  20% {
    margin-top: 2rem;
  }
  100% {
    margin-top: 1rem;
  }
}

@keyframes hidescale {
  0% {
    transform: scale(1);
  }
  30% {
    transform: scale(1.2);
  }
  60% {
    transform: scale(0.5);
  }
  100% {
    transform: scale(0);
  }
}

@keyframes hidemove {
  0% {
    margin-top: 1rem;
    opacity: 1;
  }

  20% {
    margin-top: 2rem;
    opacity: .5;

  }
  100% {
    margin-top: -100px;
    opacity: 0;

  }
}

.menu {
  position: fixed;
  top: -0.5rem;
  left: 0;
  right: 0;
  margin: 0 auto;

  background: #4170f3;
  box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
  width: 3rem;
  height: 3rem;
  padding: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  z-index: 2;
  .img {
    width: 1rem;
  }
}
.hide {
  animation-name: hidemove !important;
  .item {
    animation-name: hidescale !important;
  }
}
.list {
  animation-name: showmove;
  animation-duration: 0.5s;
  animation-fill-mode: forwards;
  .item {
    background: #4170f3;
    margin-top: 1rem;
    padding: 1rem;
    color: #fff;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    animation-duration: 1s;
    animation-name: showscale;
    animation-fill-mode: forwards;
    .left {
      display: flex;
      align-items: center;
      font-size: 1.2rem;
    }
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
  .open {
    animation-name: open;
    animation-duration: 0.5s !important;
    // animation-fill-mode: forwards;
  }
  .close {
    animation-name: close;
    // animation-duration: 0.5s !important;
    // animation-fill-mode: forwards;
  }
}
.bk {
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

    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
  }
}
</style>
