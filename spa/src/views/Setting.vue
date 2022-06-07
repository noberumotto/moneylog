<template>
  <div>
    <title-bar subtitle="" title="设置" />

    <div class="main">
      <div class="wellcome"><img src="../assets/icon.png"/>Moneylog.cloud<label></label></div>
      <touch-view>
        <div class="card" style="margin-top: 0">
          <div class="hi">Hi! 👋</div>
          <div class="email">{{ email }}</div>
        </div>
      </touch-view>

      <div class="setting-list">
        <div class="item" @click="$router.push('/password')">
          <div class="icon">🔐</div>
          <div class="name">更新密码</div>
        </div>
        <div class="item" @click="$router.push('/import')">
          <div class="icon">📥</div>
          <div class="name">导入账单</div>
        </div>
        <div class="item" @click="$router.push('/waitsync')">
          <div class="badge" v-if="waitSyncNum > 0">{{ waitSyncNum }}</div>
          <div class="icon">🌨</div>
          <div class="name">待同步账单</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
import Dialog2 from "../components/Dialog2.vue";
import NavMenu from "../components/NavMenu.vue";
import ScopeButtons from "../components/ScopeButtons.vue";
import TitleBar from "../components/TitleBar.vue";
import TouchView from "../components/TouchView/TouchView.vue";

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons, TouchView },
  data() {
    return {
      email: "",
      waitSyncNum: 0,
    };
  },
  computed: {},
  mounted() {
    this.email = localStorage.getItem("email");

    let data = localStorage.getItem("waitSyncData")
      ? JSON.parse(localStorage.getItem("waitSyncData"))
      : [];

    this.waitSyncNum = data.length;
  },
  methods: {},
};
</script>
<style lang="less" scoped>
.main {
  padding: 0 2rem;
  margin-top: 2rem;
  .touch-view{
    margin-top: 1rem;
    border-radius: 1rem;
  }
}
.wellcome {
  font-size: 1.2rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  // justify-content: space-between;
  label {
    font-size: 0.9rem;
    font-weight: normal;
    margin-left: 0.5rem;
  }
  img{
    width: 2.5rem;
    margin-right: 1rem;
  }
}
.email {
  color: #999;
  margin-top: 1rem;
}
.setting-list {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 1rem;
  justify-content: space-between;
  .item {
    width: 7rem;
    height: 6rem;
    border-radius: 1rem;
    padding: 1rem;
    background: #fff;
    margin-top: 1rem;
    position: relative;

    .name {
      margin-top: 0.5rem;
      font-size: 0.9rem;
      text-align: center;
      color: rgb(59, 59, 59);
    }
    .icon {
      text-align: center;
      font-size: 1.2rem;
    }
  }
}
</style>