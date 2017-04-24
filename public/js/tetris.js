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
		color: '#c00'
	},
	{
		map: [[1, 1, 0], [1, 1, 0], [0, 0, 0]],
		color: '#09f'
	},
	{
		map: [[1, 0, 0], [1, 1, 0], [0, 1, 0]],
		color: '#0c0'
	},
	{
		map: [[0, 0, 1], [0, 1, 1], [0, 1, 0]],
		color: '#c0c'
	},
	{
		map: [[0, 1, 0], [0, 1, 0], [0, 1, 1]],
		color: '#fc3'
	},
	{
		map: [[0, 1, 0], [0, 1, 0], [1, 1, 0]],
		color: '#f93'
	},
	{
		map: [[0, 1, 0], [1, 1, 1], [0, 0, 0]],
		color: '#96c'
	},
];
var playGame = false, stepTime, playTime = 500;

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
		var map = [
			[this.shape.map[0][0], this.shape.map[0][1], this.shape.map[0][2]],
			[this.shape.map[1][0], this.shape.map[1][1], this.shape.map[1][2]],
			[this.shape.map[2][0], this.shape.map[2][1], this.shape.map[2][2]],
		];
		if (direction == 'left') {
			gameArea.clearFigure(this);
			this.shape.map = [
				[map[2][0], map[1][0], map[0][0]],
				[map[2][1], map[1][1], map[0][1]],
				[map[2][2], map[1][2], map[0][2]],
			];
			if (gameArea.checkCollision(this)) this.shape.map = map;
			gameArea.paintFigure(this);
		}
		if (direction == 'right') {
			gameArea.clearFigure(this);
			this.shape.map = [
				[map[0][2], map[1][2], map[2][2]],
				[map[0][1], map[1][1], map[2][1]],
				[map[0][0], map[1][0], map[2][0]],
			];
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

