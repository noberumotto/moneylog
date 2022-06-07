<template>
  <div class="chart">
    <title-bar subtitle="" title="统计">
      <div class="date">
        <div
          v-if="type == 0 || type == 2 || type == 3"
          class="button"
          @click="chooseDate"
        >
          🗓 {{ type == 0 ? day : type == 2 ? month : year }}
        </div>
      </div>
    </title-bar>
    <div class="time">
      <scope-buttons :data="['日', '周', '月', '年']" v-model="type" />
      <div class="date">
        {{ dateTitle }}
      </div>
    </div>
    <div class="data">
      <div
        class="card"
        style="padding-top: 1rem"
        @touchstart="touch"
        @touchmove="touchmove"
        @touchend="touchend"
      >
        <div class="info" v-if="avgIn && avgIn > 0">
          平均收入<label class="in-text"><money-value :money="avgIn" /></label>
        </div>
        <div class="info" v-if="avgOut && avgOut > 0">
          平均支出
          <label class="out-text"><money-value :money="avgOut" /></label>
        </div>
        <div class="touch-container" ref="touchcontainer">
          <div class="touch">
            <!-- <div class="touch-block"></div> -->
            <chart
              name="点"
              :values1="outcomeList"
              :values2="incomeList"
              :isShowAll="type == 3"
              :title="titleList"
            />
            <!-- <div class="touch-block"></div> -->
          </div>
        </div>
      </div>

      <div class="card">
        <div class="header-title">🌍 总览</div>
        <div class="overview">
          <div class="item" ref="valueContainer">
            <div class="icon">👇</div>
            <div class="title">收入</div>
            <div
              class="value in-text"
              :style="{ fontSize: getFontSize(income) + 'rem' }"
            >
              {{ income > 0 ? "+" : "" }}<money-value :money="income" />
            </div>
            <div class="unit" v-if="income >= 1000 || income <= -1000">
              <span> {{ unit(income) }}</span>
            </div>
          </div>
          <div class="item">
            <div class="icon">{{ getProfitsIcon(profits) }}</div>
            <div class="title">结余</div>
            <div
              :style="{ fontSize: getFontSize(profits) + 'rem' }"
              class="value"
              :class="{ 'red-text': profits > 0, 'green-text': profits < 0 }"
            >
              <money-value :money="profits" />
            </div>
            <div class="unit" v-if="profits >= 1000 || profits <= -1000">
              <span>{{ unit(profits) }}</span>
            </div>
          </div>
          <div class="item">
            <div class="icon">👆</div>
            <div class="title">支出</div>
            <div
              class="value out-text"
              :style="{ fontSize: getFontSize(outcome) + 'rem' }"
            >
              {{ outcome > 0 ? "-" : "" }}<money-value :money="outcome" />
            </div>
            <div class="unit" v-if="outcome >= 1000 || outcome <= -1000">
              <span> {{ unit(outcome) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="header-title">分类收入</div>
        <chart
          class="pd"
          :values1="incomeCategoryValueList"
          :title="incomeCategoryTitleList"
          :type="1"
          :icons="incomeCategoryIconList"
          :color="'#4170f3'"
        />
      </div>

      <div class="card">
        <div class="header-title">分类支出</div>
        <chart
          class="pd"
          :values1="outcomeCategoryValueList"
          :title="outcomeCategoryTitleList"
          :type="1"
          :icons="outcomeCategoryIconList"
        />
      </div>

      <div class="card">
        <div class="header-title">账户收入</div>
        <chart
          class="pd"
          :values1="incomeAccountValueList"
          :title="incomeAccountTitleList"
          :type="1"
          :color="'#4170f3'"
        />
      </div>

      <div class="card">
        <div class="header-title">账户支出</div>
        <chart
          class="pd"
          :values1="outcomeAccountValueList"
          :title="outcomeAccountTitleList"
          :type="1"
        />
      </div>
    </div>
  </div>
</template>
<script>
import BigNumber from "bignumber.js";
import { mapState } from "vuex";
import Chart from "../components/Chart.vue";
import Dialog2 from "../components/Dialog2.vue";
import NavMenu from "../components/NavMenu.vue";
import ScopeButtons from "../components/ScopeButtons.vue";
import TitleBar from "../components/TitleBar.vue";
import data from "../library/data";
import time from "../library/time";
import MoneyValue from "../components/MoneyValue.vue";

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons, Chart, MoneyValue },
  data() {
    let date = time.toString(undefined, "y-m-d");

    return {
      day: date, //  日期：yyyy-mm-dd
      week: "", //  周:yyyy-mm-dd~yyyy-mm-dd
      month: "", // 月：yyyy-mm
      year: "", //  年：yyyy
      isShowTypeSelect: false,
      list: [],
      tabIndex: 0,
      dialogTitle: "",
      dialogContent: "",
      isShowDelDialog: false,
      selectedItem: null,
      isShowAddDialog: false,
      newBank: "",
      type: 0,
      titleList: [],
      incomeList: [],
      outcomeList: [],
      outcomeCategoryValueList: [],
      outcomeCategoryTitleList: [],
      outcomeCategoryIconList: [],
      incomeCategoryValueList: [],
      incomeCategoryTitleList: [],
      incomeCategoryIconList: [],
      outcomeAccountValueList: [],
      outcomeAccountTitleList: [],
      incomeAccountValueList: [],
      incomeAccountTitleList: [],

      isThisDate: true,
      thisWeek: "",
      touchStartX: 0,
      dayIndex: 0,
      weekIndex: 0,
      monthIndex: 0,
      yearIndex: 0,
      scrollMaxValue: 0,
      scroll: 0,
      scrollCenterValue: 0,
      isTouched: true,
    };
  },
  computed: {
    avgIn: function () {
      return new BigNumber(this.income)
        .div(this.incomeList.length)
        .toFixed(2)
        .toString();
    },
    avgOut: function () {
      return new BigNumber(this.outcome)
        .div(this.outcomeList.length)
        .toFixed(2)
        .toString();
    },
    income: function () {
      let sum = this.sum(this.incomeList);

      if (sum == 0) {
        this.incomeList = [];
      }

      return sum;
    },
    outcome: function () {
      let sum = this.sum(this.outcomeList);
      if (sum == 0) {
        this.outcomeList = [];
      }
      return sum;
    },
    profits: function () {
      return new BigNumber(this.income).minus(this.outcome);
    },
    dateTitle: function () {
      if (this.type == 0) {
        return time.toString(undefined, "y-m-d") == this.day
          ? "今日"
          : this.day;
      } else if (this.type == 1) {
        return this.thisWeek == this.week ? "本周" : this.week;
      } else if (this.type == 2) {
        return time.toString(time.getMonth(), "y-m") == this.month
          ? "本月"
          : this.month;
      } else if (this.type == 3) {
        return time.toString(time.getMonth(), "y") == this.year
          ? "今年"
          : this.year;
      }
    },
  },
  watch: {
    type: function (val) {
      this.getData();
    },
    income: function (val) {
      this.incomeValueFontSize = this.getFontSize(val);
    },
    outcome: function (val) {
      this.outcomeValueFontSize = this.getFontSize(val);
    },
    profits: function (val) {
      this.profitsValueFontSize = this.getFontSize(val);
    },
  },
  mounted() {
    time.getWeekRange({
      res: (start, end) => {
        this.week =
          time.toString(start, "y-m-d") + "~" + time.toString(end, "y-m-d");
        this.thisWeek =
          time.toString(start, "y-m-d") + "~" + time.toString(end, "y-m-d");
      },
    });

    this.month = time.toString(time.getMonth(), "y-m");

    this.year = time.toString(time.getYear(), "y");
    this.getData();
  },

  methods: {
    getProfitsIcon(val) {
      if (val > 0) {
        return "🎉";
      } else if (val == 0) {
        return "🦕";
      } else {
        return "💸";
      }
    },
    chooseDate() {
      this.$datepicker({
        date:
          this.type == 2 ? this.month : this.type == 3 ? this.year : this.day,
        res: (date) => {
          if (this.type == 2) {
            this.month = date;
          } else if (this.type == 3) {
            this.year = date;
          } else {
            this.day = date;
          }

          console.log(date);

          this.getData();
        },
      });
    },
    unit: function (val) {
      return this.$common.getUnit(val);
    },
    getFontSize(val) {
      if (val > 1000000 || val < -1000000) {
        return 0.8;
      } else if (val > 100000 || val < -100000) {
        return 1;
      } else {
        return 1.2;
      }
    },
    scrollToCenter() {
      this.$nextTick(() => {
        let left = this.$refs.touchcontainer.offsetLeft;
        //  可视区域宽度
        let viewWidth = this.$refs.touchcontainer.clientWidth;
        let chartWidth = 20 * window.$fontSize;

        let to = Math.round(left - (viewWidth - chartWidth) / 2);

        let haswidth = 3 * window.$fontSize - (viewWidth - chartWidth) / 2;

        this.scrollMaxValue = to + haswidth;

        this.$refs.touchcontainer.scrollLeft = to;

        this.scrollCenterValue = to;
        this.scroll = 0;

        this.isTouched = true;
      });
    },

    touch(e) {
      this.touchStartX = e.touches[0].clientX;
      this.scroll = 0;
    },
    touchend(e) {
      if (!this.isTouched) {
        return;
      }
      // let scrollLeft = this.$refs.touchcontainer.scrollLeft;
      // if (scrollLeft < this.scrollCenterValue) {
      //   this.scroll = -1;
      //   // alert(1)
      //   alert(scrollLeft + "/" + this.scrollCenterValue);
      //   // console.log("左" + scrollLeft + "<" + this.scrollCenterValue);
      // }
      // if (scrollLeft > this.scrollCenterValue) {
      //   this.scroll = 1;
      //   // alert(2)

      //   // console.log("左2" + scrollLeft + ">" + this.scrollCenterValue);
      // }

      // console.log(this.$refs.touchcontainer.scrollLeft);

      // this.isTouched = true;
      // let diff = this.touchEndX - this.touchStartX;
      // console.log(diff)
      if (this.scroll == -1) {
        // console.log("左");
        this.handleTouchAction(-1);
      }
      if (this.scroll == 1) {
        // console.log("右");
        this.handleTouchAction(1);
      }
    },
    touchmove(e) {
      this.scroll = 0;

      let diff = e.touches[0].clientX - this.touchStartX;
      if (diff > 100) {
        // console.log("左");
        this.scroll = -1;
        // this.handleTouchAction(-1);
      }
      if (diff < -100) {
        this.scroll = 1;
        // console.log("右");
        // this.handleTouchAction(1);
      }
    },
    handleTouchAction(num) {
      console.log(this.isTouched);
      this.isTouched = false;

      if (this.type == 0) {
        this.dayIndex += num;
        this.day = time.toString(time.getDay(null, this.dayIndex), "y-m-d");
        this.getData();
      } else if (this.type == 1) {
        this.weekIndex += num;
        time.getWeekRange({
          date: new Date(),
          week: this.weekIndex,
          res: (start, end) => {
            this.week =
              time.toString(start, "y-m-d") + "~" + time.toString(end, "y-m-d");
            this.getData();
          },
        });
      } else if (this.type == 2) {
        this.monthIndex += num;
        this.month = time.toString(time.getMonth(null, this.monthIndex), "y-m");
        this.getData();
      } else if (this.type == 3) {
        this.yearIndex += num;
        this.year = time.toString(time.getYear(null, this.yearIndex), "y");
        this.getData();
      }
    },
    getData() {
      // this.scrollToCenter();

      //  清空数据
      this.incomeList = [];
      this.outcomeList = [];
      this.titleList = [];
      this.incomeCategoryValueList = [];
      this.incomeCategoryTitleList = [];
      this.incomeCategoryIconList = [];
      this.outcomeCategoryValueList = [];
      this.outcomeCategoryTitleList = [];
      this.outcomeCategoryIconList = [];
      this.incomeAccountTitleList = [];
      this.incomeAccountValueList = [];

      this.outcomeAccountTitleList = [];
      this.outcomeAccountValueList = [];

      this.$loading();
      let typeList = ["day", "week", "month", "year"];
      let date = this.day;
      if (this.type == 1) {
        date = this.week;
      } else if (this.type == 2) {
        date = this.month;
      } else if (this.type == 3) {
        date = this.year;
      }
      this.$request.post({
        url: "moneylog/data",
        data: {
          type: typeList[this.type],
          date: date,
        },
        success: (res) => {
          this.scrollToCenter();

          this.$loading.closeAll();
          this.handleData(res.data);
        },
      });
    },
    //  求和
    sum(arr) {
      let result = 0;
      arr.forEach((value) => {
        result = new BigNumber(value).plus(result);
      });

      return new BigNumber(result).toNumber();
    },
    handleData(res) {
      this.incomeList = res.incomeList;
      this.outcomeList = res.outcomeList;

      if (this.type != 0) {
        let titleList = [];
        let week = ["周一", "周二", "周三", "周四", "周五", "周六", "周日"];

        for (let i = 0; i < this.incomeList.length; i++) {
          if (this.type == 1) {
            titleList.push(week[i]);
          } else if (this.type == 2) {
            titleList.push(i + 1 + "日");
          } else if (this.type == 3) {
            titleList.push(i + 1 + "");
          }
        }
        this.titleList = titleList;
      } else {
        this.titleList = [];
      }
      let incategoryValueList = [];
      let incategoryTitleList = [];
      let incategoryIconList = [];

      res.incategoryData.forEach((item) => {
        incategoryValueList.push(item.sum);
        incategoryTitleList.push(item.name);
        incategoryIconList.push(item.icon_name);
      });

      this.incomeCategoryValueList = incategoryValueList;
      this.incomeCategoryTitleList = incategoryTitleList;
      this.incomeCategoryIconList = incategoryIconList;

      let outcategoryValueList = [];
      let outcategoryTitleList = [];
      let outcategoryIconList = [];

      res.outcategoryData.forEach((item) => {
        outcategoryValueList.push(item.sum);
        outcategoryTitleList.push(item.name);
        outcategoryIconList.push(item.icon_name);
      });

      this.outcomeCategoryValueList = outcategoryValueList;
      this.outcomeCategoryTitleList = outcategoryTitleList;
      this.outcomeCategoryIconList = outcategoryIconList;

      //  账户数据
      let inAccountTitleList = [];
      let inAccountValueList = [];
      let outAccountTitleList = [];
      let outAccountValueList = [];
      res.inAccountData.forEach((item) => {
        inAccountValueList.push(item.sum);
        inAccountTitleList.push(item.name);
      });
      res.outAccountData.forEach((item) => {
        outAccountValueList.push(item.sum);
        outAccountTitleList.push(item.name);
      });

      this.incomeAccountTitleList = inAccountTitleList;
      this.incomeAccountValueList = inAccountValueList;

      this.outcomeAccountTitleList = outAccountTitleList;
      this.outcomeAccountValueList = outAccountValueList;
    },
  },
};
</script>
<style lang="less" scoped>
.time {
  padding: 0 2rem;
  .date {
    margin-top: 1rem;
    font-weight: bold;
    font-size: 1.2rem;
  }
}
.data {
  padding: 0 2rem;
  margin-top: 1rem;
  .info {
    font-size: 0.8rem;
    // letter-spacing:.1rem
    label {
      margin-left: 0.2rem;
    }
  }
  .touch-container {
    // overflow: scroll;
    .touch {
      // width: 26rem;

      // .touch-block {
      //   width: 3rem;
      //   height: 1rem;
      //   float: left;
      //   background: red;
      // }
      .chart {
        // width: 20rem;
        // float: left;
        margin-top: 1rem;
      }
    }
  }
}

.overview {
  display: flex;
  margin-top: 1rem;
  align-items: flex-start;
  justify-content: space-between;
  padding: 1rem;
  .icon {
    text-align: center;
  }

  .title {
    text-align: center;
    color: #999;
    margin-top: 0.2rem;
    font-size: 0.8rem;
  }
  .value {
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: 0.5rem;
    color: rgb(59, 59, 59);
    height: 1rem;
  }
  .unit {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    span {
      font-size: 0.8rem;
      background: #ffe81b;
      color: #6b6000;

      padding: 0.2rem 0.5rem;
      border-radius: 0.2rem;
    }
  }
}
</style>