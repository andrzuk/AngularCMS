angular.module('MainApp', [
    'appRoutes',
    'config',
    'mainDirective',
    'uxDirectives',
    'mainFilter',
    'navController',
    'navService',
    'mainController',
    'metaController',
    'metaService',
    'authController',
    'authService',
    'paginController',
    'paginService',
    'adminController',
    'adminService',
    'contactController',
    'contactService',
    'settingsController',
    'settingsService',
    'homeController',
    'categoryController',
    'pageController',
    'usersController',
    'usersService',
    'aclController',
    'aclService',
    'categoriesController',
    'categoriesService',
    'pagesController',
    'pagesService',
    'imagesController',
    'imagesService',
    'messagesController',
    'messagesService',
    'visitorsController',
    'visitorsService',
    'loginsController',
    'loginsService',
    'stylesController',
    'stylesService',
    'scriptsController',
    'scriptsService',
    'installController',
    'installService',
    'searchesController',
    'searchesService',
    'gamesController',
    'gamesService',
    'ui.tinymce',
    ])

.config(function ($httpProvider) {
    $httpProvider.interceptors.push('AuthInterceptor');
})

.config(function ($provide) {

    $provide.decorator('$document', ['$delegate', function ($delegate) {
        $delegate.getReferer = function() {
            return document.referrer;
        };
        return $delegate;
    }]);

    $provide.decorator('formDirective', ['$delegate', function ($delegate) {
        var formDirective = $delegate[0];
        var oldCompile = formDirective.compile;
        formDirective.compile = function (element, attrs, transclude) {
            var compile = oldCompile ? oldCompile.apply(this, arguments) : {};
            element.attr("spellcheck", "false");
            return compile;
        };
        return $delegate;
    }]);

});
