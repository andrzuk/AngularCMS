angular.module('scriptsService', [])

.factory('Scripts', ['$http', 'config', function($http, config) {
	
	var scriptsFactory = {};

	var componentName = 'scripts';

	scriptsFactory.one = function() {
		return $http.get(config.apiUrl + componentName + '/get_script.php');
	};

	scriptsFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_script.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			scriptsFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			scriptsFactory.success = false;
			return error;
		});
	};

	return scriptsFactory;
}]);
