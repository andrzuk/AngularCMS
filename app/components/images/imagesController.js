angular.module('imagesController', ['imagesService', 'config'])

.controller('ImagesController', ['$location', '$scope', 'Images', function($location, $scope, Images) {
	
	$scope.images_data = [];
	$scope.lastImageId = 0;
	
	$scope.componentName = 'images';

	$scope.getImages = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Images.all($scope.lastImageId).then(function(response) {
			$scope.imagesList = response.data;
			angular.forEach($scope.imagesList, function(value, key) {
				$scope.getImage(value.id, 'thumbnail');
			});
			if ($scope.imagesList.length) {
				$scope.lastImageId = $scope.imagesList[$scope.imagesList.length - 1].id;
			}
			$scope.moreRows = response.data.length > 0;
			$scope.processing = false;
		});
	};

	$scope.getMore = function() {
		$scope.action = 'list';
		$scope.appending = true;
		$scope.state = null;
		Images.more($scope.lastImageId).then(function(response) {
			angular.forEach(response.data, function(value, key) {
				$scope.imagesList.push(value);
				$scope.getImage(value.id, 'thumbnail');
			});
			if ($scope.imagesList.length) {
				$scope.lastImageId = $scope.imagesList[$scope.imagesList.length - 1].id;
			}
			$scope.moreRows = response.data.length > 0;
			$scope.appending = false;
		});
	};

	$scope.showMore = function() {
		$scope.action = 'gallery';
		$scope.appending = true;
		$scope.state = null;
		Images.more($scope.lastImageId).then(function(response) {
			angular.forEach(response.data, function(value, key) {
				$scope.imagesList.push(value);
				$scope.getImage(value.id, 'thumbnail');
			});
			if ($scope.imagesList.length) {
				$scope.lastImageId = $scope.imagesList[$scope.imagesList.length - 1].id;
			}
			$scope.moreRows = response.data.length > 0;
			$scope.appending = false;
		});
	};

	$scope.getImage = function(id, type) {
		Images.one(id, type).then(function(response) {
			$scope.images_data[id] = response.data.image_data;
		});
	};

	$scope.newImage = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.imageNew = null;
	};

	$scope.addImage = function() {
		if ($scope.imageNew) {
			$scope.processing = true;
			Images.add($scope.imageNew).then(function(response) {
				if (response.data.success) {
					$scope.imageNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.moreRows = true;
				}
				else {
					$scope.action = 'add';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.editImage = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Images.one(id, 'original').then(function(response) {
			$scope.result_data = response.data.image_data;
			$scope.file_name = response.data.file_name;
			$scope.processing = false;
		});
	};

	$scope.saveImage = function(id) {
		if ($scope.imageEdit) {
			$scope.processing = true;
			Images.update($scope.imageEdit, id).then(function(response) {
				if (response.data.success) {
					$scope.getImage(id, 'thumbnail');
					$scope.detailsImage(id);
					$scope.imageEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
				}
				else {
					$scope.action = 'edit';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.deleteImage = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Images.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				angular.forEach($scope.imagesList, function(value, key) {
					if (value.id == id) {
						$scope.imagesList.splice(key, 1);
					}
				});
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.previewImage = function(id) {
		$scope.id = id;
		$scope.action = 'preview';
		$scope.state = null;
		$scope.processing = true;
		Images.one(id, 'original').then(function(response) {
			$scope.result_data = response.data.image_data;
			$scope.file_name = response.data.file_name;
			$scope.processing = false;
		});
	};

	$scope.detailsImage = function(id) {
		Images.details(id).then(function(response) {
			angular.forEach($scope.imagesList, function(value, key) {
				if (value.id == id) {
					$scope.imagesList[key] = response.data;
				}
			});
		});
	};

	$scope.showGallery = function() {
		$scope.action = 'gallery';
		$scope.state = null;
	};

	$scope.cancelImage = function() {
		$scope.imageNew = null;
		$scope.imageEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);

