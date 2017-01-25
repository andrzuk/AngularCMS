angular.module('searchesService', [])

.factory('Searches', ['$http', 'config', function($http, config) {
	
	var searchesFactory = {};

	var componentName = 'searches';

	searchesFactory.all = function() {
		return $http.get(config.apiUrl + componentName + '/get_searches_list.php');
	};

	searchesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_search.php?id=' + id);
	};

	return searchesFactory;
}]);
