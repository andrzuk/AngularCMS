angular.module('settingsController', ['settingsService', 'config', 'paginService'])

.controller('SettingsController', ['$location', '$scope', 'Settings', 'Paginator', function($location, $scope, Settings, Paginator) {
	
	$scope.moduleName = 'settings';
	$scope.componentName = 'settings';

	$scope.getSettings = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Paginator.reset();
		$scope.currentPage = 1;
		var showRows = Paginator.getLines($scope.moduleName);
		Settings.all(showRows, $scope.currentPage).then(function(response) {
			$scope.settingsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Settings.all(showRows, $scope.currentPage).then(function(response) {
			$scope.settingsList = response.data;
		});
	};

	$scope.newSetting = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.settingNew = null;
	};

	$scope.addSetting = function() {
		if ($scope.settingNew) {
			$scope.processing = true;
			Settings.add($scope.settingNew).then(function(response) {
				if (response.data.success) {
					$scope.settingNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getSettings();
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

	$scope.editSetting = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Settings.one(id).then(function(response) {
			$scope.settingEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveSetting = function(id) {
		if ($scope.settingEdit) {
			$scope.processing = true;
			Settings.update($scope.settingEdit).then(function(response) {
				if (response.data.success) {
					$scope.settingEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getSettings();
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

	$scope.deleteSetting = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Settings.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getSettings();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelSetting = function() {
		$scope.settingNew = null;
		$scope.settingEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
