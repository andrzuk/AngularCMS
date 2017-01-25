angular.module('installService', [])

.factory('Install', ['$http', 'config', function ($http, config) {

	var installFactory = {};

	installFactory.check = function () {
		return $http.get(config.installUrl + 'check_install.php');
	};

	installFactory.install = function(formData) {
		return $http({
			method: 'POST',
			url: config.installFolder + 'install_database.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			installFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			installFactory.success = false;
			return error;
		});
	};

	installFactory.delete = function () {
		return $http.get(config.installUrl + 'finish_install.php');
	};

	return installFactory;

}]);
