angular.module('aclService', [])

.factory('Acl', ['$http', 'config', function($http, config) {
	
	var aclFactory = {};

	var componentName = 'acl';

	aclFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_acl_list.php?rows=' + rows + '&page=' + page);
	};

	aclFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_acl.php?id=' + id);
	};

	aclFactory.getFiltered = function(search) {
		return $http.get(config.apiUrl + componentName + '/get_acl_filtered.php?search=' + search);
	};

	aclFactory.add = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/add_acl.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			aclFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			aclFactory.success = false;
			return error;
		});
	};

	aclFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_acl.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			aclFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			aclFactory.success = false;
			return error;
		});
	};

	aclFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_acl.php?id=' + id);
	};

	return aclFactory;
}]);
