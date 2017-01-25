angular.module('aclController', ['aclService', 'config'])

.controller('AclController', ['$location', '$scope', 'Acl', function($location, $scope, Acl) {
	
	$scope.options = [{id: 0, name: 'Niedostępny'}, {id: 1, name: 'Dostępny'}];

	$scope.getAcl = function() {
		$scope.action = 'list';
		$scope.processing = true;
		Acl.all().then(function(response) {
			$scope.aclList = response.data;
			$scope.processing = false;
		});
	};

	$scope.newAcl = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.aclNew = null;
	};

	$scope.addAcl = function() {
		if ($scope.aclNew) {
			$scope.processing = true;
			Acl.add($scope.aclNew).then(function(response) {
				if (response.data.success) {
					$scope.aclNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getAcl();
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

	$scope.editAcl = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Acl.one(id).then(function(response) {
			$scope.aclEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveAcl = function(id) {
		if ($scope.aclEdit) {
			$scope.processing = true;
			Acl.update($scope.aclEdit).then(function(response) {
				if (response.data.success) {
					$scope.aclEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getAcl();
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

	$scope.deleteAcl = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Acl.delete(id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getAcl();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelAcl = function() {
		$scope.aclNew = null;
		$scope.aclEdit = null;
		$scope.passwordEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);
