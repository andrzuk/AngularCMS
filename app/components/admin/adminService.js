angular.module('adminService', [])

.factory('Admin', ['$http', 'config', function($http, config) {
	
	var adminFactory = {};

	adminFactory.getStatistics = function() {
		return $http.get(config.apiUrl + 'get_admin_statistics.php');
	};

	return adminFactory;
}]);
