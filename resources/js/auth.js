window.auth = function (guard = null) {
    return Vue.createApp({
        data() {
            return {}
        },
        methods: {
            user: function () {
                if (guard === undefined || guard === null) {
                    return JSON.parse(document.getElementsByTagName('meta').namedItem('auth-user').content);
                }

                if (typeof guard === 'string') {
                    return document.getElementById(guard) === null ? null : JSON.parse(document.getElementById(guard).content);
                }

                throw 'TypeError';
            },
            id: function () {
                return this.user() === null ? null : this.user().id;
            },
            check: function () {
                return this.user() !== null;
            }
        }
    }).mount('meta[name="auth-user"]') ?? {
        user: function () {
            return null;
        },
        id: function () {
            return null;
        },
        check: function () {
            return false;
        }
    };
}