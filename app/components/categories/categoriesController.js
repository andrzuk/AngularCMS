angular.module('categoriesController', ['categoriesService', 'config', 'paginService'])

.controller('CategoriesController', ['$location', '$scope', 'Categories', 'Paginator', function($location, $scope, Categories, Paginator) {
	
	$scope.moduleName = 'categories';
	$scope.componentName = 'categories';

	$scope.getCategories = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Paginator.reset();
		$scope.currentPage = 1;
		var showRows = Paginator.getLines($scope.moduleName);
		Categories.all(showRows, $scope.currentPage).then(function(response) {
			$scope.categoriesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Categories.all(showRows, $scope.currentPage).then(function(response) {
			$scope.categoriesList = response.data;
		});
	};

	$scope.newCategory = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.categoryNew = null;
	};

	$scope.addCategory = function() {
		if ($scope.categoryNew) {
			$scope.processing = true;
			Categories.add($scope.categoryNew).then(function(response) {
				if (response.data.success) {
					$scope.categoryNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getCategories();
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

	$scope.editCategory = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Categories.one(id).then(function(response) {
			$scope.categoryEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveCategory = function(id) {
		if ($scope.categoryEdit) {
			$scope.processing = true;
			Categories.update($scope.categoryEdit).then(function(response) {
				if (response.data.success) {
					$scope.categoryEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getCategories();
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

	$scope.deleteCategory = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Categories.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getCategories();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.moveCategory = function(id, direction) {
		$scope.processing = true;
		Categories.move(id, direction).then(function(response) {
			if (response.data.success) {
				$scope.state = 'info';
				$scope.getCategories();
			}
			else {
				$scope.state = 'error';
			}
			$scope.message = response.data.message;
			$scope.processing = false;
		});
	};

	$scope.cancelCategory = function() {
		$scope.categoryNew = null;
		$scope.categoryEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
