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
			$scope.series_v = ['Unique Visitors'];
			$scope.series_n = ['Navigations Count'];
			$scope.series_t = ['Total Time [min]'];
			$scope.colors_v = ['#FFB1C1'];
			$scope.colors_n = ['#97BBCD'];
			$scope.colors_t = ['#FDB45C'];
			$scope.data_v = [[]];
			$scope.data_n = [[]];
			$scope.data_t = [[]];
			angular.forEach(response.data, function(value, key) {
				$scope.labels.push(key % 4 ? '' : value.date);
				$scope.data_v[0].push(value.count);
				$scope.data_n[0].push(value.sum);
				$scope.data_t[0].push(value.time);
			});
			$scope.datasetOverride = [{ yAxisID: 'y-axis' }];
			$scope.options = {
				scales: {
					yAxes: [
						{ id: 'y-axis', type: 'linear', display: true, position: 'left', ticks: { beginAtZero: true } }
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

