angular.module('categoriesService', [])

.factory('Categories', ['$http', 'config', function($http, config) {
	
	var categoriesFactory = {};

	var componentName = 'categories';

	categoriesFactory.all = function() {
		return $http.get(config.apiUrl + componentName + '/get_categories_list.php');
	};

	categoriesFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_category.php?id=' + id);
	};

	categoriesFactory.add = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/add_category.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			categoriesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			categoriesFactory.success = false;
			return error;
		});
	};

	categoriesFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_category.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			categoriesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			categoriesFactory.success = false;
			return error;
		});
	};

	categoriesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_category.php?id=' + id);
	};

	categoriesFactory.move = function(id, direction) {
		return $http.get(config.apiUrl + componentName + '/move_category.php?id=' + id + '&direction=' + direction);
	};

	return categoriesFactory;
}]);
