/**
 * 获取一周的开始结束日期，默认返回本周
 * @param {*} options date:指定日期,week:查找多少周，支出负数向后查找,res:function(startdate,enddate)回调函数返回开始结束日期
 */
const getWeekRange = function (options = {}) {
    let date = options.date ? options.date : new Date();
    if (typeof (date) != Date) {
        date = new Date(date);
    }
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();

    let week = date.getDay();

    let startDate, endDate;
    if (week == 1) {
        startDate = date;

    }
    else {
        if (week == 0) {
            week = 7;
        }
        day -= week - 1;

        startDate = new Date(year, date.getMonth(), day);
    }

    //  向前或后多少周查找
    if (options.week) {
        let weekNum = Math.abs(options.week);

        if (options.week > 0) {
            //  向前
            startDate = new Date(startDate.setDate(startDate.getDate() + 7 * weekNum));
        }
        else {
            //  向后
            startDate = new Date(startDate.setDate(startDate.getDate() - 7 * weekNum));
        }
    }

    endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
    endDate = new Date(endDate.setDate(endDate.getDate() + 6));

    if (options.res) {
        options.res(startDate, endDate);
    }
    // console.log("日期：" + toString(date));

    // console.log("开始日期：" + toString(startDate));
    // console.log("结束日期：" + toString(endDate));

}

/**
 * 获取日期的月份开始日期（即1号），默认本月
 * @param {Date|String} date 日期，默认本月
 * @param {Number} num 向前或后查找几个月
 * @returns {Date} 
 */
const getMonth = function (date = undefined, num = 0) {
    date = date ? date : new Date();
    if (typeof (date) != Date) {
        date = new Date(date);
    }


    let year = date.getFullYear();
    let month = date.getMonth();

    date = new Date(year, month, 1);


    date = new Date(date.setMonth(date.getMonth() + num));


    return date;
}

/**
 * 获取指定日期的年份开始日期，默认今年
 * @param {Date|String} date 日期
 * @param {Number} num 向前或后查找年份（0当年，1后一年，-1前一年）
 * @returns {Date}
 */
const getYear = function (date = undefined, num = 0) {
    date = date ? date : new Date();
    if (typeof (date) != Date) {
        date = new Date(date);
    }


    let year = date.getFullYear();

    date = new Date(year, 0, 1);
    date = new Date(date.setFullYear(date.getFullYear() + num));

    // console.log(toString(date))
    return date;
}

const getDay = function (date = undefined, num = 0) {
    date = date ? date : new Date();
    if (typeof (date) != Date) {
        date = new Date(date);
    }

    new Date().setDate

    date = new Date(date.setDate(date.getDate() + num));

    return date;
}

/**
 * 格式化日期
 * @param {Date|String} time 日期
 * @param {String} parseStr 格式化（y:年,m:月,d:日,h:时,i:分,s:秒），默认:y-m-d h:i
 * @returns {String}
 */
const toString = function (time = undefined, parseStr = "y-m-d h:i") {
    let date = time ? time : new Date();
    if (typeof (date) != Date) {

        date = date.toString().length == 10 ? date * 1000 : date;

        date = new Date(date);
    }

    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let seconds = date.getSeconds();

    month = month < 10 ? "0" + month : month;
    day = day < 10 ? "0" + day : day;

    hours = hours < 10 ? "0" + hours : hours;
    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    parseStr = parseStr.replace("y", year);
    parseStr = parseStr.replace("m", month);
    parseStr = parseStr.replace("d", day);
    parseStr = parseStr.replace("h", hours);
    parseStr = parseStr.replace("i", minutes);
    parseStr = parseStr.replace("s", seconds);

    return parseStr;
}

export default {
    getDay,
    getYear,
    getWeekRange,
    toString,
    getMonth
}