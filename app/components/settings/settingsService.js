angular.module('settingsService', [])

.factory('Settings', ['$http', 'config', function($http, config) {
	
	var settingsFactory = {};

	var componentName = 'settings';

	settingsFactory.all = function() {
		return $http.get(config.apiUrl + componentName + '/get_settings_list.php');
	};

	settingsFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_setting.php?id=' + id);
	};

	settingsFactory.add = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/add_setting.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			settingsFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			settingsFactory.success = false;
			return error;
		});
	};

	settingsFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_setting.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			settingsFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			settingsFactory.success = false;
			return error;
		});
	};

	settingsFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_setting.php?id=' + id);
	};

	return settingsFactory;
}]);
