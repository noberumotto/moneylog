<template>
  <div>
    <dialog2
      v-show="isShowAddDialog"
      title="添加账户"
      v-on:yes="onAddDialogYes"
      v-on:no="isShowAddDialog = false"
    >
      <input
        class="inputbox"
        placeholder="如 招商银行"
        type="text"
        maxlength="4"
        v-model="newBank"
      />
    </dialog2>
    <!-- <nav-menu /> -->
    <title-bar title="账户" />

    <div class="tab">
      <div class="head">
        <scope-buttons :data="['支出账户', '收入账户']" v-model="tabIndex" />

        <!-- <div class="item" :class="{ selected: tabIndex == 0 }" @click="tab(0)">
          支出账户
        </div>
        <div class="item" :class="{ selected: tabIndex == 1 }" @click="tab(1)">
          收入账户
        </div> -->
      </div>
      <div class="card">
        <div class="action">
          <div class="button" @click="isShowAddDialog = true">✨ 添加</div>
        </div>
        <div class="empty-tip mt" v-if="count == 0">
          🐾 还没有与此相关的账户
        </div>

        <div class="list">
          <div
            class="item"
            v-for="(item, index) in list"
            :key="index"
            v-show="item.status == tabIndex"
          >
            <div class="name">
              <input
                type="text"
                :value="item.name"
                maxlength="4"
                @click="selectedItem = item"
                @change="changeName"
              />
            </div>

            <div class="action">
              <div class="btn" @click="del(index)">
                <div class="icon">🗑</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="form" v-if="false">
      <div class="input">
        <input type="email" placeholder="邮箱" />
      </div>

      <div class="input">
        <input type="password" placeholder="密码" />
      </div>

      <div class="action">
        <div class="btn" @click="login()">Go<span>→</span></div>
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
import data from "../library/data";

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons },
  data() {
    return {
      list: [],
      tabIndex: 0,
      dialogTitle: "",
      dialogContent: "",
      isShowDelDialog: false,
      selectedItem: null,
      isShowAddDialog: false,
      newBank: "",
    };
  },
  computed: {
    count: function () {
      return this.list
        .map((item) => item.status == this.tabIndex)
        .reduce((prev, curr) => prev + curr, 0);
    },
  },
  mounted() {
    this.getData();
  },
  methods: {
    changeName(e) {
      let newName = e.target.value;
      if (newName == "") {
        e.target.value = this.selectedItem.name;
        return;
      }
      this.$request.post({
        url: "account/edit",
        data: {
          id: this.selectedItem.id,
          name: newName,
        },
        success: (res) => {
          this.selectedItem.name = newName;

          this.$store.commit("user/setAccountData", this.list);
        },
      });
    },
    tab(index) {
      this.tabIndex = index;
    },
    getData() {
      data.getAccountData({
        success: (res) => {
          this.list = res;
        },
      });
    },

    del(index) {
      let item = this.list[index];

      this.$modal({
        title: "提示",
        content: "删除账户 " + item.name + " 吗？",
        confirm: () => {
          this.$request.post({
            url: "account/del",
            data: {
              id: item.id,
            },
            success: (res) => {
              this.list.splice(index, 1);

              this.$store.commit("user/setAccountData", this.list);
            },
          });
        },
      });
    },
    onAddDialogYes() {
      if (this.newBank == "") {
        this.$toast({
          content: "请输入名称",
        });
        return;
      }

      let isCheck = true;
      this.list.forEach((item) => {
        if (item.name == this.newBank && item.status == this.tabIndex) {
          isCheck = false;
          this.$toast({
            content: "名称已存在",
          });
          return;
        }
      });

      //this.$loading();
      if (isCheck) {
        this.$request.post({
          url: "account/add",
          data: {
            name: this.newBank,
            status: this.tabIndex,
          },
          success: (res) => {
            this.list.push({
              id: res.data,
              name: this.newBank,
              status: this.tabIndex,
            });

            this.isShowAddDialog = false;

            this.newBank = "";
            this.$store.commit("user/setAccountData", this.list);
          },
        });
      }

      //  this.$loading.closeAll();
    },
  },
};
</script>
<style lang="less" scoped>
.list {
  margin-top: 1rem;
  .item {
    display: flex;
    align-items: center;
    // background: #f7f7f8;
    justify-content: space-between;
    input {
      border: none;
      font-size: 1rem;
    }
    .action {
      .btn {
        // background: #fa4545;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        border-radius: 0.2rem;
        height: 3rem;
      }
    }
  }
}
.tab {
  position: relative;
  // margin-top: 1rem;
  // box-shadow: 0 0 15px 14px rgba(0, 0, 0, 0.1);
  padding: 0 2rem;
  .card {
    // background: #fff;
    // margin: 2rem;
    // margin-top: 1rem;
    // border-radius: 1.5rem;

    // padding: 2rem;
    .action {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
  }
  .head {
    // display: flex;
    // // z-index: -1;
    // background: #fff;
    // margin: 2rem;
    // border-radius: 1rem;
    // padding: 2rem;

    .item {
      background: #fff;
      padding: 1rem 2rem;
      border-radius: 1rem;
      color: #161616;
      display: flex;
      align-items: center;
      margin-right: 1rem;

      // margin-top: -0.5rem;
    }
    .item:last-child {
      // background: #fa4545;
    }
    .selected {
      // margin-top: -1rem;
      // border-radius: 1rem;
      background: rgb(65, 112, 243);
      color: #fff;
      box-shadow: 0 0 10px 10px rgba(65, 112, 243, 0.1);
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
.form {
  background: #fff;
  margin: 2rem;
  padding: 2rem;
  border-radius: 1rem;
  .input {
    margin-bottom: 1rem;
    input {
      border: none;
      background: #f3f3f3;
      font-size: 1.4rem;
      box-sizing: border-box;
      width: 100%;
      border-radius: 1rem;
      padding: 1rem;
    }
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
  }
}
</style>