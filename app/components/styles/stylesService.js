angular.module('stylesService', [])

.factory('Styles', ['$http', 'config', function($http, config) {
	
	var stylesFactory = {};

	var componentName = 'styles';

	stylesFactory.one = function() {
		return $http.get(config.apiUrl + componentName + '/get_style.php');
	};

	stylesFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_style.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			stylesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			stylesFactory.success = false;
			return error;
		});
	};

	return stylesFactory;
}]);
