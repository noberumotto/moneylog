<template>
  <div>
    <nav-menu />
    <date-picker v-on:done="selectDate" v-show="isShowDatePicker" />
    <div class="time" @click="isShowDatePicker = true">
      <img class="img" :src="require('../assets/icon/time.png')" />{{
        isToday ? "今天" : time[0] + "/" + time[1] + "/" + time[2]
      }}
      {{ time[3] }}:{{ time[4] }}
    </div>

    <div class="head">
      <div class="paytype" @click="chooseAccount">
        <div class="icon">
          <img class="img" :src="require('../assets/icon/paytype.png')" />
        </div>
        {{ selectedBank ? selectedBank.name : "选择账户" }}
      </div>
      <div class="category" @click="chooseTag">
        <div class="icon">
          <img
            v-if="selectedCategory"
            class="img"
            :src="
              require('../assets/icon/category/' +
                selectedCategory.icon_name +
                '.png')
            "
          />
        </div>
        <div class="name">
          {{ selectedCategory ? selectedCategory.name : "选择分类" }}
        </div>
      </div>
    </div>
    <div class="input">
      <!-- <div class="action" :class="{ 'action-out': action == 0 }">
        {{ action == 1 ? "收入" : "支出" }}
      </div> -->
      <div class="money" v-if="input.length != 0">
        <div class="unit" v-if="unit">{{ unit }}</div>
        <div class="value">{{ money }}</div>
      </div>
      <touch-view
        :class="{ backspace: action == 0 }"
        class="btn"
        v-on:click="onInput('-')"
        v-on:longtap="onInput('÷')"
        @touchend="inputActionTouchend($event, '-')"
      >
        -
      </touch-view>

      <div
        :class="{ out: action == 0 }"
        class="value"
        ref="input"
        :style="{
          fontSize: inputFontSize + 'rem',
        }"
        v-on:click="onBackspace()"
        @touchend="inputActionTouchend($event, '<')"
      >
        {{ input }}<label></label>
      </div>
      <touch-view
        class="btn"
        :class="{ backspace: action == 0 }"
        v-on:click="onInput('+')"
        v-on:longtap="onInput('×')"
        @touchend="inputActionTouchend($event, '+')"
      >
        +
      </touch-view>
    </div>

    <div class="keyboard">
      <div class="line">
        <div
          class="btn"
          v-on:click="onInput(1)"
          @touchend="inputActionTouchend($event, '1')"
        >
          1
        </div>
        <div
          class="btn"
          v-on:click="onInput(2)"
          @touchend="inputActionTouchend($event, '2')"
        >
          2
        </div>
        <div
          class="btn"
          v-on:click="onInput(3)"
          @touchend="inputActionTouchend($event, '3')"
        >
          3
        </div>
      </div>
      <div class="line">
        <div
          class="btn"
          v-on:click="onInput(4)"
          @touchend="inputActionTouchend($event, '4')"
        >
          4
        </div>
        <div
          class="btn"
          v-on:click="onInput(5)"
          @touchend="inputActionTouchend($event, '5')"
        >
          5
        </div>
        <div
          class="btn"
          v-on:click="onInput(6)"
          @touchend="inputActionTouchend($event, '6')"
        >
          6
        </div>
      </div>
      <div class="line">
        <div
          class="btn"
          v-on:click="onInput(7)"
          @touchend="inputActionTouchend($event, '7')"
        >
          7
        </div>
        <div
          class="btn"
          v-on:click="onInput(8)"
          @touchend="inputActionTouchend($event, '8')"
        >
          8
        </div>
        <div
          class="btn"
          v-on:click="onInput(9)"
          @touchend="inputActionTouchend($event, '9')"
        >
          9
        </div>
      </div>
      <div class="line">
        <div
          class="btn"
          v-on:click="onInput('.')"
          @touchend="inputActionTouchend($event, '.')"
        >
          .
        </div>
        <div
          class="btn"
          v-on:click="onInput(0)"
          @touchend="inputActionTouchend($event, '0')"
        >
          0
        </div>
        <div :class="{ out: action == 0 }" class="btn post" @click="addlog">
          <div class="text">
            {{ action == 0 ? "支出" : "收入" }}
          </div>
          {{ action == 0 ? "↑" : "↓" }}
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
import BigNumber from "bignumber.js";
import DatePicker from "../components/DatePicker.vue";
import Bank from "../components/Bank.vue";
import Category from "../components/Category.vue";
import NavMenu from "../components/NavMenu.vue";
import data from "../library/data";
import TouchView from "../components/TouchView/TouchView.vue";
import calculator from "../library/calculator";
import input from "../library/input";

export default {
  components: { DatePicker, Bank, Category, NavMenu, TouchView },
  data() {
    let date = new Date();
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let day = date.getDate();

    hours = hours < 10 ? "0" + hours : hours;
    minutes = minutes < 10 ? "0" + minutes : minutes;

    return {
      action: 0, // 操作：0=支出，1=收入
      money: "", // 最终金额
      unit: "", // 金额单位
      input: "", // 输入
      inputFontSize: 3, // 输入字体大小
      isShowDatePicker: false,
      time: [year, month, day, hours, minutes],
      isToday: true,
      isShowBank: false,
      selectedBank: null, // 当前选中账户
      selectedCategory: null, //  当前选中分类
      syncTimer: null,
      combo: 0,
      inCategoryList: [], //  收入分类
      outCategoryList: [], //  支出分类
      inAccountList: [], //  收入账户
      outAccountList: [], //  支出账户
      lastTouchEnd: 0,
    };
  },
  computed: {
    ...mapState(["user"]),
  },

  mounted() {
    this.input = input.getInputStr();
    this.getCategoryData();
    this.getAccountData();

    this.loadSelect();
  },
  destroyed() {
    //  保存当前选择的时间
    this.$store.commit("user/update", {
      selectedTime: this.time,
      selectedAction: this.action,
    });
  },
  watch: {
    action: function () {
      this.loadSelect();
    },
    time: function (val) {
      let date = new Date();
      let year = date.getFullYear();
      let month = date.getMonth() + 1;

      let day = date.getDate();

      this.isToday = val[0] == year && val[1] == month && val[2] == day;
    },
    money: function (val) {
      this.getunit();
    },
    input: function (newVal, oldVal) {
      // 计算结果
      if (newVal.length != 0) {
        this.money = calculator.evaluate(this.input);
      } else {
        this.money = 0;
      }

      let t = setTimeout(() => {
        this.$refs.input.scrollLeft = this.$refs.input.scrollWidth;
        clearTimeout(t);
      }, 10);

      // 刷新字体大小
      let newFontSize = 0;
      let newLength = newVal.length;
      let oldLength = oldVal.length;
      if (newLength > oldLength && newLength > 6) {
        newFontSize = new BigNumber(this.inputFontSize).minus(0.2).toNumber();
      } else {
        newFontSize = new BigNumber(this.inputFontSize).plus(0.2).toNumber();
      }

      if (newFontSize <= 1) {
        newFontSize = 1;
      } else if (newFontSize > 3) {
        newFontSize = 3;
      }

      if (newFontSize != this.inputFontSize) {
        this.inputFontSize = newFontSize;
      }
    },
  },

  methods: {
    //  加载选择项
    loadSelect() {
      let state = this.$store.state.user;

      this.action = state.selectedAction;

      if (state.selectedTime.length) {
        this.time = state.selectedTime;
      }

      let category =
        this.action == 0 ? this.outCategoryList : this.inCategoryList;
      let account = this.action == 0 ? this.outAccountList : this.inAccountList;

      if (this.action == 0) {
        //  支出

        if (state.selectedOutCategory.id) {
          this.selectedCategory = state.selectedOutCategory;
        } else {
          this.selectedCategory = category ? category[0] : null;
        }

        if (state.selectedOutAccount.id) {
          this.selectedBank = state.selectedOutAccount;
        } else {
          this.selectedBank = account ? account[0] : null;
        }
      } else {
        //  收入
        if (state.selectedInAccount.id) {
          this.selectedBank = state.selectedInAccount;
        } else {
          this.selectedBank = account ? account[0] : null;
        }

        if (state.selectedInCategory.id) {
          this.selectedCategory = state.selectedInCategory;
        } else {
          this.selectedCategory = category ? category[0] : null;
        }
      }
    },
    inputActionTouchend(e, input) {
      //  防止浏览器双击放大
      let now = new Date().getTime();
      if (now - this.lastTouchEnd <= 300) {
        this.onInput(input);
        e.preventDefault();
      }
      this.lastTouchEnd = now;
    },
    chooseAccount() {
      this.$wselect({
        title: "请选择" + (this.action == 0 ? "支出账户" : "收入账户"),
        data: this.action == 0 ? this.outAccountList : this.inAccountList,
        res: (res) => {
          this.selectedBank = res;

          //  保存state
          let state =
            this.action == 0
              ? {
                  selectedOutAccount: res,
                }
              : { selectedInAccount: res };

          this.$store.commit("user/update", state);
        },
      });
    },
    chooseTag() {
      this.$wselect({
        title: "请选择" + (this.action == 0 ? "支出分类" : "收入分类"),
        data: this.action == 0 ? this.outCategoryList : this.inCategoryList,
        res: (res) => {
          this.selectedCategory = res;

          //  保存state
          let state =
            this.action == 0
              ? {
                  selectedOutCategory: res,
                }
              : { selectedInCategory: res };

          this.$store.commit("user/update", state);
        },
      });
    },
    getCategoryData() {
      data.getCategoryData({
        success: (res) => {
          res.forEach((element) => {
            if (element.status == 0) {
              //支出
              this.outCategoryList.push(element);
            } else {
              this.inCategoryList.push(element);
            }
          });

          this.loadSelect();
        },
      });
    },
    getAccountData() {
      data.getAccountData({
        success: (res) => {
          res.forEach((element) => {
            if (element.status == 0) {
              //支出
              this.outAccountList.push(element);
            } else {
              this.inAccountList.push(element);
            }
          });

          this.loadSelect();
        },
      });
    },
    selectDate(e) {
      this.isShowDatePicker = false;
      this.time = e;
    },
    onBackspace() {
      this.onInput("<");
    },
    onInput(val) {
      if (val == "-" || val == "+") {
        //  处理切换收支模式
        if (this.input.length == 0) {
          //  保存state
          this.$store.commit("user/update", {
            selectedAction: val == "-" ? 0 : 1,
          });

          this.action = val == "-" ? 0 : 1;
        }
      }
      this.input = input.input(val);
    },
    getunit() {
      this.unit = this.$common.getUnit(this.money);
    },
    addlog() {
      if (!this.selectedBank) {
        this.$toast({
          content: "🗃 请选择一个账户",
          light: true,
        });
        return;
      }
      if (!this.selectedCategory) {
        this.$toast({
          content: "📂 请选择一个分类",
          light: true,
        });
        return;
      }

      if (this.money <= 0) {
        this.$toast({
          content: "🤔 似乎这笔帐算错了",
          light: true,
        });
        return;
      }
      if (this.money < 0.01) {
        this.$toast({
          content: "😶 太小了",
          light: true,
        });
        return;
      }
      if (this.money >= 100000000) {
        this.$toast({
          content: "😨 暂时无法处理这笔巨款",
          light: true,
        });
        return;
      }

      let time = Number(
        new Date(
          this.time[0],
          this.time[1] - 1,
          this.time[2],
          this.time[3],
          this.time[4]
        )
      );

      time = time / 1000;

      let item = {
        money: this.money,
        account_id: this.selectedBank.id,
        tags_id: this.selectedCategory.id,
        status: this.action,
        time: time,
      };

      //  先储存到本地
      let waitSyncData = localStorage.getItem("waitSyncData")
        ? JSON.parse(localStorage.getItem("waitSyncData"))
        : [];

      waitSyncData.push(item);

      localStorage.setItem("waitSyncData", JSON.stringify(waitSyncData));

      this.input = "";

      input.reset();

      if (this.combo > 0) {
        this.$toast({
          content: "🎈 +" + this.combo,
          light: true,
        });
      } else {
        this.$toast({
          content: "👌",
          light: true,
        });
      }
      if (this.syncTimer != null) {
        clearTimeout(this.syncTimer);
      }

      this.syncTimer = setTimeout(() => {
        console.log("开始同步");
        clearTimeout(this.syncTimer);
        this.syncTimer = null;
        this.combo = 0;
        data.postWaitSyncData({
          success: () => {
            this.$toast({
              content: "🎉 同步已完成",
              light: true,
            });
          },
        });
      }, 3000);
      this.combo++;
    },
  },
};
</script>

<style lang="less" scoped>
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

.time {
  position: fixed;
  top: -0.5rem;
  right: 2rem;
  color: #fff;
  background: #4170f3;
  box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
  height: 3rem;
  padding: 1rem 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  z-index: 2;
  .img {
    width: 1rem;
    margin-right: 0.3rem;
  }
}

.keyboard {
  margin: 1rem;
  .line {
    display: flex;
    .btn {
      flex: 1;
      height: 5rem;
      margin: 0.5rem;
      background: #fff;
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      // box-shadow: 0 5px 0px 2px rgba(216, 210, 210, 0.1);
    }

    .btn:active,
    .post:active {
      opacity: 0.1;
    }
    .post {
      background: #4170f3;
      color: #fff;
      box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    }
    .text {
      font-size: 1.4rem;
      margin-right: 0.5rem;
    }
    .out {
      background: rgb(250, 69, 69);
      box-shadow: 0 1px 0px 2px rgba(250, 69, 69, 0.1);
    }
  }
}
.input {
  background: #fff;
  margin: 0 1.5rem;
  padding: 1.5rem 1rem;
  border-radius: 1rem;
  font-size: 3rem;
  display: flex;
  position: relative;
  margin-top: 1rem;
  align-items: center;
  box-shadow: 0 10px 0px 2px rgba(216, 210, 210, 0.2);

  // border: 2px solid #4170f3;
  // box-shadow: 0 1px 0px 2px rgba(250, 69, 69, 0.1);
  .action {
    font-size: 0.8rem;
    position: absolute;
    top: -1.6rem;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    border-top-left-radius: 0.2rem;
    border-top-right-radius: 0.2rem;
    padding: 0.2rem 0;
    right: 0;
    width: 3rem;
    // z-index: -1;

    background: #4170f3;

    margin: 0 auto;
  }
  .action-out {
    background: #fa4545;
  }
  .money {
    font-size: 0.8rem;
    position: absolute;
    bottom: -1rem;
    // left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    // color: #fff;
    border-radius: 0.2rem;
    // padding: 0.2rem 0.5rem;
    // width: 3rem;
    // z-index: -1;

    background: rgb(255, 232, 27);
    color: #6b6000;
    margin: 0 auto;
    box-shadow: 0 1px 0px 2px rgba(230, 208, 15, 0.2);

    .unit {
      font-size: 0.8rem;
      background: #ffe81b;
      color: #6b6000;

      padding: 0.2rem 0.5rem;
      // border-bottom-left-radius: 0.2rem;
      // border-bottom-right-radius: 0.2rem;
      border-top-left-radius: 0.2rem;
      border-bottom-left-radius: 0.2rem;
    }
    .value {
      padding: 0.2rem 0.5rem;
      margin: 0;
      color: #6b6000;
    }
  }

  .btn {
    height: 4rem;
    width: 4rem;
    padding: 0 1rem;
    border-radius: 1rem;
    background: #4170f3;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
  }
  .backspace {
    padding: 0 1rem;
    border-radius: 1rem;
    // margin-left: 1rem;
    color: #fff;
    background: #fa4545;
    box-shadow: 0 1px 0px 2px rgba(250, 69, 69, 0.1);
  }
  .out {
    color: #fa4545 !important;
    label {
      border-color: #fa4545 !important;
      box-shadow: 0 1px 0px 2px rgba(250, 69, 69, 0.1) !important;
    }
  }
  .value {
    margin: 0 0.5rem;
    flex: 1;
    white-space: nowrap;
    color: #4170f3;
    overflow: scroll;
    label {
      width: 2rem;
      display: inline-block;
      margin-left: 0.5rem;
      border-radius: 0.1rem;
      border-bottom: 0.5rem solid #4170f3;
      animation: flash 0.9s infinite;
      box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    }

    @keyframes flash {
      0% {
        opacity: 0;
      }

      50% {
        opacity: 1;
      }
      100% {
        opacity: 0;
      }
    }
  }
}
.head {
  display: flex;
  margin-top: 2.5rem;
}
</style> 