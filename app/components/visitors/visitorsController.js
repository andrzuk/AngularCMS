angular.module('visitorsController', ['visitorsService', 'config', 'paginService', 'chart.js'])

.controller('VisitorsController', ['$scope', 'Visitors', 'Paginator', function($scope, Visitors, Paginator) {
	
	$scope.moduleName = 'visitors';
	$scope.componentName = 'visitors';

	$scope.getVisitors = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.currentPage = 1;
		Paginator.getSize($scope.moduleName).then(function(response) {
			Paginator.reset(response.data.counter);
		});
		var showRows = Paginator.getLines($scope.moduleName);
		Visitors.all(showRows, $scope.currentPage).then(function(response) {
			$scope.visitorsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.changePage = function(page) {
		var newPage = Paginator.getPage(page);
		if (newPage == $scope.currentPage) return;
		$scope.currentPage = newPage;
		var showRows = Paginator.getLines($scope.moduleName);
		Visitors.all(showRows, $scope.currentPage).then(function(response) {
			$scope.visitorsList = response.data;
		});
	};

	$scope.viewVisitor = function(id) {
		$scope.id = id;
		$scope.action = 'view';
		$scope.state = null;
		Visitors.one(id).then(function(response) {
			$scope.visitorView = response.data;
		});
	};

	$scope.excludeVisitor = function(ip) {
		$scope.processing = true;
		Visitors.exclude(ip).then(function(response) {
			if (response.data.success) {
				$scope.state = 'info';
			}
			else {
				$scope.state = 'error';
			}
			$scope.message = response.data.message;
			$scope.processing = false;
			$scope.getVisitors();
		});
	};

	$scope.viewChart = function() {
		$scope.action = 'chart';
		$scope.state = null;
		$scope.processing = true;
		Visitors.statistics().then(function(response) {
			$scope.labels = [];
			$scope.series = ['Unique Visitors', 'Navigations Count'];
			$scope.colors = ['#97BBCD', '#F2C099'];
			$scope.data = [[], []];
			angular.forEach(response.data, function(value, key) {
				$scope.labels.push(key % 4 ? '' : value.date);
				$scope.data[0].push(value.count);
				$scope.data[1].push(value.sum);
			});
			$scope.datasetOverride = [{ yAxisID: 'y-axis-left' }, { yAxisID: 'y-axis-right' }];
			$scope.options = {
				scales: {
					yAxes: [
						{ id: 'y-axis-left', type: 'linear', display: true, position: 'left', ticks: { beginAtZero: true } },
						{ id: 'y-axis-right', type: 'linear', display: true, position: 'right', ticks: { beginAtZero: true } }
					]
				},
				legend: {
					display: true,
					labels: {
						fontColor: 'rgb(128, 128, 128)',
						fontSize: 13,
						padding: 10
					}
				}
			};
			$scope.processing = false;
		});
	};

	$scope.findVisitors = function() {
		$scope.action = 'list';
		$scope.processing = true;
		$scope.state = null;
		$scope.visitorsList = [];
		$scope.currentPage = 1;
		Paginator.reset(0);
		Visitors.getFiltered($scope.searchValue).then(function(response) {
			$scope.visitorsList = response.data;
			$scope.processing = false;
		});
	};

	$scope.closeFilter = function() {
		$scope.searchValue = '';
		$scope.getVisitors();
	};

	$scope.cancelVisitor = function() {
		$scope.visitorView = null;
		$scope.action = 'list';
		$scope.state = null;
	};

}]);

