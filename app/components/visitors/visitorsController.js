angular.module('visitorsController', ['visitorsService', 'config', 'paginService'])

.controller('VisitorsController', ['$scope', 'Visitors', 'Paginator', function($scope, Visitors, Paginator) {
	
	$scope.moduleName = 'visitors';
	$scope.componentName = 'visitors';

	$scope.getVisitors = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.moduleName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Visitors.all(showRows, $scope.currentPage).then(function(response) {
			$scope.visitorsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Visitors.all(showRows, $scope.currentPage).then(function(response) {
			$scope.visitorsList = response.data;
		});
	};

	$scope.viewVisitor = function(id) {
		$scope.id = id;
		$scope.action = 'view';
		$scope.state = null;
		Visitors.one(id).then(function(response) {
			$scope.visitorView = response.data;
		});
	};

	$scope.cancelVisitor = function() {
		$scope.visitorView = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);

