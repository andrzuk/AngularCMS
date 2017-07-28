angular.module('messagesController', ['messagesService', 'config', 'paginService'])

.controller('MessagesController', ['$location', '$scope', 'Messages', 'Paginator', function($location, $scope, Messages, Paginator) {
	
	$scope.moduleName = 'messages';
	$scope.componentName = 'messages';
	
	$scope.getMessages = function(mode) {
		$scope.mode = mode;
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Messages.getCount($scope.mode).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Messages.all($scope.mode, showRows, $scope.currentPage).then(function(response) {
			$scope.messagesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Messages.all($scope.mode, showRows, $scope.currentPage).then(function(response) {
			$scope.messagesList = response.data;
		});
	};

	$scope.editMessage = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Messages.one(id).then(function(response) {
			$scope.messageEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveMessage = function(id) {
		if ($scope.messageEdit) {
			$scope.processing = true;
			Messages.update($scope.messageEdit).then(function(response) {
				$scope.message = response.data.message;
				$scope.processing = false;
				if (response.data.success) {
					$scope.messageEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getMessages($scope.mode);
				}
				else {
					$scope.action = 'edit';
					$scope.state = 'error';
				}
			});
		}
	};

	$scope.deleteMessage = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Messages.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
				$scope.getMessages($scope.mode);
			});
		}
	};

	$scope.findMessages = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.state = null;
		$scope.messagesList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Messages.getFiltered($scope.searchValue).then(function(response) {
			$scope.messagesList = response.data;
			$scope.processing = false;
		});
	};

	$scope.cancelMessage = function() {
		$scope.messageNew = null;
		$scope.messageEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
