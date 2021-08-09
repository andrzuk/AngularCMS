angular.module('categoriesController', ['categoriesService', 'config', 'paginService'])

.controller('CategoriesController', ['$location', '$scope', 'Categories', 'Paginator', function($location, $scope, Categories, Paginator) {
	
	$scope.moduleName = 'categories';
	$scope.componentName = 'categories';
	
	$scope.searchValue = '';

	$scope.getCategories = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.moduleName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Categories.all(showRows, $scope.currentPage).then(function(response) {
			$scope.categoriesList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Categories.all(showRows, $scope.currentPage).then(function(response) {
			$scope.categoriesList = response.data;
			focusInputField('search-value');
		});
	};

	$scope.newCategory = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.categoryNew = null;
		focusInputField('caption');
	};

	$scope.addCategory = function() {
		if ($scope.categoryNew) {
			$scope.processing = true;
			Categories.add($scope.categoryNew).then(function(response) {
				$scope.message = response.data.message;
				$scope.processing = false;
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
			focusInputField('caption');
		});
	};

	$scope.saveCategory = function(id) {
		if ($scope.categoryEdit) {
			$scope.processing = true;
			Categories.update($scope.categoryEdit).then(function(response) {
				if (response.data.success) {
					Categories.one(id).then(function(response) {
						$scope.categoryEdit = response.data;
						angular.forEach($scope.categoriesList, function(value, key) {
							if (value.id == id) {
								$scope.categoriesList[key] = $scope.categoryEdit;
							}
						});
					});
					$scope.categoryEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
				}
				else {
					$scope.action = 'edit';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
				focusInputField('search-value');
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
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
				$scope.getCategories();
			});
		}
	};

	$scope.moveCategory = function(id, direction) {
		$scope.processing = true;
		Categories.move(id, direction).then(function(response) {
			$scope.message = response.data.message;
			$scope.processing = false;
			if (response.data.success) {
				$scope.state = 'info';
				$scope.getCategories();
			}
			else {
				$scope.state = 'error';
			}
		});
	};

	$scope.findCategories = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.state = null;
		$scope.categoriesList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Categories.getFiltered($scope.searchValue).then(function(response) {
			$scope.categoriesList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.closeFilter = function() {
		$scope.searchValue = '';
		$scope.getCategories();
	};

	$scope.cancelCategory = function() {
		$scope.categoryNew = null;
		$scope.categoryEdit = null;
		$scope.action = 'list';
		$scope.state = null;
		focusInputField('search-value');
	};

}]);
