angular.module('stylesController', ['stylesService', 'config'])

.controller('StylesController', ['$location', '$scope', 'Styles', function($location, $scope, Styles) {
	
	$scope.componentName = 'styles';
	
	$scope.editStyle = function() {
		$scope.processing = true;
		Styles.one().then(function(response) {
			$scope.styleEdit = response.data;
			$scope.processing = false;
		});
	};

	$scope.saveStyle = function() {
		if ($scope.styleEdit) {
			$scope.processing = true;
			Styles.update($scope.styleEdit).then(function(response) {
				if (response.data.success) {
					$scope.styleEdit = null;
					$scope.state = 'info';
					$scope.editStyle();
				}
				else {
					$scope.state = 'error';
				}
				$scope.message = response.data.message;
				$scope.processing = false;
			});
		}
	};

	$scope.cancelStyle = function() {
		$scope.styleEdit = null;
		$scope.state = null;
		$location.path("/settings");
	};

}]);
