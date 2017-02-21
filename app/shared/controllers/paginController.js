angular.module('paginController', ['paginService'])

.controller('PaginatorController', ['$scope', 'Paginator', function($scope, Paginator) {
	
	$scope.pointers = [];

	$scope.initPaginator = function() {
		Paginator.pointers = [{ label: '...', active: false }];
	};

	$scope.$watch(function() { return Paginator.pointers; }, function(newValue, oldValue) {
		if (typeof newValue !== 'undefined') {
			$scope.pointers = Paginator.pointers;
		}
	});
	
}]);
