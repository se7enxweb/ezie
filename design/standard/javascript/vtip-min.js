/**
Vertigo Tip by www.vertigo-project.com
Requires jQuery
*/
this.vtip=function(){this.xOffset=0;this.yOffset=-40;$(".vtip").off().on("mouseenter",function(a){this.t=this.title;this.title="";this.top=(a.pageY+yOffset);this.left=(a.pageX+xOffset);$("body").append('<p id="vtip">'+this.t+"</p>");$("p#vtip").css("top",this.top+"px").css("left",this.left+"px").wait(1000).fadeIn("slow")}).on("mouseleave",function(){this.title=this.t;$("p#vtip").fadeOut("slow").remove()}).on('mousemove', function(a){this.top=(a.pageY+yOffset);this.left=(a.pageX+xOffset);$("p#vtip").css("top",this.top+"px").css("left",this.left+"px")})};jQuery(document).ready(function(a){vtip()});