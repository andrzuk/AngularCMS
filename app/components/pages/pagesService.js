angular.module('pagesService', [])

.factory('Pages', ['$http', 'config', function($http, config) {
	
	var pagesFactory = {};

	var componentName = 'pages';

	pagesFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_pages_list.php?rows=' + rows + '&page=' + page);
	};

	pagesFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_page.php?id=' + id);
	};

	pagesFactory.getFiltered = function(search) {
		return $http.get(config.apiUrl + componentName + '/get_pages_filtered.php?search=' + search);
	};

	pagesFactory.add = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/add_page.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			pagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			pagesFactory.success = false;
			return error;
		});
	};

	pagesFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_page.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			pagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			pagesFactory.success = false;
			return error;
		});
	};

	pagesFactory.archives = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_archives_list.php?id=' + id);
	};

	pagesFactory.store = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/archive_page.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			pagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			pagesFactory.success = false;
			return error;
		});
	};

	pagesFactory.restore = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/restore_page.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			pagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			pagesFactory.success = false;
			return error;
		});
	};

	pagesFactory.preview = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_archive.php?id=' + id);
	};

	pagesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_page.php?id=' + id);
	};

	pagesFactory.public_index = function() {
		return $http.get(config.apiUrl + 'get_page_index.php');
	};

	pagesFactory.public_contact = function() {
		return $http.get(config.apiUrl + 'get_page_contact.php');
	};

	pagesFactory.public_all = function(id) {
		return $http.get(config.apiUrl + 'get_pages_public.php?id=' + id);
	};

	pagesFactory.public_one = function(id) {
		return $http.get(config.apiUrl + 'get_page_public.php?id=' + id);
	};

	return pagesFactory;
}]);
