angular.module('homeController', ['pagesService', 'ngSanitize', 'ui.bootstrap'])

.controller('HomeController', ['$scope', '$sce', 'Pages', 'ngMeta', function ($scope, $sce, Pages, ngMeta) {

	$scope.getPage = function() {
		$scope.processing = true;
		Pages.public_index().then(function(response) {
			$scope.page = response.data;
			$scope.page.contents = $sce.trustAsHtml($scope.page.contents);
			$scope.processing = false;
			ngMeta.setTitle($scope.page.title);
			ngMeta.setTag('description', $scope.page.description);
		});
	};

}]);
