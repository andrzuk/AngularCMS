angular.module('authController', ['authService', 'config'])

.controller('AuthController', ['$scope', '$location', 'Auth', function ($scope, $location, Auth) {

    $scope.doLogin = function () {
        $scope.processing = true;
        $scope.error = false;
        Auth.login($scope.loginData).success(function (response) {
            $scope.processing = false;
            Auth.getUser().then(function (response) {
                $scope.user = response.data;
            });
            if (response.success) {
                $location.path('/admin');
            }
            else {
                $scope.error = true;
                $scope.message = response.message;
            }
        });
    };

    $scope.doLogout = function () {
        Auth.logout().then(function (response) {
            $scope.message = response.message;
            $location.path('/login');
        });
    };

    $scope.doReset = function () {
        $scope.processing = true;
        $scope.info = false;
        $scope.error = false;
        Auth.reset($scope.resetData).success(function (response) {
            $scope.processing = false;
            $scope.info = response.success;
            $scope.error = !response.success;
            $scope.message = response.message;
            $scope.resetData = null;
        });
    };

    $scope.doRegister = function () {
        $scope.processing = true;
        $scope.error = false;
        Auth.register($scope.registerData).success(function (response) {
            $scope.processing = false;
            Auth.getUser().then(function (response) {
                $scope.user = response.data;
            });
            if (response.success) {
                $location.path('/admin');
            }
            else {
                $scope.error = true;
                $scope.message = response.message;
            }
        });
    };
}]);
