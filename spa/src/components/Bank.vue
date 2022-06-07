<template>
  <div class="bk">
    <div class="paytype" @click="isShowList = true">
      <div class="icon">
        <img class="img" :src="require('../assets/icon/paytype.png')" />
      </div>
      {{ selectItem ? selectItem.name : "选择账户" }}
    </div>

    <div class="masklayer" v-show="isShowList" @click="isShowList = false">
      <div class="container">
        <div class="header-title">选择一个{{ action == 0 ? "支出" : "收入" }}账户</div>
        <div class="empty-tip mt" v-if="!selectItem">👻 什么都没有，快去添加一个吧</div>
        <div class="list">
          <div
            class="item"
            @click="onSelect(item)"
            v-for="(item, index) in data"
            :key="index"
            v-show="item.status == action"
          >
            {{ item.name }}
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
  name: "Bank",
  props: {
    data: {
      type: Array,
      default: [], //账户数据
    },
    value: {
      type: Object,
    },
    action: {
      type: Number,
    },
  },
  computed: {
    ...mapState(["user"]),
  },
  data() {
    return {
      isShowList: false,
      selectItem: this.value,
    };
  },
  mounted() {
    // this.selectItem = this.selectValue;
    // console.log(this.selectValue);
  },
  watch: {
    value(newVal) {
      this.selectItem = newVal;
    },
    data(val) {
      if (!this.selectItem) {
        this.checkDefault();
      }
    },
    selectItem(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$emit("input", this.selectItem);
      }
    },
    action(val) {
      this.checkDefault();
    },
  },
  methods: {
    onSelect(val) {
      this.selectItem = val;
      this.isShowList = false;
    },
    checkDefault() {
      this.data.forEach((element) => {
        if (element.status == this.action) {
          this.selectItem = element;
          return;
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.list {
  .item {
    background: #4170f3;
    margin-top: 1rem;
    padding: 1rem;
    color: #fff;
    border-radius: 1rem;
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
  }
}
</style>
