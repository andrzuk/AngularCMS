angular.module('visitorsController', ['visitorsService', 'config'])

.controller('VisitorsController', ['$scope', 'Visitors', function($scope, Visitors) {
	
	$scope.getVisitors = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Visitors.all().then(function(response) {
			$scope.visitorsList = response.data;
			$scope.processing = false;
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

