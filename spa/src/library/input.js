const operatorArr = ['-', '+', '×', '÷'];

let state = "wait";// wait,in,out,

let res = "";// 返回算式

let value = "";// 输入


//  运算符
let inputOperatorArr = [];

//  输入的数字
let inputNumberArr = [];

//  上一个字符
let lastChar = "";

/**
 * 重置输入
 */
const reset = function () {
    inputOperatorArr = [];
    lastChar = "";
    state = "wait";
    res = "";
}

/**
 * 获得当前的算式字符串
 * @returns String
 */
const getInputStr = function () {
    return res.toString();
}
/**
 * 输入并返回算式字符串
 * @param {String} char 字符：0-9|.|-+×÷
 * @returns String
 */
const input = function (char) {

    let type = getInputType(char);


    switch (state) {
        case "close":
            switch (type) {
                case "operator":
                    res += char;
                    value = "";
                    inputOperatorArr.push(char);
                    state = "wait";
                    lastChar = char;
                    break;
                case "del":
                    //  删除
                    del();
                    break
            }
            break;
        case "wait":
            switch (type) {
                case "positive":
                    state = "in";
                    res += char;
                    value += char;
                    lastChar = char;
                    break;
                case "zero":
                    state = "in";
                    value += char;
                    res += char;
                    lastChar = char;
                    break;
                case "del":
                    //  删除
                    del();
                    break
            }
            break;
        case "in":
            switch (type) {
                case "operator":

                    //  完成数字输入
                    if (value == "0") {
                        break;
                    }

                    if (lastChar == ".") {
                        break;
                    }

                    //  判断是否需要加上括号
                    if (char == "×" || char == "÷") {

                        if (inputOperatorArr.length > 0) {
                            let lastOperator = inputOperatorArr[inputOperatorArr.length - 1];
                            if (lastOperator == "-" || lastOperator == "+") {
                                res = "(" + res + ")";
                            }
                        }
                    }

                    // inputNumberArr.push(value);
                    res += char;
                    value = "";
                    inputOperatorArr.push(char);
                    state = "wait";
                    lastChar = char;
                    break;
                case "positive":
                case "zero":
                    state = "in";

                    if (value == "0" && type == "zero" || value == "0" && type == "positive") {
                        break;
                    }

                    value += char;
                    res += char;
                    lastChar = char;
                    break;
                case "dot":
                    //  需要判断
                    if (value.indexOf(".") == -1) {
                        value += char;
                        res += char;

                        lastChar = char;
                    }
                    state = "in";
                    break;
                case "del":
                    //  删除
                    del();
                    break

            }
            break;
    }


    return res;
}

function del() {
    let resLength = res.toString().length;


    if (resLength == 0) {
        return;
    }

    //  即将删除的字符
    let delChar = res.substring(resLength - 1, resLength);

    value = value.substring(0, value.toString().length - 1);

    let delStartIndex = 0;

    let delCharType = getInputType(delChar);

    if (delCharType == "operator") {
        inputOperatorArr.splice(inputOperatorArr.length - 1, 1);
    }
    else if (delCharType == "close") {
        //  删除括号时对等
        delStartIndex = 1;
    }

    res = res.substring(delStartIndex, resLength - 1);

    //  判断删除后是否还有括号
    resLength = res.toString().length;
    let lastStr = res.substring(resLength - 1, resLength);
    if (lastStr == ")") {
        //  继续删除
        res = res.substring(1, resLength - 1);
    }


    resLength = res.toString().length;
    lastStr = res.substring(resLength - 1, resLength);

    lastChar = lastStr;

    rebackState(lastStr);

    value = getRightValue(res);
}

/**
 * 回滚状态
 * @param {String} char 输入字符
 */
function rebackState(char) {
    if (char == undefined || char == "") {
        state = "wait";
        return;
    }
    let type = getInputType(char);
    switch (type) {
        case "zero":
        case "dot":
        case "positive":
            state = "in";
            break;
        case "operator":
            state = "wait"

            break
        case "close":
            state = "close"
            break
        default:
            state = "wait"
    }
}


function getRightValue(str) {


    let index = str.toString().length - 1;
    let end = -1;

    let rightValue = "";

    let goto = true;

    while (index > end && goto) {
        let char = str.substring(index, index + 1);
        let chartType = getInputType(char);

        switch (chartType) {
            case "operator":
                if (rightValue.toString().length == 0) {
                    rightValue = char;
                }
                goto = false;
                break;
            case "dot":
            case "zero":
            case "positive":
                rightValue = char + "" + rightValue;
                break;
        }

        index--;
    }
    return rightValue;
}
/**
 * 获取输入类型
 * @param {String} char 单个字符
 * @returns 字符类型 open=左括号(,close=右括号),operator=运算符,dot=小数点,positive=正数,del=删除,zero=0
 */
function getInputType(char) {
    if (char == "<") {
        return "del";
    }
    else if (char == "(") {
        return "open";
    }
    else if (char == ")") {
        return "close";
    } else if (operatorArr.includes(char)) {
        return "operator";
    } else if (char == ".") {
        return "dot";
    }
    else if (char == "0") {
        return "zero";
    }
    else {
        return "positive";
    }
}

export default {
    input,
    reset,
    getInputStr
}