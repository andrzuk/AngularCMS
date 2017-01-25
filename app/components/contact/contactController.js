angular.module('contactController', ['contactService', 'pagesService', 'config', 'ngSanitize'])

.controller('ContactController', ['$scope', 'Contact', '$sce', 'Pages', 'ngMeta', function($scope, Contact, $sce, Pages, ngMeta) {

	$scope.getPage = function() {
		$scope.processing = true;
		Pages.public_contact().then(function(response) {
			$scope.page = response.data;
			$scope.page.contents = $sce.trustAsHtml($scope.page.contents);
			$scope.processing = false;
			ngMeta.setTitle($scope.page.title);
			ngMeta.setTag('description', $scope.page.description);
		});
	};

	$scope.sendMessage = function() {
		if ($scope.messageData !== undefined) {
			Contact.send($scope.messageData).then(function(response) {
				if (response.data.success) {
					$scope.messageData = null;
					$scope.info = true;
					$scope.error = false;
				}
				else {
					$scope.info = false;
					$scope.error = true;
				}
				$scope.message = response.data.message;
			});
		}
	};

}]);