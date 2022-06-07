const getUnit = function (money) {
    let unit = "";
    money = Math.abs(money);
    if (money < 10) {
        unit = "";
    } else if (money >= 10 && money < 100) {
        unit = "拾";
    } else if (money >= 100 && money < 1000) {
        unit = "佰";
    } else if (money >= 1000 && money < 10000) {
        unit = "仟";
    } else if (money >= 10000 && money < 100000) {
        unit = "万";
    } else if (money >= 100000 && money < 1000000) {
        unit = "拾万";
    } else if (money >= 1000000 && money < 10000000) {
        unit = "佰万";
    } else if (money >= 10000000 && money < 100000000) {
        unit = "仟万";
    } else if (money >= 100000000 && money < 1000000000) {
        unit = "亿";
    } else {
        unit = "😵";
    }
    return unit;
}

export default {
    getUnit
}