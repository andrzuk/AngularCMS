angular.module('navController', ['navService'])

.controller('NavController', ['$scope', function($scope) {
	
	$scope.navPills = [
		{
			'link': 'settings',
			'caption': 'Ustawienia'
		},
		{
			'link': 'scripts',
			'caption': 'JS'
		},
		{
			'link': 'styles',
			'caption': 'CSS'
		},
		{
			'link': 'users',
			'caption': 'Konta'
		},
		{
			'link': 'acl',
			'caption': 'Dostęp'
		},
		{
			'link': 'categories',
			'caption': 'Kategorie'
		},
		{
			'link': 'pages',
			'caption': 'Strony'
		},
		{
			'link': 'images',
			'caption': 'Galeria'
		},
		{
			'link': 'messages',
			'caption': 'Wiadomości'
		},
		{
			'link': 'visitors',
			'caption': 'Wizyty'
		},
		{
			'link': 'searches',
			'caption': 'Szukania'
		}
	];
}])

.controller('MenuController', ['$rootScope', '$scope', '$location', 'Menu', function($rootScope, $scope, $location, Menu) {

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

}]);

