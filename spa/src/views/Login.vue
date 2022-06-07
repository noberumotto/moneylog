<template>
  <div>
    <div class="title">登 录<span>Moneylog</span></div>
    <div class="card">
      <div class="input">
        <input class="inputbox" type="email" placeholder="邮箱" v-model="email" />
      </div>

      <div class="input">
        <input class="inputbox" type="password" placeholder="密码" v-model="password" />
      </div>

      <div class="action">
        <div class="btn" @click="login()"><!-- Go<span>→</span> -->👌</div>
      </div>
    </div>

    <div class="menu">
      <div class="text">纯粹记账体验</div>

      <div class="text">👇</div>
      <!-- <div class="line"></div> -->

      <div class="btn" @click="$router.push('/register')">🦒 注册账号</div>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      email: "",
      password: "",
      starting: false,
      info: [],
      locationTimer: null,
      index: 0,
    };
  },
  computed: {
    ...mapState(["client"]),
  },
  mounted() {},
  methods: {
    login() {
      if (
        !/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(
          this.email
        )
      ) {
        this.$toast({
          content: "😉 请输入正确的邮箱",
        });
        return;
      } else if (!this.password) {
        this.$toast({
          content: "🤔 暗号是什么呢",
        });
        return;
      }
      this.$loading();
      this.$request.post({
        url: "user/login",
        data: {
          email: this.email,
          password: this.password,
        },
        handle: true,
        success: (res) => {
          this.$toast({
            content: "👋 欢迎使用 Moneylog.cloud",
            light: true,
          });
          this.$store.commit("user/setLogin", {
            token: res.data.userinfo.token,
            email: this.email,
          });
          this.$router.push("/");
        },
        fail: () => {
          this.$toast({
            content: "🦀 噢欧，远方似乎没有传来回应。检查一下暗号是否正确",
          });
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
    color: #4170f3;
    margin-left: 0.4rem;
  }
}
.card {
  // background: #fff;
  margin: 2rem;
  padding: 2rem;
  // border-radius: 1rem;
  .input {
    margin-bottom: 1rem;
    // input {
    //   border: none;
    //   background: rgb(242, 242, 242);
    //   font-size: 1.4rem;
    //   box-sizing: border-box;
    //   width: 100%;
    //   border-radius: 1rem;
    //   padding: 1rem;
    //   // box-shadow: 0 2px 0px 2px rgba(196, 195, 195, 0.1);
    // }
  }
  .action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 2rem;
    .btn {
      background: #4170f3;
      color: #fff;
      font-size: 1rem;
      padding: 1rem 3rem;
      border-radius: 1rem;
      display: flex;
      box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
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
    background: #ffe81b;
    color: #6b6000;
    border-radius: 1rem;
    text-align: center;
    box-shadow: 0 2px 0px 2px rgba(255, 232, 27, 0.2);
  }
}
</style>