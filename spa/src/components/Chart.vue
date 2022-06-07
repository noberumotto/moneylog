<template>
  <div class="chart">
    <div
      class="empty-tip"
      v-if="
        (type == 0 &&
          (!values1 || values1.length == 0) &&
          (!values2 || values2.length == 0)) ||
        (type == 1 &&
          (!values1 || values1.length == 0) &&
          (!values2 || values2.length == 0))
      "
    >
      🤔 暂时还没有数据哦
    </div>
    <tool-tip
      v-show="type == 0 && tooltipContent && touchIndex != -1"
      :content="tooltipContent"
      :style="{
        left: tooltipLeft,
        top: tooltipTop,
      }"
      ref="tolltip"
    />
    <!--柱状图-->
    <div
      class="type0"
      v-if="
        type == 0 &&
        ((values1 && values1.length > 0) || (values2 && values2.length > 0))
      "
    >
      <div class="grid" ref="grid">
        <div class="max"><money-value :money="maxValue" /></div>
        <div class="medium"><money-value :money="mediumValue" /></div>
        <div
          class="values"
          v-for="(item, index) in list"
          :key="index"
          @touchstart="touchstart"
          @touchmove="touchmove"
          :class="{
            touch: touchIndex == index,
          }"
        >
          <div
            class="value"
            :style="{ height: item.height + 'rem', zIndex: item.zIndex }"
          ></div>
          <div
            v-if="item.height2"
            class="value v2"
            :class="{ opacity: item.height2 == item.height }"
            :style="{ height: item.height2 + 'rem', zIndex: item.zIndex2 }"
          ></div>
        </div>
        <div class="center-line"></div>

      </div>
      <div class="footer">
        <div
          class="title"
          :class="{ center: titleList.length <= 7 || isShowAll }"
          v-for="(item, index) in titleList"
          :key="index"
        >
          <div class="text" v-if="item.show">{{ item.name }}</div>
        </div>
      </div>
    </div>
    <!--柱状图-->

    <!--条形图-->
    <div class="type1" v-if="type == 1 && values1 && values1.length > 0">
      <div class="grid">
        <div class="item" style="height: 0; opacity: 0">
          <div class="info"></div>
          <div class="value" ref="type1Value" style="margin-right: 1rem">
            <div class="num" ref="type1MaxValue">{{ maxValue }}</div>
          </div>
        </div>
        <div class="item" v-for="(item, index) in type1List" :key="index">
          <div
            class="info"
            :class="{
              del: item.icon_name == '' && item.name == '已删除',
            }"
          >
            <img
              v-if="item.icon_name"
              :src="
                require('../assets/icon/category/' + item.icon_name + '.png')
              "
            />
            {{ item.name }}
          </div>
          <div class="value">
            <div
              class="block"
              :style="{ width: item.width + 'px', background: color }"
            ></div>
            <div class="num"><money-value :money="item.value" /></div>
          </div>
        </div>
      </div>
    </div>
    <!--条形图-->
  </div>
</template>

<script>
import { mapState } from "vuex";
import BigNumber from "bignumber.js";
import MoneyValue from "./MoneyValue.vue";
import ToolTip from "./ToolTip.vue";

export default {
  components: { MoneyValue, ToolTip },
  name: "Chart",
  props: {
    type: {
      type: Number,
      default: 0, // 0柱状图,1条形图
    },
    values1: {
      type: Array,
    },
    values2: {
      type: Array,
    },
    title: {
      type: Array,
    },
    icons: {
      type: Array,
    },
    name: {
      type: String,
    },
    isShowAll: {
      type: Boolean,
      default: false,
    },
    color: {
      type: String,
      default: "#f5dd0a",
    },
  },

  data() {
    return {
      titleList: [],
      maxValue: 0,
      mediumValue: 0,
      type1List: [],
      isShowContrast: true,
      itemRectList: [],
      itemList: [],
      touchIndex: -1,
      tooltipLeft: "",
      tooltipTop: "",
      tooltipContent: "",
    };
  },
  watch: {
    values1: function (val) {
      if (this.type == 1) {
        let maxValue = Math.max(...val);
        this.maxValue = maxValue;
        this.isShowContrast = true;
      }
      this.touchIndex = -1;
    },
    maxValue: function () {
      if (this.type == 1) {
        this.$nextTick(function () {
          if (this.$refs.type1MaxValue) {
            this.handleType1List();
          }
        });
      }

      // this.$nextTick(() => {
      //   if (this.$refs.grid) {
      //     // console.log(this.$refs.grid.children[0]);
      //     for (let i = 2; i < this.$refs.grid.children.length; i++) {
      //       let item = this.$refs.grid.children[i];

      //       item.ontouchmove = function (e) {
      //         console.log("move" + i);
      //         e.stopPropagation();
      //         var touch = e.targetTouches[0];

      //         var ele = document.elementsFromPoint(touch.pageX, touch.pageY);
      //         console.log(touch.pageX, touch.pageY);
      //       };
      //     }
      //     // this.$refs.grid.children.forEach((element) => {
      //     //   console.log(element);
      //     // });
      //   }
      // });
    },
    touchIndex: function (val) {
      if (this.list[val]) {
        this.tooltipContent =
          this.titleList[val].name +
          "，支出：" +
          (this.list[val].value1 ? this.list[val].value1 : 0) +
          "，收入：" +
          (this.list[val].value2 ? this.list[val].value2 : 0);
        this.updateTooltipLocation(val);
      }
    },
  },
  computed: {
    list: function () {
      if (this.type != 0) {
        return;
      }
      let data = this.values1;
      if (this.values2) {
        data = data.concat(this.values2);
      }
      let maxValue = Math.max(...data);
      let heightRatio = 8 / maxValue;

      this.maxValue = maxValue;
      this.mediumValue = new BigNumber(maxValue).div(2).toFixed(2).toString();
      let list = [];

      let titleList = [];

      let isShowAll = true;
      let marginNum = 0;

      let values = Math.max(...this.values1) > 0 ? this.values1 : this.values2;
      if (values.length > 7 && !this.isShowAll) {
        isShowAll = false;
        marginNum = parseInt(values.length / 4);
      }

      let nextShowIndex = 0;
      for (let i = 0; i < values.length; i++) {
        let v1 = this.values1[i];

        let item = {};
        item.height = heightRatio * v1;
        if (item.height <= 0.01 && v1 > 0) {
          item.height = 0.1;
        }

        item.value1 = v1;

        if (this.values2) {
          let v2 = this.values2[i];
          item.value2 = v2;

          item.height2 = heightRatio * v2;
          if (parseFloat(v2) > parseFloat(v1)) {
            item.zIndex = 2;
            item.zIndex2 = 1;
          } else {
            item.zIndex = 1;
            item.zIndex2 = 2;
          }

          if (item.height2 <= 0.01 && v2 > 0) {
            item.height2 = 0.1;
          }
        }

        list.push(item);

        let title = {};

        title.name =
          this.title && this.title.length > 0 ? this.title[i] : i + this.name;
        title.show = true;
        if (!isShowAll && nextShowIndex != i) {
          title.show = false;
        } else {
          nextShowIndex = i + marginNum;
        }

        titleList.push(title);
      }
      this.titleList = titleList;

      this.$nextTick(() => {
        this.updateItem();
      });
      return list;
    },
  },
  mounted() {},
  methods: {
    isInElement(touchX, el) {
      return touchX >= el.left && touchX <= el.right;
    },
    findElement(touchX) {
      let res;
      for (let i = 0; i < this.itemRectList.length; i++) {
        let element = this.itemRectList[i];

        if (this.isInElement(touchX, element)) {
          res = i;
          break;
        }
      }
      return res;
    },
    updateItem() {
      //  获取所有元素位置信息
      let parent = this.$refs.grid;
      if (!parent) {
        return;
      }

      this.itemList = [];
      this.itemRectList = [];

      for (let i = 2; i < parent.children.length; i++) {
        this.itemRectList.push(parent.children[i].getBoundingClientRect());
        this.itemList.push(parent.children[i]);
      }
    },
    touchstart(e) {
      let index = this.findElement(e.targetTouches[0].clientX);
      this.touchIndex = index;

      // this.updateTooltipLocation();
    },
    touchmove(e) {
      let index = this.findElement(e.targetTouches[0].clientX);
      this.touchIndex = index;
    },
    updateTooltipLocation(index) {
      if (!this.$refs.tolltip) {
        return;
      }

      console.log("update location" + this.itemRectList[index].left);
      this.tooltipLeft = this.itemRectList[index].left + "px";
      this.tooltipTop = this.itemRectList[index].top + "px";
    },

    handleType1List() {
      let data = this.values1;

      let maxValueWidth = this.$refs.type1MaxValue.offsetWidth;

      let maxWidth = this.$refs.type1Value.offsetWidth - maxValueWidth - 20;

      let widthRatio = maxWidth / this.maxValue;
      // console.log(
      //   "maxvalue:" +
      //     this.maxValue +
      //     ",width:" +
      //     maxValueWidth +
      //     ",maxWidth:" +
      //     maxWidth
      // );

      //  隐藏测量用的div
      this.isShowContrast = false;
      //  组装数据
      let result = [];

      // console.log(data)
      for (let i = 0; i < data.length; i++) {
        let item = {};
        item.name = this.title[i];
        if (this.icons) {
          item.icon_name = this.icons[i];
        }
        item.width = widthRatio * data[i];
        // console.log(item.width)
        if (item.width <= 0.1 && data[i] > 0) {
          item.width = 0.2;
        }
        item.value = data[i];

        result.push(item);
      }
      //  从大到小排序
      // data.sort(function (a, b) {
      //   return b - a;
      // });
      result.sort(function (a, b) {
        return b.width - a.width;
      });

      this.type1List = result;
    },
  },
};
</script>

<style lang="less" scoped>
.chart {
  // background: #fff;
  // padding: 1rem;
  // padding-top: 2rem;
  // border-radius: 1rem;
  //  柱状图
  .type0 {
    .grid {
      display: flex;
      height: 8rem;
      position: relative;
      padding-right: 1rem;
      color: #999;
      border-style: dotted;
      border-color: rgba(216, 210, 210, .8);
      border-top: none;
      border-width: 1px;
      .center-line{
        position: absolute;
        height: 1px;
        left: .3rem;
        right: .3rem;
        border-top: 1px dotted rgba(216, 210, 210, .9);
        top: 0;
        bottom: 0;
        margin: auto;
      }
      .max {
        position: absolute;
        right: -0.5rem;
        font-size: 0.8rem;
        z-index: 2;
        top: -1rem;
        display: flex;
        justify-content: flex-end;
        padding: 0;
      }
      .medium {
        position: absolute;
        font-size: 0.8rem;
        top: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        right: -0.5rem;
        z-index: 2;
      }
      .touch {
        border-top-left-radius: 0.4rem;
        border-top-right-radius: 0.4rem;
        z-index: 5;
        background: rgba(211, 211, 209, 0.5);
      }
      .values {
        flex: 1;
        text-align: center;
        margin-top: 1rem;
        position: relative;
        margin: 0 0.2rem;
        display: flex;
        align-items: flex-end;
        .value {
          background: #f5dd0a;
          // border-radius: 0.3rem;
          border-top-left-radius: 0.4rem;
          border-top-right-radius: 0.4rem;
          flex: 1;
          position: absolute;
          width: 100%;
        }
        .v2 {
          background: #4170f3;
          opacity: 1;
        }
        .opacity {
          opacity: 0.5;
        }
      }
      .values:hover {
        // background: #4170f3;
      }
    }

    .footer {
      margin-top: 1rem;
      display: flex;
      height: 1.5rem;
      padding-right: 1rem;
      .title {
        flex: 1;
        text-align: left;
        position: relative;
        font-size: 0.8rem;
        margin: 0 0.2rem;
        // height: 3rem;
        .text {
          position: absolute;
          top: 0;
          width: 3rem;
        }
      }
      .center {
        text-align: center;
        .text {
          position: unset;
          width: unset;
        }
      }
    }
  }

  //  条形图
  .type1 {
    .grid {
      // padding-right: 2rem;
      .item {
        display: flex;
        margin-top: 0.5rem;

        .info {
          width: 30%;
          display: flex;
          align-items: center;
          font-size: 1rem;
          color: rgb(88, 88, 88);
          img {
            width: 1rem;
            margin-right: 0.4rem;
          }
        }
        .value {
          flex: 1;
          position: relative;
          display: flex;
          align-items: center;
          .block {
            height: 100%;
            background: rgb(245, 223, 26);
            border-top-right-radius: 0.4rem;
            border-bottom-right-radius: 0.4rem;
          }
          .num {
            // display: inline-block;
            // position: absolute;
            // right: -2rem;
            // top: 0;
            // width: 2rem;
            margin-left: 0.5rem;
            font-size: 0.8rem;
            // bottom: 0;
          }
        }
      }
      .item:first-child {
        margin-top: 0;
      }
    }
  }
}
</style>
