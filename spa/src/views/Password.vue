<template>
  <div>
    <title-bar subtitle="" title="更新密码" />

    <div class="main">
      <div class="card">
        <div class="label">新密码</div>
        <div class="input">
          <input
            class="inputbox"
            type="password"
            placeholder="新密码"
            v-model="newPassword"
          />
        </div>
        <div class="label">输入旧密码以验证身份</div>

        <div class="input">
          <input
            class="inputbox"
            type="password"
            placeholder="旧密码"
            v-model="oldPassword"
          />
        </div>

        <div class="action">
          <div class="button max yellow" @click="go()">更新</div>
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

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons },
  data() {
    return {
      newPassword: "",
      oldPassword: "",
    };
  },
  computed: {},
  mounted() {},
  methods: {
    go() {
      if (!this.newPassword || !this.oldPassword) {
        this.$toast({
          content: "请输入正确的信息",
        });
        return;
      }
      if (this.newPassword == this.oldPassword) {
        this.$toast({
          content: "新密码与旧密码重复",
        });
        return;
      }
      if (this.newPassword.length < 6 || this.newPassword.length > 30) {
        this.$toast({
          content: "密码应该在6~30位之间哦",
        });
        return;
      }
      this.$request.post({
        url: "user/resetpwd",
        data: {
          newPassword: this.newPassword,
          oldPassword: this.oldPassword,
        },
        handle: true,
        success: (res) => {
          localStorage.setItem("Token", res.data);

          this.$toast({
            content: "🔐✨ 密码已更新",
          });
          this.newPassword = "";
          this.oldPassword = "";
        },
        fail: () => {
          this.$toast({
            content: "操作失败",
          });
        },
      });
    },
  },
};
</script>
<style lang="less" scoped>
.main {
  padding: 0 2rem;
  margin-top: 2rem;
  .action {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  .label {
    margin: 0.8rem 0;
    color: rgb(49, 49, 49);
  }
}
</style>