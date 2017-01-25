angular.module('metaService', [])

.factory('Meta', ['$http', 'config', function ($http, config) {

	var metaFactory = {};

	metaFactory.getMeta = function () {
		return $http.get(config.apiUrl + 'get_meta.php');
	};

	return metaFactory;
}]);
