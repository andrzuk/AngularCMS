angular.module('imagesController', ['imagesService', 'config', 'paginService'])

.controller('ImagesController', ['$location', '$scope', 'Images', 'Paginator', function($location, $scope, Images, Paginator) {
	
	$scope.images_data = [];
	
	$scope.componentName = 'images';
	$scope.moduleName = 'gallery';

	$scope.getImages = function() {
		$scope.action = 'list';
		$scope.mode = 1;
		$scope.processing = true;
		$scope.currentPage = 1;
		$scope.imagesList = [];
		Paginator.getSize($scope.componentName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.componentName);
		Images.all(showRows, $scope.currentPage).then(function(response) {
			$scope.imagesList = response.data;
			angular.forEach($scope.imagesList, function(value, key) {
				$scope.getImage(value.id, 'thumbnail');
			});
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		if ($scope.action == 'list') {
			var showRows = Paginator.getLines($scope.componentName);
			Images.all(showRows, $scope.currentPage).then(function(response) {
				$scope.imagesList = response.data;
				angular.forEach($scope.imagesList, function(value, key) {
					$scope.getImage(value.id, 'thumbnail');
				});
			});
		}
		if ($scope.action == 'gallery') {
			var showRows = Paginator.getLines($scope.moduleName);
			Images.all(showRows, $scope.currentPage).then(function(response) {
				$scope.imagesList = response.data;
				angular.forEach($scope.imagesList, function(value, key) {
					$scope.getImage(value.id, 'thumbnail');
				});
			});
		}
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
				$scope.message = response.data.message;
				$scope.processing = false;
				if (response.data.success) {
					$scope.imageNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getImages();
				}
				else {
					$scope.action = 'add';
					$scope.state = 'error';
				}
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
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
				$scope.getImages();
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
		$scope.mode = 2;
		$scope.state = null;
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.componentName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Images.all(showRows, $scope.currentPage).then(function(response) {
			$scope.imagesList = response.data;
			angular.forEach($scope.imagesList, function(value, key) {
				$scope.getImage(value.id, 'thumbnail');
			});
			$scope.processing = false;
		});
	};

	$scope.cancelImage = function() {
		$scope.imageNew = null;
		$scope.imageEdit = null;
		if ($scope.mode == 1) {
			$scope.action = 'list';
		}
		if ($scope.mode == 2) {
			$scope.action = 'gallery';
		}
		$scope.state = null;
	};

}]);

