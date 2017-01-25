angular.module('visitorsService', [])

.factory('Visitors', ['$http', 'config', function($http, config) {
	
	var visitorsFactory = {};

	var componentName = 'visitors';

	visitorsFactory.all = function() {
		return $http.get(config.apiUrl + componentName + '/get_visitors_list.php');
	};

	visitorsFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_visitor.php?id=' + id);
	};

	return visitorsFactory;
}]);
