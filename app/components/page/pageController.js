angular.module('pageController', ['pagesService', 'ngSanitize', 'ui.bootstrap'])

.controller('PageController', ['$scope', '$routeParams', '$sce', 'Pages', 'ngMeta', function ($scope, $routeParams, $sce, Pages, ngMeta) {

	$scope.params = $routeParams;

	$scope.getPage = function() {
		$scope.processing = true;
		Pages.public_one($scope.params.id).then(function(response) {
			$scope.type = response.data.type;
			$scope.label = response.data.label;
			if ($scope.type == 'item') {
				$scope.page = response.data;
				$scope.page.contents = $sce.trustAsHtml($scope.page.contents);
				ngMeta.setTitle($scope.page.title);
				ngMeta.setTag('description', $scope.page.description);
			}
			$scope.processing = false;
		});
	};

}]);
