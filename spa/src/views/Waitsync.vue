<template>
  <div>
    <title-bar subtitle="" title="待同步账单">
      <div v-if="data && data.length > 0" class="button" @click="repost()">
        重新同步
      </div>
    </title-bar>

    <!--日-->
    <div style="margin: 0 2rem" class="empty-tip">
      📌
      什么是待同步账单？当你记的账无法成功提交到云端时会将这些账单保存在浏览器本地中。
    </div>
    <div class="list" v-if="data">
      <div class="item" v-for="(item, index) in data" :key="index">
        <div class="icon" @click="chooseTag(item)">
          <img
            v-if="item.tag && item.tag.icon_name"
            :src="
              require('../assets/icon/category/' + item.tag.icon_name + '.png')
            "
          />
        </div>
        <div class="info">
          <div
            @click="chooseTag(item)"
            class="title"
            :class="{ del: !item.tag }"
          >
            {{ item.tag ? item.tag.name : "已删除" }}
          </div>
          <div
            class="account"
            :class="{ del: !item.account }"
            @click="chooseAccount(item)"
          >
            {{ item.account ? item.account.name : "已删除" }}
          </div>
          <div class="time">{{ parseTime(item.time) }}</div>
        </div>
        <div
          class="money"
          :class="{
            'red-text': item.status == 1,
            'green-text': item.status == 0,
          }"
        >
          {{ item.status == 1 ? "+" : "-" }} <money-value :money="item.money" />
        </div>
      </div>
    </div>

    <div style="margin: 0 2rem" class="empty-tip" v-if="data.length == 0">
      ✌ 当前没有同步失败的账单~
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

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons, MoneyValue },
  data() {
    return {
      data: [],
      categoryData: [],
      accountData: [],
    };
  },
  computed: {
    ...mapState(["user"]),
  },
  watch: {},
  mounted() {
    this.getWaitsyncData();
    this.getData();
  },

  methods: {
    getWaitsyncData() {
      let data = localStorage.getItem("waitSyncData")
        ? JSON.parse(localStorage.getItem("waitSyncData"))
        : [];

      data.map((item) => {
        item.tag = null;
        item.account = null;
        return item;
      });
      this.data = data;
    },
    getData() {
      //  获取分类数据
      data.getCategoryData({
        request: true,
        success: (res) => {
          this.categoryData = res;

          this.update();
        },
      });

      data.getAccountData({
        request: true,
        success: (res) => {
          this.accountData = res;
        },
      });
    },
    parseTime(val) {
      return time.toString(val);
    },
    chooseTag(item) {
      let list = [];
      this.categoryData.forEach((e) => {
        if (item.status == e.status) {
          list.push(e);
        }
      });
      this.$wselect({
        title: "请选择" + (item.status == 0 ? "支出分类" : "收入分类"),
        data: list,
        res: (res) => {
          item.tags_id = res.id;
          this.update();
        },
      });
    },
    chooseAccount(item) {
      let list = [];
      this.accountData.forEach((e) => {
        if (item.status == e.status) {
          list.push(e);
        }
      });
      this.$wselect({
        title: "请选择" + (item.status == 0 ? "支出账户" : "收入账户"),
        data: list,
        res: (res) => {
          item.account_id = res.id;
          this.update();
        },
      });
    },
    repost() {
      this.$loading();
      data.postWaitSyncData({
        success: () => {
          this.data = [];
          this.$toast({
            content: "🙌 同步成功！",
          });
        },
        complete: () => {
          this.$loading.closeAll();
          let timer = setTimeout(() => {
            this.getWaitsyncData();
            this.update();
            clearTimeout(timer);
          }, 500);
        },
      });
    },
    update() {
      this.data.map((item) => {
        item.tag = this.user.categoryData.find((e) => e.id == item.tags_id);
        item.account = this.user.accountData.find(
          (e) => e.id == item.account_id
        );
        return item;
      });

      data.pushWaitSyncData(this.data, true);
    },
  },
};
</script>
<style lang="less" scoped>
.head {
  margin: 0 2rem;
}

.list {
  padding: 0.5rem 2rem;
  margin-top: 1rem;
  .item {
    background: #fff;
    border-radius: 2rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
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
      }
      .account {
        margin-top: 0.1rem;
        color: rgb(104, 104, 104);
      }
    }
    .money {
      width: 8rem;
      text-align: right;
      font-size: 1.1rem;
      font-weight: bold;
    }
  }
}
</style>