angular.module('loginsController', ['loginsService', 'config', 'paginService'])

.controller('LoginsController', ['$scope', 'Logins', 'Paginator', function($scope, Logins, Paginator) {
	
	$scope.moduleName = 'logins';
	$scope.componentName = 'logins';

	$scope.getLogins = function(mode) {
		$scope.mode = mode;
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Logins.getCount($scope.mode).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Logins.all($scope.mode, showRows, $scope.currentPage).then(function(response) {
			$scope.loginsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Logins.all($scope.mode, showRows, $scope.currentPage).then(function(response) {
			$scope.loginsList = response.data;
		});
	};

	$scope.viewLogin = function(id) {
		$scope.id = id;
		$scope.action = 'view';
		$scope.state = null;
		Logins.one(id).then(function(response) {
			$scope.loginView = response.data;
		});
	};

	$scope.findLogins = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.state = null;
		$scope.loginsList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Logins.getFiltered($scope.searchValue).then(function(response) {
			$scope.loginsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.closeFilter = function() {
		$scope.searchValue = '';
		$scope.getLogins($scope.mode);
	};

	$scope.cancelLogin = function() {
		$scope.loginView = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);

