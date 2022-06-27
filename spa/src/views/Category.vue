<template>
  <div>
    <icon-picker
      v-show="isShowIconPicker"
      v-model="selectedIcon"
      v-on:done="onSelectedIcon"
    />

    <dialog2
      v-show="isShowAddDialog"
      title="添加分类"
      v-on:yes="onAddDialogYes"
      v-on:no="isShowAddDialog = false"
    >
      <div class="flex">
        <img
          @click="isShowIconPicker = true"
          v-if="selectedIcon"
          class="img wicon mr"
          :src="require('../assets/icon/category/' + selectedIcon + '.png')"
        />
        <input
          class="inputbox"
          placeholder="如 购物"
          type="text"
          maxlength="4"
          v-model="newName"
        />
      </div>
    </dialog2>
    <!-- <nav-menu /> -->
    <title-bar title="分类" />

    <div class="tab">
      <div class="head">
        <scope-buttons :data="['支出分类', '收入分类']" v-model="tabIndex" />
      </div>
      <div class="card">
        <div class="action">
          <div class="button" @click="add()">添加</div>
        </div>
        <div class="empty-tip mt" v-if="count == 0">🌳🦒 还没有任何分类</div>

        <div class="list">
          <div
            class="item"
            v-for="(item, index) in list"
            :key="index"
            v-show="item.status == tabIndex"
          >
            <div class="name">
              <div class="icon">
                <img
                  @click="editIcon(item)"
                  class="img"
                  :src="
                    require('../assets/icon/category/' +
                      item.icon_name +
                      '.png')
                  "
                />
              </div>
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
  </div>
</template>
<script>
import { mapState } from "vuex";
import Dialog2 from "../components/Dialog2.vue";
import IconPicker from "../components/IconPicker.vue";
import NavMenu from "../components/NavMenu.vue";
import TitleBar from "../components/TitleBar.vue";
import data from "../library/data";
import config from "../config";
import ScopeButtons from "../components/ScopeButtons.vue";

export default {
  components: { NavMenu, Dialog2, TitleBar, IconPicker, ScopeButtons },
  data() {
    return {
      list: [],
      tabIndex: 0,
      dialogTitle: "",
      dialogContent: "",
      isShowDelDialog: false,
      selectedItem: null,
      selectedIndex: 0,
      isShowAddDialog: false,
      newName: "",
      isShowIconPicker: false,
      selectedIcon: config.icons[0],
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
    add() {
      this.selectedItem = null;

      this.isShowAddDialog = true;
    },
    tab(index) {
      this.tabIndex = index;
    },
    getData() {
      data.getCategoryData({
        success: (res) => {
          this.list = res;
        },
      });
    },

    onAddDialogYes() {
      if (this.newName == "") {
        this.$toast({
          content: "请输入名称",
        });
        return;
      }

      let isCheck = true;
      this.list.forEach((item) => {
        if (item.name == this.newName) {
          isCheck = false;
          this.$toast({
            content: "名称已存在",
          });
          return;
        }
      });

      //this.$loading();
      if (isCheck) {
        this.$loading();

        this.$request.post({
          url: "tags/add",
          data: {
            name: this.newName,
            icon_name: this.selectedIcon,
            status: this.tabIndex,
          },
          success: (res) => {
            this.$loading.closeAll();

            this.list.push({
              id: res.data,
              name: this.newName,
              icon_name: this.selectedIcon,
              status: this.tabIndex,
            });

            this.newName = "";
            this.isShowAddDialog = false;
            this.$store.commit("user/setCategoryData", this.list);
          },
        });
      }

      //  this.$loading.closeAll();
    },
    onSelectedIcon() {
      this.isShowIconPicker = false;

      if (this.selectedItem) {
        if (this.selectedItem.icon_name == this.selectedIcon) {
          return;
        }

        this.selectedItem.icon_name = this.selectedIcon;
        this.update({
          icon_name: this.selectedItem.icon_name,
        });
      }
    },
    editIcon(item) {
      this.selectedIcon = item.icon_name;
      this.selectedItem = item;
      this.isShowIconPicker = true;
    },
    changeName(e) {
      let newName = e.target.value;
      if (newName == "") {
        e.target.value = this.selectedItem.name;
        return;
      }

      this.update({
        name: newName,
      });
    },
    update(data) {
      let d = {
        id: this.selectedItem.id,
      };
      Object.assign(d, data);
      this.$request.post({
        url: "tags/edit",
        data: d,
        success: (res) => {
          if (data.name) {
            this.selectedItem.name = data.name;
          }
          if (data.icon_name) {
            this.selectedItem.icon_name = data.icon_name;
          }
          this.$store.commit("user/setCategoryData", this.list);
        },
      });
    },
    del(index) {
      let item = this.list[index];

      this.$modal({
        title: "提示",
        content: "删除分类 " + item.name + " 吗？",
        confirm: () => {
          let d = {
            id: item.id,
          };
          this.$request.post({
            url: "tags/del",
            data: d,
            success: (res) => {
              this.list.splice(index, 1);

              this.$store.commit("user/setCategoryData", this.list);
            },
          });
        },
      });
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
    justify-content: space-between;
    margin-bottom: 0.5rem;
    .name {
      display: flex;
      align-items: center;
      .icon {
        border: 1px solid #f3f3f3;
        border-radius: 0.5rem;
        display: flex;
        padding: 0.5rem;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        box-shadow: 0 1px 0px 1px rgba(216, 210, 210, 0.1);

        img {
          width: 1.5rem;
          height: 1.5rem;
        }
      }
    }

    .categoryicon {
      border: 1px solid #f3f3f3;
      border-radius: 0.2rem;
      display: flex;
      width: 3rem;
      height: 3rem;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
    }
    input {
      border: none;
      width: 100%;
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
  margin-top: 1rem;
  // box-shadow: 0 0 15px 14px rgba(0, 0, 0, 0.1);
  padding: 0 2rem;
  .card {
    // background: #fff;
    // margin: 2rem;
    // border-radius: 1.5rem;
    // margin-top: 1rem;
    // padding: 2rem;
    .action {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
  }
  .head {
    // display: flex;
    // z-index: -1;
    // background: #fff;

    // border-radius: 1rem;
    // padding: 1rem;
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