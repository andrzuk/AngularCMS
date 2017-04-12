angular.module('visitorsService', [])

.factory('Visitors', ['$http', 'config', function($http, config) {
	
	var visitorsFactory = {};

	var componentName = 'visitors';

	visitorsFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_visitors_list.php?rows=' + rows + '&page=' + page);
	};

	visitorsFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_visitor.php?id=' + id);
	};

	visitorsFactory.statistics = function() {
		return $http.get(config.apiUrl + componentName + '/get_statistics.php');
	};

	return visitorsFactory;
}]);
