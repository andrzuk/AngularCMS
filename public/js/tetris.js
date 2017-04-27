/*
 *	Tetris v1.0
 *	Copyright (C) 2017 Andrzej Żukowski
 *	http://angular-cms.pl
 */

var canvas = document.getElementById("game-canvas");
var ctx = canvas.getContext("2d");

canvas.width = parseInt($("canvas#game-canvas").css("width"));
canvas.height = parseInt($("canvas#game-canvas").css("height"));

var shapes = [
	{
		map: [[0, 0, 0], [1, 1, 1], [0, 0, 0]],
		color: '#cc0000'
	},
	{
		map: [[1, 1, 0], [1, 1, 0], [0, 0, 0]],
		color: '#0099ff'
	},
	{
		map: [[1, 0, 0], [1, 1, 0], [0, 1, 0]],
		color: '#00cc00'
	},
	{
		map: [[0, 0, 1], [0, 1, 1], [0, 1, 0]],
		color: '#cc00cc'
	},
	{
		map: [[0, 1, 0], [0, 1, 0], [0, 1, 1]],
		color: '#ffcc33'
	},
	{
		map: [[0, 1, 0], [0, 1, 0], [1, 1, 0]],
		color: '#ff9933'
	},
	{
		map: [[0, 1, 0], [1, 1, 1], [0, 0, 0]],
		color: '#9966cc'
	},
	{
		map: [[0, 0, 0], [1, 1, 1], [1, 0, 1]],
		color: '#cc6600'
	},
];
var playGame = false, stepTime, playTime = 500, localStorageKeyName = 'Tetris_Maps';

var gameArea = {
	cellSize: 20,
	cellDist: 2,
	colsCount: 20,
	rowsCount: 25,
	cellColor: "#cde",
	cellStatus: [],
	level: 0,
	scores: 0,
	init: function() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		ctx.fillStyle = "#def";
		ctx.fillRect(0, 0, canvas.width, canvas.height);
		this.cellStatus = [];
		for (i = 0; i < this.colsCount; i++) {
			var row = [];
			for (j = 0; j < this.rowsCount; j++) {
				this.paintCell(i, j, this.cellColor);
				row.push(0);
			}
			this.cellStatus.push(row);
		}
		this.level = 0;
		this.scores = 0;
		gameFigure.counter = 0;
		$('div#game-message').text('Gra w toku...');
		$('div#game-button').css({ display: 'none' });
	},
	paintCell: function(x, y, color) {
		ctx.fillStyle = color;
		ctx.fillRect(x * this.cellSize + this.cellDist / 2, y * this.cellSize + this.cellDist / 2, this.cellSize - this.cellDist, this.cellSize - this.cellDist);
	},
	clearFigure: function(figure) {
		for (i = 0; i < figure.size; i++) {
			for (j = 0; j < figure.size; j++) {
				if (figure.shape.map[i][j]) {
					this.paintCell(figure.position.x + i, figure.position.y + j, this.cellColor);
				}
			}
		}
	},
	paintFigure: function(figure) {
		for (i = 0; i < figure.size; i++) {
			for (j = 0; j < figure.size; j++) {
				if (figure.shape.map[i][j]) {
					this.paintCell(figure.position.x + i, figure.position.y + j, figure.shape.color);
				}
			}
		}
	},
	checkCollision: function(figure) {
		for (i = 0; i < figure.size; i++) {
			for (j = 0; j < figure.size; j++) {
				if (figure.shape.map[i][j]) {
					if (figure.position.x + i < 0) return true;
					if (figure.position.x + i > this.colsCount - 1) return true;
					/*
					if (figure.position.y + j < 0) return true;
					*/
					if (figure.position.y + j > this.rowsCount - 1) return true;
					if (gameArea.cellStatus[figure.position.x + i][figure.position.y + j]) return true;
				}
			}
		}
		return false;
	},
	checkCompletion: function() {
		var completed = 0;
		for (i = 0; i < this.colsCount; i++) {
			completed += gameArea.cellStatus[i][this.rowsCount - 1] ? 1 : 0;
		}
		return completed == this.colsCount;
	},
	collapse: function() {
		for (j = this.rowsCount - 1; j > 0; j--) {
			for (i = 0; i < this.colsCount; i++) {
				gameArea.cellStatus[i][j] = gameArea.cellStatus[i][j - 1];
			}
		}
		for (i = 0; i < this.colsCount; i++) {
			gameArea.cellStatus[i][0] = 0;
		}
		for (j = this.rowsCount - 1; j > 0; j--) {
			for (i = 0; i < this.colsCount; i++) {
				if (this.cellStatus[i][j]) {
					this.paintCell(i, j, shapes[this.cellStatus[i][j] - 1].color);
				}
				else {
					this.paintCell(i, j, this.cellColor);
				}
			}
		}
		for (i = 0; i < this.colsCount; i++) {
			this.paintCell(i, 0, this.cellColor);
		}
		this.level++;
		this.scores += this.colsCount;
		if (playTime > 20) playTime -= 10;
		gameFigure.init();
	},
};

var gameFigure = {
	size: 0,
	index: 0,
	position: { x: 0, y: 0 },
	shape: {},
	drop: false,
	counter: 0,
	init: function() {
		var idx = Math.floor(Math.random() * shapes.length);
		this.shape = shapes[idx];
		this.index = idx + 1;
		this.size = this.shape.map.length;
		this.position = { x: gameArea.colsCount / 2 - 2, y: -this.size };
		var prepare = Math.floor(Math.random() * 4);
		switch (prepare) {
			case 1:
				this.rotate('left');
				break;
			case 2:
				this.rotate('right');
				break;
			case 3:
				for (i = 0; i < 2; i++) {
					this.rotate('left');
				}
				break;
		}
		this.drop = false;
		stepTime = playTime;
		this.counter++;
		gameArea.scores += gameArea.level;
		playGame = true;
		$('#blocks').text('Bloczek: ' + gameFigure.counter.toString());
		$('#level').text('Poziom: ' + gameArea.level.toString());
		$('#scores').text('Punktów: ' + gameArea.scores.toString());
		if (gameArea.checkCollision(this)) {
			playGame = false;
			$('div#game-message').text('Gra zakończona.');
			$('div#game-button').css({ display: 'block' });
		}
		else {
			gameFigure.animate();
		}
	},
	animate: function() {
		if (!playGame) return;
		var collision = false;
		gameArea.clearFigure(this);
		this.position.y++;
		if (gameArea.checkCollision(this)) {
			this.position.y--;
			collision = true;
		}
		gameArea.paintFigure(this);
		$('#position').text('Pozycja: (' + this.position.x.toString() + ', ' + this.position.y.toString() + ')');
		if (collision) {
			this.settle();
		}
		else {
			setTimeout(function() {
				gameFigure.animate();
			}, stepTime);
		}
	},
	move: function(direction) {
		if (direction == 'left') {
			gameArea.clearFigure(this);
			this.position.x--;
			if (gameArea.checkCollision(this)) this.position.x++;
			gameArea.paintFigure(this);
		}
		if (direction == 'right') {
			gameArea.clearFigure(this);
			this.position.x++;
			if (gameArea.checkCollision(this)) this.position.x--;
			gameArea.paintFigure(this);
		}
		if (direction == 'up') {
			/*
			gameArea.clearFigure(this);
			this.position.y--;
			if (gameArea.checkCollision(this)) this.position.y++;
			gameArea.paintFigure(this);
			*/
		}
		if (direction == 'down') {
			gameArea.clearFigure(this);
			this.position.y++;
			if (gameArea.checkCollision(this)) this.position.y--;
			gameArea.paintFigure(this);
		}
	},
	rotate: function(direction) {
		var map = [], row = [];
		for (i = 0; i < this.size; i++) {
			row = [];
			for (j = 0; j < this.size; j++) {
				row.push(this.shape.map[i][j]);
			}
			map.push(row);
		}
		if (direction == 'left') {
			gameArea.clearFigure(this);
			for (i = 0; i < this.size; i++) {
				for (j = 0; j < this.size; j++) {
					this.shape.map[i][j] = map[this.size - j - 1][i];
				}
			}
			if (gameArea.checkCollision(this)) this.shape.map = map;
			gameArea.paintFigure(this);
		}
		if (direction == 'right') {
			gameArea.clearFigure(this);
			for (i = 0; i < this.size; i++) {
				for (j = 0; j < this.size; j++) {
					this.shape.map[i][j] = map[j][this.size - i - 1];
				}
			}
			if (gameArea.checkCollision(this)) this.shape.map = map;
			gameArea.paintFigure(this);
		}
	},
	settle: function() {
		for (i = 0; i < this.size; i++) {
			for (j = 0; j < this.size; j++) {
				if (this.shape.map[i][j]) {
					gameArea.cellStatus[this.position.x + i][this.position.y + j] = this.shape.map[i][j] * this.index;
				}
			}
		}
		if (gameArea.checkCompletion()) {
			setTimeout(function() {
				gameArea.collapse();
			}, playTime);
		}
		else {
			this.init();
		}
	},
};

var mapEditor = {
	customMaps: [],
	customMap: [],
	customColors: [], 
	customColor: '#336699',
	loadMaps: function() {
		this.customMaps = [];
		for (item = 0; item < shapes.length; item++) {
			this.customMap = [];
			for (j = 0; j < shapes[item].map.length; j++) {
				var row = [];
				for (i = 0; i < shapes[item].map.length; i++) {
					row.push(shapes[item].map[i][j]);
				}
				this.customMap.push(row);
			}
			this.customMaps.push(this.customMap);
		}
		var size = this.customMaps[this.customMaps.length - 1].length;
		this.customMap = [];
		for (j = 0; j < size; j++) {
			var row = [];
			for (i = 0; i < size; i++) {
				row.push(0);
			}
			this.customMap.push(row);
		}
		var mapItems = '';
		mapItems += '<div id="maps-container">';
		for (item = 0; item < this.customMaps.length; item++) {
			size = this.customMaps[item].length;
			this.customColors[item] = shapes[item].color;
			var mapItem = '';
			mapItem += '<div class="item-container">';
			mapItem += '<div class="lp">' + (item + 1).toString() + '.' + '</div>';
			mapItem += '<div class="map">';
			mapItem += '<table align="center">';
			for (j = 0; j < this.customMaps[item].length; j++) {
				mapItem += '<tr>';
				for (i = 0; i < this.customMaps[item].length; i++) {
					cellStyle = this.customMaps[item][i][j] ? 'background: ' + shapes[item].color : '';
					mapItem += '<td><button id="map-' + item + '-' + i + '-' + j + '" class="btn btn-default map-element" style="' + cellStyle + '"></button></td>';
				}
				mapItem += '</tr>';
			}
			mapItem += '</table>';
			mapItem += '</div>';
			mapItem += '<div class="color"><input type="color" id="colorpicker-' + item + '" onchange="mapEditor.setColor(this.id, this.value)" value="' + shapes[item].color + '"></div>';
			mapItem += '<div class="actions"><button id="save-' + item + '" class="btn btn-xs btn-primary update-map">Zapisz</button>&nbsp;<button id="delete-' + item + '" class="btn btn-xs btn-warning delete-map">Usuń</button></div>';
			mapItem += '</div>';
			mapItems += mapItem;
		}
		var newItem = '';
		newItem += '<div class="item-container">';
		newItem += '<div class="lp">-</div>';
		newItem += '<div class="map">';
		newItem += '<table align="center">';
		for (j = 0; j < size; j++) {
			newItem += '<tr>';
			for (i = 0; i < size; i++) {
				newItem += '<td><button id="map-new-' + i + '-' + j + '" class="btn btn-default map-element"></button></td>';
			}
			newItem += '</tr>';
		}
		newItem += '</table>';
		newItem += '</div>';
		newItem += '<div class="color"><input type="color" id="colorpicker-new" onchange="mapEditor.setColor(this.id, this.value)" value="' + mapEditor.customColor + '"></div>';
		newItem += '<div class="actions"><button id="add-map" class="btn btn-xs btn-success disabled">Dodaj do kolekcji</button></div>';
		newItem += '</div>';
		mapItems += newItem;
		mapItems += '</div>';
		$('div#maps-table').html(mapItems);
		$('button.map-element').bind('click', function() {
			var id = $(this).attr('id');
			var parts = id.split('-');
			var item = parts[1], x = parts[2], y = parts[3];
			if (item == 'new') {
				mapEditor.customMap[x][y] = 1 - mapEditor.customMap[x][y];
				var cellColor = mapEditor.customMap[x][y] ? mapEditor.customColor : '';
				$('button#map-new-' + x + '-' + y).css({ 'background': cellColor });
				var filled = false;
				for (i = 0; i < mapEditor.customMap.length; i++) {
					for (j = 0; j < mapEditor.customMap.length; j++) {
						if (mapEditor.customMap[i][j]) {
							filled = true;
						}
					}
				}
				if (filled) {
					$('button#add-map').removeClass('disabled');
				}
				else {
					$('button#add-map').addClass('disabled');
				}
			}
			else {
				mapEditor.customMaps[item][x][y] = 1 - mapEditor.customMaps[item][x][y];
				var cellColor = mapEditor.customMaps[item][x][y] ? shapes[item].color : '';
				$('button#map-' + item + '-' + x + '-' + y).css({ 'background': cellColor });
				var filled = false;
				for (i = 0; i < mapEditor.customMaps[item].length; i++) {
					for (j = 0; j < mapEditor.customMaps[item].length; j++) {
						if (mapEditor.customMaps[item][i][j]) {
							filled = true;
						}
					}
				}
				if (filled) {
					$('button#save-' + item).attr('disabled', false);
				}
				else {
					$('button#save-' + item).attr('disabled', true);
				}
			}
		});
		$('button#add-map').bind('click', function() {
			var newMap = [];
			for (j = 0; j < mapEditor.customMap.length; j++) {
				var row = [];
				for (i = 0; i < mapEditor.customMap.length; i++) {
					row.push(mapEditor.customMap[i][j]);
				}
				newMap.push(row);
			}
			if (mapEditor.checkUnique(newMap)) {
				shapes.push({ map: newMap, color: mapEditor.customColor });
				mapEditor.loadMaps();
				mapEditor.setMessage('Mapa została pomyślnie dodana do kolekcji.', 'success');
			}
			else {
				mapEditor.setMessage('Taka mapa już istnieje w kolekcji.', 'error');
			}
		});
		$('button.update-map').bind('click', function() {
			var object = $(this).attr('id');
			var parts = object.split('-');
			var id = parts[1];
			if (mapEditor.checkOthers(mapEditor.customMaps[id], id)) {
				for (j = 0; j < shapes[id].map.length; j++) {
					for (i = 0; i < shapes[id].map.length; i++) {
						shapes[id].map[i][j] = mapEditor.customMaps[id][j][i];
					}
				}
				shapes[id].color = mapEditor.customColors[id];
				mapEditor.loadMaps();
				mapEditor.setMessage('Mapa została pomyślnie zapisana.', 'success');
			}
			else {
				mapEditor.setMessage('Taka mapa już istnieje w kolekcji.', 'error');
			}
		});
		$('button.delete-map').bind('click', function() {
			var object = $(this).attr('id');
			var parts = object.split('-');
			var id = parts[1];
			if (shapes.length > 1) {
				shapes.splice(id, 1);
				mapEditor.loadMaps();
				mapEditor.setMessage('Mapa została pomyślnie usunięta z kolekcji.', 'success');
			}
			else {
				mapEditor.setMessage('Kolekcja musi zawierać przynajmniej jedną mapę.', 'error');
			}
		});
	},
	checkUnique: function(map) {
		for (q = 0; q < 4; q++) {
			map = this.rotateQuarter(map);
			for (item = 0; item < shapes.length; item++) {
				var difference = true;
				for (j = 0; j < shapes[item].map.length; j++) {
					for (i = 0; i < shapes[item].map.length; i++) {
						if (map[i][j] != shapes[item].map[i][j]) {
							difference = false;
						}
					}
				}
				if (difference) {
					return false;
				}
			}
		}
		return true;
	},
	checkOthers: function(map, id) {
		for (q = 0; q < 4; q++) {
			map = this.rotateQuarter(map);
			for (item = 0; item < shapes.length; item++) {
				if (item == id) continue;
				var difference = true;
				for (j = 0; j < shapes[item].map.length; j++) {
					for (i = 0; i < shapes[item].map.length; i++) {
						if (map[j][i] != shapes[item].map[i][j]) {
							difference = false;
						}
					}
				}
				if (difference) {
					return false;
				}
			}
		}
		return true;
	},
	rotateQuarter: function(map) {
		var row = [], rotated = [];
		for (i = 0; i < map.length; i++) {
			row = [];
			for (j = 0; j < map.length; j++) {
				row.push(map[i][j]);
			}
			rotated.push(row);
		}
		for (i = 0; i < map.length; i++) {
			for (j = 0; j < map.length; j++) {
				rotated[i][j] = map[j][map.length - i - 1];
			}
		}
		return rotated;
	},
	setColor: function(object, color) {
		var parts = object.split('-');
		var id = parts[1];
		if (id == 'new') {
			this.customColor = color;
		}
		else {
			this.customColors[id] = color;
		}
	},
	setMessage: function(message, type) {
		switch (type) {
			case 'success':
				$('div#alert-info').text(message);
				$('div#alert-info').css({ display: 'block' });
				$('div#alert-danger').css({ display: 'none' });
				break;
			case 'error':
				$('div#alert-danger').text(message);
				$('div#alert-info').css({ display: 'none' });
				$('div#alert-danger').css({ display: 'block' });
				break;
			default:
				$('div#alert-info').css({ display: 'none' });
				$('div#alert-danger').css({ display: 'none' });
				break;
		}
	}
};

$(document).ready(function() {
	var data = localStorage.getItem(localStorageKeyName);
	if (data !== null) {
		var object = JSON.parse(data);
		shapes = [];
		for (i = 0; i < object.maps.length; i++) {
			shapes.push(object.maps[i]);
		}
	}
	mapEditor.loadMaps();
});

if (typeof eventsListenerRegistered == 'undefined') {
	
	document.addEventListener('keydown', function(event) {
		if (event.keyCode == 37) {
			gameFigure.move('left');
		}
		if (event.keyCode == 39) {
			gameFigure.move('right');
		}
		if (event.keyCode == 38) {
			gameFigure.move('up');
		}
		if (event.keyCode == 40) {
			gameFigure.move('down');
		}
		if (event.keyCode == 33) {
			gameFigure.rotate('left');
		}
		if (event.keyCode == 34) {
			gameFigure.rotate('right');
		}
		if (event.keyCode == 32) {
			if (!gameFigure.drop) {
				playTime = stepTime;
				stepTime = 20;
				gameFigure.drop = true;
			}
		}
	});
	
	eventsListenerRegistered = true;
}

$('button#start').on('click', function() {
	gameArea.init();
	gameFigure.init();
});

$('button#rotate-left').on('click', function() {
	gameFigure.rotate('left');
});

$('button#rotate-right').on('click', function() {
	gameFigure.rotate('right');
});

$('button#move-left').on('click', function() {
	gameFigure.move('left');
});

$('button#move-right').on('click', function() {
	gameFigure.move('right');
});

$('button.drop-down').on('click', function() {
	if (!gameFigure.drop) {
		playTime = stepTime;
		stepTime = 20;
		gameFigure.drop = true;
	}
});

$('button#map-editor').on('click', function() {
	$('div#game-play').css({ display: 'none' });
	$('div#game-map').css({ display: 'block' });
	mapEditor.setMessage(null, null);
});

$('button#close-editor').on('click', function() {
	$('div#game-play').css({ display: 'block' });
	$('div#game-map').css({ display: 'none' });
	mapEditor.setMessage(null, null);
});

$('button#save-maps').on('click', function() {
	var customMaps = { 'maps': shapes };
	localStorage.setItem(localStorageKeyName, JSON.stringify(customMaps));
	$('button#close-editor').click();
});

