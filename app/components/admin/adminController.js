angular.module('adminController', ['adminService', 'config'])

.controller('AdminController', ['$scope', 'Admin', function($scope, Admin) {

	$scope.admin = { settings: 0, users: 0, acl: 0, categories: 0, pages: 0, images: 0, messages: 0, visitors: 0 };

	$scope.getStatistics = function() {
		$scope.processing = true;
		Admin.getStatistics().then(function(response) {
			$scope.admin = response.data;
			$scope.processing = false;
		});
	};

}]);