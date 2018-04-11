angular.module('uxDirectives', ['config'])

.directive('dynamicElementHeightComponent', [ '$rootScope', function( $rootScope ) {
	return {
		restrict: 'EA',
    scope: {},
		link: function(scope, element, attributes) {
      angular.element(document).ready( function () {

        function changeSidebarHeigthDynamicly() {
              element.height(Math.max($("body").height(), $("aside").height()));
        }

        function ifBigScreenAdaptedSidebarHeightElseSetToAuto () {
          if ( window.innerWidth > 800 ) {
          	changeSidebarHeigthDynamicly();
          }
          else {
            element.css('height','auto');
          }
        }

				// on init
        setTimeout( function () {
          ifBigScreenAdaptedSidebarHeightElseSetToAuto();
        }, 200 );

				// if window become resized reset height of sidebar
        var lastWidth = $(window).width();

      	$(window).resize(function() {
      		if ( window.innerWidth != lastWidth) {
      				lastWidth = window.innerWidth;
              setTimeout( function () {
                ifBigScreenAdaptedSidebarHeightElseSetToAuto();
              }, 200 );
      		}
      	})


        $rootScope.$on('$locationChangeStart', function (event, next, current) {
          setTimeout( function () {
            ifBigScreenAdaptedSidebarHeightElseSetToAuto();
          }, 300 );
      	});

      })
		}
	}
}])
