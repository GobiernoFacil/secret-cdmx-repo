// CDMX - STATISTICS
// @package  : cdmx
// @location : /js/apps/home
// @file     : controller.js
// @author   : Gobierno fácil <howdy@gobiernofacil.com>
// @url      : http://gobiernofacil.com

define(function(require){

  //
  // L O A D   T H E   A S S E T S   A N D   L I B R A R I E S
  // --------------------------------------------------------------------------------
  //
  var Backbone 	 	 = require('backbone'),
      d3        	 = require("d3"),
      ScrollMagic    = require("ScrollMagic"),
      TweenLite      = require("TweenLite"),
      SplitText      = require("splitText"),
      TweenMax       = require("TweenMax"),
      xxx            = require("ScrollMagic.animation.gsap"),
      ScrollToPlugin = require("ScrollToPlugin"),
      TimelineMax    = require("TimelineMax");    

  //
  // C A C H E   T H E   C O M M O N   E L E M E N T S
  // --------------------------------------------------------------------------------
  //
    

    
  //
  // I N I T I A L I Z E   T H E   B A C K B O N E   " C O N T R O L L E R "
  // --------------------------------------------------------------------------------
  //
  var controller = Backbone.View.extend({
    
    //
    // [ DEFINE THE EVENTS ]
    //
    events :{
	    // STANDAR
		"click .stages a"     : "show_step",
    },

    //
    // [ DEFINE THE TEMPLATES ]
    //

    // 
    // [ SET THE CONTAINER ]
    //
    //
    el : 'body',

    //
    // [ THE INITIALIZE FUNCTION ]
    //
    //
    initialize : function(){
    },
  



   
    //
    // L O C A L   T R A N S I T I O N S
    // --------------------------------------------------------------------------------
    //
	show_step : function(e){
    	e.preventDefault();
    	var   div 	= $( e.currentTarget ).attr("data-step");    	  		
		$(".pasos .slide").addClass("hide");
		$(".slide." + div).removeClass("hide");   
		$("a.nav_stage").removeClass("current");
		$(e.currentTarget).addClass("current");
		this.animate_step(div);
    },

    animate_step : function(div){
      var m         = new TimelineMax();
      m.add(TweenMax.from(".slide."+div, .6, {opacity : 0}));
      m.staggerFrom(".slide."+ div +" .description", .5, {opacity:0, scale:0}, .1, "+=0");
    },

  });

  //
  // R E T U R N   T H E   B A C K B O N E   " C O N T R O L L E R "
  // --------------------------------------------------------------------------------
  //
  return controller;
});