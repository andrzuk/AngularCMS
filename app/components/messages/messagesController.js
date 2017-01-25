angular.module('messagesController', ['messagesService', 'config'])

.controller('MessagesController', ['$location', '$scope', 'Messages', function($location, $scope, Messages) {
	
	$scope.getMessages = function(mode) {
		$scope.mode = mode;
		$scope.action = 'list';
		$scope.processing = true;
		Messages.all(mode).then(function(response) {
			$scope.messagesList = response.data;
			$scope.processing = false;
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
				$scope.message = response.data.message;
				$scope.processing = false;
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
				$scope.getMessages($scope.mode);
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelMessage = function() {
		$scope.messageNew = null;
		$scope.messageEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
