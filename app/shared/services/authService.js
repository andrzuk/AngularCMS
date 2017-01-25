angular.module('authService', [])

.factory('Auth', ['$http', '$q', 'AuthToken','config', function ($http, $q, AuthToken, config) {

    var authFactory = {};

    authFactory.login = function (credentials) {
        return $http({
            method: 'POST',
            url: config.apiUrl + 'login.php',
            data: $.param(credentials),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .success(function (data) {
            AuthToken.setToken(data.token);
            AuthToken.setUserId(data.user_id);
            return data;
        });
    };

    authFactory.logout = function () {
        return $http({
            method: 'POST',
            url: config.apiUrl + 'logout.php',
            data: $.param({user_id: AuthToken.getUserId(), token: AuthToken.getToken()}),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .success(function (data) {
            AuthToken.setToken();
            AuthToken.setUserId();
            return data;
        });
    };

    authFactory.isLoggedIn = function () {
        if (AuthToken.getToken()) {
            return true;
        }
        else {
            return false;
        }
    };

    authFactory.getUser = function () {
        if (AuthToken.getToken()) {
            return $http.get(config.apiUrl + 'get_account.php');
        }
        else {
            return $q.reject({
                message: "Nie posiadasz uprawnień do uruchomionej usługi."
            });
        }
    };

    authFactory.reset = function (credentials) {
        return $http({
            method: 'POST',
            url: config.apiUrl + 'password.php',
            data: $.param(credentials),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .success(function (data) {
            return data;
        });
    };

    authFactory.register = function (credentials) {
        return $http({
            method: 'POST',
            url: config.apiUrl + 'register.php',
            data: $.param(credentials),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .success(function (data) {
            AuthToken.setToken(data.token);
            AuthToken.setUserId(data.user_id);
            return data;
        });
    };

    return authFactory;
}])

.factory('AuthToken', ['$window', function ($window) {

    var authTokenFactory = {};

    authTokenFactory.getToken = function () {
        return $window.localStorage.getItem('token');
    };

    authTokenFactory.getUserId = function () {
        return $window.localStorage.getItem('user_id');
    };

    authTokenFactory.setToken = function (token) {
        if (token) {
            $window.localStorage.setItem('token', token);
        }
        else {
            $window.localStorage.removeItem('token');
        }
    };

    authTokenFactory.setUserId = function (user_id) {
        if (user_id) {
            $window.localStorage.setItem('user_id', user_id);
        }
        else {
            $window.localStorage.removeItem('user_id');
        }
    };

    return authTokenFactory;
}])

.factory('AuthInterceptor', ['$q', '$location', 'AuthToken', function ($q, $location, AuthToken) {

    var interceptorFactory = {};

    interceptorFactory.request = function (config) {
        var token = AuthToken.getToken();
        if (token) {
            config.headers['x-access-token'] = token;
        }
        return config;
    };

    interceptorFactory.responseError = function (response) {
        if (response.status == 403) {
            $location.path('/login');
        }
        return $q.reject(response);
    };

    return interceptorFactory;
}]);
