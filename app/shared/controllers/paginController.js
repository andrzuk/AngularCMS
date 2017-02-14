angular.module('paginController', ['paginService'])

.controller('PaginatorController', ['$scope', 'Paginator', function($scope, Paginator) {
	
	$scope.pointers = [];

	$scope.initPaginator = function(table) {
		$scope.pointers.push({ label: '...', active: false });
		Paginator.getSize(table).then(function(response) {
			Paginator.setPages(response.data.counter);
			$scope.pointers = [];
			$scope.setPointers(Paginator.getPages());
		});
	};

	$scope.setPointers = function(pages) {
		var i = 0, j = 0;
		for (i = 1; i <= Paginator.getBand(); i++) {
			if (i <= Paginator.getPages()) {
				$scope.pointers.push({ label: i, active: true });
			}
		}
		if (Paginator.getPages() > 2 * Paginator.getBand()) {
			$scope.pointers.push({ label: '...', active: false });
		}
		for (j = Paginator.getPages() - Paginator.getBand() + 1; j <= Paginator.getPages(); j++) {
			if (j >= i) {
				$scope.pointers.push({ label: j, active: true });
			}
		}
		if (!$scope.pointers.length) {
			$scope.pointers.push({ label: 1, active: true });
		}
	};

}]);
