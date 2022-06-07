<template>
  <div>
    <title-bar subtitle="" title="导入账单" />

    <div class="main">
      <div class="card">
        <div class="empty-tip">
          🦉 目前支持：微信账单。服务器状态：{{ server_status }}
        </div>
        <div class="label">选择账单文件（.csv）</div>
        <input
          ref="fileInput"
          v-show="false"
          type="file"
          @change="chooseFile"
          accept=".csv"
        />
        <div class="file">{{ fileName }}</div>

        <div class="action">
          <div v-if="!fileName" class="button max yellow" @click="select()">
            📃👈
          </div>
          <div v-if="fileName" class="button max" @click="go()">📥</div>
        </div>
      </div>

      <div class="card">
        <div class="header-title">待处理文件列表</div>

        <div class="list mt" v-if="list.length != 0">
          <div class="item" v-for="(item, index) in list" :key="index">
            {{ item }}
          </div>
        </div>
        <div class="empty-tip mt" v-if="list.length == 0">🦊 还没有哦</div>
        <div class="empty-tip mt" v-if="list.length != 0">
          💾💡
          服务器资源有限，所以会按照上传的先后顺序依次处理，过段时间再来看看吧。
        </div>
      </div>

      <div class="card">
        <div class="header-title">已导入账单记录条数</div>

        <div class="num">{{ importTotal }}</div>
        <div class="action center">
          <div class="button max" @click="clear">清空所有导入账单</div>
        </div>
      </div>

      <div class="empty-tip mt">
        🔦
        数据使用声明：平台不会保存账单文件，在处理完成后会立即删除原始文件。但是会收集导入失败数据的：交易方名称、交易类型、支付方式、支付状态，用于定位和修复导入失败的原因。
      </div>
      <div class="empty-tip mt">
        🦖
        导入功能说明：系统只会导入能够识别出分类的记录哦，如果本次导入的账单处理得不够满意可以过一段时间重新清空再导入试试。系统会随着导入的账单数量逐渐增强识别能力。
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

export default {
  components: { NavMenu, Dialog2, TitleBar, ScopeButtons },
  data() {
    return {
      fileName: "",
      files: [],
      list: [],
      importTotal: 0,
      server_status: "",
    };
  },
  computed: {},
  mounted() {
    this.getlist();
  },
  methods: {
    clear() {
      this.$modal({
        title: "🚨 提示",
        content:
          "即将清空所有导入的账单记录是否继续？仅清空历史导入账单，手动记录的并不会清空哦。",
        confirm: () => {
          this.clearPost();
        },
      });
    },
    getlist() {
      this.$loading();
      this.$request.post({
        url: "import/list",
        success: (res) => {
          this.list = res.data.wait_list;
          this.importTotal = res.data.import_count;
          this.server_status = res.data.server_status;
        },
        complete: () => {
          this.$loading.closeAll();
        },
      });
    },
    clearPost() {
      this.$loading();
      this.$request.post({
        url: "import/clear",
        success: (res) => {
          this.getlist();
          this.$toast({
            content: "操作已完成",
          });
        },
        complete: () => {
          this.$loading.closeAll();
        },
      });
    },
    select() {
      this.$refs.fileInput.click();
    },
    chooseFile(e) {
      let files = e.target.files;
      if (files.length == 0) {
        return;
      }
      this.files = files;
      this.fileName = files[0].name;
    },
    go() {
      this.$modal({
        title: "导入提示",
        content:
          "即将导入：" +
          this.fileName +
          " 账单，是否确认？系统会自动创建一些分类和账户。",
        confirm: () => {
          this.postFile();
        },
      });
    },

    postFile() {
      this.$loading();

      this.$request.file({
        url: "import/upload",
        file: this.files[0],
        success: () => {
          this.files = [];
          this.fileName = "";
          this.getlist();

          this.$modal({
            title: "提示",
            content: "账单已经进入处理队列，当前页面可查看状态信息",
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
.main {
  padding: 0 2rem;
  margin-top: 2rem;
  .action {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  .center {
    justify-content: center !important;
  }
  .label {
    margin: 0.8rem 0;
    color: rgb(49, 49, 49);
    margin-top: 1.5rem;
  }
  .file {
    color: rgb(196, 177, 6);
  }

  .list {
    padding: 1rem;
    .item {
      color: rgb(196, 177, 6);
      overflow: scroll;
    }
  }
  .num {
    text-align: center;
    margin: 1rem 0;
    color: rgb(65, 112, 243);
    font-size: 1.5rem;
  }
}
</style>