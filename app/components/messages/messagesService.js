angular.module('messagesService', [])

.factory('Messages', ['$http', 'config', function($http, config) {
	
	var messagesFactory = {};

	var componentName = 'messages';

	messagesFactory.all = function(mode, rows, page) {
		return $http.get(config.apiUrl + componentName + '/get_messages_list.php?mode=' + mode + '&rows=' + rows + '&page=' + page);
	};

	messagesFactory.one = function(id) {
		return $http.get(config.apiUrl + componentName + '/get_message.php?id=' + id);
	};

	messagesFactory.update = function(formData) {
		return $http({
			method: 'POST',
			url: config.apiUrl + componentName + '/update_message.php',
			data: $.param(formData),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
		.success(function(response) {
			messagesFactory.success = response.success;
			return response;
		})
		.error(function(error) {
			messagesFactory.success = false;
			return error;
		});
	};

	messagesFactory.delete = function(id) {
		return $http.get(config.apiUrl + componentName + '/delete_message.php?id=' + id);
	};

	return messagesFactory;
}]);
