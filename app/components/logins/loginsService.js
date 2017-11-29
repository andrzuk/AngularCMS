angular.module('loginsService', [])

.factory('Logins', ['$http', 'config', function($http, config) {
	
	var loginsFactory = {};

	var componentName = 'logins';

	loginsFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_logins_list.php?rows=' + rows + '&page=' + page);
	};

	loginsFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_login.php?id=' + id);
	};

	loginsFactory.getFiltered = function(search) {
		return $http.get(config.apiUrl + componentName + '/get_logins_filtered.php?search=' + search);
	};

	return loginsFactory;
}]);
