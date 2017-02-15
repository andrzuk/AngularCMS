angular.module('pagesController', ['pagesService', 'categoriesService', 'imagesService', 'config', 'ngSanitize', 'paginService'])

.controller('PagesController', ['$location', '$scope', '$sce', 'Pages', 'Categories', 'Images', 'Paginator', function($location, $scope, $sce, Pages, Categories, Images, Paginator) {
	
	$scope.images_data = [];
	$scope.lastImageId = 0;

	$scope.moduleName = 'pages';
	$scope.componentName = 'pages';

	$scope.getPages = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Paginator.reset();
		$scope.currentPage = 1;
		var showRows = Paginator.getLines($scope.moduleName);
		Pages.all(showRows, $scope.currentPage).then(function(response) {
			$scope.pagesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Pages.all(showRows, $scope.currentPage).then(function(response) {
			$scope.pagesList = response.data;
		});
	};

	$scope.newPage = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.pageNew = null;
		Categories.all(1000, 1).then(function(response) {
			$scope.categories = response.data;
		});
	};

	$scope.addPage = function() {
		if ($scope.pageNew) {
			$scope.processing = true;
			Pages.add($scope.pageNew).then(function(response) {
				if (response.data.success) {
					$scope.pageNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getPages();
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

	$scope.editPage = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Categories.all(1000, 1).then(function(response) {
			$scope.categories = response.data;
			Pages.one(id).then(function(response) {
				$scope.pageEdit = response.data;
				$scope.processing = false;
			});
		});
	};

	$scope.savePage = function(id) {
		if ($scope.pageEdit) {
			$scope.processing = true;
			Pages.update($scope.pageEdit).then(function(response) {
				if (response.data.success) {
					$scope.pageEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getPages();
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

	$scope.archivePage = function(id) {
		$scope.id = id;
		$scope.action = 'archive';
		$scope.state = null;
		$scope.processing = true;
		Pages.archives(id).then(function(response) {
			$scope.archives = response.data;
			Pages.one(id).then(function(response) {
				$scope.pageArchive = response.data;
				$scope.processing = false;
			});
		});
	};

	$scope.storePage = function(id) {
		if ($scope.pageArchive) {
			$scope.processing = true;
			Pages.store($scope.pageArchive).then(function(response) {
				if (response.data.success) {
					$scope.pageArchive = null;
					$scope.action = 'list';
					$scope.state = 'info';
				}
				else {
					$scope.action = 'archive';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.restorePage = function(id) {
		if ($scope.pageArchive) {
			$scope.processing = true;
			Pages.restore($scope.pageArchive).then(function(response) {
				if (response.data.success) {
					$scope.pageArchive = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getPages();
				}
				else {
					$scope.action = 'archive';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.deletePage = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Pages.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getPages();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.previewPage = function() {
		if ($scope.pageArchive.archive_id) {
			$scope.processing = true;
			Pages.preview($scope.pageArchive.archive_id).then(function(response) {
				$scope.pageArchive = response.data;
				$scope.pageArchive.contents = $sce.trustAsHtml($scope.pageArchive.contents);
				$scope.processing = false;
				$scope.action = 'preview';
				$scope.state = null;
			});
		}
	};

	$scope.showGallery = function() {
		$scope.lastAction = $scope.action;
		$scope.action = 'gallery';
		$scope.state = null;
		if (!$scope.lastImageId) {
			$scope.processing = true;
			Images.all($scope.lastImageId).then(function(response) {
				$scope.galleryDir = Images.config().gallery;
				$scope.thumbDir = Images.config().thumb;
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
		}
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

	$scope.insertImage = function(id) {
		$scope.action = $scope.lastAction;
		$scope.state = null;
		var imgTemplate = '\n<img src="' + $scope.galleryDir + '/' + id + '" width="auto" height="auto" class="img-responsive bordered" alt="">\n';
		$scope.$broadcast('add', imgTemplate);
	};

	$scope.cancelGallery = function() {
		$scope.action = $scope.lastAction;
		$scope.state = null;
	};

	$scope.cancelPage = function() {
		$scope.pageNew = null;
		$scope.pageEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
