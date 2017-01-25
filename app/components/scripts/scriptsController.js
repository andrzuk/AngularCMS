angular.module('scriptsController', ['scriptsService', 'config'])

.controller('ScriptsController', ['$location', '$scope', 'Scripts', function($location, $scope, Scripts) {
	
	$scope.editScript = function() {
		$scope.processing = true;
		Scripts.one().then(function(response) {
			$scope.scriptEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveScript = function() {
		if ($scope.scriptEdit) {
			$scope.processing = true;
			Scripts.update($scope.scriptEdit).then(function(response) {
				if (response.data.success) {
					$scope.scriptEdit = null;
					$scope.state = 'info';
					$scope.editScript();
				}
				else {
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelScript = function() {
		$scope.scriptEdit = null;
		$scope.state = null;
		$location.path("/settings");
	};

}]);
