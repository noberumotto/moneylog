<template>
  <div class="bk">
    <div class="category" @click="isShowList = true">
      <div class="icon">
        <img
          v-if="selectItem"
          class="img"
          :src="
            require('../assets/icon/category/' + selectItem.icon_name + '.png')
          "
        />
      </div>
      <div class="name">{{ selectItem ? selectItem.name : "选择分类" }}</div>
    </div>

    <div class="masklayer" v-show="isShowList" @click="isShowList = false">
      <div class="container">
        <div class="header-title">
          选择一个{{ action == 0 ? "支出" : "收入" }}分类
        </div>

        <div class="empty-tip mt" v-if="!selectItem">☃ 空空如也</div>
        <div class="list">
          <div
            class="item"
            @click="onSelect(item)"
            v-for="(item, index) in data"
            :key="index"
            v-show="item.status == action"
          >
            <div class="icon">
              <img
                class="img"
                :src="
                  require('../assets/icon/category/' + item.icon_name + '.png')
                "
              />
            </div>
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

const iconArray = ["life", "bus"];
export default {
  name: "Category",
  props: {
    data: {
      type: Array,
      default: [],
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
  mounted() {},
  watch: {
    value(newVal) {
      this.selectItem = newVal;
    },
    selectItem(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$emit("input", this.selectItem);
      }
    },
    data(val) {
      if (!this.selectItem) {
        this.checkDefault();
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
    display: flex;
    align-items: center;
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
  flex: 1;
  .category {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 2rem 1.5rem;
    margin-left: 0;
    flex: 1;
    border-radius: 2rem;
    background: rgb(65, 112, 243);
    color: #fff;
    padding: 1rem;
    box-shadow: 0 0 15px 15px rgba(65, 112, 243, 0.1);
    // box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    .icon {
      background: #fff;
      width: 3rem;
      height: 3rem;
      display: flex;
      justify-content: center;
      border-radius: 1rem;
      align-items: center;
      box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.3);

      .img {
        width: 2rem;
      }
      margin-right: 1rem;
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
