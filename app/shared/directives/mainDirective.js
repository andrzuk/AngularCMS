angular.module('mainDirective', ['config'])

.directive('bootstrapSwitch', function() {
	return {
		restrict: 'A',
		require: '?ngModel',
		link: function(scope, element, attributes, ngModel) {
			element.bootstrapSwitch();
			element.on('switchChange.bootstrapSwitch', function(event, state) {
				if (ngModel) {
					scope.$apply(function() {
						ngModel.$setViewValue(state);
					});
				}
			});
			scope.$watch(attributes.ngModel, function(newValue, oldValue) {
				if (newValue) {
					element.bootstrapSwitch('state', true, true);
				} 
				else {
					element.bootstrapSwitch('state', false, true);
				}
			});
		}
	};
})

.directive('fileModel', ['$parse', function ($parse) {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			var model = $parse(attributes.fileModel);
			var modelSetter = model.assign;
			element.bind('change', function() {
				scope.$apply(function() {
					modelSetter(scope, element[0].files[0]);
				});
			});
		}
	};
}])

.directive('imageType', ['$http', 'config', function ($http, config) {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			if (attributes.imageId && attributes.imageType) {
				$http.get(config.apiUrl + 'get_image.php?id=' + attributes.imageId + '&type=' + attributes.imageType).then(function(response) {
					attributes.$set('src', response.data.image_data);
				});
			}
		}
	};
}])

.directive('autoCategoryId', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			scope.$watch(attributes.ngModel, function(value) {
				var linkTemplate = value ? '/category/{id}' : null;
				if (scope.categoryNew) {
					scope.categoryNew.item_link = linkTemplate;
				}
			});
		}
	};
})

.directive('systemPageDisable', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			scope.$watch(attributes.ngModel, function(value) {
				if (value > 0) {
					if (scope.pageNew) {
						scope.pageNew.main_page = false;
						scope.pageNew.contact_page = false;
					}
					if (scope.pageEdit) {
						scope.pageEdit.main_page = false;
						scope.pageEdit.contact_page = false;
					}
				}
			});
		}
	};
})

.directive('categoryDisableMain', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			scope.$watch(attributes.ngModel, function(value) {
				if (scope.pageNew) {
					if (scope.pageNew.main_page) {
						scope.pageNew.contact_page = false;
						scope.pageNew.category_id = 0;
					}
				}
				if (scope.pageEdit) {
					if (scope.pageEdit.main_page) {
						scope.pageEdit.contact_page = false;
						scope.pageEdit.category_id = 0;
					}
				}
			});
		}
	};
})

.directive('categoryDisableContact', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			scope.$watch(attributes.ngModel, function(value) {
				if (scope.pageNew) {
					if (scope.pageNew.contact_page) {
						scope.pageNew.main_page = false;
						scope.pageNew.category_id = 0;
					}
				}
				if (scope.pageEdit) {
					if (scope.pageEdit.contact_page) {
						scope.pageEdit.main_page = false;
						scope.pageEdit.category_id = 0;
					}
				}
			});
		}
	};
})

.directive('myText', function() {
	return {
		link: function(scope, element, attributes) {
			scope.$on('add', function(e, val) {
				var domElement = element[0];
				if (document.selection) {
					domElement.focus();
					var sel = document.selection.createRange();
					sel.text = val;
					domElement.focus();
				} 
				else if (domElement.selectionStart || domElement.selectionStart === 0) {
					var startPos = domElement.selectionStart;
					var endPos = domElement.selectionEnd;
					var scrollTop = domElement.scrollTop;
					domElement.value = domElement.value.substring(0, startPos) + val + domElement.value.substring(endPos, domElement.value.length);
					domElement.focus();
					domElement.selectionStart = startPos + val.length;
					domElement.selectionEnd = startPos + val.length;
					domElement.scrollTop = scrollTop;
				} 
				else {
					domElement.value += val;
					domElement.focus();
				}
			});
		}
	};
})

.directive('onCarouselChange', function ($parse) {
	return {
		require: 'carousel',
		link: function (scope, element, attributes, carouselCtrl) {
			var fn = $parse(attributes.onCarouselChange);
			var origSelect = carouselCtrl.select;
			carouselCtrl.select = function (nextSlide, direction) {
				if (nextSlide !== this.currentSlide) {
					fn(scope, {
						nextSlide: nextSlide,
						direction: direction,
					});
				}
				return origSelect.apply(this, arguments);
			};
		}
	};
})

.directive('autoFocus', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			$(element).focus();
		}
	};
})

.directive('smoothShow', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attributes) {
			attributes.$set('style', 'display: none');
			$(element).fadeOut(function() {
				$(element).fadeIn();
			});
		}
	};
});
