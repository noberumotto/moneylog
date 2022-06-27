<template>
  <div class="dp">
    <div class="masklayer">
      <div class="container">
        <div class="header-title">选择图标</div>
        <div class="list">
          <div
            class="item"
            v-for="(icon, index) in icons"
            :key="index"
            @click="selectItem = icon"
            :class="{ selected: value == icon }"
          >
            <img
              class="img"
              :src="require('../assets/icon/category/' + icon + '.png')"
            />
          </div>
        </div>
      </div>

      <div class="action">
        <!-- <div class="btn" @click="today">现在</div> -->
        <div class="btn done" @click="onDone">确认</div>
      </div>
    </div>
  </div>
</template>

<script>
import config from "../config";
export default {
  name: "IconPicker",
  props: {
    value: {
      type: String,
    },
  },
  data() {
    return {
      icons: config.icons,
      selectItem: "",
    };
  },
  watch: {
    value(newVal) {
      this.selectItem = newVal;
    },
    selectItem(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$emit("input", this.selectItem);
      }
    },
  },
  methods: {
    onDone() {
      this.$emit("done", this.selectItem);
    },
  },
};
</script>

<style lang="less" scoped>
.list {
  display: flex;
  flex-wrap: wrap;
  margin-top: 1rem;
  .item {
    width: 3rem;
    height: 3rem;
    padding: 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid transparent;

    img {
      width: 1.5rem;
    }
  }
  .selected {
    background: #fff;
    border-radius: 1rem;
    border: 2px solid #4170f3;
    box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    img{
      width: 2rem;
    }
  }
}
.dp {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1999;
  .masklayer {
    background: rgba(0, 0, 0, 0.8);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
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
      font-size: 1rem;
      box-shadow: 0 2px 0px 2px rgba(65, 112, 243, 0.2);
    }
  }
  .container {
    z-index: 1000;
    background: #fff;
    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 3px 0px 2px rgba(65, 112, 243, 0.3);
  }
}
</style>
