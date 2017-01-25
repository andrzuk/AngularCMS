angular.module('categoryController', ['ngRoute', 'pagesService', 'ngSanitize', 'ui.bootstrap'])

.controller('CategoryController', ['$scope', '$routeParams', '$sce', 'Pages', 'ngMeta', function ($scope, $routeParams, $sce, Pages, ngMeta) {

	$scope.params = $routeParams;

	$scope.getPages = function() {
		$scope.processing = true;
		Pages.public_all($scope.params.id).then(function(response) {
			$scope.type = response.data.type;
			$scope.label = response.data.label;
			if ($scope.type == 'item') {
				$scope.pageContent = response.data;
				$scope.pageContent.contents = $sce.trustAsHtml($scope.pageContent.contents);
				ngMeta.setTitle($scope.pageContent.title);
				ngMeta.setTag('description', $scope.pageContent.description);
			}
			if ($scope.type == 'list') {
				$scope.pagesList = response.data;
				ngMeta.setTitle($scope.label);
			}
			$scope.processing = false;
		});
	};

}]);

