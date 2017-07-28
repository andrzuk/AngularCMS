angular.module('searchesService', [])

.factory('Searches', ['$http', 'config', function($http, config) {
	
	var searchesFactory = {};

	var componentName = 'searches';

	searchesFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_searches_list.php?rows=' + rows + '&page=' + page);
	};

	searchesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_search.php?id=' + id);
	};

	searchesFactory.getFiltered = function(search) {
		return $http.get(config.apiUrl + componentName + '/get_searches_filtered.php?search=' + search);
	};

	return searchesFactory;
}]);
