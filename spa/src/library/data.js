const getCategoryData = (options = {}) => {

    if (options.request) {
        window.$request.get({
            url: "tags/list",
            success: (res) => {
                window.$store.commit("user/setCategoryData", res.data);

                options.success(window.$store.state.user.categoryData)
            },
        });
        return;
    }
    let storeData = window.$store.state.user.categoryData;
    if (storeData.length == 0) {
        let localData = localStorage.getItem("categoryData");
        if (localData) {
            window.$store.commit("user/setCategoryData", JSON.parse(localData));
            options.success(window.$store.state.user.categoryData)
        } else {
            window.$request.get({
                url: "tags/list",
                success: (res) => {
                    window.$store.commit("user/setCategoryData", res.data);

                    options.success(window.$store.state.user.categoryData)
                },
            });
        }
    }
    else {
        options.success(storeData)
    }
}

const getAccountData = (options = {}) => {
    if (options.request) {
        window.$request.get({
            url: "account/list",
            success: (res) => {
                window.$store.commit("user/setAccountData", res.data);

                options.success(window.$store.state.user.accountData)
            },
        });
        return;
    }
    let storeData = window.$store.state.user.accountData;
    if (storeData.length == 0) {
        let localData = localStorage.getItem("accountData");
        if (localData) {
            window.$store.commit("user/setAccountData", JSON.parse(localData));
            options.success(window.$store.state.user.accountData)
        } else {
            window.$request.get({
                url: "account/list",
                success: (res) => {
                    window.$store.commit("user/setAccountData", res.data);

                    options.success(window.$store.state.user.accountData)
                },
            });
        }
    }
    else {
        options.success(storeData)
    }
}

const postWaitSyncData = (options = {}) => {

    let data = localStorage.getItem("waitSyncData")
        ? JSON.parse(localStorage.getItem("waitSyncData"))
        : null;
    if (data && data.length > 0) {
        //  有待同步的数据

        //  先移除
        localStorage.removeItem("waitSyncData");


        window.$request.post({
            url: "moneylog/add",
            data: data,
            success: (res) => {
                if (options.complete) {
                    options.complete();
                }
                if (res.data.length == 0) {
                    //  同步失败后，重新放回
                    pushWaitSyncData(data);

                    window.$toast({
                        content: "🚨 同步数据失败"
                    });
                }
                else if (res.data.length !== data.length) {
                    //  有部分数据失败
                    window.$toast({
                        content: "📌 有：" + (data.length - res.data.length) + "条记录同步失败"
                    });

                    //  取出失败数据重新返回
                    data.forEach((local, index) => {
                        res.data.forEach(suc => {
                            if (suc.account_id == local.account_id &&
                                suc.time == local.time &&
                                suc.status == local.status &&
                                suc.tags_id == local.tags_id &&
                                suc.money == local.money) {
                                data.splice(index, 1);

                            }
                        });
                    });

                    pushWaitSyncData(data);

                }
                else {

                    options.success()
                }


            }
            , error: () => {
                //  同步失败后，重新放回
                pushWaitSyncData(data);

                if (options.complete) {
                    options.complete();
                }
            }
        });
    }
    else {
        if (options.complete) {
            options.complete();
        }
    }

}

/**
 * 丢入待同步数据
 * @param {Array} data 数据
 */
function pushWaitSyncData(data, isClear = false) {
    if (data && data.length > 0) {
        if (isClear) {
            localStorage.removeItem("waitSyncData");
        }
        let waitSyncData = localStorage.getItem("waitSyncData")
            ? [...JSON.parse(localStorage.getItem("waitSyncData")), ...data]
            : data;

        localStorage.setItem("waitSyncData", JSON.stringify(waitSyncData));
    }
}

export default {
    getCategoryData,
    getAccountData,
    postWaitSyncData,
    pushWaitSyncData
}
