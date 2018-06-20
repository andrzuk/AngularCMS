angular.module('loginsService', [])

.factory('Logins', ['$http', 'config', function($http, config) {
	
	var loginsFactory = {};

	var componentName = 'logins';

	loginsFactory.getCount = function(mode) {
		return $http.get(config.apiUrl + componentName + '/get_logins_count.php?mode=' + mode);
	};

	loginsFactory.all = function(mode, rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_logins_list.php?mode=' + mode + '&rows=' + rows + '&page=' + page);
	};

	loginsFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_login.php?id=' + id);
	};

	loginsFactory.getFiltered = function(search) {
		return $http.get(config.apiUrl + componentName + '/get_logins_filtered.php?search=' + search);
	};

	return loginsFactory;
}]);
