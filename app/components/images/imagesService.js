angular.module('imagesService', [])

.factory('Images', ['$http', 'config', function($http, config) {
	
	var imagesFactory = {};

	var componentName = 'images';

	imagesFactory.all = function(rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_images_list.php?rows=' + rows + '&page=' + page);
	};

	imagesFactory.one = function(id, type) {
		return $http.get(config.apiUrl + componentName + '/get_image.php?id=' + id + '&type=' + type);
	};

	imagesFactory.details = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_image_details.php?id=' + id);
	};

	imagesFactory.add = function(formData) {
		var fd = new FormData();
		fd.append('file', formData.file_data);
		return $http.post(config.apiUrl + componentName + '/add_image.php', fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response) {
			imagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			imagesFactory.success = false;
			return error;
		});
	};

	imagesFactory.update = function(formData, id) {
		var fd = new FormData();
		fd.append('file', formData.file_data);
		return $http.post(config.apiUrl + componentName + '/update_image.php?id=' + id, fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response) {
			imagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			imagesFactory.success = false;
			return error;
		});
	};

	imagesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_image.php?id=' + id);
	};

	imagesFactory.config = function() {
		return {
			gallery: config.galleryUrl,
			thumb: config.thumbUrl,
		};
	}

	return imagesFactory;
}]);
