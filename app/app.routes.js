angular.module('appRoutes', ['ngRoute', 'ngMeta'])

.config(['$routeProvider', '$locationProvider', 'ngMetaProvider', function($routeProvider, $locationProvider, ngMetaProvider) {

    $routeProvider

    .when('/', {
        templateUrl: 'app/components/home/homeView.html',
        controller: 'HomeController',
        isProtected: false,
    })
    .when('/category/:id', {
        templateUrl: 'app/components/category/categoryView.html',
        controller: 'CategoryController',
        isProtected: false,
    })
    .when('/page/:id', {
        templateUrl: 'app/components/page/pageView.html',
        controller: 'PageController',
        isProtected: false,
    })
    .when('/login', {
        templateUrl: 'app/components/login/loginView.html',
        controller: 'AuthController',
        isProtected: false,
    })
    .when('/password', {
        templateUrl: 'app/components/login/passwordView.html',
        controller: 'AuthController',
        isProtected: false,
    })
    .when('/register', {
        templateUrl: 'app/components/register/registerView.html',
        controller: 'AuthController',
        isProtected: false,
    })
    .when('/logout', {
        templateUrl: 'app/components/login/logoutView.html',
        isProtected: true,
    })
    .when('/contact', {
        templateUrl: 'app/components/contact/contactView.html',
        isProtected: false,
    })
    .when('/admin', {
        templateUrl: 'app/components/admin/adminView.html',
        isProtected: true,
    })
    .when('/acl', {
        templateUrl: 'app/components/acl/aclView.html',
        isProtected: true,
    })
    .when('/settings', {
        templateUrl: 'app/components/settings/settingsView.html',
        isProtected: true,
    })
    .when('/users', {
        templateUrl: 'app/components/users/usersView.html',
        isProtected: true,
    })
    .when('/categories', {
        templateUrl: 'app/components/categories/categoriesView.html',
        isProtected: true,
    })
    .when('/pages', {
        templateUrl: 'app/components/pages/pagesView.html',
        isProtected: true,
    })
    .when('/images', {
        templateUrl: 'app/components/images/imagesView.html',
        isProtected: true,
    })
    .when('/messages', {
        templateUrl: 'app/components/messages/messagesView.html',
        isProtected: true,
    })
    .when('/visitors', {
        templateUrl: 'app/components/visitors/visitorsView.html',
        isProtected: true,
    })
    .when('/logins', {
        templateUrl: 'app/components/logins/loginsView.html',
        isProtected: true,
    })
    .when('/styles', {
        templateUrl: 'app/components/styles/stylesView.html',
        isProtected: true,
    })
    .when('/scripts', {
        templateUrl: 'app/components/scripts/scriptsView.html',
        isProtected: true,
    })
    .when('/install', {
        templateUrl: 'app/components/install/installView.html',
        controller: 'InstallController',
        isProtected: false,
    })
    .when('/search', {
        templateUrl: 'app/components/search/searchView.html',
        isProtected: false,
    })
    .when('/searches', {
        templateUrl: 'app/components/search/searchesView.html',
        isProtected: true,
    })
    .when('/games', {
        templateUrl: 'app/components/games/gamesView.html',
        isProtected: true,
    })
    .otherwise({
        redirectTo: '/'
    });

    $locationProvider.html5Mode(true);

    ngMetaProvider.useTitleSuffix(true);
    ngMetaProvider.setDefaultTitle('Angular CMS');
    ngMetaProvider.setDefaultTitleSuffix(' | Angular CMS - Single Page Application @ AngularJS framework');
    ngMetaProvider.setDefaultTag('author', 'Andrzej Żukowski');

}])

.run(['ngMeta', function(ngMeta) { 

    ngMeta.init();

}]);
