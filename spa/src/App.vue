<template>
  <div id="app">
    <router-view />
  </div>
</template>
<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      maxscreen: false,
    };
  },
  computed: {
    ...mapState(["user"]),
  },
  mounted() {
    let html = document.getElementsByTagName("html")[0];

    let width = document.body.clientWidth;
    if (width > 1600) {
      this.maxscreen = true;
    }
    let fontSize = (width / 1080) * 40;
    html.style.fontSize = fontSize + "px";
    window.$fontSize = fontSize;
  },
  methods: {},
};
</script>
<style>
body {
  margin: 0;
  background: #f1f3f4;
}

* {
  box-sizing: border-box;
  font-family: "Google Sans", "Roboto", "Helvetica Neue", Arial, sans-serif;
  outline: none;

  /* font-family: "simsun" !important; */
}

#app {
  color: #000;
  font-size: 1rem;
  padding-bottom: 2rem;
}
input {
  outline: none;
  -webkit-appearance: none;
}

.button {
  background: rgb(65, 112, 243) !important;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}
.max {
  padding: 0.8rem 2rem;
}

.yellow {
  background: rgb(255, 232, 27) !important;
  color: #6b6000 !important;
  box-shadow: 0 1px 0px 2px rgba(230, 208, 15, 0.2) !important;
}
.inputbox {
  border: 2px solid transparent;
  background: #f3f3f3;
  font-size: 1.4rem;
  box-sizing: border-box;
  width: 100%;
  border-radius: 1rem;
  padding: 1rem;
}
.inputbox:focus {
  animation-name: inputfocus;
  animation-duration: 0.6s;
  animation-fill-mode: forwards;
}
@keyframes inputfocus {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    background: rgba(65, 112, 243, 0.1);
    border: 2px solid #4170f3;
    box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    -webkit-box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    transform: scale(1.05);
  }
}

.flex {
  display: flex;
  align-items: center;
}
.wicon {
  width: 2rem;
}
.ml {
  margin-left: 1rem;
}
.mr {
  margin-right: 1rem;
}
.mt {
  margin-top: 1rem;
}
.pd {
  padding: 1rem;
}
.in-text {
  color: #4170f3 !important;
}
.out-text {
  color: #ac9a00 !important;
}
.red-text {
  color: #fa4545 !important;
}
.green-text {
  color: #25bb07 !important;
}
.badge {
  position: absolute;
  background: #fa4545;
  right: 0;
  top: -0.5rem;
  color: #fff;
  padding: 0.2rem 0.6rem;
  border-radius: 1rem;
}
.card {
  background: #fff;
  margin-top: 1rem;
  border-radius: 1rem;
  padding: 1rem;
  box-shadow: 0 5px 0px 2px rgba(216, 210, 210, 0.1);
}
.header-title {
  font-size: 1.1rem;
  color: #000;
  font-weight: bold;
}
.del {
  text-decoration: line-through;
  color: rgb(180, 180, 180) !important;
  /* font-size: .8rem !important; */
}
.empty-tip {
  background: #fcf8da;
  color: #6b6000;
  padding: 1rem;
  border-radius: 1rem;
  font-size: 0.8rem;
  box-shadow: 0 5px 0px 2px rgba(250, 247, 59, 0.2);
}
</style>
