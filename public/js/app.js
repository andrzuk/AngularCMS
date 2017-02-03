/*
 *	Gallery Dynamic Browser v1.0
 *	Copyright (C) 2017 Andrzej Żukowski
 *	http://angular-cms.pl
 */

$(document).ready(function() {
});

var itemIndex = 0;
var imageMode = 0;
var picturesInterval = null;
var pictureReady = true;

var smoothImageLoad = function() {
	$('img.bordered')
	.css({opacity: '0.0'})
	.on('load', function() { 
		$(this).fadeIn(function() {
			$(this).css({opacity: '1.0'});
		}); 
	});
};

var smoothPictureLoad = function() {
	$('img.bordered')
	.hide()
	.on('load', function() { 
		$(this).fadeIn(); 
	});
};

var dynamicPictureLoad = function() {
	$('div.img-shape').css({ border: "1px solid #ccc", "background-image": "url('public/img/loader.gif')", "background-repeat": "no-repeat", "background-position": "center", "background-color": "#eee" });
	$('img.bordered')
	.hide()
	.on('load', function() { 
		$(this).fadeIn(function() {
			$(this).parent().css({ border: "1px solid #fff", "background-image": "none", "background-color": "#fff" });
		}); 
	});
};

var blinkPictures = function(count, period, effects) {
	var idx = 0;
	var direction = false;
	var picCount = 0;
	var directionCount = 0;
	var runMode = false;
	$('img.bordered').hide();
	$('img.bordered').on('load', function() {
		$(this).show();
		picCount++;
		if (picCount == count) {
			startPictures();
			runMode = true;
		}
	});
	var startPictures = function() {
		picturesInterval = setInterval(function() {
			if (idx % count == 0) {
				direction = !direction;
				directionCount++;
			}
			if (directionCount == (effects ? 2 : 5)) {
				clearInterval(picturesInterval);
				if (effects) {
					directionCount = 0;
					idx = count - 1;
					movePictures();
				}
				else {
					$('img.bordered').show();
					runMode = false;
				}
				return;
			}
			if (direction) {
				$('img#img-' + (idx % count + 1)).hide();
			}
			else {
				$('img#img-' + (idx % count + 1)).show();
			}
			idx++;
		}, period);
	};
	var movePictures = function() {
		picturesInterval = setInterval(function() {
			$('img#img-' + (idx % count + 1)).hide();
			idx++;
			if (idx % count == 0) {
				directionCount++;
			}
			if (directionCount == 2) {
				clearInterval(picturesInterval);
				directionCount = 0;
				tailPictures();
				return;
			}
			$('img#img-' + (idx % count + 1)).show();
		}, period);
	};
	var tailPictures = function() {
		picturesInterval = setInterval(function() {
			$('img#img-' + (idx % count + 1)).show();
			idx++;
			if (idx % count == 0) {
				directionCount++;
			}
			if (directionCount == 1) {
				clearInterval(picturesInterval);
				directionCount = 0;
				runMode = false;
				return;
			}
		}, period);
	};
	$('img.bordered').on('click', function() {
		if (!runMode) {
			idx = 0;
			directionCount = 0;
			direction = false;
			startPictures();
			runMode = true;
		}
	});
	$('img.bordered').hover(function() {
		if (runMode) {
			$(this).css({ cursor: 'default' });
		}
		else {
			$(this).css({ cursor: 'pointer' });
		}
	});
};

var previewPictures = function(itemWidth, itemHeight, previewWidth, previewHeight, distance) {
	if (picturesInterval) {
		clearInterval(picturesInterval);
	}
	$('img.bordered').addClass('img-responsive');
	$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
	$('img.bordered').hover(function() {
		$(this).css({ cursor: 'pointer' });
	});
	$('div.picture-set').css({ margin: 0, padding: 0, 'text-align': 'center' });
	$('div.picture-item').css({ display: 'inline-block', 'vertical-align': 'middle', margin: 0, padding: 0, width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
	$('img.bordered').on('click', function() {
		var selectedImg = this;
		if (imageMode == 0) {
			$(this).fadeOut(function() {
				$(this).fadeIn(function() {
					$('img.bordered').fadeOut(function() {
						$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
						$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
						$(selectedImg).fadeIn(function() {
							imageMode = 1;
						});
					});
				});
			});
		}
		else {
			$(selectedImg).fadeOut(function() {
				$('div.picture-item').css({ display: 'inline-block', width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
				$(selectedImg).css({ width: itemWidth + 'px', height: itemHeight + 'px' });
				$('img.bordered').fadeIn(function() {
					imageMode = 0;
				});
			});
		}
	});	
};

var browsePictures = function(itemsCount, itemWidth, itemHeight, previewWidth, previewHeight, distance) {
	if (picturesInterval) {
		clearInterval(picturesInterval);
	}
	$('img.bordered').addClass('img-responsive');
	$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
	$('img.bordered').hover(function() {
		$(this).css({ cursor: 'pointer' });
	});
	$('div.picture-set').css({ margin: 0, padding: 0, 'text-align': 'center' });
	$('div.picture-item').css({ display: 'inline-block', 'vertical-align': 'middle', margin: 0, padding: 0, width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
	$('img.bordered').on('click', function() {
		var selectedImg = this;
		if (imageMode == 0) {
			$(this).fadeOut(function() {
				$(this).fadeIn(function() {
					$('img.bordered').fadeOut(function() {
						$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
						$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
						$(selectedImg).fadeIn(function() {
							imageMode = 1;
						});
					});
				});
			});
		}
		else {
			$(this).fadeOut(function() {
				if (itemIndex < itemsCount) {
					itemIndex++;
					selectedImg = $('img#img-' + itemIndex);
					$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
					$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
					$(selectedImg).fadeIn();
				}
				else {
					$(selectedImg).fadeOut(function() {
						$('div.picture-item').css({ display: 'inline-block', width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
						$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
						$('img.bordered').fadeIn(function() {
							imageMode = 0;
							itemIndex = 0;
						});
					});
				}
			});
		}
	});
};

var btnPrevClick = function(itemsCount, previewWidth, previewHeight) {
	if (!pictureReady) return;
	pictureReady = false;
	if (itemIndex > 1) {
		$('img#img-' + itemIndex).fadeOut(function() {
			itemIndex--;
			var selectedImg = $('img#img-' + itemIndex);
			$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
			$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
			$(selectedImg).fadeIn(function() {
				$('span#item-info').text(itemIndex + ' z ' + itemsCount);
				pictureReady = true;
			});
		});
	}
	else {
		$('span#item-info').text('Początek.');
		pictureReady = true;
	}
};

var btnNextClick = function(itemsCount, previewWidth, previewHeight) {
	if (!pictureReady) return;
	pictureReady = false;
	if (itemIndex < itemsCount) {
		$('img#img-' + itemIndex).fadeOut(function() {
			itemIndex++;
			var selectedImg = $('img#img-' + itemIndex);
			$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
			$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
			$(selectedImg).fadeIn(function() {
				$('span#item-info').text(itemIndex + ' z ' + itemsCount);
				pictureReady = true;
			});
		});
	}
	else {
		$('span#item-info').text('Koniec.');
		pictureReady = true;
	}
};

var btnCloseClick = function(itemWidth, itemHeight, distance) {
	if (!pictureReady) return;
	pictureReady = false;
	$('div.button-close').css({ display: 'none' });
	$('div.buttons-change').css({ display: 'none' });
	$('img#img-' + itemIndex).fadeOut(function() {
		$('div.picture-item').css({ display: 'inline-block', width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
		$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
		$('img.bordered').fadeIn(function() {
			imageMode = 0;
			itemIndex = 0;
			pictureReady = true;
		});
	});
};

var browsePicturesWithButtons = function(itemsCount, itemWidth, itemHeight, previewWidth, previewHeight, distance) {
	if (picturesInterval) {
		clearInterval(picturesInterval);
	}
	$('img.bordered').addClass('img-responsive');
	$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
	$('img.bordered').hover(function() {
		$(this).css({ cursor: 'pointer' });
	});
	$('div.picture-set').css({ margin: 0, padding: 0, 'text-align': 'center' });
	$('div.picture-item').css({ display: 'inline-block', 'vertical-align': 'middle', margin: 0, padding: 0, width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
	$('div.button-close').css({ 'text-align': 'right', height: 0, display: 'none' });
	$('div.buttons-change').css({ 'text-align': 'center', padding: '10px', display: 'none' });
	var $btnPrev = $('<input type="button" id="btn-prev" class="btn btn-success" value="&lt; Prev">');
	var $btnNext = $('<input type="button" id="btn-next" class="btn btn-success" value="Next &gt;">');
	var $btnClose = $('<input type="button" id="btn-close" class="btn btn-danger" value="X">');
	var $spanInfo = $('<span id="item-info"></span>');
	$btnPrev.appendTo($('div.buttons-change'));
	$spanInfo.appendTo($('div.buttons-change'));
	$btnNext.appendTo($('div.buttons-change'));
	$btnClose.appendTo($('div.button-close'));
	$('input#btn-prev').css({ margin: '0 10px' });
	$('span#item-info').css({ margin: '0 5px', 'text-align': 'center', display: 'inline-block', width: '80px', 'font-weight': 'bold', color: '#369' });
	$('input#btn-next').css({ margin: '0 10px' });
	$('input#btn-prev').on('click', function() {
		$('span#item-info').html('<img src="public/img/preload.gif">');
		btnPrevClick(itemsCount, previewWidth, previewHeight);
	});
	$('input#btn-next').on('click', function() {
		$('span#item-info').html('<img src="public/img/preload.gif">');
		btnNextClick(itemsCount, previewWidth, previewHeight);
	});
	$('input#btn-close').on('click', function() {
		btnCloseClick(itemWidth, itemHeight, distance);
	});
	$('img.bordered').on('click', function() {
		if (!pictureReady) return;
		pictureReady = false;
		var selectedImg = this;
		var selectedId = $(this).attr('id');
		itemIndex = selectedId.substring(4);
		$('span#item-info').html('<img src="public/img/preload.gif">');
		if (imageMode == 0) {
			$(this).fadeOut(function() {
				$(this).fadeIn(function() {
					$('img.bordered').fadeOut(function() {
						$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
						$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
						$(selectedImg).fadeIn(function() {
							$('div.button-close').css({ display: 'block' });
							$('div.buttons-change').css({ display: 'block' });
							$('span#item-info').text(itemIndex + ' z ' + itemsCount);
							imageMode = 1;
							pictureReady = true;
						});
					});
				});
			});
		}
		else {
			$(this).fadeOut(function() {
				if (itemIndex < itemsCount) {
					itemIndex++;
					selectedImg = $('img#img-' + itemIndex);
					$('div.picture-item').css({ display: 'inline', width: '0px', height: '0px' });
					$(selectedImg).css({ width: previewWidth + 'px', height: previewHeight + 'px' });
					$(selectedImg).fadeIn(function() {
						$('div.button-close').css({ display: 'block' });
						$('div.buttons-change').css({ display: 'block' });
						$('span#item-info').text(itemIndex + ' z ' + itemsCount);
						pictureReady = true;
					});
				}
				else {
					$('div.button-close').css({ display: 'none' });
					$('div.buttons-change').css({ display: 'none' });
					$(selectedImg).fadeOut(function() {
						$('div.picture-item').css({ display: 'inline-block', width: itemWidth + distance + 'px', height: itemHeight + distance + 'px' });
						$('img.bordered').css({ width: itemWidth + 'px', height: itemHeight + 'px', 'max-width': '100%' });
						$('img.bordered').fadeIn(function() {
							imageMode = 0;
							itemIndex = 0;
							pictureReady = true;
						});
					});
				}
			});
		}
	});
};

/*
 *  Slider v1.0
 *  Copyright (C) 2017 Andrzej Żukowski
 *  http://angular-cms.pl
 */

var slowSlider = {
	
	galleryName: null,
	imageClass: null,
	slideTime: 0,
	fadeTime: 0,
	loopIndex: 0,
	sliderItems: 0,
	itemIndex: 1,
	runMode: false,
	
	init: function(galleryName, imageClass, slideTime, fadeTime) {
		
		this.galleryName = galleryName;
		this.imageClass = imageClass;
		this.slideTime = slideTime;
		this.fadeTime = fadeTime;
		
		this.sliderItems = $('div#' + this.galleryName).children().size();
	},
	
	showSlide: function() {
		
		slowSlider.runMode = true;
		$('img.' + slowSlider.imageClass + '#image-' + slowSlider.itemIndex).fadeOut(slowSlider.fadeTime, function() {
			$('img.' + slowSlider.imageClass).css({ display: 'none' });
			slowSlider.itemIndex = slowSlider.itemIndex == slowSlider.sliderItems ? 1 : slowSlider.itemIndex + 1;
			$('img.' + slowSlider.imageClass + '#image-' + slowSlider.itemIndex).fadeIn(slowSlider.fadeTime, function() {
				if (slowSlider.loopIndex < slowSlider.sliderItems) {
					setTimeout(function() {
						slowSlider.showSlide();
					}, slowSlider.slideTime);
					slowSlider.loopIndex++;
				}
				else {
					slowSlider.loopIndex = 0;
					slowSlider.runMode = false;
				}
			});
		});
	},
	
	show: function() {
		
		var loadedItems = 0;
		$('img.' + slowSlider.imageClass).on('load', function() {
			loadedItems++;
			if (loadedItems == slowSlider.sliderItems) {
				slowSlider.showSlide();
			}
		});
		$('img.' + slowSlider.imageClass).on('click', function() {
			if (!slowSlider.runMode) {
				slowSlider.showSlide();
			}
		});
		$('img.' + slowSlider.imageClass).hover(function() {
			if (slowSlider.runMode) {
				$(this).css({ cursor: 'default' });
				$(this).attr('title', 'Proszę czekać...');
			}
			else {
				$(this).css({ cursor: 'pointer' });
				$(this).attr('title', 'Kliknij, aby uruchomić ponownie.');
			}
		});
	}
};