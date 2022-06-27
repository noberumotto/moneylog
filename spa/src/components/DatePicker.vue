<template>
  <div class="dp">
    <div class="masklayer">
      <div class="container">
        <div class="header-title">选择记账时间</div>
        <!--日期-->
        <div class="date">
          <div class="head">
            <div class="year">
              <div class="btn" @click="setYear(-1)">←</div>
              <div class="value">{{ year }}</div>
              <div class="btn" @click="setYear(1)">→</div>
            </div>
            <div class="month">
              <div class="btn" @click="setMonth(-1)">←</div>
              <div class="value">{{ month }}</div>
              <div class="btn" @click="setMonth(1)">→</div>
            </div>
          </div>

          <div class="days">
            <div
              class="day"
              v-for="(i, index) in days"
              :key="index"
              @click="chooseDay(i)"
              :class="{ selected: i == day }"
            >
              {{ i }}
            </div>
            <div class="day"></div>
            <div class="day"></div>
            <div class="day"></div>
            <div class="day"></div>
          </div>
        </div>

        <!--时间-->
        <div class="time">
          <select v-model="hours">
            <option v-for="(i, index) in hoursArray" :key="index" :value="i">
              {{ i }}
            </option>
          </select>
          <label></label>
          <select v-model="minutes">
            <option v-for="(i, index) in minutesArray" :key="index" :value="i">
              {{ i }}
            </option>
          </select>
        </div>
      </div>

      <div class="action">
        <div class="btn" @click="today">现在</div>
        <div class="btn done" @click="onDone">确认</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DatePicker",
  props: {
    time: {
      type: String,
      default: "",
    },
  },
  data() {
    let date = new Date();
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let day = date.getDate();
    let days = new Date(year, month, 0).getDate();

    hours = hours < 10 ? "0" + hours : hours;
    minutes = minutes < 10 ? "0" + minutes : minutes;
    let hoursArray = [];
    for (let i = 0; i < 24; i++) {
      hoursArray.push(i < 10 ? "0" + i : i);
    }

    let minutesArray = [];
    for (let i = 0; i < 60; i++) {
      minutesArray.push(i < 10 ? "0" + i : i);
    }
    return {
      year: year,
      month: month,
      days: days,
      day: day,
      hoursArray: hoursArray,
      minutesArray: minutesArray,
      hours: hours,
      minutes: minutes,
    };
  },
  methods: {
    onDone() {
      this.$emit("done", [
        this.year,
        this.month,
        this.day,
        this.hours,
        this.minutes,
      ]);
    },
    setYear(num) {
      let date = new Date();
      let year = date.getFullYear();
      let newYear = parseInt(this.year) + parseInt(num);
      if (newYear > year) {
        newYear = year;
      }

      this.year = newYear;
      this.updateDays();
    },
    setMonth(num) {
      let newVal = parseInt(this.month) + parseInt(num);
      if (newVal > 12) {
        newVal = 12;
      } else if (newVal < 1) {
        newVal = 1;
      }

      this.month = newVal;
      this.day = 1;
      this.updateDays();
    },
    updateDays() {
      this.days = new Date(this.year, this.month, 0).getDate();
    },
    chooseDay(val) {
      this.day = val;
    },
    today() {
      let date = new Date();
      this.days = new Date(
        date.getFullYear(),
        date.getMonth() + 1,
        0
      ).getDate();
      let year = date.getFullYear();
      let month = date.getMonth() + 1;
      let hours = date.getHours();
      let minutes = date.getMinutes();
      let day = date.getDate();
      this.year = year;
      this.month = month;
      this.day = day;
      this.hours = hours < 10 ? "0" + hours : hours;
      this.minutes = minutes < 10 ? "0" + minutes : minutes;
    },
  },
};
</script>

<style lang="less" scoped>
.dp {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
  .masklayer {
    background: rgba(0, 0, 0, 0.8);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }
  .action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-right: 2rem;
    .btn {
      padding: 0 2rem;
      background: #ffe81b;
      border-radius: 0.5rem;
      margin-left: 2rem;
      height: 4rem;
      display: flex;
      align-items: center;
      box-shadow: 0 1px 0px 2px rgba(250, 69, 69, 0.1);
    }
    .done {
      padding: 0 3rem;
      background: #4170f3;
      color: #fff;
      font-size: 1.2rem;
      box-shadow: 0 1px 0px 2px rgba(65, 112, 243, 0.1);
    }
  }
  .container {
    z-index: 1000;
    background: #fff;
    margin: 2rem;
    padding: 1rem;
    border-radius: 2rem;
    box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.1);

    .time {
      // margin-top: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.3);
      margin: 1rem 4rem;
      border-radius: 1rem;
      label {
        width: 1px;
        height: 1rem;
        background: #ecebeb;
      }
      select {
        font-size: 2rem;
        border: none;
        appearance: none;
        margin: 0;
        padding: 0.5rem 1rem;
        background: #fff;
        text-align: center;
        color: #4170f3;
      }
    }

    .date {
      text-align: center;
      color: rgb(116, 116, 116);
      margin-top: 1rem;
      .head {
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .year,
      .month {
        display: flex;
        align-items: center;
      }

      .days {
        display: flex;
        flex-wrap: wrap;
        margin-top: 1rem;
        justify-content: space-between;

        .day {
          width: 3rem;
          height: 3rem;
          padding: 1rem 0;
          border: 2px solid #fff;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .selected {
          // background: #4170f3;
          border-radius: 1rem;
          color: #4170f3;
          border: 2px solid #4170f3;
          font-size: 1.5rem;
          font-weight: bold;
          box-shadow: 0 1px 0px 1px rgba(65, 112, 243, 0.1);
        }
      }
      .value {
        font-size: 1.2rem;
        margin: 0 0.6rem;
      }
      .btn {
        background: #fff;
        border-radius: 0.5rem;
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        color: rgb(94, 94, 94);
        justify-content: center;
        box-shadow: 0 1px 0px 2px rgba(216, 210, 210, 0.3);
      }
    }
  }
}
</style>
