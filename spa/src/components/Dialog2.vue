<template>
  <div class="bk">
    <div class="masklayer">
      <div class="container">
        <div class="header-title">{{ title }}</div>
        <div class="content" v-if="content">{{ content }}</div>
        <div class="slot"><slot></slot></div>
      </div>
      <div class="action">
        <div class="btn" @click="$emit('no')">🙅‍</div>

        <div class="btn button" @click="$emit('yes')">👌</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "Dialog2",
  props: {
    title: {
      type: String,
      default: "",
    },
    content: {
      type: String,
      default: "",
    },
  },
  computed: {
    ...mapState(["user"]),
  },
  data() {
    return {};
  },
  mounted() {},

  methods: {},
};
</script>

<style lang="less" scoped>
.slot {
  margin-top: 1rem;
}
.bk {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
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
    background: #fff;
    margin: 2rem;
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 0 15px 15px rgba(255, 232, 27, 0.1);
    .content {
      background: rgb(255, 232, 27);
      padding: 1rem;
      border-radius: 0.5rem;
      margin-top: 1rem;
    }
  }
}
</style>
