
import BigNumber from "bignumber.js";

const operatorArr = ['-', '+', '×', '÷'];

/**
 * 从左往右的简单算式计算
 * 不支持先乘除后加减的判断，必须从左到右依次括号包围如：((1+2)+3)×3，1+2+3+4，如果输入：1+2×3结果为：9而不是7
 * @param {String} str 算式
 */
const evaluate = function (str) {
    let index = 0;
    let end = str.length;


    let leftValue = "";
    let operator = "";

    let state = "wait";// wait,in,out,


    let value = "";// 输入
    while (index < end) {
        let char = str.substring(index, index + 1);

        let type = getChartType(char);

        // console.log(char + ",type:" + type + ",state:" + state);
        switch (state) {

            case "wait":
                //  完成
                switch (type) {
                    case "open":
                        state = "wait";
                        break;
                    case "number":
                        state = "in";

                        value += char;

                        break;
                    case "operator":
                        state = "in";
                        value = char;
                        break;
                }
                break;

            case "in":
                //  完成
                switch (type) {
                    case "number":
                        state = "in";
                        value += char;
                        break;
                    case "dot":
                        state = "in";
                        value += char;
                        break;
                    case "operator":
                        state = "wait";
                        if (leftValue.toString().length > 0) {
                            //  计算
                            leftValue = calculate(leftValue, value, operator);


                        }
                        else {
                            leftValue = value;
                        }
                        //  清空读取缓存
                        value = "";
                        //  保存运算符
                        operator = char;
                        // console.log(leftValue + "|" + value + "|" + operator);
                        break;
                    case "close":
                        //  计算
                        leftValue = calculate(leftValue, value, operator);
                        value = "";
                        state = "out";
                        break;

                }

                break;
            case "out":
                switch (type) {
                    case "operator":
                        state = "wait";
                        operator = char;
                        value = "";
                        break;
                }
                break;
        }
        // console.log("leftvalue:" + leftValue + ",value:" + value + ",state:" + state + ",char:" + char);
        index++;
    }
    if (value.length > 0 && operator.length > 0) {
        leftValue = calculate(leftValue, value, operator);
    }

    if (leftValue.length == 0) {
        return str;
    }

    console.log("计算结果：" + str + " = " + leftValue);

    return leftValue;
}

/**
 * 获取字符的类型
 * @param {String} char 单个字符
 * @returns 字符类型 open=左括号(,close=右括号),operator=运算符,dot=小数点,number=数字
 */
function getChartType(char) {
    if (char == "(") {
        return "open";
    }
    else if (char == ")") {
        return "close";
    } else if (operatorArr.includes(char)) {
        return "operator";
    } else if (char == ".") {
        return "dot";
    } else {
        return "number";
    }
}

/**
 * 计算
 * @param {String|Number} left 左数值
 * @param {String|Number} right 右数值
 * @param {String} operator 运算符：-|+|×|÷
 * @returns Number
 */
function calculate(left, right, operator) {
    if (operator == "-") {
        return new BigNumber(left).minus(right).toNumber();
    } else if (operator == "+") {
        return new BigNumber(left).plus(right).toNumber();
    } else if (operator == "×") {
        return new BigNumber(left).multipliedBy(right).toNumber();
    } else if (operator == "÷") {
        return new BigNumber(left).dividedBy(right).toNumber();
    }
}
export default {
    evaluate
}