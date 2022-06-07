import axios from 'axios';
import config from '../config'
import toast from "../components/Toast/index";

axios.defaults.baseURL = config.apiBaseUrl;

const file = function (options = {}) {
    axios.defaults.headers.common['token'] = localStorage.getItem('Token');


    axios.postForm(options.url, {
        'file': options
    }).then(function (response) {
        handleSuccess(options, response);
    })
        .catch(function (error) {
            console.log(window)
            handleError(options, error);

        })
        .then(function () {
            if (options.complete) {
                options.complete();
            }
        });
}
const get = function (options = {}) {

    if (options.data === undefined) {
        options.data = {}
    }

    options.method = "get";
    request(options);
}
const post = function (options = {}) {
    if (options.data === undefined) {
        options.data = {}
    }

    options.method = "post";
    request(options);
}

function request(options = {}) {
    axios.defaults.headers.common['token'] = localStorage.getItem('Token');

    let config = Object.assign(options, {});
    if (options.method == "get" && options.data) {
        config.params = options.data;
    }

    axios(config)
        .then(function (response) {
            handleSuccess(options, response);
        })
        .catch(function (error) {
            console.log(window)
            handleError(options, error);

        })
        .then(function () {
            if (options.complete) {
                options.complete();
            }
        });
}

function handleSuccess(options, res) {

    if (res.data.code == 0) {
        if (!options.handle) {
            window.$toast({
                content: res.data.msg
            });
        }
        if (options.fail) {
            options.fail(res.data)
        }
        if (options.error) {
            options.error(res);
        }
        return;
    }
    if (options.success) {
        options.success(res.data);
    }
}

function handleError(options, error) {
    console.log(error)
    let statusCode = error.response.status;
    if (statusCode === 401) {

        window.$store.commit('user/logOut');
        window.$toast({
            content: "💡 身份信息已过期，请重新登录"
        })
    }
    if (statusCode === 404) {
        window.$toast({
            content: "服务器好像在维护"
        })
    }
    if (statusCode === 500) {
        window.$toast({
            content: "🐞 糟糕，出Bug了"
        })
    }
    if (error.code == "ERR_NETWORK") {
        window.$toast({
            content: "📡 暂时与服务器失去了连接"
        })
    }

    if (options.error) {
        options.error(error);
    }
}
export default {
    get,
    post,
    file
}