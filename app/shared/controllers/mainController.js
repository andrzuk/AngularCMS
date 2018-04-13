angular.module('mainController', ['ngRoute', 'authService', 'navService', 'config'])

.controller('MainController', ['$rootScope', '$location', '$route', '$routeParams', '$scope', '$document', 'Auth', 'Nav', function($rootScope, $location, $route, $routeParams, $scope, $document, Auth, Nav) {

	$scope.user = {};
	$scope.carouselContext = [];
	$scope.foundList = {};
	$scope.searchToggled = false;

	$scope.toggleSearch = function() {
		$scope.searchToggled = true;
	};

	$scope.layout = {
		header: 'app/templates/header.html',
		content: 'app/templates/content.html',
		footer: 'app/templates/footer.html',
		navigator: 'app/templates/navigator.html',
		paginator: 'app/templates/paginator.html',
		messager: 'app/templates/messager.html',
		access: 'app/templates/access.html',
	};

	$scope.$location = $location;
	$scope.$route = $route;
	$scope.$routeParams = $routeParams;

	Nav.carousel().then(function(response) {
		$scope.carouselInterval = response.data.interval;
		$scope.carouselSlides = response.data.slides;
		$scope.carouselTemplate = 'app/templates/carousel.html';
		$scope.carouselContext = [];
		angular.forEach(response.data.context_list, function(item, i) {
			$scope.carouselContext.push(item);
		});
		$scope.carouselEnabled = $scope.checkCarouselEnabled();
	});

	Nav.sidebar().then(function(response) {
		$scope.sidebarTemplate = 'app/templates/sidebar.html';
		$scope.sidebarContext = [];
		angular.forEach(response.data.context_list, function(item, i) {
			$scope.sidebarContext.push(item);
		});
		$scope.sidebarEnabled = $scope.checkSidebarEnabled();
	});

	$rootScope.$on('$routeChangeStart', function (event, next, current) {
		$scope.user.isLoggedIn = Auth.isLoggedIn();
		if ($scope.user.isLoggedIn) {
			Auth.getUser().then(function (response) {
				$scope.user = response.data;
			});
		}
		else {
			$scope.user = {};
			angular.forEach($route.routes, function (route, path) {
				if (route.isProtected) {
					if (next.templateUrl == route.templateUrl) {
						$location.path('/login');
						return;
					}
				}
			});
		}
	});

	$rootScope.$on('$locationChangeStart', function (event, next, current) {
		$scope.carouselEnabled = $scope.checkCarouselEnabled();
		$scope.sidebarEnabled = $scope.checkSidebarEnabled();
		setTimeout( function () {window.scrollTo( 0, 0);},100 );
	});

	$rootScope.$on('$routeChangeSuccess', function (event, next, current) {
		var referer = $document.getReferer();
		var url = $location.url();
		Nav.registerVisitor(referer, url);
		$scope.searchToggled = false;
	});

	$scope.checkCarouselEnabled = function() {
		var url = $location.url();
		var segments = url.split('/');
		var result = false;
		angular.forEach($scope.carouselContext, function(obj, i) {
			if (segments[1] == '') {
				if (obj.key_name == 'carousel_index_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'contact') {
				if (obj.key_name == 'carousel_contact_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'page') {
				if (obj.key_name == 'carousel_page_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'category') {
				if (obj.key_name == 'carousel_category_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'search') {
				if (obj.key_name == 'carousel_search_enabled') {
					result = obj.key_value;
				}
			}
		});
		return result;
	};

	$scope.checkSidebarEnabled = function() {
		var url = $location.url();
		var segments = url.split('/');
		var result = false;
		angular.forEach($scope.sidebarContext, function(obj, i) {
			if (segments[1] == '') {
				if (obj.key_name == 'sidebar_index_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'contact') {
				if (obj.key_name == 'sidebar_contact_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'page') {
				if (obj.key_name == 'sidebar_page_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'category') {
				if (obj.key_name == 'sidebar_category_enabled') {
					result = obj.key_value;
				}
			}
			if (segments[1] == 'search') {
				if (obj.key_name == 'sidebar_search_enabled') {
					result = obj.key_value;
				}
			}
		});
		return result;
	};

	$scope.getFound = function(searchText) {
		$scope.processing = true;
		$scope.state = null;
		searchText = searchText == undefined ? '%' : searchText;
		Nav.found(searchText).then(function(response) {
			$scope.foundList = response.data;
			$scope.searchText = searchText;
			$scope.processing = false;
			$scope.state = 'finish';
			Nav.store(searchText, $scope.foundList.length).then(function(response) {
			});
			$location.path('/search');
			$scope.searchToggled = false;
		});
	};

}]);
