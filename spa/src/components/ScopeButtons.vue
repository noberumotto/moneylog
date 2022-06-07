<template>
  <div class="scope-buttons">
    <div
      class="item"
      v-for="(name, index) in data"
      :key="index"
      @click="onSelect(index)"
      :class="{ selected: selectIndex == index }"
    >
      {{ name }}
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "ScopeButtons",
  props: {
    data: {
      type: Array,
      default: [],
    },
    value: {
      type: Number,
      default: 0,
    },
  },

  data() {
    return {
      selectIndex: this.value,
    };
  },
  mounted() {},
  watch: {
    value(newVal) {
      this.selectIndex = newVal;
    },
    selectIndex(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$emit("input", this.selectIndex);
      }
    },
  },
  methods: {
    onSelect(val) {
      this.selectIndex = val;
      this.$emit("change");
    },
  },
};
</script>

<style lang="less" scoped>
.scope-buttons {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 0.4rem;
  border-radius: 0.8rem;
  box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.1);

  .item {
    padding: 0.4rem 0.6rem;
    color: #a7a7a7;
    flex: 1;
    text-align: center;
  }
  .selected {
    background: #ebebeb;
    color: rgb(59, 59, 59);
    border-radius: 0.4rem;
    box-shadow: 0 2px 2px 0px rgba(168, 168, 168, 0.5);
    // box-shadow: 0 2px 2px 0px rgba(65, 112, 243, 0.3);
  }
}
.list {
  .item {
    background: rgb(65, 112, 243);
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
