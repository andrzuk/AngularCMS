angular.module('contactService', [])

.factory('Contact', ['$http', 'config', function($http, config) {
	
	var contactFactory = {};

	contactFactory.send = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + 'store_message.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			contactFactory.success = response.success;
			contactFactory.error = !response.success;
			return response;
		})
		.error(function(error) {
			return error;
		});
	};

	return contactFactory;
}]);
