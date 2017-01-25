angular.module('searchesController', ['searchesService', 'config'])

.controller('SearchesController', ['$scope', 'Searches', function($scope, Searches) {
	
	$scope.getSearches = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Searches.all().then(function(response) {
			$scope.searchesList = response.data;
			$scope.processing = false;
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
