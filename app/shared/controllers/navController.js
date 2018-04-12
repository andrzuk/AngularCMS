angular.module('navController', ['navService'])

.controller('NavController', ['$scope', function($scope) {

	$scope.navPills = [
		{
			'icon': 'cog',
			'link': 'settings',
			'caption': 'Ustawienia'
		},
		{
			'icon': 'user',
			'link': 'users',
			'caption': 'Konta'
		},
		{
			'icon': 'check-square-o',
			'link': 'acl',
			'caption': 'Dostęp'
		},
		{
			'icon': 'folder-open-o',
			'link': 'categories',
			'caption': 'Kategorie'
		},
		{
			'icon': 'clone',
			'link': 'pages',
			'caption': 'Strony'
		},
		{
			'icon': 'film',
			'link': 'images',
			'caption': 'Galeria'
		},
		{
			'icon': 'envelope-o',
			'link': 'messages',
			'caption': 'Wiadomości'
		},
		{
			'icon': 'globe',
			'link': 'visitors',
			'caption': 'Wizyty'
		},
	];
}])

.controller('MenuController', ['$rootScope', '$scope', '$location', 'Menu', 'Nav', function($rootScope, $scope, $location, Menu, Nav) {

	$scope.menuList = {};
	$scope.submenuList = {};

	$rootScope.$on('$routeChangeStart', function (event, next, current) {
		if (current) {
			if (current.$$route) {
				if (current.$$route.originalPath == '/categories') {
					$scope.updateMenu();
				}
			}
		}
		if (next) {
			if (next.$$route) {
				if (next.$$route.originalPath == '/page/:id') {
					var url = $location.url();
					var segments = url.split('/');
					$scope.updateSubmenu(segments[2]);
				}
				else {
					$scope.submenuList = {};
				}
			}
		}
	});

	Menu.getMenu().then(function(response) {
		if (angular.isArray(response.data)) {
			$scope.menuList = response.data;
		}
	});

	$scope.updateMenu = function() {
		Menu.getMenu().then(function(response) {
			if (angular.isArray(response.data)) {
				$scope.menuList = response.data;
			}
		});
	}

	$scope.updateSubmenu = function(id) {
		Menu.getSubmenu(id).then(function(response) {
			if (angular.isArray(response.data)) {
				$scope.submenuList = response.data;
			}
		});
	}

	function checkIfMenusAreEnabled () {
		angular.forEach($scope.menuSettingsContext, function(obj, i) {
			if ( obj.key_name == 'menu_sidebar_enabled') {
				$scope.menuInSidebarEnabled = obj.key_value;
			}
			if ( obj.key_name == 'submenu_sidebar_enabled') {
				$scope.submenuInSidebarEnabled = obj.key_value;
			}
			if ( obj.key_name == 'menu_navbar_enabled') {
				$scope.menuInNavbarEnabled = obj.key_value;
			}
			if ( obj.key_name == 'submenu_navbar_enabled') {
				$scope.submenuInNavbarEnabled = obj.key_value;
			}
		});
	}
	
	Nav.menuSettings().then(function(response) {
		$scope.menuSettingsContext = [];
		angular.forEach(response.data.context_list, function(item, i) {
			$scope.menuSettingsContext.push(item);
		});
		checkIfMenusAreEnabled();

		console.log( 'active' );
	});

}]);
