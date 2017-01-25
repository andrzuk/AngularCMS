angular.module('installController', ['installService', 'config'])

.run(['$http', '$rootScope', '$location', 'Install', function($http, $rootScope, $location, Install) {

	Install.check().then(function(response) {
		if (response.data.install_found) {
			$rootScope.installMode = true;
			$location.path('/install');
		}
		else {
			$rootScope.installMode = false;
		}
	});
	
}])

.controller('InstallController', ['$scope', '$rootScope', '$location', 'Install', function($scope, $rootScope, $location, Install) {

	$scope.initInstall = function() {
		Install.check().then(function(response) {
			$scope.action = 'init';
			$scope.state = null;
			$scope.installInit = null;
			if (!response.data.install_found) {
				$location.path('/');
			}
		});
	};

	$scope.saveInstall = function() {
		if ($scope.installInit) {
			$scope.processing = true;
			$scope.state = null;
			Install.install($scope.installInit).then(function(response) {
				if (response.data.success) {
					$scope.result = response.data.settings;
					$scope.message = response.data.message;
					Install.delete().then(function(response) {
						$rootScope.installMode = false;
						$scope.installInit = null;
						$scope.action = 'result';
						$scope.state = 'info';
					});
				}
				else {
					$scope.action = 'init';
					$scope.state = 'error';
					$scope.message = 'Brak połączenia z bazą danych. Sprawdź ustawienia.';
				}
				$scope.processing = false;
			});
		}
	};

	$scope.cancelInstall = function() {
		$location.path('/');
	};

}]);