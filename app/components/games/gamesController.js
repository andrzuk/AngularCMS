angular.module('gamesController', ['gamesService', 'config', 'paginService'])

.controller('GamesController', ['$scope', 'Games', 'Paginator', function($scope, Games, Paginator) {
	
	$scope.moduleName = '_game_stats';
	$scope.componentName = 'games';
	
	$scope.getGames = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.moduleName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Games.all(showRows, $scope.currentPage).then(function(response) {
			$scope.gamesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Games.all(showRows, $scope.currentPage).then(function(response) {
			$scope.gamesList = response.data;
		});
	};

	$scope.deleteGame = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Games.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
				$scope.getGames();
			});
		}
	};

	$scope.cancelGame = function() {
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
