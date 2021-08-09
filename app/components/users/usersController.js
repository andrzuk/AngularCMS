angular.module('usersController', ['usersService', 'config', 'paginService'])

.controller('UsersController', ['$location', '$scope', 'Users', 'Paginator', function($location, $scope, Users, Paginator) {
	
	$scope.roles = [{id: 1, name: 'Admin'}, {id: 2, name: 'Operator'}, {id: 3, name: 'User'}];

	$scope.moduleName = 'access_levels';
	$scope.componentName = 'users';

	$scope.searchValue = '';
	$scope.rightsValue = '';
	$scope.modulesValue = '';
	$scope.mode = 0;

	$scope.getUsers = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		$scope.usersList = [];
		Paginator.getSize($scope.componentName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.componentName);
		Users.all(showRows, $scope.currentPage, $scope.user.id).then(function(response) {
			$scope.usersList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		if ($scope.action == 'list') {
			var showRows = Paginator.getLines($scope.componentName);
			Users.all(showRows, $scope.currentPage, $scope.user.id).then(function(response) {
				$scope.usersList = response.data;
				focusInputField('search-value');
			});
		}
		if ($scope.action == 'rights') {
			var showRows = Paginator.getLines($scope.moduleName);
			Users.rights($scope.id, $scope.mode, showRows, $scope.currentPage).then(function(response) {
				$scope.rightsList = response.data;
				angular.forEach($scope.usersList, function(value, key) {
					if (value.id == $scope.id) {
						$scope.userEdit = $scope.usersList[key];
						focusInputField('search-value');
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
		focusInputField('login');
	};

	$scope.addUser = function() {
		if ($scope.userNew) {
			$scope.processing = true;
			$scope.userNew.author = $scope.user.id;
			Users.add($scope.userNew).then(function(response) {
				$scope.message = response.data.message;
				$scope.processing = false;
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
			focusInputField('login');
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
				focusInputField('search-value');
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
				$scope.action = 'list';
				$scope.processing = false;
				$scope.message = response.data.message;
				$scope.getUsers();
			});
		}
	};

	$scope.changePassword = function(id) {
		$scope.id = id;
		$scope.passwordEdit = { id: $scope.id, author: $scope.user.id };
		$scope.action = 'password';
		$scope.state = null;
		focusInputField('password_set');
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
				focusInputField('search-value');
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
		Users.rights($scope.id, $scope.mode, showRows, $scope.currentPage).then(function(response) {
			$scope.rightsList = response.data;
			angular.forEach($scope.usersList, function(value, key) {
				if (value.id == $scope.id) {
					$scope.userEdit = $scope.usersList[key];
				}
			});
			$scope.userEdit.author = $scope.user.id;
			$scope.processing = false;
			focusInputField('search-value');
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

	$scope.editModules = function(id) {
		$scope.id = id;
		$scope.action = 'modules';
		$scope.state = null;
		$scope.processing = true;
		Users.modules($scope.id).then(function(response) {
			$scope.modulesList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.setModule = function(module_id, access) {
		$scope.saving = true;
		var admitData = {
			user: $scope.userEdit.id,
			module: module_id,
			access: access,
			author: $scope.user.id
		};
		Users.admit(admitData).then(function(response) {
			if (response.data.success) {
				angular.forEach($scope.modulesList, function(value, key) {
					if (value.user_id == admitData.user && value.module_id == admitData.module) {
						$scope.modulesList[key].access = access == true ? 1 : 0;
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

	$scope.findUsers = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.state = null;
		$scope.usersList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Users.getFiltered($scope.searchValue, $scope.user.id).then(function(response) {
			$scope.usersList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.findRights = function(mode) {
		$scope.mode = mode;
		$scope.action = 'rights';
		$scope.processing = true;
		$scope.state = null;
		$scope.rightsList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Users.getRights($scope.mode, $scope.rightsValue, $scope.id).then(function(response) {
			$scope.rightsList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.findModules = function() {
		$scope.action = 'modules';
		$scope.processing = true;
		$scope.state = null;
		$scope.modulesList = [];
		Users.getModules($scope.modulesValue, $scope.id).then(function(response) {
			$scope.modulesList = response.data;
			$scope.processing = false;
			focusInputField('search-value');
		});
	};

	$scope.closeFilter = function() {
		$scope.mode = 0;
		if ($scope.action == 'list') {
			$scope.searchValue = '';
			$scope.getUsers();
		}
		if ($scope.action == 'rights') {
			$scope.rightsValue = '';
			$scope.editRights($scope.id);
		}
		if ($scope.action == 'modules') {
			$scope.modulesValue = '';
			$scope.editModules($scope.id);
		}
	};

	$scope.cancelUser = function() {
		$scope.userNew = null;
		$scope.userEdit = null;
		$scope.passwordEdit = null;
		$scope.action = 'list';
		$scope.state = null;
		focusInputField('search-value');
	};

}]);

