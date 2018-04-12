angular.module('navService', [])

.factory('Menu', ['$http', 'config', function ($http, config) {

	var menuFactory = {};

	menuFactory.getMenu = function () {
		return $http.get(config.apiUrl + 'get_menu.php');
	};

	menuFactory.getSubmenu = function(id) {
		return $http.get(config.apiUrl + 'get_submenu.php?id=' + id);
	};

	return menuFactory;

}])

.factory('Nav', ['$http', 'config', function ($http, config) {

	var navigateFactory = {};

	navigateFactory.found = function(text) {
		return $http.get(config.apiUrl + 'get_found_list.php?search=' + text);
	};

	navigateFactory.store = function(text, results) {
		return $http.get(config.apiUrl + 'store_search.php?search=' + text + '&count=' + results);
	};

	navigateFactory.carousel = function() {
		return $http.get(config.apiUrl + 'get_carousel.php');
	};

	navigateFactory.sidebar = function() {
		return $http.get(config.apiUrl + 'get_sidebar.php');
	};

	navigateFactory.menuSettings = function() {
		return $http.get(config.apiUrl + 'get_menu_settings.php');
	};

	navigateFactory.registerVisitor = function (referer, url) {
		return $http({
			method: 'POST',
			url: config.apiUrl + 'store_visit.php',
			data: $.param({ referer: referer, url: url }),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		});
	};

	return navigateFactory;

}]);
