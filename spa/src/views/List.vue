<template>
  <div>
    <title-bar subtitle="" title="明细" v-on:back="clearState">
      <div class="date">
        <div v-if="tabIndex == 0" class="button" @click="chooseDate">
          🗓 {{ isNowDate(day) ? "今天" : day }}
        </div>
        <div v-if="tabIndex == 1" class="button" @click="chooseDate">
          🗓 {{ isNowDate(month) ? "本月" : month }}
        </div>
      </div>
    </title-bar>

    <div class="head">
      <scope-buttons :data="['日', '月']" v-model="tabIndex" />

      <div class="search">
        <input
          placeholder="搜索分类、账户、备注、金额"
          class="inputbox"
          type="text"
          :value="keyword"
          @change="search"
        />
        <div class="button yellow" @click="reload">🔍</div>
      </div>
    </div>

    <!--日-->
    <div class="total" v-if="tabIndex == 0 && !keyword">
      <div class="item">
        <div class="icon">👇</div>
        <div class="value" :class="{ small: isOut(dayTotalIn) }">
          +<money-value :money="dayTotalIn" />
        </div>
        <div class="title">总收入</div>
      </div>
      <div class="item">
        <div class="icon">
          {{ getProfitsIcon(dayProfits) }}
        </div>
        <div
          class="value"
          :class="{
            'red-text': dayProfits > 0,
            'green-text': dayProfits < 0,
            small: isOut(dayProfits),
          }"
        >
          <money-value :money="dayProfits" />
        </div>
        <div class="title">结余</div>
      </div>
      <div class="item">
        <div class="icon">👆</div>
        <div class="value" :class="{ small: isOut(dayTotalOut) }">
          -<money-value :money="dayTotalOut" />
        </div>
        <div class="title">总支出</div>
      </div>
    </div>

    <div class="list" v-if="dayData && tabIndex == 0">
      <touch-view
        v-for="(item, index) in dayData"
        :key="index"
        v-on:longtap="longTap(index)"
        v-on:doubleclick="edit(index)"
      >
        <div class="item">
          <div class="icon">
            <img
              v-if="item.tag"
              :src="
                require('../assets/icon/category/' +
                  item.tag.icon_name +
                  '.png')
              "
            />
          </div>
          <div class="info">
            <div class="title" :class="{ del: !item.tag }">
              {{ item.tag ? item.tag.name : "已删除" }}
            </div>
            <div class="account" :class="{ del: !item.account }">
              {{ item.account ? item.account.name : "已删除" }}
            </div>

            <div class="time">
              {{ parseTime(item.time_text) }}
              <div class="remark" v-if="item.remark">
                <div class="text">{{ item.remark }}</div>
              </div>
            </div>
          </div>
          <div
            class="money"
            :class="{
              'red-text': item.status == 1,
              'green-text': item.status == 0,
            }"
          >
            {{ item.status == 1 ? "+" : "-" }}
            <money-value :money="item.money" />
          </div>
        </div>
      </touch-view>
    </div>

    <div
      style="margin: 0 2rem"
      class="empty-tip"
      v-if="dayData.length == 0 && tabIndex == 0"
    >
      {{
        keyword
          ? "🔍 在 " + day + " 没有找到 “" + keyword + "” 相关的记录"
          : "🐞 🐕 没有任何记录"
      }}
    </div>

    <!--月-->
    <div class="total" v-if="tabIndex == 1 && !keyword">
      <div class="item">
        <div class="icon">👇</div>
        <div class="value" :class="{ small: isOut(monthTotalIn) }">
          +<money-value :money="monthTotalIn" />
        </div>
        <div class="title">总收入</div>
      </div>
      <div class="item">
        <div class="icon">
          {{ getProfitsIcon(monthProfits) }}
        </div>
        <div
          class="value"
          :class="{
            'red-text': monthProfits > 0,
            'green-text': monthProfits < 0,
            small: isOut(monthProfits),
          }"
        >
          <money-value :money="monthProfits" />
        </div>
        <div class="title">结余</div>
      </div>
      <div class="item">
        <div class="icon">👆</div>
        <div class="value" :class="{ small: isOut(monthTotalOut) }">
          -<money-value :money="monthTotalOut" />
        </div>
        <div class="title">总支出</div>
      </div>
    </div>

    <div class="month-data" v-if="tabIndex == 1">
      <div
        class="item"
        v-for="(month, monthIndex) in monthDataList"
        :key="monthIndex"
      >
        <div class="month-date">
          <div class="month-day">
            {{ isNowDate(month.day) ? "今天" : month.day }}
          </div>
          <div class="day-total">
            <div v-if="month.in > 0">
              <label>收入</label
              ><money-value :money="month.in" :autoColor="true" symobl="+" />
            </div>

            <div v-if="month.out > 0">
              <label>支出</label
              ><money-value :money="-month.out" :autoColor="true" />
            </div>
          </div>
        </div>
        <div class="list" v-if="month.list">
          <touch-view
            v-for="(item, index) in month.list"
            :key="index"
            v-on:longtap="longTap(index, monthIndex)"
            v-on:doubleclick="edit(index, monthIndex)"
          >
            <div class="item">
              <div class="icon">
                <img
                  v-if="item.tag"
                  :src="
                    require('../assets/icon/category/' +
                      item.tag.icon_name +
                      '.png')
                  "
                />
              </div>
              <div class="info">
                <div class="title" :class="{ del: !item.tag }">
                  {{ item.tag ? item.tag.name : "已删除" }}
                </div>
                <div class="account" :class="{ del: !item.account }">
                  {{ item.account ? item.account.name : "已删除" }}
                </div>
                <div class="time">
                  {{ parseTime(item.time_text) }}
                  <div class="remark" v-if="item.remark">
                    <div class="text">{{ item.remark }}</div>
                  </div>
                </div>
              </div>
              <div
                class="money"
                :class="{
                  'red-text': item.status == 1,
                  'green-text': item.status == 0,
                }"
              >
                {{ item.status == 1 ? "+" : "-" }}
                <money-value :money="item.money" />
              </div>
            </div>
          </touch-view>
        </div>
      </div>
    </div>

    <div
      style="margin: 1rem 2rem"
      class="empty-tip"
      v-if="monthDataList.length == 0 && tabIndex == 1"
    >
      {{
        keyword
          ? "🔍 在 " + month + " 没有找到 “" + keyword + "” 相关的记录"
          : "🐀 🐈 这个月什么都没有"
      }}
    </div>
  </div>
</template>
<script>
import BigNumber from "bignumber.js";
import { mapState } from "vuex";
import Dialog2 from "../components/Dialog2.vue";
import MoneyValue from "../components/MoneyValue.vue";
import NavMenu from "../components/NavMenu.vue";
import ScopeButtons from "../components/ScopeButtons.vue";
import TitleBar from "../components/TitleBar.vue";
import data from "../library/data";
import time from "../library/time";
import TouchView from "../components/TouchView/TouchView.vue";

export default {
  components: {
    NavMenu,
    Dialog2,
    TitleBar,
    ScopeButtons,
    MoneyValue,
    TouchView,
  },
  data() {
    return {
      isLoadState: true,
      tabIndex: 0,
      dayPageIndex: 0,
      dayTotalPage: 1,
      dayData: [],
      monthPageIndex: 0,
      monthTotalPage: 1,
      isLoading: false,
      day: "", // y-m-d
      month: "", // y-m
      dayTotalIn: 0,
      dayTotalOut: 0,
      monthTotalIn: 0,
      monthTotalOut: 0,
      monthDataList: [],
      todayDate: "",
      keyword: "", //  搜索关键词
    };
  },
  computed: {
    ...mapState(["user"]),

    dayProfits: function () {
      return new BigNumber(this.dayTotalIn)
        .minus(this.dayTotalOut)
        .toFixed(2)
        .toString();
    },
    monthProfits: function () {
      return new BigNumber(this.monthTotalIn)
        .minus(this.monthTotalOut)
        .toFixed(2)
        .toString();
    },
  },
  watch: {
    tabIndex: function (newVal, oldVal) {
      // if (this.isLoadState) {
      //   // this.isLoadState = false;

      //   return;
      // }
      if (newVal !== oldVal) {
        if (newVal == 0 && this.dayData.length == 0) {
          this.getData(6);
        }
        if (newVal == 1 && this.monthDataList.length == 0) {
          this.getData(5);
        }
      }
    },
    day: function (newVal, oldVal) {
      if (this.isLoadState) {
        // this.isLoadState = false;

        return;
      }
      if (newVal != oldVal) {
        this.dayData = [];
        this.dayTotalPage = 1;
        this.dayPageIndex = 0;
        this.getData(4);
      }
    },
    month: function (newVal, oldVal) {
      if (this.isLoadState) {
        // this.isLoadState = false;
        return;
      }
      if (newVal != oldVal) {
        this.monthDataList = [];
        this.monthTotalPage = 1;
        this.monthPageIndex = 0;
        this.getData(3);
      }
    },
  },
  mounted() {
    let state = this.loadState();
    let date = new Date();
    this.todayDate = time.toString(date, "y-m-d");

    if (!state) {
      this.day = time.toString(date, "y-m-d");
      this.month = time.toString(date, "y-m");

      this.getData(2);
    }
    window.addEventListener("scroll", this.scroll);
  },
  destroyed() {
    window.removeEventListener("scroll", this.scroll);
  },
  methods: {
    search(e) {
      this.keyword = e.target.value;
    },
    reload() {
      this.dayData = [];
      this.dayTotalPage = 1;
      this.dayPageIndex = 0;
      this.monthDataList = [];
      this.monthTotalPage = 1;
      this.monthPageIndex = 0;
      this.getData();
    },
    clearState() {
      this.$store.commit("user/update", {
        pageData: [],
      });
    },
    //  从state加载数据
    loadState() {
      let pageData = this.user.pageData;
      if (pageData.length > 0) {
        let listPageData = pageData.find((e) => {
          return e.name == "list";
        });
        if (listPageData) {
          let data = listPageData.data;

          this.day = data.day;
          this.month = data.month;
          this.dayData = data.dayData;
          this.dayTotalPage = data.dayTotalPage;
          this.dayTotalIn = data.dayTotalIn;
          this.dayTotalOut = data.dayTotalOut;
          this.dayPageIndex = data.dayPageIndex;
          this.monthTotalPage = data.monthTotalPage;
          this.monthTotalIn = data.monthTotalIn;
          this.monthTotalOut = data.monthTotalOut;
          this.monthPageIndex = data.monthPageIndex;

          this.tabIndex = data.tabIndex;
          this.monthDataList = data.monthDataList;

          if (listPageData.update) {
            if (listPageData.monthIndex != -1) {
              this.monthDataList[listPageData.monthIndex].list[
                listPageData.index
              ] = listPageData.update;
            } else {
              this.dayData[listPageData.index] = listPageData.update;
            }
          }

          window.scrollTo(0, listPageData.scroll);

          return true;
        }
      }

      return false;
    },
    saveState(index, monthIndex = -1) {
      var scrollTop =
        document.body.scrollTop || document.documentElement.scrollTop;

      this.$store.commit("user/update", {
        pageData: [
          {
            name: "list",
            data: this.$data,
            update: null,
            index: index,
            monthIndex: monthIndex,
            scroll: scrollTop,
          },
        ],
      });
    },
    edit(index, monthIndex = -1) {
      this.saveState(index, monthIndex);
      let item = {};

      if (monthIndex != -1) {
        item = this.monthDataList[monthIndex].list[index];
      } else {
        item = this.dayData[index];
      }
      this.$router.push(
        "/edit?id=" +
          item.id +
          "&tags_id=" +
          item.tags_id +
          "&account_id=" +
          item.account_id +
          "&money=" +
          item.money +
          "&time=" +
          item.time +
          "&status=" +
          item.status +
          "&remark=" +
          (item.remark ? item.remark : "")
      );
    },
    isOut(val) {
      return (val + "").length >= 9;
    },
    longTap(index, monthIndex = 0) {
      this.$wselect({
        title: "操作",
        data: [
          {
            id: 1,
            name: "编辑",
          },
          {
            id: 2,
            name: "❗ 删除",
          },
        ],
        res: (res) => {
          if (res.id == 1) {
            this.edit(index, monthIndex);
          } else if (res.id == 2) {
            this.$modal({
              title: "🚨 提示",
              content: "即将删除这条记录，是否确定？",
              confirm: () => {
                this.del(index, monthIndex);
              },
            });
          }
        },
      });
    },
    del(index, monthIndex) {
      let item =
        this.tabIndex == 0
          ? this.dayData[index]
          : this.monthDataList[monthIndex].list[index];
      this.$loading();
      this.$request.post({
        url: "moneylog/del",
        data: {
          id: item.id,
        },
        success: () => {
          if (this.tabIndex == 0) {
            let delItem = this.dayData[index];
            if (delItem.status == 0) {
              //  支出
              this.dayTotalOut = new BigNumber(this.dayTotalOut)
                .minus(delItem.money)
                .toFixed(2);
            } else {
              this.dayTotalIn = new BigNumber(this.dayTotalIn)
                .minus(delItem.money)
                .toFixed(2);
            }

            this.dayData.splice(index, 1);
          } else {
            //  重新计算收支
            let delItem = this.monthDataList[monthIndex].list[index];
            if (delItem.status == 0) {
              //  支出
              this.monthTotalOut = new BigNumber(this.monthTotalOut)
                .minus(delItem.money)
                .toFixed(2);
            } else {
              this.monthTotalIn = new BigNumber(this.monthTotalIn)
                .minus(delItem.money)
                .toFixed(2);
            }
            this.monthDataList[monthIndex].list.splice(index, 1);

            if (this.monthDataList[monthIndex].list.length == 0) {
              this.monthDataList.splice(monthIndex, 1);
            }
          }
        },
        complete: () => {
          this.$loading.closeAll();
        },
      });
    },
    parseTime(val) {
      if (this.isNowDate(val)) {
        return val.split(" ")[1];
      }
      return val;
    },
    isNowDate(val) {
      if (!val) {
        return false;
      }
      if (val.indexOf(":") !== -1) {
        val = /[0-9]*-[0-9]*-[0-9]*/.exec(val)[0];
      }

      if (/[0-9]*-[0-9]*-[0-9]*/.test(val)) {
        return val == this.todayDate;
      } else {
        return val + "-" + this.todayDate.split("-")[2] == this.todayDate;
      }
    },
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
        date: this.tabIndex == 0 ? this.day : this.month,
        res: (date) => {
          this.isLoadState = false;

          if (this.tabIndex == 0) {
            this.day = date;
          } else {
            this.month = date;
          }
        },
      });
    },
    scroll(e) {
      var clientHeight = document.documentElement.clientHeight;
      var scrollTop =
        document.body.scrollTop || document.documentElement.scrollTop;
      var scrollHeight = document.body.scrollHeight;

      var value = (scrollTop + clientHeight) / scrollHeight;
      if (value >= 0.9) {
        this.getData(1);
      }
    },
    getData(i) {
      if (this.isLoading) {
        return;
      }
      if (this.tabIndex == 0) {
        //  日
        if (this.dayPageIndex == this.dayTotalPage || this.dayTotalPage == 0) {
          //  已经没数据了
          return;
        }

        this.dayPageIndex++;
      } else {
        //  月
        if (
          this.monthPageIndex == this.monthTotalPage ||
          this.monthTotalPage == 0
        ) {
          //  已经没数据了

          return;
        }

        this.monthPageIndex++;
      }

      this.isLoading = true;

      this.$loading();
      this.$request.post({
        url: "moneylog/list",
        data: {
          keyword: this.keyword,
          page: this.tabIndex == 0 ? this.dayPageIndex : this.monthPageIndex,
          type: this.tabIndex == 0 ? "day" : "month",
          date: this.tabIndex == 0 ? this.day : this.month,
        },
        success: (res) => {
          this.$loading.closeAll();
          if (this.tabIndex == 0) {
            this.dayData = this.dayData.concat(res.data.list.data);
            this.dayTotalPage = res.data.list.last_page;
            this.dayTotalIn = res.data.in;
            this.dayTotalOut = res.data.out;
            this.dayPageIndex = res.data.list.current_page;
          } else {
            // this.monthData = this.monthData.concat(res.data.list.data);
            this.handleMonthData(res.data.list.data);
            this.monthTotalPage = res.data.list.last_page;
            this.monthTotalIn = res.data.in;
            this.monthTotalOut = res.data.out;
            this.monthPageIndex = res.data.list.current_page;
          }
          this.isLoading = false;
        },
      });
    },
    handleMonthData(arr) {
      arr.forEach((element) => {
        let item;
        this.monthDataList.forEach((el) => {
          if (el.day == element.day) {
            item = el;
          }
        });

        if (!item) {
          this.monthDataList.push({
            day: element.day,
            list: [element],
          });
        } else {
          item.list.push(element);
        }
      });

      this.monthDataList.map((item) => {
        item.in = this.sumListMoney(item.list, 1);
        item.out = this.sumListMoney(item.list, 0);

        return item;
      });
    },
    sumListMoney(list, status) {
      let total = list.reduce(function (sum, item) {
        if (item.status == status) {
          return new BigNumber(item.money).plus(sum).toNumber();
        }
        return sum;
      }, 0);
      return new BigNumber(total).toFixed(2).toString();
    },
  },
};
</script>
<style lang="less" scoped>
.small {
  font-size: 0.9rem !important;
}
.head {
  margin: 0 2rem;
  .search {
    background: #fff;
    padding: 0.5rem;
    display: flex;
    margin-top: 1rem;
    border-radius: 1rem;
    justify-content: space-between;
    input {
      height: 3rem;
      flex: 1;
      font-size: 1rem;
    }
    .button {
      margin-left: 1rem;
    }
  }
}
.month-data {
  .month-date {
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    .month-day {
      background: rgb(255, 232, 27);
      color: #6b6000;
      padding: 0.5rem;
      border-radius: 0.5rem;
      font-size: 0.9rem;
      box-shadow: 0 1px 0px 2px rgba(230, 208, 15, 0.2);
    }
    .day-total {
      font-size: 0.8rem;
      label {
        margin-left: 0.4rem;
        margin-right: 0.2rem;
        color: rgb(116, 116, 116);
      }
    }
  }
}
.date {
  color: rgb(78, 78, 78);
  font-size: 1.1rem;
  font-weight: bold;
  display: flex;
  justify-content: flex-end;
}
.total {
  padding: 0.5rem 2rem;
  color: rgb(126, 126, 126);
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
  .item {
    background: #fff;
    padding: 1rem 0;
    border-radius: 1rem;
    text-align: center;
    margin-right: 0.6rem;
    flex: 1;
    // margin: 0 1rem;
    .title {
      font-size: 0.8rem;
      margin-top: 0.2rem;
    }

    .value {
      font-size: 1.2rem;
      margin-top: 0.2rem;
      color: #111;
    }
  }
  .item:last-child {
    margin-right: 0;
  }
}
.list {
  padding: 0.5rem 2rem;
  .touch-view {
    border-radius: 2rem;
    margin-bottom: 1rem;
  }
  .item {
    background: #fff;
    border-radius: 2rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 0px 2px rgba(177, 177, 177, 0.1);

    .icon {
      width: 3rem;
      height: 3rem;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      img {
        width: 2rem;
      }
    }
    .info {
      flex: 1;
      .title {
        font-size: 1.2rem;
        font-weight: bold;
      }
      .time {
        margin-top: 0.2rem;
        font-size: 0.8rem;
        color: #999;
        display: flex;
        align-items: center;
        // justify-content: space-between;
        .remark {
          margin-left: 0.5rem;
          .text {
            display: inline-block;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            background: rgb(255, 232, 27) !important;
            color: #6b6000 !important;
            padding: 0.2rem 0.5rem;
          }
        }
      }
      .account {
        margin-top: 0.1rem;
        color: rgb(104, 104, 104);
      }
    }
    .money {
      width: 6rem;
      text-align: right;
      font-size: 1.1rem;
      font-weight: bold;
    }
  }
}
</style>