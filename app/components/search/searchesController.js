angular.module('searchesController', ['searchesService', 'config', 'paginService'])

.controller('SearchesController', ['$scope', 'Searches', 'Paginator', function($scope, Searches, Paginator) {
	
	$scope.moduleName = 'searches';
	$scope.componentName = 'searches';
	
	$scope.getSearches = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Paginator.reset();
		$scope.currentPage = 1;
		var showRows = Paginator.getLines($scope.moduleName);
		Searches.all(showRows, $scope.currentPage).then(function(response) {
			$scope.searchesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Searches.all(showRows, $scope.currentPage).then(function(response) {
			$scope.searchesList = response.data;
		});
	};

	$scope.deleteSearch = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Searches.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getSearches();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelSearch = function() {
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
