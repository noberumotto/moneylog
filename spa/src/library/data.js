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
        //  ?????????????????????

        //  ?????????
        localStorage.removeItem("waitSyncData");


        window.$request.post({
            url: "moneylog/add",
            data: data,
            success: (res) => {
                if (options.complete) {
                    options.complete();
                }
                if (res.data.length == 0) {
                    //  ??????????????????????????????
                    pushWaitSyncData(data);

                    window.$toast({
                        content: "???? ??????????????????"
                    });
                }
                else if (res.data.length !== data.length) {
                    //  ?????????????????????
                    window.$toast({
                        content: "???? ??????" + (data.length - res.data.length) + "?????????????????????"
                    });

                    //  ??????????????????????????????
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
                //  ??????????????????????????????
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
 * ?????????????????????
 * @param {Array} data ??????
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
