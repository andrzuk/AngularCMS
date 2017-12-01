angular.module('adminController', ['adminService', 'config', 'paginService', 'usersService'])

.controller('AdminController', ['$location', '$scope', 'Admin', 'Paginator', 'Users', function ($location, $scope, Admin, Paginator, Users) {

    $scope.admin = {
        settings: 0,
        users: 0,
        acl: 0,
        categories: 0,
        pages: 0,
        images: 0,
        messages: 0,
        visitors: 0
    };

    $scope.getStatistics = function () {
        $scope.action = 'panel';
        $scope.processing = true;
        Admin.getStatistics().then(function (response) {
            $scope.admin = response.data;
            $scope.processing = false;
        });
        Paginator.init();
    };

    $scope.userOptions = function (id) {
        $scope.id = id;
        $scope.action = 'options';
        $scope.state = null;
        $scope.processing = true;
        Users.one(id).then(function (response) {
            $scope.userEdit = response.data;
            $scope.userEdit.author = $scope.user.id;
            $scope.passwordEdit = {id: $scope.id, author: $scope.user.id};
            $scope.processing = false;
        });
    };

    $scope.saveUser = function (id) {
        if ($scope.userEdit) {
            $scope.processing = true;
            Users.update($scope.userEdit).then(function (response) {
                if (response.data.success) {
                    Users.one(id).then(function (response) {
                        $scope.userEdit = response.data;
                        $scope.user.name = $scope.userEdit.login;
                        $scope.user.email = $scope.userEdit.email;
                    });
                    $scope.userEdit = null;
                    $scope.action = 'panel';
                    $scope.state = 'info';
                } 
                else {
                    $scope.action = 'options';
                    $scope.state = 'error';
                }
                $scope.message = response.data.message;
                $scope.processing = false;
            });
        }
    };

    $scope.savePassword = function (id) {
        if ($scope.passwordEdit) {
            $scope.processing = true;
            Users.password($scope.passwordEdit).then(function (response) {
                if (response.data.success) {
                    $scope.passwordEdit = null;
                    $scope.action = 'panel';
                    $scope.state = 'info';
                } 
                else {
                    $scope.action = 'options';
                    $scope.state = 'error';
                }
                $scope.message = response.data.message;
                $scope.processing = false;
            });
        }
    };

    $scope.disableUser = function (id, confirmed) {
        if (!confirmed) {
            $scope.id = id;
            $scope.action = 'dialog';
            $scope.state = null;
        } 
        else {
            $scope.processing = true;
            Users.lock(id, $scope.user.id).then(function (response) {
                $scope.processing = false;
                $location.path('/logout');
            });
        }
    };

    $scope.cancelOptions = function () {
        $scope.userEdit = null;
        $scope.passwordEdit = null;
        $scope.action = 'panel';
        $scope.state = null;
    };

}]);
