angular.module('usersController', ['usersService', 'config', 'paginService'])

.controller('UsersController', ['$location', '$scope', 'Users', 'Paginator', function($location, $scope, Users, Paginator) {
	
	$scope.roles = [{id: 1, name: 'Admin'}, {id: 2, name: 'Operator'}, {id: 3, name: 'User'}];

	$scope.moduleName = 'access_levels';
	$scope.componentName = 'users';

	$scope.getUsers = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.componentName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.componentName);
		Users.all(showRows, $scope.currentPage).then(function(response) {
			$scope.usersList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		if ($scope.action == 'list') {
			var showRows = Paginator.getLines($scope.componentName);
			Users.all(showRows, $scope.currentPage).then(function(response) {
				$scope.usersList = response.data;
			});
		}
		if ($scope.action == 'rights') {
			var showRows = Paginator.getLines($scope.moduleName);
			Users.rights($scope.id, $scope.user.id, showRows, $scope.currentPage).then(function(response) {
				$scope.rightsList = response.data;
				angular.forEach($scope.usersList, function(value, key) {
					if (value.id == $scope.id) {
						$scope.userEdit = $scope.usersList[key];
					}
				});
				$scope.userEdit.author = $scope.user.id;
			});
		}
	};

	$scope.newUser = function() {
		$scope.action = 'add';
		$scope.state = null;
		$scope.userNew = null;
	};

	$scope.addUser = function() {
		if ($scope.userNew) {
			$scope.processing = true;
			$scope.userNew.author = $scope.user.id;
			Users.add($scope.userNew).then(function(response) {
				if (response.data.success) {
					$scope.userNew = null;
					$scope.action = 'list';
					$scope.state = 'info';
					$scope.getUsers();
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

	$scope.editUser = function(id) {
		$scope.id = id;
		$scope.action = 'edit';
		$scope.state = null;
		$scope.processing = true;
		Users.one(id).then(function(response) {
			$scope.userEdit = response.data;
			$scope.userEdit.author = $scope.user.id;
			$scope.processing = false;
		});
	};

	$scope.saveUser = function(id) {
		if ($scope.userEdit) {
			$scope.processing = true;
			Users.update($scope.userEdit).then(function(response) {
				if (response.data.success) {
					Users.one(id).then(function(response) {
						$scope.userEdit = response.data;
						$scope.userEdit.author = $scope.user.id;
						angular.forEach($scope.usersList, function(value, key) {
							if (value.id == id) {
								$scope.usersList[key] = $scope.userEdit;
							}
						});
					});
					$scope.userEdit = null;
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

	$scope.deleteUser = function(id, confirmed) {
		if (!confirmed) {
			$scope.id = id;
			$scope.action = 'dialog';
			$scope.state = null;
		}
		else {
			$scope.processing = true;
			Users.delete(id, $scope.user.id).then(function(response) {
				if (response.data.success) {
					$scope.state = 'info';
				}
				else {
					$scope.state = 'error';
				}
				$scope.getUsers();
				$scope.action = 'list';
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.changePassword = function(id) {
		$scope.id = id;
		$scope.passwordEdit = {id: $scope.id, author: $scope.user.id};
		$scope.action = 'password';
		$scope.state = null;
	};

	$scope.savePassword = function(id) {
		if ($scope.passwordEdit) {
			$scope.processing = true;
			Users.password($scope.passwordEdit).then(function(response) {
				if (response.data.success) {
					$scope.passwordEdit = null;
					$scope.action = 'list';
					$scope.state = 'info';
				}
				else {
					$scope.action = 'password';
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.logoutUser = function(id) {
		$scope.id = id;
		$scope.state = null;
		Users.logout(id, $scope.user.id).then(function(response) {
			if (response.data.success) {
				$scope.state = 'info';
			}
			else {
				$scope.state = 'error';
			}
			$scope.getUsers();
			$scope.message = response.data.message;
		});
	};

	$scope.editRights = function(id) {
		$scope.id = id;
		$scope.action = 'rights';
		$scope.state = null;
		$scope.processing = true;
		$scope.rightsList = [];
		$scope.currentPage = 1;
		Paginator.getSize($scope.moduleName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Users.rights($scope.id, $scope.user.id, showRows, $scope.currentPage).then(function(response) {
			$scope.rightsList = response.data;
			angular.forEach($scope.usersList, function(value, key) {
				if (value.id == $scope.id) {
					$scope.userEdit = $scope.usersList[key];
				}
			});
			$scope.userEdit.author = $scope.user.id;
			$scope.processing = false;
		});
	};

	$scope.setAccess = function(resource_id, access) {
		$scope.saving = true;
		var rightsData = {
			user: $scope.userEdit.id,
			resource: resource_id,
			access: access,
			author: $scope.user.id
		};
		Users.access(rightsData).then(function(response) {
			if (response.data.success) {
				angular.forEach($scope.rightsList, function(value, key) {
					if (value.user_id == rightsData.user && value.resource_id == rightsData.resource) {
						$scope.rightsList[key].access = access == true ? 1 : 0;
					}
				});
				$scope.state = 'info';
			}
			else {
				$scope.state = 'error';
			}
			$scope.message = response.data.message;
			$scope.saving = false;
		});
	};

	$scope.cancelUser = function() {
		$scope.userNew = null;
		$scope.userEdit = null;
		$scope.passwordEdit = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);

