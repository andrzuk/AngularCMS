angular.module('paginService', [])

.factory('Paginator', ['$http', 'config', function ($http, config) {

	var paginFactory = {};
	
	paginFactory.pagination = {
		selected: 0,
		pages: 0,
		band: 5,
		rows: 10
	};

	paginFactory.init = function() {
		this.reset();
		$http.get(config.apiUrl + 'get_settings_value.php?key=list_rows_per_page').then(function(response) {
			paginFactory.pagination.rows = response.data.value;
		});
		$http.get(config.apiUrl + 'get_settings_value.php?key=paginator_pointer_band').then(function(response) {
			paginFactory.pagination.band = response.data.value;
		});
	};

	paginFactory.reset = function() {
		this.pagination.selected = 1;
	};

	paginFactory.getSize = function(table) {
		return $http.get(config.apiUrl + 'get_table_size.php?name=' + table);
	};

	paginFactory.getLines = function() {
		return this.pagination.rows;
	};

	paginFactory.getBand = function() {
		return this.pagination.band;
	};

	paginFactory.setRows = function(value) {
		this.pagination.rows = parseInt(value);
	};

	paginFactory.setWidth = function(value) {
		this.pagination.band = parseInt(value);
	};

	paginFactory.setPages = function(elements) {
		this.pagination.pages = Math.ceil(elements / this.pagination.rows);
	};

	paginFactory.getPage = function(page) {
		if (angular.isNumber(page)) {
			this.pagination.selected = page;
		}
		else {
			switch (page) {
				case 'first':
					this.pagination.selected = 1;
				break;
				case 'prev':
					if (this.pagination.selected > 1)
						this.pagination.selected--;
				break;
				case 'next':
					if (this.pagination.selected < this.pagination.pages)
						this.pagination.selected++;
				break;
				case 'last':
					this.pagination.selected = this.pagination.pages;
				break;
			}
		}
		return this.pagination.selected;
	};

	paginFactory.getPages = function() {
		return this.pagination.pages;
	};

	return paginFactory;

}]);
