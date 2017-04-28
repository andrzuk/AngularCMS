angular.module('gamesService', [])

.factory('Games', ['$http', 'config', function($http, config) {
	
	var gamesFactory = {};

	var componentName = 'games';

	gamesFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_games_list.php?rows=' + rows + '&page=' + page);
	};

	gamesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_game.php?id=' + id);
	};

	return gamesFactory;
}]);
