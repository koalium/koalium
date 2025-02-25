/*!
 * Copyright 2013 Justinmind. All rights reserved.
 */

(function(window, undefined) {
	
	var margin = 5;
	
	//MOBILE VARIABLES
	var mobileCase = $("#jim-case");
	var jimContainer = $("#jim-container");
	var jimMobileWrapper = $("#jim-mobile");
	var sim = $("#simulation");
	var toppanel = $("#toppanel"); 
	var body = $("#jim-body");
	
	var value;
	
	function buildScenarioGraph() {
	  var wrapper = $("#scenarioWrapper");
	  var nodes = $(".ui-scenario .device");
	  var graph = {};
	  
	  graph['width'] = wrapper.outerWidth();
	  graph['height'] = wrapper.outerHeight();
	  
	  nodes.each(function () {
		var edges = [];
		if ($(this).attr("links") != "") edges = $(this).attr("links").split(",");
		var node = {screenId : $(this).attr("value"), left: parseInt($(this).css("left")), top: parseInt($(this).css("top")), width: parseInt($(this).css("width")), links : edges};	
		  
	    graph[$(this).attr("id")] = node;
	  });	
	  
	  return graph;
	}
	
    var jimScenarios = {
	  "bindScreenEvents" : function() {
	    $(".ui-scenario .device[value]").on('click', function (event) {
	      if (!jimComments ||  !jimComments.commentsMode) {
	        value = $(this).attr("value");
	        jimScenarios.currentNode = $(this).attr("id");
		    $(".ui-scenario .scenarioShadow").fadeIn();
		    $(".ui-scenario .filterDialog").fadeIn();
		    $("#simulation").trigger("scroll");
		  }
		});
	    
	    $(".scenarioShadow, #filterCloseButton, #openPageButton.scenarioButton").on('click', function (event) {
	      $(".scenarioShadow,.filterDialog").fadeOut();
	    });
	    
	    $(".ui-scenario #startScenarioButton.scenarioButton").on('click', function (event) { 
	      $("#scenarioThumbnail #scenarioWrapper").remove();
	      if ($(this).attr("id") == "startScenarioButton") {
	        jimScenarios.activeScenario = buildScenarioGraph();
			//Copy scenario HTML
			var thumbnail = $("#scenarioThumbnail");
			thumbnail.append($("#scenarioWrapper").clone());
			thumbnail.append($(".ui-page.ui-scenario > link").clone());
			$("#scenarioThumbnail > #scenarioWrapper").addClass($(".ui-page.ui-scenario").attr('id'))
			
	        $("#infoContent .filterText").css({"display": "inline-block"});
	        $("#infoContent #scenarioName").text($(".ui-scenario").attr("name"));
	        if ($("#sidepanel").hasClass("open")) $("#sidepanel").trigger("closePane");
	      }
	      else jimScenarios.currentNode = -1;
	      $("#screenTab").trigger("click");
	      jimMain.navigate("screens/" + value, {"transition": "none"}, 1);
	    });
	    
		var scrollHandler = $("#simulation").bind('scroll', function () {
	      var dialog = $(".filterDialog");
	      if (dialog.length > 0) {	    	  
	        var scrollY = $(this).scrollTop();
	        var scrollX = $(this).scrollLeft();
	      
	        var pos = dialog[0].getBoundingClientRect();
	        scrollY = scrollY - pos.height/2;
	        scrollX = scrollX - pos.width/2;
	        dialog.css({"transform": "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-webkit-transform" : "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-o-transform"      : "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-ms-transform"	   : "translate(" + scrollX + "px," + scrollY + "px)"});
	      }
	      else {
	      	$(this).unbind('scroll', scrollHandler);  
	      }
	    });
	  },
	  "isValidLink" : function (target) {
		//scenarios case, always can navigate to scenario
		var tType = target.substring(0,target.lastIndexOf("/") - 1);
		if(tType == "scenario") return true;
		//other cases
    	var links = jimScenarios.activeScenario[jimScenarios.currentNode].links;
    	var t = target.substring(target.lastIndexOf("/") + 1);
    	var valid = false;
    	  
    	for (var i = 0; i < links.length; ++i) {
    	  var sId = jimScenarios.activeScenario[links[i]].screenId;
    	  if (sId == t) {
      	    jimScenarios.currentNode = links[i];
    	    valid = true;
    	    break;
    	  }
    	}
    	return valid;
	  },
	  "deleteFilter" : function () {
		jimScenarios.currentNode = -1;
        $("#infoContent .filterText").css({"display": ""}); 
        $("#scenarioThumbnail #scenarioWrapper").remove();
	  },
	  "activateThumbnail" : function () {
		var namePos = $("#scenarioName")[0].getBoundingClientRect();
		var scenario = jimScenarios.activeScenario;
		var thumbnail = $("#scenarioThumbnail");
		thumbnail.stop(true, true);
		var tWidth = thumbnail.outerWidth();
		var tHeight = thumbnail.outerHeight();
		
		//Apply scaling if the scenario is too big
		var width = scenario.width;
		var height = scenario.height;
		var scale = (tWidth -  35)/ width;

		var left = 35;
		var top = 0;
		if (height * scale > (tHeight - 70)) {
		  scale = (tHeight - 70) / height;
		  left = 0;
		  top = 70;
		}

		left = ((tWidth + left) - (width * scale))/2;
		top = (((tHeight + top) - (height * scale))/2);
		$("#scenarioWrapper").css("transform"," translate(-50%,-50%) scale("+ scale +")");
		
		var triangle = $("#thumbnailTriangle");
		triangle.stop(true, true);
		triangle.css({"left": (namePos.left + namePos.width/2) - 25});
		
		//Obtain img initial position and scale node pos 
		var pos = $("#thumbnailPos");
		var node = jimScenarios.activeScenario[jimScenarios.currentNode];
		pos.css({"left": (tWidth/2 - (width * scale)/2) + (node.left + node.width/2)*scale - 7, "top": node.top * scale + (tHeight/2 - (height * scale)/2) - 16});
		
		triangle.fadeIn();
		thumbnail.fadeIn();
	  },
	  "closeThumbnail" : function () {
		$("#scenarioThumbnail").fadeOut();
		$("#thumbnailTriangle").fadeOut();
	  },
	  "initializeScenarios" : function () {
		var separator = $(".rightcontrols .comments-separator");
		var highlight = $(".highlight");
		var commentscontrol = $("#commentscontrol");
		
		if ($(".ui-scenario").length > 0) {
		  highlight.css("opacity", 0);
		  $('#comments-separator1').css("opacity", '0');
		  $('#highlight-select').css('cursor', 'default');
		}
    	else  if (!commentscontrol.hasClass("active")) {
    	  $('#comments-separator1').css("opacity", '');
      	  highlight.css("opacity", '');
      	  $('#highlight-select').css('cursor', '');
    	}
	  },
	  "updateAllShapesBackgrounds" : function() {
		$("#scenarioWrapper .scenarioShape").each(function(i, shape) {
			jimScenarios.updateShapeBackground($(shape));
		});
	  },
	  "updateShapeBackground" : function(shape) {	
		var colorItem = shape.find(".backgroundLayer .colorLayer");
		var imageItem = shape.find(".backgroundLayer .imageLayer");
		var id = shape.attr("id");
		var pathItem = shape.find("path#path-" + id);
		var backgroundColor= colorItem.css('background-color');
		var width = parseFloat(shape.css("width"));
		var height = parseFloat(shape.css("height"));
		
		pathItem.css("fill", backgroundColor);
		
		var defsContent;
		var backgroundImg = imageItem.css('background-image');
		var backgroundImgOpacity= imageItem.css('opacity');
		var backgroundGradient = colorItem.css('background-image');

		//delete current defs
		shape.find("defs").children("pattern").remove();
		shape.find("defs").children("linearGradient").remove();

		//create random id to avoid repeated patterns (ej:datagrids)
		var random4Id = Math.round(Math.random() * 10000);

		if(backgroundColor !== 'transparent' && backgroundImg && backgroundImg.match('url')!=null){
			var patternContainer = document.createElementNS("http://www.w3.org/2000/svg", "pattern");
			patternContainer.setAttribute("id","svg-fill-pattern-" + id + random4Id);
			patternContainer.setAttribute("patternUnits", "objectBoundingBox");
			patternContainer.setAttribute("height", 1);
			patternContainer.setAttribute("width", 1);
		}
		
		//background image
		if (backgroundImg && backgroundImg.match('url') != null){
			var repeat = imageItem.css('background-repeat');
			var bgSize = imageItem.css('background-size');
			var bgPosition = imageItem.css('background-position');
			var shapeSize = {
				width : width,
				height : height
			};
			defsContent = jimShapes._getImagePattern(backgroundImg, repeat, bgPosition, bgSize, shapeSize);
			defsContent.setAttribute("id", "svg-fill-image-"+ id + random4Id);
			
			if(patternContainer){
				patternContainer.appendChild(defsContent);
			}
		}
		
		//gradient
		if(backgroundGradient && backgroundGradient.match('gradient') != null){
			if (backgroundGradient.match("linear-gradient") || backgroundGradient.match("radial-gradient")){
				defsContent = jimScenarios.getStandardGradient(backgroundGradient, width, height);
				defsContent.setAttribute("id", "svg-fill-gradient-"+ id + random4Id);
			 }
			 if(patternContainer){
				 patternContainer.appendChild(defsContent);
			 }
		}

		var $defsObj =  shape.find("defs");
		if(patternContainer){
			$defsObj.append(patternContainer);
			var shapeColorOrGradient = pathItem.clone();
			shapeColorOrGradient.removeAttr("id");
			shapeColorOrGradient.removeAttr("class");
			if(shapeColorOrGradient.attr("style")!=null)
				shapeColorOrGradient.removeAttr("style");

			var shapeImage = pathItem.clone();
			shapeImage.removeAttr("id");
			shapeImage.removeAttr("class");
			if(shapeImage.attr("style")!=null)
				shapeImage.removeAttr("style");

			if(backgroundColor!=='transparent' && backgroundGradient && backgroundGradient.match('gradient')==null){
				jQuery(shapeColorOrGradient).css("fill", backgroundColor);
			}else{
				jQuery(shapeColorOrGradient).css("fill", "url(#svg-fill-gradient-" + id + random4Id + ")");
			}
			jQuery(shapeImage).css("fill", "url(#svg-fill-image-" + id + random4Id + ")");
			jQuery(shapeImage).css("fill-opacity",backgroundImgOpacity);

			if(backgroundColor==='transparent' || backgroundColor=='undefined'){
				jQuery(shapeColorOrGradient).css("fill-opacity","0");
			}
			else{
				jQuery(shapeColorOrGradient).css("fill-opacity","1");
			}

			$(patternContainer).append(shapeColorOrGradient);
			$(patternContainer).append(shapeImage);

			pathItem.css("fill", "url(#" + patternContainer.getAttribute("id") + ")");
		} else if (defsContent != null) {
			defsContent.setAttribute("id", "svg-fill-" + id + random4Id);
			//delete current defs
			shape.find("defs").children("pattern").remove();
			shape.find("defs").children("linearGradient").remove();
			$defsObj.append(defsContent);
			pathItem.css("fill", "url(#" + defsContent.getAttribute("id") + ")");
		}
      },
	  "getStandardGradient" : function(background, width, height) {
		var sParams = background.substring(background.indexOf('(', 0) + 1, background.length - 1), params = sParams.split(',');
		var colorMatches = background.match(/rgba{0,1}\(([0-9]+, [0-9]+, [0-9]+(?:, ([0-9\.]+)){0,1})\) (-?[0-9\.]+)%,{0,1}/mg);
		var NS = "http://www.w3.org/2000/svg";
		
		if (background.indexOf("linear-gradient") >= 0) {
			var degreesMatches = background.match(/([0-9\.]+)deg/mg);
			var degrees = degreesMatches==null ? 180 : parseFloat(degreesMatches[0]);
			var radians = degrees * Math.PI / 180.0;
			var linearGradientObj = document.createElementNS(NS, "linearGradient");
			
			linearGradientObj.setAttribute("gradientUnits" , "userSpaceOnUse");
			
			// START AND END
			var center = { x : width / 2, y : height / 2};
			var gradientLength = Math.abs(width * Math.sin(radians)) + Math.abs(height * Math.cos(radians));
			var start = { x : center.x, y : center.y + gradientLength / 2};
			var rotatedStart = {};
			rotatedStart.x = center.x + (start.x - center.x) * Math.cos(radians) - (start.y - center.y) * Math.sin(radians);
			rotatedStart.y = center.y + (start.x - center.x) * Math.sin(radians) + (start.y - center.y) * Math.cos(radians);				
			
			var end = { x : center.x, y : center.y - gradientLength / 2};
			var rotatedEnd = {};
			rotatedEnd.x = center.x + (end.x - center.x) * Math.cos(radians) - (end.y - center.y) * Math.sin(radians);
			rotatedEnd.y = center.y + (end.x - center.x) * Math.sin(radians) + (end.y - center.y) * Math.cos(radians);
			
			linearGradientObj.setAttribute("x1", rotatedStart.x);
			linearGradientObj.setAttribute("x2", rotatedEnd.x);
			linearGradientObj.setAttribute("y1", rotatedStart.y);
			linearGradientObj.setAttribute("y2", rotatedEnd.y);
			
			// COLORS
			for (var i = 0; i < colorMatches.length; ++i) {
				var current = colorMatches[i];									
				var stopObj = document.createElementNS(NS, "stop");					
				var offset = parseFloat(current.match(/-?[0-9\.]+%/mg)[0]);
								
				stopObj.setAttribute("offset", offset + "%");
				stopObj.setAttribute("stop-color", current.match(/rgba{0,1}\([0-9]+, [0-9]+, [0-9]+(?:, ([0-9\.]+)){0,1}\)/mg)[0]);
				linearGradientObj.appendChild(stopObj);
			}
							
			return linearGradientObj;
		} else if (background.indexOf("radial-gradient") >= 0) {
			var circleMatches = background.match(/-?[0-9\.]+% -?[0-9\.]+% at -?[0-9\.]+% -?[0-9\.]+%/mg)[0].match(/(-?[0-9\.]+)%/mg);
			var radialGradientObj = document.createElementNS(NS, "radialGradient");
			
			// CENTER AND RADIUS
			radialGradientObj.setAttribute("cx", parseFloat(circleMatches[2]) + "%");
			radialGradientObj.setAttribute("cy", parseFloat(circleMatches[3]) + "%");
			
			var minRadius = Math.min(parseFloat(circleMatches[0]), parseFloat(circleMatches[1]));
			radialGradientObj.setAttribute("r", (minRadius + "%"));
			
			// COLORS
			var stops = [];
			for (var i = 0; i < colorMatches.length; ++i) {
				var current = colorMatches[i];
									
				var stopObj = document.createElementNS(NS, "stop");
				stopObj.setAttribute("offset", current.match(/[0-9\.]+%/mg)[0]);
				stopObj.setAttribute("stop-color", current.match(/rgba{0,1}\([0-9]+, [0-9]+, [0-9]+(?:, ([0-9\.]+)){0,1}\)/mg)[0]);
				radialGradientObj.appendChild(stopObj);
			}
			
			return radialGradientObj;
		}

		var gradientObj = document.createElementNS(NS, "linearGradient");
		return gradientObj;
	  },
	  "activeScenario" : {},
	  "currentNode" : -1
    };

	window.jimScenarios = jimScenarios;
})(window);
