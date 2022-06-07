<template>
  <div>
    <title-bar title="编辑账单" />
    <div class="card">
      <div class="type">
        <div
          class="content"
          :class="{
            out: item.status == 0,
          }"
        >
          {{ item.status == 0 ? "支" : "收" }}
        </div>
      </div>
      <div class="label">分类</div>

      <div class="input">
        <div class="item" v-if="item" @click="chooseTag()">
          <div class="icon">
            <img
              v-if="item.tag && item.tag.icon_name"
              :src="
                require('../assets/icon/category/' +
                  item.tag.icon_name +
                  '.png')
              "
            />
            <div>{{ item.tag ? item.tag.name : "请选择" }}</div>
          </div>
        </div>
      </div>
      <div class="label">账户</div>

      <div class="input" @click="chooseAccount()">
        <div class="item">
          {{ item.account ? item.account.name : "请选择" }}
        </div>
      </div>

      <div class="label">时间</div>

      <div class="input" @click="chooseDate()">
        <div class="item">
          {{ item.time_text }}
        </div>
      </div>
      <div class="label">金额</div>

      <div class="input">
        <input
          class="inputbox"
          type="number"
          placeholder="金额"
          v-model="item.money"
        />
      </div>
      <div class="label">备注</div>

      <div class="input">
        <input
          class="inputbox"
          type="text"
          placeholder="备注"
          v-model="item.remark"
          maxlength="10"
        />
      </div>

      <div class="action">
        <div class="button yellow max" @click="done()">
          <!-- Go<span>→</span> -->👌
        </div>
      </div>

      <!-- <div class="footer">
        <div class="icon"></div>
        <div class="line"></div>
        <div class="beautify">
          <div>1</div>
          <div>1</div>
          <div>1</div>
        </div>
      </div> -->
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
import TitleBar from "../components/TitleBar.vue";
import data from "../library/data";
import time from "../library/time";
import BigNumber from "bignumber.js";
export default {
  components: { TitleBar },
  data() {
    return {
      item: [],
      categoryData: [],
      accountData: [],
    };
  },
  computed: {
    ...mapState(["user"]),
  },
  mounted() {
    let data = this.$route.query;
    this.item = {
      id: data.id,
      tags_id: data.tags_id,
      account_id: data.account_id,
      time: data.time,
      money: data.money,
      status: data.status,
      tag: {},
      time_text: "",
      account: {},
      remark: data.remark,
    };
    this.getData();
  },
  methods: {
    chooseTag() {
      let item = this.item;
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
          item.tag = res;
        },
      });
    },
    chooseDate() {
      this.$datepicker({
        date: this.item.time,
        res: (res) => {
          let item = this.item;
          item.time = res;
          item.time_text = time.toString(res);
        },
      });
    },
    chooseAccount() {
      let item = this.item;
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
          item.account = res;
        },
      });
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
          this.update();
        },
      });
    },

    update() {
      let item = this.item;
      item.tag = this.categoryData.find((e) => e.id == item.tags_id);
      item.account = this.accountData.find((e) => e.id == item.account_id);
      item.time_text = time.toString(item.time);
    },
    done() {
      let money = new BigNumber(this.item.money).toNumber();
      if (isNaN(money) || money == 0) {
        this.$toast({
          content: "😧 金额错误",
        });
      } else if (money < 0.01) {
        this.$toast({
          content: "😶 太小了",
          light: true,
        });
        return;
      } else if (money >= 100000000) {
        this.$toast({
          content: "😨 暂时无法处理这笔巨款",
          light: true,
        });
        return;
      } else if (this.item.remark.length > 10) {
        this.$toast({
          content: "🐻 备注只能在10个字内",
        });
      } else {
        this.$loading();
        this.$request.post({
          url: "moneylog/edit",
          data: this.item,
          success: () => {
            this.updateState();
            this.$toast({
              content: "✨ 已更新",
            });
          },
          complete: () => {
            this.$loading.closeAll();
          },
        });
      }
    },
    updateState() {
      let pageData = this.user.pageData;
      if (pageData.length > 0) {
        let listPageData = pageData.find((e) => {
          return e.name == "list";
        });

        listPageData.update = this.item;

        this.$store.commit("user/update", {
          pageData: pageData,
        });
      }
    },
  },
};
</script>
<style lang="less" scoped>
.footer {
  position: absolute;
  left: 0;
  right: 0;
  bottom: -2.8rem;
  pointer-events: none;

  .line {
    background: white;
    height: 1rem;
    margin: 0 0.2rem;
    margin-bottom: -6rem;
  }
  .beautify {
    display: flex;
    margin-bottom: -1rem;

    div {
      background: white;
      height: 9rem;
      width: 9rem;
      z-index: -1;
      border-radius: 9rem;
      box-shadow: 0 5px 0px 2px rgba(216, 210, 210, 0.1);
    }
  }
}
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
  position: relative;
  // padding-bottom: 1rem;
  // margin-bottom: 6rem;
  .type {
    position: absolute;
    right: 2rem;
    // font-weight: bold;
    font-size: 1rem;
    top: 1.5rem;
    color: #4170f3;
    .content {
      background: #e9efff;
      width: 2rem;
      height: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 0.2rem;
    }
    .out {
      background: #fcf8da;
      color: #6b6000;
    }
  }
  .label {
    margin: 0.8rem 0;
    color: rgb(102, 102, 102);
  }
  // border-radius: 1rem;
  .input {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px dotted #f2f2f2;
    .item {
      display: inline-block;
      background: #f2f2f2;
      padding: 0.5rem 1rem;
      border-radius: 1rem;
      color: #3f3f3f;
      box-shadow: 0 2px 0px 2px rgba(216, 210, 210, 0.4);
      margin-bottom: 0.5rem;
    }
    .icon {
      display: flex;
      align-items: center;
      font-size: 1.2rem;
      img {
        width: 2rem;
        margin-right: 0.5rem;
      }
    }
    input {
      border: none;
      background: rgb(242, 242, 242);
      font-size: 1.4rem;
      box-sizing: border-box;
      width: 100%;
      border-radius: 1rem;
      padding: 1rem;
      // box-shadow: 0 2px 0px 2px rgba(196, 195, 195, 0.1);
    }
  }
  .action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 2rem;
    z-index: 3;
    .button {
      // width: 5rem;
      // height: 5rem;
      // font-size: 4rem;
      // font-weight: bold;
    }
    // .btn {
    //   background: #4170f3;
    //   color: #fff;
    //   font-size: 1rem;
    //   padding: 1rem 3rem;
    //   border-radius: 1rem;
    //   display: flex;
    //   box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    //   align-items: center;
    //   span {
    //     margin-left: 1rem;
    //   }
    // }
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