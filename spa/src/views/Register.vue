<template>
  <div>
    <div class="title">注 册<span>Moneylog</span></div>
    <div class="card">
      <div class="input">
        <input
          class="inputbox"
          type="email"
          placeholder="邮箱"
          v-model="email"
          @change="onEmailChange"
        />
      </div>
      <div class="input" v-if="isSendCode">
        <input
          class="inputbox"
          type="text"
          placeholder="验证码"
          v-model="code"
        />
      </div>
      <div class="input">
        <input
          class="inputbox"
          type="password"
          placeholder="密码"
          v-model="password"
        />
      </div>
      <div class="input">
        <input
          type="password"
          placeholder="请重复一次密码"
          v-model="repassword"
          class="inputbox"
        />
      </div>
      <div class="action">
        <div class="btn" @click="reg()">创建账号</div>
      </div>
    </div>

    <div class="menu">
      <div class="text">已有账号</div>

      <div class="text">👇</div>
      <!-- <div class="line"></div> -->

      <div class="btn" @click="$router.push('/login')">登 录</div>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      starting: false,
      info: [],
      locationTimer: null,
      index: 0,
      email: "",
      code: "",
      password: "",
      repassword: "",
      isSendCode: false,
    };
  },
  computed: {
    ...mapState(["client"]),
  },
  mounted() {},
  methods: {
    onEmailChange() {
      if (
        /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(
          this.email
        )
      ) {
        this.$modal({
          title: "询问",
          content:
            "是否确认使用：" +
            this.email +
            "？Moneylog.cloud将向此邮箱发送验证码以确认身份",
          confirm: () => {
            this.sendEmail();
          },
        });
      } else {
        this.$toast({
          content: "邮箱格式不被支持",
        });
      }
    },
    sendEmail() {
      this.$loading();

      this.$request.post({
        url: "ems/send",
        data: {
          email: this.email,
          event: "register",
        },
        success: (res) => {
          this.$toast({
            content: "✨ 验证码已发送，请注意查收",
          });
          this.isSendCode = true;
        },
        complete: () => {
          this.$loading.closeAll();
        },
      });
    },
    reg() {
      if (!this.email || !this.password || !this.repassword) {
        this.$toast({
          content: "请输入正确的信息",
        });
        return;
      }
      if (!this.code) {
        this.$toast({
          content: "请输入验证码",
        });
        return;
      }
      if (
        !/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(
          this.email
        )
      ) {
        this.$toast({
          content: "邮箱格式不正确",
        });
        return;
      }

      if (this.password != this.repassword) {
        this.$toast({
          content: "两次密码输入不一致",
        });
        return;
      }

      this.$loading();

      this.$request.post({
        url: "user/register",
        data: {
          email: this.email,
          password: this.password,
          code: this.code,
        },
        success: (res) => {
          this.$toast({
            content: "👋 欢迎使用 Moneylog.cloud",
          });
          this.$store.commit("user/setLogin", {
            token: res.data.userinfo.token,
            email: res.data.userinfo.email,
          });
          this.$router.push("/");
        },
        complete: () => {
          this.$loading.closeAll();
        },
      });
    },
  },
};
</script>
<style lang="less" scoped>
.title {
  font-size: 1.2rem;
  margin: 2rem;
  span {
    font-size: 1.8rem;
    color: #f5dd0a;
    margin-left: 0.4rem;
  }
}
.card {
  margin: 2rem;
  padding: 2rem;
  // border-radius: 1rem;
  .input {
    margin-bottom: 1rem;
    // input {
    //   border: none;
    //   background: #f3f3f3;
    //   font-size: 1.4rem;
    //   box-sizing: border-box;
    //   width: 100%;
    //   border-radius: 1rem;
    //   padding: 1rem;
    // }
  }
  .action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 2rem;
    .btn {
      background: #f5dd0a;
      color: #6b6000;
      font-size: 1rem;
      padding: 1rem 3rem;
      border-radius: 1rem;
      display: flex;
      box-shadow: 0 2px 0px 2px rgba(255, 232, 27, 0.2);

      align-items: center;
      span {
        margin-left: 1rem;
      }
    }
  }
}
.menu {
  padding: 2rem;
  .line {
    margin: 2rem 8rem;
    height: 1px;
    background: #d1d1d1;
  }
  .text {
    text-align: center;
    color: #afafaf;
    margin-bottom: 2rem;
  }
  .btn {
    padding: 1rem;
    background: #4170f3;
    color: #fff;
    border-radius: 1rem;
    text-align: center;
    box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
  }
}
</style>