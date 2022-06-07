import config from "../../config";

export default {
    namespaced: true,
    state: {
        isLogin: false,
        email: '',
        accountData: [],// 账户数据
        categoryData: [],//分类数据
        selectedInAccount: {},// 选择的收入账户
        selectedOutAccount: {},//    选择的支出账户
        selectedInCategory: {},//    选择的收入分类
        selectedOutCategory: {},//   选择的支出分类
        selectedTime: {},//  选择的时间
        selectedAction: 0,// 选择的记账类型：0=支出，1=收入
        pageData: [],//  页面数据，用于处理返回后数据重加载
    },
    mutations: {
        update(state, payload) {
            for (let i in payload) {
                for (let j in state) {
                    if (i === j) {
                        state[j] = payload[i];
                    }
                }
            }
        },
        setAccountData(state, data) {
            state.accountData = data;
            localStorage.setItem("accountData", JSON.stringify(data));
        },
        setCategoryData(state, data) {
            data.forEach(element => {
                if (!config.icons.includes(element.icon_name)) {
                    element.icon_name = config.icons[0];
                }
            });
            state.categoryData = data;
            localStorage.setItem("categoryData", JSON.stringify(data));
        },
        setLogin(state, data) {
            state.isLogin = true;
            state.email = data.email;
            localStorage.setItem("Token", data.token);
            localStorage.setItem("email", data.email);

        },
        logOut(state) {
            state.isLogin = false;
            state.email = '';
            state.accountData = [];
            state.categoryData = [];

            localStorage.removeItem('Token');
            localStorage.removeItem('email');
            localStorage.removeItem('categoryData');
            localStorage.removeItem('accountData');

            window.$router.push('/login');

        }
    },
    actions: {
    }
};