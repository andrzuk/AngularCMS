angular.module('usersService', [])

.factory('Users', ['$http', 'config', function($http, config) {
	
	var usersFactory = {};

	var componentName = 'users';

	usersFactory.all = function(rows, page, author) {
		return $http.get(config.apiUrl + componentName + '/get_users_list.php?rows=' + rows + '&page=' + page + '&author=' + author);
	};

	usersFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_user.php?id=' + id);
	};

	usersFactory.getFiltered = function(search, author) {
		return $http.get(config.apiUrl + componentName + '/get_users_filtered.php?search=' + search + '&author=' + author);
	};

	usersFactory.rights = function(id, mode, rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_user_rights.php?id=' + id + '&mode=' + mode + '&rows=' + rows + '&page=' + page);
	};

	usersFactory.getRights = function(mode, search, id) {
		return $http.get(config.apiUrl + componentName + '/get_rights_filtered.php?mode=' + mode + '&search=' + search + '&id=' + id);
	};

	usersFactory.modules = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_user_modules.php?id=' + id);
	};

	usersFactory.getModules = function(search, id) {
		return $http.get(config.apiUrl + componentName + '/get_modules_filtered.php?search=' + search + '&id=' + id);
	};

	usersFactory.add = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/add_user.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			usersFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			usersFactory.success = false;
			return error;
		});
	};

	usersFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_user.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			usersFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			usersFactory.success = false;
			return error;
		});
	};

	usersFactory.password = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/change_password.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			usersFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			usersFactory.success = false;
			return error;
		});
	};

	usersFactory.logout = function(id, author) {
		return $http.get(config.apiUrl + componentName + '/logout_user.php?id=' + id + '&author=' + author);
	};

	usersFactory.delete = function(id, author) {
		return $http.get(config.apiUrl + componentName + '/delete_user.php?id=' + id + '&author=' + author);
	};

	usersFactory.lock = function(id, author) {
		return $http.get(config.apiUrl + componentName + '/lock_user.php?id=' + id + '&author=' + author);
	};

	usersFactory.access = function(itemData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_user_rights.php',
			data: $.param(itemData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			usersFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			usersFactory.success = false;
			return error;
		});
	};

	usersFactory.admit = function(itemData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_user_modules.php',
			data: $.param(itemData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			usersFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			usersFactory.success = false;
			return error;
		});
	};

	return usersFactory;
}]);
