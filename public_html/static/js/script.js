var TEMP = null;

// Range Slider
!function(t){"use strict";function i(){t(this).removeClass("range-slider"),t(this).data("range-slider",void 0),t(this).init.prototype.range=void 0,t("*",this).remove()}function n(){t(this).rangeSlider("destroy"),t(this).rangeSlider()}function a(t){var i=Array.prototype.slice.call(arguments);return e[t].apply(this,i)}var e=Object();e.type=function(i,n){var a=t(this).attr("data-type");if(n){var a=n;return"vertical"!=a&&(a="horizontal"),t(this).attr("data-type",a),a}var a=t(this).attr("data-type");return"vertical"!=a&&(a="horizontal",t(this).attr("data-type",a)),a},e.step=function(i,n,a){var e;if(n)return e=Number(n),isNaN(e)&&(e=1),t(this).attr("data-step",e),e;var r=t(this).attr("data-step");try{e=t.parseJSON(r)}catch(i){e=Number(r),isNaN(e)&&(e=1),t(this).attr("data-step",e)}if(Array.isArray(e)){var o=r;t(this).attr("save_jason",o);var e=0;if(t(this).attr("data-step")){for(var d=JSON.parse(t(this).attr("data-step")),s=0;s<d.length;s++){var m=d[s],l=parseInt(m.start),p=parseInt(m.end),h=parseInt(m.step);e+=(p-l)/h}var u=t(this).rangeSlider("option","unit");t(this).attr("data-step",u/e)}}return e},e.min=function(i,n){if(n){var a=n;return isNaN(a)&&(a=0),t(this).attr("data-min",a),a}var a=parseInt(t(this).attr("data-min"));return isNaN(a)&&(a=0,t(this).attr("data-min",a)),a},e.max=function(i,n){var a=parseInt(t(this).attr("data-min"));if(n){var e=n;return(isNaN(e)||a>=e)&&(e=a+100),t(this).attr("data-max",e),e}var e=parseInt(t(this).attr("data-max"));return(isNaN(e)||a>=e)&&(e=a+100,t(this).attr("data-max",e)),e},e.min_unit=function(i,n){if(n){var a=n;return isNaN(a)&&(a=0),t(this).attr("data-min-unit",a),a}var a=Number(t(this).attr("data-min-unit"));return isNaN(a)&&(a=0,t(this).attr("data-min-unit",a)),a},e.unit=function(i,n){var a,e=Number(t(this).attr("data-max")),r=Number(t(this).attr("data-min")),a=e-r;return a},e.min_title=function(i,n){var a=t(this).attr("data-min-title");if(n){var a=n;t(this).attr("data-min-title",a)}return a},e.max_title=function(i,n){var a=t(this).attr("data-show-title");if(n){var a=n;t(this).attr("data-show-title",a)}},e.max_limit=function(i,n){var a;if(a=parseInt(t(this).attr("data-max-limit")),n&&(a=parseInt(n)),isNaN(a)&&(a=void 0),n){a?t(this).attr("data-max-limit",a):t(this).removeAttr("data-max-limit");var e=a-t(this).rangeSlider("option","min"),r=100*e/t(this).rangeSlider("option","unit"),o="vertical"==t(this).rangeSlider("option","type")?"top":"left";t(".max_limit",this).length||t(this).append("<div class='max_limit'></div>"),"top"==o?t(this).find(".max_limit").css("top",100-r+"%"):t(this).find(".max_limit").css("left",r+"%")}return a},e.min_default=function(i,n){t(this).parents("range-slider");if("min"!=t(this).attr("data-infinity")){var a=parseInt(t(this).attr("data-min"));if(n){var e=n;return(isNaN(e)||a>e)&&(e=0),t(this).attr("data-min-default",e),e}var e=Number(t(this).attr("data-min-default"));return(isNaN(e)||a>e)&&(e=0,t(this).attr("data-min-default",e)),e}return 0},e.max_default=function(i,n){if("max"!=t(this).attr("data-infinity")){var a=Number(t(this).attr("data-max")),e=Number(t(this).attr("data-min")),r=a-e;if(n){var o=n;return(isNaN(o)||a<o)&&(o=r),t(this).attr("data-max-default",o),o}var o=Number(t(this).attr("data-max-default"));return(isNaN(o)||a<o)&&(o=r,t(this).attr("data-max-default",o)),o}return t(this).rangeSlider("option","max")},e.margin=function(i,n){var a="vertical"==t(this).rangeSlider("option","type")?"height":"width",e=parseInt(t(this).find(".dynamic-margin").css(a));return e},e.depth=function(i,n){var a=Number(t(this).attr("data-max")),e=a;return e},e.unit_to_pixel=function(i,n){var a=t(this).rangeSlider("option","max")-t(this).rangeSlider("option","min"),e="vertical"==t(this).rangeSlider("option","type")?"height":"width",r=parseInt(t(this).css(e)),o=parseInt(n*r/a);return o},e.range_width=function(i,n){var a="vertical"==t(this).rangeSlider("option","type")?"height":"width",e=parseInt(t(this).find(".dynamic-range").css(a));return e},e.total_width=function(i,n){var a="vertical"==t(this).rangeSlider("option","type")?"height":"width",e=parseInt(t(this).css(a));return e},e.range=function(i,n,a,e){if(null==e){if("from"==n)return t(this).rangeSlider("option","margin");if("to"==n)return t(this).rangeSlider("option","margin")+t(this).rangeSlider("option","range_width")}return"to"==n?t(this).range(null,e,a):"from"==n?"min"!=t(this).attr("data-infinity")&&t(this).range(e,null,a):void 0},t.fn.rangeSlider=function(e){var o=Array.prototype.slice.call(arguments);switch(e){case"destroy":return i.call(this);case"restart":return n.call(this);case"option":return a.apply(this,o.splice(1))}t(this).each(function(){t(this).hasClass("range-slider")&&t(this).rangeSlider("destroy"),t(this).trigger("range-slider::init::before"),t(this).addClass("range-slider");var i=t(this).attr("id");i&&(t('<input type="hidden" name="'+i+'-max" data-range-bind="'+i+'" data-range-value="max">').appendTo(this),t('<input type="hidden" name="'+i+'-min" data-range-bind="'+i+'" data-range-value="min">').appendTo(this));var n=t(this).attr("data-infinity");t(this).rangeSlider("option","type",t(this).attr("data-type")),t(this).rangeSlider("option","min",t(this).attr("data-min")),t(this).rangeSlider("option","max",t(this).attr("data-max")),t(this).rangeSlider("option","unit",t(this).attr("data-unit")),t(this).rangeSlider("option","min_default",t(this).attr("data-min-default")),t(this).rangeSlider("option","max_default",t(this).attr("data-max-default")),t(this).data("range-slider",{}),t(this).init.prototype.range=function(i,n,a){var e=i,r=n,o=t(this).data("range-slider");o.type=this.rangeSlider("option","type"),o.step=this.rangeSlider("option","step"),o.min=this.rangeSlider("option","min"),o.max=this.rangeSlider("option","max"),o.unit=this.rangeSlider("option","unit"),o.min_default=this.rangeSlider("option","min_default"),o.max_default=this.rangeSlider("option","max_default"),o.margin=this.rangeSlider("option","margin"),o.depth=this.rangeSlider("option","depth"),o.min_unit=this.rangeSlider("option","min_unit"),o.min_title=this.rangeSlider("option","min_title"),o.max_title=this.rangeSlider("option","max_title"),o.max_limit=this.rangeSlider("option","max_limit"),o.unit_to_pixel=this.rangeSlider("option","unit_to_pixel"),o.range_width=this.rangeSlider("option","range_width"),o.total_width=this.rangeSlider("option","total_width");var d={type:"unit"};t.extend(d,a),d.from_type=d.type,d.to_type=d.type;var s="vertical"==o.type?"height":"width";null!==e&&e!==!1&&void 0!==e||(e=this.find(".dynamic-margin")[s](),"vertical"==t(this).rangeSlider("option","type")&&(e=this[s]()-(e+this.find(".dynamic-range")[s]())),d.from_type="pixel"),null!==r&&r!==!1&&void 0!==r||(r=this.find(".dynamic-margin")[s]()+this.find(".dynamic-range")[s](),"vertical"==t(this).rangeSlider("option","type")&&(r=this[s]()-this.find(".dynamic-margin")[s]()),d.to_type="pixel");var m=this[s]();"pixel"==d.from_type?e=e*t(this).rangeSlider("option","unit")/m:"percent"==d.from_type?e=e*t(this).rangeSlider("option","unit")/100:"ziro_unit"==d.from_type?e-=t(this).rangeSlider("option","min"):"step_plus"==d.from_type&&(e=e*t(this).rangeSlider("option","step")+o.from),"pixel"==d.to_type?r=r*t(this).rangeSlider("option","unit")/m:"percent"==d.to_type?r=r*t(this).rangeSlider("option","unit")/100:"ziro_unit"==d.to_type?r-=t(this).rangeSlider("option","min"):"step_plus"==d.to_type&&(r=r*t(this).rangeSlider("option","step")+o.to);var l=Math.round(e/t(this).rangeSlider("option","step"))*t(this).rangeSlider("option","step"),p=Math.round(r/t(this).rangeSlider("option","step"))*t(this).rangeSlider("option","step");p>o.max_limit-o.min&&(p=o.max_limit-o.min),p>o.max_limit-o.min&&(p=o.max_limit-o.min),l>o.max_limit-o.min-o.min_unit&&(l=o.max_limit-o.min-o.min_unit),p<l&&(o.to==p?p=l:l=p),p>o.unit&&(p=o.unit),l>o.unit&&(l=o.unit),p<0&&(p=0),l<0&&(l=0),t(this).attr("data-max-limit")&&t(this).rangeSlider("option","max_limit",t(this).attr("data-max-limit"));var h=t(this).rangeSlider("option","min_unit");t(this).find(".dynamic-range .min .mount").attr("data-value-show",parseInt(o.min+l)),t(this).find(".dynamic-range .max .mount").attr("data-value-show",parseInt(o.min+p)),p-l<=h&&(t(this).find(".dynamic-range .max .mount").attr("data-value-show",h+o.min+l),t(this).find(".dynamic-range .min .mount").attr("data-value-show",p-h+o.min),p<=h+l&&(null==i?l=p-h:null==n&&(p=l+h),p>=t(this).rangeSlider("option","unit")&&(p=t(this).rangeSlider("option","unit"),l=t(this).rangeSlider("option","unit")-h)),l<=o.min&&t(this).find(".dynamic-range .min .mount").attr("data-value-show",o.min));var u=t(this).attr("save_jason"),g=[],c=[],f=[];if(u){for(var v=jQuery.parseJSON(u),y=0;y<v.length;y++){var x=v[y],S=parseInt(x.start),_=parseInt(x.end),b=parseInt(x.step);g.push(b),c.push(S),f.push(_);var w=Math.round(l/t(this).rangeSlider("option","step")),N=Math.round(p/t(this).rangeSlider("option","step"))}for(var I=[],C=0,k=0;k<c.length;k++)C+=(f[k]-c[k])/g[k],I.push(C);for(var y=0;y<I.length;y++)if(N<=I[y]){if(isNaN(N-I[y-1]))var T=N;else var T=N-I[y-1];var A=c[y]+T*g[y];break}for(var y=0;y<I.length;y++)if(w<=I[y]){if(isNaN(w-I[y-1]))var T=w;else var T=w-I[y-1];var E=c[y]+T*g[y];break}t(this).find(".dynamic-range .min .mount").attr("data-value-show",parseInt(E)),t(this).find(".dynamic-range .max .mount").attr("data-value-show",parseInt(A))}if(null==i)var X="max",Y=t(this).find(".dynamic-range .max .mount").attr("data-value-show");else if(null==n)var X="min",Y=t(this).find(".dynamic-range .min .mount").attr("data-value-show");var j,O=t(this).attr("data-show-title");try{j=t.parseJSON(O)}catch(t){j=O}if(Array.isArray(j))for(var y=j.length-1;y>=0;y--){var z=j[y];for(var D in z)"min"==D?D=o.min:"max"==D&&(D=o.max),Y==D&&(D==o.min?D="min":D==o.max&&(D="max"),"min"==X?t(this).find(".dynamic-range .min .mount").attr("data-value-show",z[D]):"max"==X&&t(this).find(".dynamic-range .max .mount").attr("data-value-show",z[D]))}var J=this.attr("id");J&&t('[data-range-bind="'+J+'"]').each(function(){var i=t(this).attr("data-range-value");"max"==i?t(this).val(o.min+p):"min"==i&&t(this).val(o.min+l)}),e=100*l/t(this).rangeSlider("option","unit"),r=100*p/t(this).rangeSlider("option","unit");var M=r-e;o.to==p&&o.from==l||(this.data("range-slider").from=l,this.data("range-slider").to=p,this.trigger("range-slider::change::before",[o.min+l,o.min+p]),"vertical"==o.type?this.find(".dynamic-margin").css(s,100-r+"%"):this.find(".dynamic-margin").css(s,e+"%"),this.find(".dynamic-range").css(s,M+"%"),this.trigger("range-slider::change",[o.min+l,o.min+p]))};var a=t("<div class='dynamic-margin'></div>"),e=t("<div class='dynamic-range'></div>");"max"==n?r.call(this,"min").appendTo(e):"min"==n?r.call(this,"max").appendTo(e):(r.call(this,"max").appendTo(e),r.call(this,"min").appendTo(e)),e.find("div.min, div.max").append("<span class='mount'></span>");var o=e.find("div.min span.mount, div.max span.mount");t(this).find(".dynamic-range span.mount").show();var d=t(this).attr("data-fix-mount");o.hide(),"on"==d&&(o.show(),t(this).addClass("margin-range")),a.hide(),e.hide(),a.appendTo(this),e.appendTo(this),"max"==t(this).attr("data-infinity")?t(this).range(t(this).rangeSlider("option","min_default")-t(this).rangeSlider("option","min"),t(this).rangeSlider("option","max")):t(this).range(t(this).rangeSlider("option","min_default"),t(this).rangeSlider("option","max_default")),r.call(this,"min"),a.show(),e.show(),t(this).trigger("range-slider::init::after")})};var r=function(i){t(this).attr("data-infinity")||(t(this).unbind("mousemove.dynamic-range"),t(this).bind("mousemove.dynamic-range",function(){var i=t(this).find(".dynamic-range");t(i).bind("mousedown.dynamic-range",function(){var i=t(this).parents(".range-slider"),e="vertical"==a.type?event.pageY:event.pageX,r="vertical"==a.type?t(i).offset().top:t(i).offset().left,o=e-r,d=t(i).rangeSlider("option","range_width"),s=t(i).rangeSlider("option","margin");t(i).rangeSlider("option","range","to",{type:"pixel"}),t(i).rangeSlider("option","range","from",{type:"pixel"});return t(document).unbind("mousemove.dynamic-range"),t(document).bind("mousemove.dynamic-range",function(n){t(i).find(".dynamic-range div.min , .dynamic-range div.max").addClass("active"),t(i).find(".dynamic-range span.mount").show();var e="vertical"==a.type?n.pageY:n.pageX,r="vertical"==a.type?t(i).offset().top:t(i).offset().left,m=e-r;m="vertical"==a.type?t(i).height()-m:m;var l=m-o,p=t(i).rangeSlider("option","max_limit")-t(i).rangeSlider("option","min"),h=t(i).rangeSlider("option","unit_to_pixel",p),u=s+l,g=d+s+l;g>=h?u=h-d:u<=0&&(g=d),t(i).rangeSlider("option","range","to",{type:"pixel"},g),t(i).rangeSlider("option","range","from",{type:"pixel"},u)}).bind("mouseup.dynamic-range",function(){t(i).find(".dynamic-range div.min , .dynamic-range div.max").removeClass("active"),"on"!=n&&t(i).find(".dynamic-range span.mount").hide(),t(document).unbind("mouseup.dynamic-range"),t(document).unbind("mousemove.dynamic-range")}),!1}).bind("mouseup",function(){t(i).unbind("mousedown.dynamic-range")})}),t(document).unbind("touchend"),t(document).unbind("touchstart"),t(document).unbind("touchmove.dynamic-range"),t(this).bind("touchstart",function(i){i.preventDefault();var n=t(event.target),e=t(this);if(n.is(".dynamic-range")){var r="vertical"==a.type?i.originalEvent.touches[0].pageY:i.originalEvent.touches[0].pageX,o="vertical"==a.type?t(e).offset().top:t(e).offset().left,d=r-o,s=t(e).rangeSlider("option","range_width"),m=t(e).rangeSlider("option","margin");t(e).rangeSlider("option","range","to",{type:"pixel"}),t(e).rangeSlider("option","range","from",{type:"pixel"})}t(document).unbind("touchmove.dynamic-range"),t(document).bind("touchmove.dynamic-range",function(i){i.preventDefault();var n=t(event.target);if(n.is(".dynamic-range")){t(e).find(".dynamic-range div.min , .dynamic-range div.max").addClass("active"),t(e).find(".dynamic-range span.mount").show();var r="vertical"==a.type?i.originalEvent.touches[0].pageY:i.originalEvent.touches[0].pageX,o="vertical"==a.type?t(e).offset().top:t(e).offset().left,l=r-o;l="vertical"==a.type?t(e).height()-l:l;var p=l-d,h=t(e).rangeSlider("option","max_limit")-t(e).rangeSlider("option","min"),u=t(e).rangeSlider("option","unit_to_pixel",h),g=m+p,c=s+m+p;c>=u?g=u-s:g<=0&&(c=s),t(e).rangeSlider("option","range","to",{type:"pixel"},c),t(e).rangeSlider("option","range","from",{type:"pixel"},g)}})}).bind("touchend.dynamic-range",function(i){t(e).find(".dynamic-range div.min , .dynamic-range div.max").removeClass("active"),"on"!=n&&t(e).find(".dynamic-range span.mount").hide(),t(document).unbind("touchend"),t(document).unbind("touchstart"),t(document).unbind("touchmove")}));var n=t(this).attr("data-fix-mount"),a=t(this).data("range-slider"),e=this,r=t("<div class='"+i+"'></div>");return t(this).trigger("range-slider::selection",[r,i]),r.attr("tabindex","0"),r.unbind("mousedown.range-slider"),r.bind("mousedown.range-slider",function(){return t(document).unbind("mousemove.range-slider"),t(document).bind("mousemove.range-slider",function(r){t(e).find(".dynamic-range ."+i).addClass("active"),"on"!=n&&t(e).find(".dynamic-range ."+i+" span.mount").show();var o="vertical"==a.type?r.pageY:r.pageX,d="vertical"==a.type?t(e).offset().top:t(e).offset().left,s=o-d;s="vertical"==a.type?t(e).height()-s:s,"max"==i?t(e).rangeSlider("option","range","to",{type:"pixel"},s):t(e).rangeSlider("option","range","from",{type:"pixel"},s)}).bind("mouseup.range-slider",function(){t(e).find(".dynamic-range ."+i).removeClass("active"),"on"!=n&&t(e).find(".dynamic-range ."+i+" span.mount").hide(),t(document).unbind("mouseup.range-slider"),t(document).unbind("mousemove.range-slider")}),!1}).bind("mouseup",function(){t(document).unbind("mousemove.range-slider")}).bind("keydown.range-slider",function(i){if(a=1,i.shiftKey)var a=5;if(t(this).is(".max")){if("on"!=n&&t(e).find(".dynamic-range .max span.mount").show(),38==i.keyCode||39==i.keyCode)return t(this).parents(".range-slider").rangeSlider("option","range","to",{type:"step_plus"},a),!1;if(37==i.keyCode||40==i.keyCode)return t(this).parents(".range-slider").rangeSlider("option","range","to",{type:"step_plus"},-a),!1}else{if(t(e).find(".dynamic-range .min span.mount").show(),38==i.keyCode||39==i.keyCode)return t(this).parents(".range-slider").rangeSlider("option","range","from",{type:"step_plus"},a),!1;if(37==i.keyCode||40==i.keyCode)return t(this).parents(".range-slider").rangeSlider("option","range","from",{type:"step_plus"},-a),!1}"on"!=n&&(t(e).find(".dynamic-range .max span.mount").hide(),t(e).find(".dynamic-range .min span.mount").hide())}).bind("touchmove",function(r){r.preventDefault(),t(e).find(".dynamic-range ."+i).addClass("active"),"on"!=n&&t(e).find(".dynamic-range ."+i+" span.mount").show();var o="vertical"==a.type?r.originalEvent.touches[0].pageY:r.originalEvent.touches[0].pageX,d="vertical"==a.type?t(e).offset().top:t(e).offset().left,s=o-d;s="vertical"==a.type?t(e).height()-s:s,"max"==i?t(e).rangeSlider("option","range","to",{type:"pixel"},s):t(e).rangeSlider("option","range","from",{type:"pixel"},s)}).bind("touchend",function(a){a.preventDefault(),t(e).find(".dynamic-range ."+i).removeClass("active"),"on"!=n&&t(e).find(".dynamic-range ."+i+" span.mount").hide(),t(document).unbind("mouseup.range-slider"),t(document).unbind("mousemove.range-slider")}),r}}(jQuery);
/* HTML5 Sortable jQuery Plugin -http://farhadi.ir/projects/html5sortable */
!function(t){var e,r=t();t.fn.sortable=function(a){var n=String(a);return a=t.extend({connectWith:!1},a),this.each(function(){if(/^(enable|disable|destroy)$/.test(n)){var i=t(this).children(t(this).data("items")).attr("draggable","enable"==n);return void("destroy"==n&&i.add(this).removeData("connectWith items").off("dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s"))}var s,d,i=t(this).children(a.items),o=t("<"+(/^(ul|ol)$/i.test(this.tagName)?"li":"div")+' class="sortable-placeholder">');i.find(a.handle).mousedown(function(){s=!0}).mouseup(function(){s=!1}),t(this).data("items",a.items),r=r.add(o),a.connectWith&&t(a.connectWith).add(this).data("connectWith",a.connectWith),i.attr("draggable","true").on("dragstart.h5s",function(r){if(a.handle&&!s)return!1;s=!1;var n=r.originalEvent.dataTransfer;n.effectAllowed="move",n.setData("Text","dummy"),d=(e=t(this)).addClass("sortable-dragging").index()}).on("dragend.h5s",function(){e&&(e.removeClass("sortable-dragging").show(),r.detach(),d!=e.index()&&e.parent().trigger("sortupdate",{item:e}),e=null)}).not("a[href], img").on("selectstart.h5s",function(){return this.dragDrop&&this.dragDrop(),!1}).end().add([this,o]).on("dragover.h5s dragenter.h5s drop.h5s",function(n){return i.is(e)||a.connectWith===t(e).parent().data("connectWith")?"drop"==n.type?(n.stopPropagation(),r.filter(":visible").after(e),e.trigger("dragend.h5s"),!1):(n.preventDefault(),n.originalEvent.dataTransfer.dropEffect="move",i.is(this)?(a.forcePlaceholderSize&&o.height(e.outerHeight()),e.hide(),t(this)[o.index()<t(this).index()?"after":"before"](o),r.not(o).detach()):r.is(this)||t(this).children(a.items).length||(r.detach(),t(this).append(o)),!1):!0})})}}(jQuery);
// data-response library
function runDataResponse(){$(document).on("change","input, select",function(){checkInputResponse(this,!1)}),$(document).on("keyup","input[data-response-realtime]",function(){checkInputResponse(this,!1)})}function getInputValue(a){var b;switch($(a).attr("type")){case"checkbox":b=$(a).is(":checked");break;case"radio":b=$(a).val();break;case"text":default:b=$(a).val()}return b}function checkInputResponse(a,b){var c,d=!1,e=$(a).attr("id"),f=$(a).attr("name"),g=$(a).attr("type"),h=$(a).parents("[data-respnse-group]"),i=$(a).attr("data-response-get"),j=getInputValue($(a),g),k=f;"id"==i&&(k=e);var l=$('[data-response*="'+k+'"]');l.length<1&&(k=e,l=$('[data-response*="'+k+'"]')),h.length&&h.each(function(a,b){var c=$(this).attr("data-respnse-group");d=c,l=l.add('[data-response*="'+c+'"]')}),l.each(function(){var b=$(this).attr("data-response-effect"),e=$(this).attr("data-response-timing"),f=$(this).attr("data-response-where"),k=($(this).attr("data-response-where-not"),$(this).attr("data-response-toggle")),l=$(this).attr("data-response-disable"),m=$(this).attr("data-response-class"),n=$(this).attr("data-response-class-false"),o=$(this).attr("data-response-call"),p=$(this).attr("data-response-repeat");if(!l&&$(this).attr("disabled")&&(l=$(this).attr("disabled"),$(this).attr("data-response-disable","disabled-manual")),b="slide"==b?{name:"slide",toggle:"slideToggle",open:"slideDown",close:"slideUp"}:{name:"fade",toggle:"fadeToggle",open:"fadeIn",close:"fadeOut"},e||(e="fast"),d){var q=h.attr("data-response-where"),r=h.attr("data-response-where-not"),s=!0;r&&(s=!1),h.find("input").each(function(a,b){r?getInputValue(b).toString()!==r&&(s=!0):q&&getInputValue(b).toString()!==q&&(s=!1)}),f=s}else f?($.each(f.split("|"),function(a,b){b==j.toString()&&(f=!0)}),f!==!0&&(f=!1)):0!=j&&(f=!0);if(k?$(this)[b.effect](e):f?(l?$(this).prop("disabled",!1):void 0!==m?($(this).addClass(m),$(this).removeClass(n)):void 0!=p||($(this).attr("data-response-hide",null),$(this)[b.open](e)),$(this).find("[data-response-focus]").length?$(this).find("[data-response-focus]").focus():$(this).closest("[data-response-focus]").focus(),c="open"):(l?$(this).prop("disabled",!0):void 0!==m?($(this).addClass(n),$(this).removeClass(m)):void 0!=p||$(this)[b.close](e,function(){$(this).attr("data-response-hide","")}),c="close"),void 0!=p){var t="";switch(g){case"checkbox":t=$(a).is(":checked"),$(this).prop("checked",t);break;case"radio":case"text":default:t=$(a).val(),$(this).val(t)}}o&&(c=1==c,"function"==typeof window[o]&&window[o](c,$(this),$(a)))}),$(window).trigger("response:open",[k,c])}function fixSlideJumping(){$('[data-response-hide][data-response-effect="slide"]:not([data-response-notfix])',this).css("height",function(a,b){return $(this).hide(),b})}
// intro js v1.1.1
!function(t,e){"object"==typeof exports?e(exports):"function"==typeof define&&define.amd?define(["exports"],e):e(t)}(this,function(t){function e(t){this._targetElement=t,this._options={nextLabel:"Next &rarr;",prevLabel:"&larr; Back",skipLabel:"Skip",doneLabel:"Done",tooltipPosition:"bottom",tooltipClass:"",highlightClass:"",exitOnEsc:!0,exitOnOverlayClick:!0,showStepNumbers:!0,keyboardNavigation:!0,showButtons:!0,showBullets:!0,showProgress:!1,scrollToElement:!0,overlayOpacity:.8,positionPrecedence:["bottom","top","right","left"],disableInteraction:!1}}function n(t){var e=[],n=this;if(this._options.steps)for(var s=0,a=this._options.steps.length;a>s;s++){var c=o(this._options.steps[s]);if(c.step=e.length+1,"string"==typeof c.element&&(c.element=document.querySelector(c.element)),"undefined"==typeof c.element||null==c.element){var h=document.querySelector(".introjsFloatingElement");null==h&&(h=document.createElement("div"),h.className="introjsFloatingElement",document.body.appendChild(h)),c.element=h,c.position="floating"}null!=c.element&&e.push(c)}else{if(a=t.querySelectorAll("*[data-intro]"),1>a.length)return!1;for(s=0,c=a.length;c>s;s++){var h=a[s],u=parseInt(h.getAttribute("data-step"),10);u>0&&(e[u-1]={element:h,intro:h.getAttribute("data-intro"),step:parseInt(h.getAttribute("data-step"),10),tooltipClass:h.getAttribute("data-tooltipClass"),highlightClass:h.getAttribute("data-highlightClass"),position:h.getAttribute("data-position")||this._options.tooltipPosition})}for(s=u=0,c=a.length;c>s;s++)if(h=a[s],null==h.getAttribute("data-step")){for(;"undefined"!=typeof e[u];)u++;e[u]={element:h,intro:h.getAttribute("data-intro"),step:u+1,tooltipClass:h.getAttribute("data-tooltipClass"),highlightClass:h.getAttribute("data-highlightClass"),position:h.getAttribute("data-position")||this._options.tooltipPosition}}}for(s=[],a=0;a<e.length;a++)e[a]&&s.push(e[a]);return e=s,e.sort(function(t,e){return t.step-e.step}),n._introItems=e,y.call(n,t)&&(i.call(n),t.querySelector(".introjs-skipbutton"),t.querySelector(".introjs-nextbutton"),n._onKeyDown=function(e){if(27===e.keyCode&&1==n._options.exitOnEsc)void 0!=n._introExitCallback&&n._introExitCallback.call(n),l.call(n,t);else if(37===e.keyCode)r.call(n);else if(39===e.keyCode)i.call(n);else if(13===e.keyCode){var o=e.target||e.srcElement;o&&0<o.className.indexOf("introjs-prevbutton")?r.call(n):o&&0<o.className.indexOf("introjs-skipbutton")?(n._introItems.length-1==n._currentStep&&"function"==typeof n._introCompleteCallback&&n._introCompleteCallback.call(n),void 0!=n._introExitCallback&&n._introExitCallback.call(n),l.call(n,t)):i.call(n),e.preventDefault?e.preventDefault():e.returnValue=!1}},n._onResize=function(t){p.call(n,document.querySelector(".introjs-helperLayer")),p.call(n,document.querySelector(".introjs-tooltipReferenceLayer"))},window.addEventListener?(this._options.keyboardNavigation&&window.addEventListener("keydown",n._onKeyDown,!0),window.addEventListener("resize",n._onResize,!0)):document.attachEvent&&(this._options.keyboardNavigation&&document.attachEvent("onkeydown",n._onKeyDown),document.attachEvent("onresize",n._onResize))),!1}function o(t){if(null==t||"object"!=typeof t||"undefined"!=typeof t.nodeType)return t;var e,n={};for(e in t)n[e]="undefined"!=typeof jQuery&&t[e]instanceof jQuery?t[e]:o(t[e]);return n}function i(){if(this._direction="forward","undefined"==typeof this._currentStep?this._currentStep=0:++this._currentStep,this._introItems.length<=this._currentStep)"function"==typeof this._introCompleteCallback&&this._introCompleteCallback.call(this),l.call(this,this._targetElement);else{var t=this._introItems[this._currentStep];"undefined"!=typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,t.element),d.call(this,t)}}function r(){if(this._direction="backward",0===this._currentStep)return!1;var t=this._introItems[--this._currentStep];"undefined"!=typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,t.element),d.call(this,t)}function l(t){var e=t.querySelector(".introjs-overlay");if(null!=e){e.style.opacity=0,setTimeout(function(){e.parentNode&&e.parentNode.removeChild(e)},500);var n=t.querySelector(".introjs-helperLayer");if(n&&n.parentNode.removeChild(n),(n=t.querySelector(".introjs-tooltipReferenceLayer"))&&n.parentNode.removeChild(n),(t=t.querySelector(".introjs-disableInteraction"))&&t.parentNode.removeChild(t),(t=document.querySelector(".introjsFloatingElement"))&&t.parentNode.removeChild(t),(t=document.querySelector(".introjs-showElement"))&&(t.className=t.className.replace(/introjs-[a-zA-Z]+/g,"").replace(/^\s+|\s+$/g,"")),(t=document.querySelectorAll(".introjs-fixParent"))&&0<t.length)for(n=t.length-1;n>=0;n--)t[n].className=t[n].className.replace(/introjs-fixParent/g,"").replace(/^\s+|\s+$/g,"");window.removeEventListener?window.removeEventListener("keydown",this._onKeyDown,!0):document.detachEvent&&document.detachEvent("onkeydown",this._onKeyDown),this._currentStep=void 0}}function s(t,e,n,o){var i,r,l="";if(e.style.top=null,e.style.right=null,e.style.bottom=null,e.style.left=null,e.style.marginLeft=null,e.style.marginTop=null,n.style.display="inherit","undefined"!=typeof o&&null!=o&&(o.style.top=null,o.style.left=null),this._introItems[this._currentStep]){if(l=this._introItems[this._currentStep],l="string"==typeof l.tooltipClass?l.tooltipClass:this._options.tooltipClass,e.className=("introjs-tooltip "+l).replace(/^\s+|\s+$/g,""),r=this._introItems[this._currentStep].position,("auto"==r||"auto"==this._options.tooltipPosition)&&"floating"!=r){l=r,i=this._options.positionPrecedence.slice(),r=m();var s=b(e).height+10,p=b(e).width+20,u=b(t),d="floating";u.left+p>r.width||0>u.left+u.width/2-p?(h(i,"bottom"),h(i,"top")):(u.height+u.top+s>r.height&&h(i,"bottom"),0>u.top-s&&h(i,"top")),u.width+u.left+p>r.width&&h(i,"right"),0>u.left-p&&h(i,"left"),0<i.length&&(d=i[0]),l&&"auto"!=l&&-1<i.indexOf(l)&&(d=l),r=d}switch(l=b(t),t=b(e),i=m(),r){case"top":n.className="introjs-arrow bottom",a(l,15,t,i,e),e.style.bottom=l.height+20+"px";break;case"right":e.style.left=l.width+20+"px",l.top+t.height>i.height?(n.className="introjs-arrow left-bottom",e.style.top="-"+(t.height-l.height-20)+"px"):n.className="introjs-arrow left";break;case"left":1==this._options.showStepNumbers&&(e.style.top="15px"),l.top+t.height>i.height?(e.style.top="-"+(t.height-l.height-20)+"px",n.className="introjs-arrow right-bottom"):n.className="introjs-arrow right",e.style.right=l.width+20+"px";break;case"floating":n.style.display="none",e.style.left="50%",e.style.top="50%",e.style.marginLeft="-"+t.width/2+"px",e.style.marginTop="-"+t.height/2+"px","undefined"!=typeof o&&null!=o&&(o.style.left="-"+(t.width/2+18)+"px",o.style.top="-"+(t.height/2+18)+"px");break;case"bottom-right-aligned":n.className="introjs-arrow top-right",c(l,0,t,e),e.style.top=l.height+20+"px";break;case"bottom-middle-aligned":n.className="introjs-arrow top-middle",n=l.width/2-t.width/2,c(l,n,t,e)&&(e.style.right=null,a(l,n,t,i,e)),e.style.top=l.height+20+"px";break;default:n.className="introjs-arrow top",a(l,0,t,i,e),e.style.top=l.height+20+"px"}}}function a(t,e,n,o,i){return t.left+e+n.width>o.width?(i.style.left=o.width-n.width-t.left+"px",!1):(i.style.left=e+"px",!0)}function c(t,e,n,o){return 0>t.left+t.width-e-n.width?(o.style.left=-t.left+"px",!1):(o.style.right=e+"px",!0)}function h(t,e){-1<t.indexOf(e)&&t.splice(t.indexOf(e),1)}function p(t){if(t&&this._introItems[this._currentStep]){var e=this._introItems[this._currentStep],n=b(e.element),o=10;"floating"==e.position&&(o=0),t.setAttribute("style","width: "+(n.width+o)+"px; height:"+(n.height+o)+"px; top:"+(n.top-5)+"px;left: "+(n.left-5)+"px;")}}function u(){var t=document.querySelector(".introjs-disableInteraction");null===t&&(t=document.createElement("div"),t.className="introjs-disableInteraction",this._targetElement.appendChild(t)),p.call(this,t)}function d(t){"undefined"!=typeof this._introChangeCallback&&this._introChangeCallback.call(this,t.element);var e=this,n=document.querySelector(".introjs-helperLayer"),o=document.querySelector(".introjs-tooltipReferenceLayer"),a="introjs-helperLayer";if(b(t.element),"string"==typeof t.highlightClass&&(a+=" "+t.highlightClass),"string"==typeof this._options.highlightClass&&(a+=" "+this._options.highlightClass),null!=n){var c=o.querySelector(".introjs-helperNumberLayer"),h=o.querySelector(".introjs-tooltiptext"),d=o.querySelector(".introjs-arrow"),y=o.querySelector(".introjs-tooltip"),w=o.querySelector(".introjs-skipbutton"),v=o.querySelector(".introjs-prevbutton"),C=o.querySelector(".introjs-nextbutton");if(n.className=a,y.style.opacity=0,y.style.display="none",null!=c){var j=this._introItems[0<=t.step-2?t.step-2:0];(null!=j&&"forward"==this._direction&&"floating"==j.position||"backward"==this._direction&&"floating"==t.position)&&(c.style.opacity=0)}if(p.call(e,n),p.call(e,o),(j=document.querySelectorAll(".introjs-fixParent"))&&0<j.length)for(a=j.length-1;a>=0;a--)j[a].className=j[a].className.replace(/introjs-fixParent/g,"").replace(/^\s+|\s+$/g,"");j=document.querySelector(".introjs-showElement"),j.className=j.className.replace(/introjs-[a-zA-Z]+/g,"").replace(/^\s+|\s+$/g,""),e._lastShowElementTimer&&clearTimeout(e._lastShowElementTimer),e._lastShowElementTimer=setTimeout(function(){null!=c&&(c.innerHTML=t.step),h.innerHTML=t.intro,y.style.display="block",s.call(e,t.element,y,d,c),o.querySelector(".introjs-bullets li > a.active").className="",o.querySelector('.introjs-bullets li > a[data-stepnumber="'+t.step+'"]').className="active",o.querySelector(".introjs-progress .introjs-progressbar").setAttribute("style","width:"+_.call(e)+"%;"),y.style.opacity=1,c&&(c.style.opacity=1),-1===C.tabIndex?w.focus():C.focus()},350)}else{var x=document.createElement("div"),v=document.createElement("div"),n=document.createElement("div"),k=document.createElement("div"),S=document.createElement("div"),N=document.createElement("div"),E=document.createElement("div"),L=document.createElement("div");x.className=a,v.className="introjs-tooltipReferenceLayer",p.call(e,x),p.call(e,v),this._targetElement.appendChild(x),this._targetElement.appendChild(v),n.className="introjs-arrow",S.className="introjs-tooltiptext",S.innerHTML=t.intro,N.className="introjs-bullets",!1===this._options.showBullets&&(N.style.display="none");for(var x=document.createElement("ul"),a=0,I=this._introItems.length;I>a;a++){var q=document.createElement("li"),A=document.createElement("a");A.onclick=function(){e.goToStep(this.getAttribute("data-stepnumber"))},a===t.step-1&&(A.className="active"),A.href="javascript:void(0);",A.innerHTML="&nbsp;",A.setAttribute("data-stepnumber",this._introItems[a].step),q.appendChild(A),x.appendChild(q)}N.appendChild(x),E.className="introjs-progress",!1===this._options.showProgress&&(E.style.display="none"),a=document.createElement("div"),a.className="introjs-progressbar",a.setAttribute("style","width:"+_.call(this)+"%;"),E.appendChild(a),L.className="introjs-tooltipbuttons",!1===this._options.showButtons&&(L.style.display="none"),k.className="introjs-tooltip",k.appendChild(S),k.appendChild(N),k.appendChild(E),1==this._options.showStepNumbers&&(j=document.createElement("span"),j.className="introjs-helperNumberLayer",j.innerHTML=t.step,v.appendChild(j)),k.appendChild(n),v.appendChild(k),C=document.createElement("a"),C.onclick=function(){e._introItems.length-1!=e._currentStep&&i.call(e)},C.href="javascript:void(0);",C.innerHTML=this._options.nextLabel,v=document.createElement("a"),v.onclick=function(){0!=e._currentStep&&r.call(e)},v.href="javascript:void(0);",v.innerHTML=this._options.prevLabel,w=document.createElement("a"),w.className="introjs-button introjs-skipbutton",w.href="javascript:void(0);",w.innerHTML=this._options.skipLabel,w.onclick=function(){e._introItems.length-1==e._currentStep&&"function"==typeof e._introCompleteCallback&&e._introCompleteCallback.call(e),e._introItems.length-1!=e._currentStep&&"function"==typeof e._introExitCallback&&e._introExitCallback.call(e),l.call(e,e._targetElement)},L.appendChild(w),1<this._introItems.length&&(L.appendChild(v),L.appendChild(C)),k.appendChild(L),s.call(e,t.element,k,n,j)}for(!0===this._options.disableInteraction&&u.call(e),v.removeAttribute("tabIndex"),C.removeAttribute("tabIndex"),0==this._currentStep&&1<this._introItems.length?(v.className="introjs-button introjs-prevbutton introjs-disabled",v.tabIndex="-1",C.className="introjs-button introjs-nextbutton",w.innerHTML=this._options.skipLabel):this._introItems.length-1==this._currentStep||1==this._introItems.length?(w.innerHTML=this._options.doneLabel,v.className="introjs-button introjs-prevbutton",C.className="introjs-button introjs-nextbutton introjs-disabled",C.tabIndex="-1"):(v.className="introjs-button introjs-prevbutton",C.className="introjs-button introjs-nextbutton",w.innerHTML=this._options.skipLabel),C.focus(),t.element.className+=" introjs-showElement",j=f(t.element,"position"),"absolute"!==j&&"relative"!==j&&(t.element.className+=" introjs-relativePosition"),j=t.element.parentNode;null!=j&&"body"!==j.tagName.toLowerCase();)n=f(j,"z-index"),k=parseFloat(f(j,"opacity")),L=f(j,"transform")||f(j,"-webkit-transform")||f(j,"-moz-transform")||f(j,"-ms-transform")||f(j,"-o-transform"),(/[0-9]+/.test(n)||1>k||"none"!==L&&void 0!==L)&&(j.className+=" introjs-fixParent"),j=j.parentNode;g(t.element)||!0!==this._options.scrollToElement||(k=t.element.getBoundingClientRect(),j=m().height,n=k.bottom-(k.bottom-k.top),k=k.bottom-j,0>n||t.element.clientHeight>j?window.scrollBy(0,n-30):window.scrollBy(0,k+100)),"undefined"!=typeof this._introAfterChangeCallback&&this._introAfterChangeCallback.call(this,t.element)}function f(t,e){var n="";return t.currentStyle?n=t.currentStyle[e]:document.defaultView&&document.defaultView.getComputedStyle&&(n=document.defaultView.getComputedStyle(t,null).getPropertyValue(e)),n&&n.toLowerCase?n.toLowerCase():n}function m(){if(void 0!=window.innerWidth)return{width:window.innerWidth,height:window.innerHeight};var t=document.documentElement;return{width:t.clientWidth,height:t.clientHeight}}function g(t){return t=t.getBoundingClientRect(),0<=t.top&&0<=t.left&&t.bottom+80<=window.innerHeight&&t.right<=window.innerWidth}function y(t){var e=document.createElement("div"),n="",o=this;if(e.className="introjs-overlay","body"===t.tagName.toLowerCase())n+="top: 0;bottom: 0; left: 0;right: 0;position: fixed;",e.setAttribute("style",n);else{var i=b(t);i&&(n+="width: "+i.width+"px; height:"+i.height+"px; top:"+i.top+"px;left: "+i.left+"px;",e.setAttribute("style",n))}return t.appendChild(e),e.onclick=function(){1==o._options.exitOnOverlayClick&&(void 0!=o._introExitCallback&&o._introExitCallback.call(o),l.call(o,t))},setTimeout(function(){n+="opacity: "+o._options.overlayOpacity.toString()+";",e.setAttribute("style",n)},10),!0}function b(t){var e={};e.width=t.offsetWidth,e.height=t.offsetHeight;for(var n=0,o=0;t&&!isNaN(t.offsetLeft)&&!isNaN(t.offsetTop);)n+=t.offsetLeft,o+=t.offsetTop,t=t.offsetParent;return e.top=o,e.left=n,e}function _(){return 100*(parseInt(this._currentStep+1,10)/this._introItems.length)}var w=function(t){if("object"==typeof t)return new e(t);if("string"==typeof t){if(t=document.querySelector(t))return new e(t);throw Error("There is no element with given selector.")}return new e(document.body)};return w.version="1.1.1",w.fn=e.prototype={clone:function(){return new e(this)},setOption:function(t,e){return this._options[t]=e,this},setOptions:function(t){var e,n=this._options,o={};for(e in n)o[e]=n[e];for(e in t)o[e]=t[e];return this._options=o,this},start:function(){return n.call(this,this._targetElement),this},goToStep:function(t){return this._currentStep=t-2,"undefined"!=typeof this._introItems&&i.call(this),this},nextStep:function(){return i.call(this),this},previousStep:function(){return r.call(this),this},exit:function(){return l.call(this,this._targetElement),this},refresh:function(){return p.call(this,document.querySelector(".introjs-helperLayer")),p.call(this,document.querySelector(".introjs-tooltipReferenceLayer")),this},onbeforechange:function(t){if("function"!=typeof t)throw Error("Provided callback for onbeforechange was not a function");return this._introBeforeChangeCallback=t,this},onchange:function(t){if("function"!=typeof t)throw Error("Provided callback for onchange was not a function.");return this._introChangeCallback=t,this},onafterchange:function(t){if("function"!=typeof t)throw Error("Provided callback for onafterchange was not a function");return this._introAfterChangeCallback=t,this},oncomplete:function(t){if("function"!=typeof t)throw Error("Provided callback for oncomplete was not a function.");return this._introCompleteCallback=t,this},onexit:function(t){if("function"!=typeof t)throw Error("Provided callback for onexit was not a function.");return this._introExitCallback=t,this}},t.introJs=w});


/**
 * allow textarea to be resizable
 * @return {[type]} [description]
 */
function resizableTextarea()
{
	$(document).on('keyup', 'textarea[data-resizable]', function()
	{
		var min     = 100;
		var max     = 500;
		var height  = $(this).height();
		var sHeight = this.scrollHeight;

		if (sHeight > min && sHeight < max && height < sHeight)
		{
			this.style.height = min+'px';
			this.style.height = this.scrollHeight+'px';
		}
	});
}


/**
 * [openProfile description]
 * @return {[type]} [description]
 */
function openProfile()
{
	// show profile detail with tab
	$(document).on('focus', '.profile-detail a', function()
	{
		$(this).parents('.profile').addClass('open');
		// set scroll to top of page
		$('body').scrollTop(0);

	}).on('blur', '.profile-detail a', function()
	{
		$(this).parents('.profile').removeClass('open');
	});
}

/**
 * [setFav description]
 * @param {[type]} argument [description]
 */
function setFav()
{
	$(document).on('change', '[name="favorite"]', function()
	{
		_self = $(this);
		id    = _self.parents('[data-id]').attr("data-id");
		console.log(id);

		_self.ajaxify(
		{
			ajax:
			{
				data:
				{
					'type': 'favourites',
					'id': id
				},
				abort: true,
				method: 'post',
				error: function(e, data, x)
				{
					console.log(_self);
					if(data !== 'success')
					{
						_self.prop("checked", false);
						console.log(_self.prop('checked'));
					}
				},
			}
		});
	});
}


/**
 * [shortkey description]
 * @return {[type]} [description]
 */
function shortkey()
{
	$(document).keydown(function(e)
	{
		switch (e.keyCode)
		{
			// f1
			case 112:
				console.log("Need help?");
				e.preventDefault();
				introJs().start();
				break;

			default:
				break;
		}
	});
}


function showImagePreview(_file, _output)
{
	// if we do not support fileReader return false!
	if (typeof (FileReader) == "undefined")
	{
		return false;
	}
	// declare variables
	var files = $(_file)[0].files;

	// Loop through the FileList and render image files as thumbnails.
	for (var i = 0, f; f = files[i]; i++)
	{
		// Only process image files.
		if (!f.type.match('image.*'))
		{
			$(_output).addClass('otherFile');
			continue;
		}
		else
		{
			$(_output).removeClass('otherFile');
		}
		// create new instance
		var reader = new FileReader();
		// Closure to capture the file information.
		reader.onload = (function(theFile)
		{
			return function(e)
			{
				// if span of preview is not exist, then create element for preview
				// var span = document.createElement('span');

				// Render thumbnail
				var imageEl = '<img src="'+ e.target.result+ '" title="'+ escape(theFile.name)+ '"/>';
				$(_output).html(imageEl);
				$(window).trigger('cropBox:open', _output);
			};
		})(f);

		// Read in the image file as a data URL.
		reader.readAsDataURL(f);
	}
}


/**
 * [startCrop description]
 * @param  {[type]} _el [description]
 * @return {[type]}     [description]
 */
function startCrop(_el)
{
	$('#modal-crop').trigger('open');
	console.log(_el);

	var cropBox = $('#modal-crop .cropBox');
	var img     = $(_el).find('img').clone();
	console.log(img);


	cropBox.html(img);

	cropBox.find('img').cropper(
	{
		aspectRatio: 1,
		preview: '.img-preview',
		crop: function(e)
		{
			// Output the result data for cropping image.
			// console.log(e.x);
			// console.log(e.y);
			// console.log(e.width);
			// console.log(e.height);
			// console.log(e.rotate);b
			// console.log(e.scaleX);
			console.log(e);
		}
	});
}


/**
 * set link of language on each page
 */
function setLanguageURL()
{
	var urlPath     = window.location.pathname;
	var indexOfLang = urlPath.indexOf('/' + $('html').attr('lang'));

	if(indexOfLang === 0)
	{
		urlPath = urlPath.substr(4);
	}
	else
	{
		urlPath = urlPath.substr(1);
	}

	$('.langlist a').each(function(key, index)
	{
		var lang = $(index).attr('hreflang');
		if(lang == 'en')
		{
			lang = '';
		}
		var url = lang + '/' + urlPath;
		$(index).attr('href', url.trim('/'));
	})
}


// ================================================================== run on all part of each page
route('*', function ()
{
	// run rangeSlider on all pages
	$(".range-slider", this).rangeSlider();
	// run func to fixSlideJumping
	fixSlideJumping.call(this);
}).once(function()
{
	setLanguageURL();
});


// ************************************************************************************************************ Add
/**
 * add new record of answer
 */
function checkAddOpt()
{
	var numberOfEmptyInputs = 0;
	var emptyRowNumber;

	// check if current element has not value and we have no empty inputs
	$.each($('.input-group.sortable .element .input[type="text"]'), function(key, value)
	{
		if ( !$(this).val() && numberOfEmptyInputs === 0 )
		{
			numberOfEmptyInputs++;
			emptyRowNumber = key;
		}
	});

	// if we had no empty inputs and we needed one do this
	if (numberOfEmptyInputs === 0 && !$('.input-group.sortable').hasClass('editing'))
	{
		addNewOpt();
	}
	// if we had empty inputs do this
	else
	{
		// highlight empty row
	}
}


/**
 * add new option to items
 */
function addNewOpt(_group, _title)
{
	var template = $('.input-group.sortable>li').eq(0).clone();
	var num      = $('.input-group.sortable>li').length + 1;
	if(_group)
	{
		template.attr('data-profile', _group);
	}
	if(_title)
	{
		template.find('.element label.title').text(_title);
	}
	else
	{
		// if language is farsi then convert number to persian
		if($('html').attr('lang') === 'fa')
		{
			template.find('.element label.title b').text(num.toString().toFarsi());
		}
		else
		{
			template.find('.element label.title b').text(num);
		}
	}
	template.find('.element label.title').attr('for', 'answer' + num);
	template.find('.element .input').attr('id', 'answer' + num);
	template.find('.element .input').attr('name', 'answer' + num);
	template.find('.element .input').attr('value', '');
	template.find('.element .input').val('');
	// set true
	template.find('.element .true input').attr('name', 'true' + num);
	template.find('.element .true label').attr('for', 'true' + num);
	template.find('.element .true input').attr('id', 'true' + num);
	template.find('.element .true input').attr('checked', false);;
	// set file
	template.find('.element .file input').attr('name', 'file' + num);
	template.find('.element .file input').attr('id', 'file' + num);
	template.find('.element .file input').val('');
	template.find('.element .file label').attr('for', 'file' + num);
	template.find('.element .file img').remove();


	// set score
	template.find('.element .score input').attr('name', 'score' + num);
	template.find('.element .score input').attr('id', 'score' + num);
	template.find('.element .score input').val('');
	// template.find('.element').attr('data-row', num);

	$('.input-group.sortable').append(template);
	template.addClass('animated fadeInDown').delay(1000).queue(function()
	{
		$(this).removeClass("animated fadeInDown").dequeue();
	});
	setSortable();
}


/**
 * [showQuestionOptsDel description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function showQuestionOptsDel(_this)
{
	// hide all elements
	$('.input-group.sortable .element .delete').fadeOut(100);
	var currentRowValue = $(_this).parent('.element').children('input[type="text"]').val();
	// always show delete button on input focus
	if(countQuestionOpts() > 2 && currentRowValue)
	{
		$(_this).stop().fadeIn(200);
	}
}


/**
 * delete selected opt and do some event after that
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function deleteQuestionOpts(_this)
{
	var currentRowValue = $(_this).closest('li').find('input[type="text"]').val();
	if (countQuestionOpts() > 2 && currentRowValue)
	{
		$(_this).closest('li').addClass('animated fadeOutSide').slideUp(200, function()
		{
			// set focus to next input
			$(this).closest('li').next().find('input').focus();
			// remove element
			$(this).remove();
			// rearrange question opts
			rearrangeQuestionOpts();
			// recalc percentage of progress bar
			detectPercentage();
		});
	}
}


/**
 * generate sortable again and again after each change
 */
function setSortable(_onlyDestroy)
{
	$('.sortable').sortable('destroy');
	if(!_onlyDestroy)
	{
		$('.sortable').sortable({items: ':not(.fix)'},{handle: '.title'}).bind('sortupdate', function(e, ui)
		{
			rearrangeQuestionOpts();
		});
	}
}


/**
 * rearrange number of opts in question
 * @return {[type]} [description]
 */
function rearrangeQuestionOpts()
{
	$.each($('.input-group.sortable li'), function(key, value)
	{
		var row = key+1;
		$(this).find('.element').attr('data-row', row);
		$(this).find('.element label').attr('for', 'answer' + row);
		// if language is farsi then convert number to persian
		if($('html').attr('lang') === 'fa')
		{
			$(this).find('.element label.title b').text(row.toString().toFarsi());
		}
		else
		{
			$(this).find('.element label.title b').text(row);
		}
		$(this).find('.element .input').attr('id', 'answer' + row);
		$(this).find('.element .input').attr('name', 'answer' + row);
		// set true
		$(this).find('.element .true input').attr('id', 'true' + row);
		$(this).find('.element .true label').attr('for', 'true' + row);
		$(this).find('.element .true input').attr('name', 'true' + row);
		// set score
		$(this).find('.element .score input').attr('id', 'score' + row);
		$(this).find('.element .score input').attr('name', 'score' + row);
	});
}


/**
 * return count of question options exist in page
 * @return {[type]} [description]
 */
function countQuestionOpts(_fill)
{
	if(_fill)
	{
		_fill = 0;
		$.each($('.input-group.sortable .element .input[type="text"]'), function(key, value)
		{
			if($(this).val())
			{
				_fill++;
			}
		});

		return _fill;
	}
	return $('.input-group.sortable .element').length;
}


/**
 * search in tree;
 * @param  {[type]} _page [description]
 * @return {[type]}       [description]
 */
function treeSearch(_search, _notLoad)
{
	if(_notLoad && $('#tree-search').attr('data-loaded'))
	{
		return false;
	}
	$('#tree-search').attr('data-loaded', true);

	var path    = location.pathname;
	if(!_search)
	{
		_search = $('#tree-search').val();
	}
	path        = path.substr(0, path.indexOf('/add')) + '/add/tree';
	if(_search)
	{
		path = path+ '/search='+ _search;
	}

	Navigate(
	{
		data: false,
		url: path,
		nostate: true,
	});
}


/**
 * [completeProfileFill description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function completeProfileFill(_this)
{
	// create a clone form opts
	if(!window.TEMP)
	{
		window.TEMP = $('.input-group.sortable').clone();
	}
	// get options
	var dropValue      = $('#complete-profile-dropdown');
	var dropValues     = dropValue.find('option:checked').attr('data-value');
	if(dropValue.val())
	{
		if(dropValues)
		{
			var dropValueArray = dropValues.split(',');
			$('.input-group.sortable').addClass('editing');
			for (var i = 0; i < dropValueArray.length; i++)
			{
				addNewOpt(dropValue.val(), dropValueArray[i]);
				$('.input-group.sortable li[data-profile!="'+ dropValue.val() +'"]').remove();
			}
			setSortable(true);
		}
		detectPercentage();
	}
	else
	{
		completeProfileRevert();
	}
}


/**
 * revert before check complete profile exactly
 * @return {[type]} [description]
 */
function completeProfileRevert()
{
	if($('.input-group.sortable').hasClass('editing'))
	{
		$('.input-group.sortable').removeClass('editing');
		if(window.TEMP)
		{
			$('.input-group.sortable').replaceWith(window.TEMP);
			window.TEMP = null;
			setSortable();
		}
	}
	detectPercentage();
}


/**
 * [detectStep description]
 * @return {[type]} [description]
 */
function detectStep(_name)
{
	if(!_name && window.location.hash)
	{
		_name = 'step-' + (window.location.hash).substr(1);
	}
	// declare variables
	var sthis    = _name;
	var sAdd     = $('.page-progress #step-add:checkbox:checked').length;
	var sFilter  = $('.page-progress #step-filter:checkbox:checked').length;
	var sPublish = $('.page-progress #step-publish:checkbox:checked').length;

	switch(sthis)
	{
		default:
		case 'step-add':
			sthis = 'step-add';
			$('.page-progress #step-add').prop('checked', true).parent('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', false).parents('.checkbox').removeClass('active');
			$('.page-progress #step-publish').prop('checked', false).parents('.checkbox').removeClass('active');
			break;

		case 'step-filter':
			$('.page-progress #step-add').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-publish').prop('checked', false).parents('.checkbox').removeClass('active');
			break;

		case 'step-publish':
			$('.page-progress #step-add').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-publish').prop('checked', true).parents('.checkbox').addClass('active');
			break;
	}
	changeStep(sthis);
}


/**
 * [changeStep description]
 * @param  {[type]} _name [description]
 * @return {[type]}       [description]
 */
function changeStep(_name)
{
	switch(_name)
	{
		default:
		case 'step-add':
			$('.stepAdd').slideDown();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideUp();
			// window.location.hash = 'add';
			break;

		case 'step-filter':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideDown();
			$('.stepPublish').slideUp();
			// window.location.hash = 'filter';
			break;

		case 'step-publish':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideDown();
			// window.location.hash = 'publish';
			break;
	}

	_name = _name.substr(5);
	window.location.hash = _name;
	$('.page-progress').attr('data-current', _name);
	detectPercentage();
}


/**
 * detect percentage of current state
 * @return {[type]} [description]
 */
function detectPercentage(_submit)
{
	var percentage = 0;
	// if press submit then plus 10 percent
	// based on each type of page
	switch ($('.page-progress').attr('data-current'))
	{
		case 'add':
		case 'add_tree':
			if($('#title').val())
			{
				percentage += 15;
			}
			var optCount = countQuestionOpts()-1;
			optCount     = optCount<=2? 2: optCount;
			var optPercent = countQuestionOpts(true) * (30/optCount);
			if(optPercent > 30)
			{
				optPercent = 30;
			}
			percentage += optPercent;
			if(_submit)
			{
				percentage += 10;
			}
			break;

		case 'filter':
			percentage = 50;
			if(_submit)
			{
				percentage += 40;
			}
			break;

		case 'publish':
			percentage = 100;
			break;
	}
	// call draw func
	drawPercentage(percentage, '%');
}


/**
 * draw percentage of progress bar
 * @return {[type]} [description]
 */
function drawPercentage(_percent, _axis)
{
	if($('.page-progress').attr('fix'))
	{
		return;
	}
	if(_percent < 0 || _percent > 100)
	{
		return false;
	}
	var currentStep = $('.page-progress').attr('data-current');

	if(!$('.page-progress b').length)
	{
		$('.page-progress').append('<b></b>');
	}
	$('.page-progress b').width(_percent+_axis);
	// check chekcbox of this step
	if(currentStep == 'add')
	{
		if(_percent >= 50)
		{
			$('.page-progress [name="step-add"]').prop('checked', true);
		}
		else
		{
			$('.page-progress [name="step-add"]').prop('checked', false);
		}
	}
	else if(currentStep == 'filter')
	{
		if(_percent >= 90)
		{
			$('.page-progress [name="step-filter"]').prop('checked', true);
		}
		else
		{
			$('.page-progress [name="step-filter"]').prop('checked', false);
		}
	}
	else if(currentStep == 'publish')
	{
		// on some condition check checkbox after ending
	}
}


/**
 * simulate tree navigation and do not change url on this condition
 * @return {[type]} [description]
 */
function simulateTreeNavigation()
{
	$("nav.pagination a").click(function()
	{
		Navigate(
		{
			data: false,
			url: $(this).attr('href'),
			nostate: true,
		});
		return false;
	});
}


// ================================================================== @/add
// route(/\@\/add/, function()
route(/\@\/add(|\/[^\/]*)$/, function()
{
	// run textarea resizable
	// declare functions

	setSortable();
	detectPercentage();

	// run on input change and add new opt for this question
	$(this).on('input', '.input-group.sortable .element .input[type="text"]', function(event)
	{
		checkAddOpt();
	});

	// --------------------------------------------------------------------------------- Delete Elements
	// show and hide delete btn on special condition
	$(this).on('mouseenter', '.input-group.sortable .element', function()
	{
		// showQuestionOptsDel($(this).children('.delete'));
	}).on('focus', '.input-group.sortable .element input[type="text"]', function()
	{
		showQuestionOptsDel($(this).parent().children('.delete'));
	});

	// on keyup and press shift+del remove current record
	$(this).on('keyup', '.input-group.sortable .element input', function(e)
	{
		if(countQuestionOpts() > 2)
		{
			if(e.shiftKey && e.keyCode === 46)
			{
				$(this).parent().children('.delete').click();
			}
		}
		detectPercentage();
	});

	$(this).on('change', '#title', function()
	{
		detectPercentage();
	});

	// on press delete on each opt
	$(this).on('click', '.input-group.sortable .element .delete', function()
	{
		deleteQuestionOpts(this);
	}).on('keyup', '.input-group.sortable .element .delete', function(e)
	{
		if((e.shiftKey && e.keyCode === 46) || e.keyCode === 13)
		{
			$(this).parent().children('.delete').click();
		}
	});

	// --------------------------------------------------------------------------------- Tree
	$(this).on('input', '#tree-search', function(event)
	{
		var tree_search_timeout = $(this).data('tree-search-timeout');
		if(tree_search_timeout)
		{
			clearTimeout(tree_search_timeout);
		}
		var timeout = setTimeout(treeSearch.bind(this), 200);
		$(this).data('tree-search-timeout', timeout);
	});

	// if user change selection of each item
	$(this).on('change', '.tree-result-list > li > .options .checkbox', function(event)
	{
		// get list of checked item and create text from them
		var selectedOpts = $(this).parents('.options').find('input:checkbox:checked').map(function(){ return $(this).val();});
		$('[name="parent_tree_opt"]').val(selectedOpts.get());
	});

	// if($('#tree').is(':checked'))
	// {
	// 	// console.log('checkd default...');
	// 	// $('#tree-search').val($('[name="parent_tree_id"]').val());
	// 	// treeSearch();
	// }

	// --------------------------------------------------------------------------------- Complete profile
	// if remove complete profile checkbox, return to old status and rerun sortable
	$(this).on('change', '#complete-profile', function(event)
	{
		if (!this.checked)
		{
			// revert
			completeProfileRevert();
			// change dropdown to default value
			$('#complete-profile-dropdown').val('');
		}
	});

	// if any item of complete profile is selected, then fill item with profile values
	$(this).on('change', '#complete-profile-dropdown', function()
	{
		completeProfileFill();
	});


	$(this).on('click','button', function()
	{
		detectPercentage(true);
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});
}).once(function()
{
	simulateTreeNavigation();

	$('.page-progress input').on('click', function(e)
	{
		detectStep($(this).attr('name'));
		// e.stopPropagation();
		// return false;
	});
	// on init
	detectStep();


	// on open tree load content to it
	$(window).off("response:open");
	$(window).on("response:open", function(_obj, _name, _value)
	{
		// if open tree then fill with last qustions
		if(_name == 'tree' && _value == 'open')
		{
			treeSearch.call(null, null, true);
		}
	});


	// ------------------------------------------------------------------ Tree
	// if user click on title of each question
	$(document).off('change', '.tree-result-list > li > [name="parent_tree_id"]');
	$(document).on('change', '.tree-result-list > li > [name="parent_tree_id"]', function(event)
	{
		var selectedItem = $(this).parents('li').children('.options');
		if(selectedItem.is(':visible'))
		{
			// if want to close, close all tags
			$('.tree-result-list > li .options').slideUp();
		}
		else
		{
			$('.tree-result-list > li .options').slideUp();
			selectedItem.slideDown();
			// $('[name="parent_tree_id"]').val($(this).parent('li').attr('data-id'));
			$('.tree-result-list > li.active').removeClass('active');
			$(this).parents('li').addClass('active');
		}
	});


	// ------------------------------------------------------------------ File Preview
	//
	$(this).on('change', 'input[type="file"]', function(event)
	{
		var output = $(this).parents('.file').find('.preview');
		var imagePreview = showImagePreview(this, output);
	});
	// after complete loading, open cropbox
	$(window).off("cropBox:open");
	$(window).on("cropBox:open", function(_e, _el)
	{
		if(!_el)
		{
			return false;
		}
		$(_el).attr('data-modal', '');
		// start crop with this image
		// startCrop(_el);
	});
	// on click on preview of imagee
	$('body').on("click", ".file .preview", function(_e, _el)
	{
		startCrop(this);
	});
	// on click on preview of imagee
	$('body').on("click", "#modal-crop .btn", function(_e, _el)
	{
		// complete croping
		$('#modal-crop').trigger('close');
	});


});


// ************************************************************************************************************ Filter
/**
 * [calcFilterPrice description]
 * @return {[type]} [description]
 */
function calcFilterPrice()
{
	var totalEl      = $('.pay-info .price .value');
	var basePrice    = parseInt(totalEl.attr('data-basePrice'));
	// var totalPerson  = parseInt($('[data-range-bind="rangepersons"]').val());
	// var totalPerson  = $('#rangepersons').rangeSlider('to');
	var totalPerson  = $('#rangepersons').data('range-slider');
	if(totalPerson)
	{
		totalPerson = totalPerson.to;
	}
	else
	{
		totalPerson = 0;
	}
	// var totalPerson  = parseInt($('#rangepersons').val());

	var totalPercent = 0;
	var totalPrice   = 0;
	$('.badge.active[data-ratio]').each(function(index, el)
	{
		var currentRatio = parseInt($(el).attr('data-ratio'));
		totalPercent     += currentRatio;
	});
	// change percent to ratio
	totalPercent = totalPercent/100;
	totalPrice   = totalPerson + (totalPerson * totalPercent);
	totalPrice   = totalPrice * basePrice;

	// set value to show to enduser
	totalEl.text(totalPrice.toLocaleString());
}

// ================================================================== @/add/7pr/filter
// route(/\@\/add(|\/[^\/]*)$/, function()
route(/\@\/add\/.+\/filter$/, function()
{
	// if any item of complete profile is selected, then fill item with profile values
	$(this).bind('range-slider::change', '#rangepersons', function(_e, _min, _max)
	{
		calcFilterPrice.call(this);
	});

	// run rangeslider
}).once(function()
{
	console.log('once on filter....');
	calcFilterPrice.call(this);
	$(this).on('click','button', function()
	{
		detectPercentage(true);
	});
	detectPercentage();

	// on open tree load content to it
	$(window).off( "response:open");
	$(window).on( "response:open", function(_obj, _name, _value)
	{
		// console.log($(_obj).attr('data-group'));
		// console.log($(_obj).attr('data-response-group'));
		// console.log(_obj);
		// console.log(_name);
		// console.log(_value);
		calcFilterPrice.call(this);
	});
});

// ************************************************************************************************************ Publish
function runAutoComplete()
{
	$('.dropdown').autoComplete(
	{
		minChars: 0,
		// source: function(term, suggest)
		// {
		// 	term = term.toLowerCase();
		// 	var choices = ['ActionScript', 'AppleScript', 'Asp', 'PHP', 'CSS', 'JS'];
		// 	var matches = [];
		// 	for (i=0; i<choices.length; i++)
		// 		if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
		// 	suggest(matches);
		// },
		source: function(term, response)
		{
			try { xhr.abort(); } catch(e){}
			xhr = $.getJSON('/tag/', { q: term }, function(data){ response(data); });
			console.log(xhr);
		}
	});
	console.log('run...');
	$('.dropdown').on('keydown', function(e)
	{
		if(e.keyCode == 13)
		{
			saveAutoComplete();
			return false;
		}
	});
}

function saveAutoComplete()
{
	console.log('save');
}

// ================================================================== @/add/7pr/publish
// route(/\@\/add\/[\w.]+\/publish/, function()
route(/\@\/add\/.+\/publish$/, function()
{

}).once(function()
{
	console.log('once on publish....');
	runAutoComplete();
	detectPercentage();
});



// ================================================================== $
/**
 * [searchInPolls description]
 * @return {[type]} [description]
 */
function searchInPolls()
{
	var path = location.pathname;
	_search  = $('.search-box input').val();
	path     = path.substr(0, path.indexOf('/$')+2);
	if(_search)
	{
		path = path+ '/search='+ _search;
	}


	Navigate({ url: path, ajax:{method:'get', data:{'onlySearch' : true}}});
	// Navigate({ url: path});
}


route(/\$/, function()
{

}).once(function()
{
	// --------------------------------------------------------------------------------- Search
	$(this).off('input', '.search-box input');
	$(this).on('input', '.search-box input', function(event)
	{
		var search_timeout = $(this).attr('data-search-timeout');
		if(search_timeout)
		{
			clearTimeout(search_timeout);
		}
		var timeout = setTimeout(function(){ searchInPolls(); }, 500);
		$(this).attr('data-search-timeout', timeout);
	});

});



// Profile
route(/\@\/profile/, function() {
	var initial  = $('input[name="initial"]');
	var isNormal = false;

	// dblclick
	$(this).on('dblclick', '.element.has-data', function()
	{
		// if double clicked input has not class similar-tag
		if (!$(this).children('.input').hasClass('similar-tag')) {
			isNormal = true;
			initial.val( $(this).children('.input').val() );
		}
		$(this).removeClass('has-data').append(btns).children('.input').removeAttr('disabled').focus();
	});

	// click
	$(this).on('click', '.element.no-data', function()
	{
		// if clicked input has not class similar-tag
		if (!$(this).children('.input').hasClass('similar-tag')) {
			isNormal = true;
			initial.val("");
		}
		$(this).removeClass('no-data').append(btns).children('.input').removeAttr('disabled').focus();
	});

	 //  $(this).on('focus', '.element .input', function(event) {
	 //  	$(this).unbind('blur.sarshomarblur');
	 //  	$(this).bind('blur.sarshomarblur', function(e){
	 //  		$(this).unbind('blur.sarshomarblur');
	 //  		var element = $(this).parents('.element');
			// var val     = $(this).parents('.element').children('.input').val();
			// if ( isNormal )
			// {
			// 	if ( initial.val() )
			// 	{
			// 		element.addClass('has-data');
			// 	}
			// 	else
			// 	{
			// 		element.addClass('no-data');
			// 	}
			// 	element.children('.input').attr('disabled', '');
			// 	element.children('.btn').remove();
			// 	element.children('.input').val( initial.val() );
			// }
	 //  	});
	 //  });

	$(this).on('click', '.btn.save button', function(event) {
		var element = $(this).parents('.element');
		var val     = $(this).parents('.element').children('.input').val();
		var name    = $(this).parents('.element').children('.input').attr("name");
		$(this).ajaxify({
			ajax: {
				data: {
					'name': name,
					'value': val
				},
				abort: true,
				success: function(e, data, x) {
					if ( val && isNormal )
					{
						element.addClass('has-data');
						element.children('.input').attr('disabled', 'disabled');
						element.children('.btn').remove();
					}
					else if ( (!val) && isNormal)
					{
						element.addClass('no-data');
						element.children('.input').attr('disabled', 'disabled');
						element.children('.btn').remove();
					}
				},
				method: 'post'
			}
		});
	});

	$(this).on('click', '.btn.cancel button', function(event) {
		var element = $(this).parents('.element');
		var val     = $(this).parents('.element').children('.input').val();
		if ( initial.val() )
		{
			element.addClass('has-data');
		}
		else
		{
			element.addClass('no-data');
		}
		element.children('.input').attr('disabled', '');
		element.children('.btn').remove();
		element.children('.input').val( initial.val() );
	});
});


// pollsearch | Knowledge
// route(/^\/?(fa\/)?\$(.*)$/, function ()
route('*', function ()
{
	var change = 0;

	$(document).on('keyup', '.pollsearch', function(e)
	{
		var val = $(this).val();
		change  += 1;
		setTimeout(function()
		{

			if(change <= 1)
			{

				url = window.location.pathname;

				test = /\/search\=(.*)\/?/.test(url);

				if(test)
				{
					url = url.replace(/\/search\=[^\/]*\/?/, '/search=' + val + '/');
				}
				else
				{
					url = url + '/search=' + val + '/';
				}

				Navigate({ url: url });
			}
			change -= 1;
		}, 250);
	});

	$('.pollsearch').focus().val($('.pollsearch').val());
});


// Me | Profile
route('*', function ()
{
	$.each($('input.autocomplete'),function()
	{
		$(this).keyup(function(e)
		{
			name = $(this).attr('name');
			val  = $(this).val();

			$(this).ajaxify(
			{
				ajax:
				{
					method: 'post',
					url : '/',
					data:
					{
						'type'  : 'autocomplete',
						'data'  : name,
						'search': val
					},
					abort: true,
					success: function(e, data, x)
					{
						data = e.msg.callback;
						for (a in data)
						{
							console.log(data[a]['term_title']);
							console.log(data[a]['term_url']);
							console.log(data[a]['term_count']);
						}
					}
				}
			});
		});
	});
});



route('*', function ()
{
	$('.similar-tag').keypress(function (e)
	{
		// if Enter pressed disallow it and run add func
		if (e.which == 13)
		{
			var element_id = $(this).attr('id');
			addTag(element_id);
			return false;
		}
	});

	$('.similar-tag').change(function ()
	{
		// if Enter pressed disallow it and run add func
		var element_id = $(this).attr('id');
		addTag(element_id);
		return false;
	});

	$(document).on('click', '.btn-add-tags' , function () { addTag($(this).attr('element-id')); return false; });
	$(document).on('click', '.remove-tags', function ()
	{
		var span = $(this).parent();
		var split = $(this).attr('data-split');
		$('#' + split).val($('#'+ split).val().replace(span.text() + ',', ''));
		span.remove();
	});

	$('#features .wrapper .features li').on("mouseover", function (ev) { addClass( ev, this, 'in' ); });
	$('#features .wrapper .features li').on("mouseout", function (ev) { addClass( ev, this, 'out' );});

	// var tagDefault = $('#' + split).val();
	// $('#' + list).text('');
	// if (tagDefault)
	// {
	// 	$.each(tagDefault.split(', '), function (t, item)
	// 	{
	// 		if (item.trim())
	// 			$('#' + list).append("<span><i class='fa fa-times'></i>" + item + "</span>");
	// 	});
	// }

	// add tab support to cp
	$('.tabs li').click(function()
	{
		var _this     = $(this);
		var tabNum    = _this.attr('data-tab');
		var tabsItems = _this.parent().children('li');
		var tabGroup  = _this.parent().attr('data-group');
		var tabSelected;
		var tabItems
		// if use group find it else use default tab value
		if(tabGroup)
		{
			tabItems  = $('.tab[data-group="'+ tabGroup +'"]');
		}
		else
		{
			tabItems  = $('.tab');
		}

		// remove active class from all items and select clicked item
		tabsItems.removeClass('active');
		_this.addClass('active');

		if(tabNum)
		{
			tabSelected = tabItems.children("#tab-"+tabNum);
			// $('[id^=tab-]').not(tabSelected).css('display', "none");
			tabItems.children('[id^=tab-]').not(tabSelected).css('display', "none");
		}
		else
		{
			tabNum = _this.index()+1;
			tabSelected = tabItems.children("li:nth-child("+tabNum+")");
			tabItems.children('li').not(tabSelected).css('display', "none");
		}
		$(tabSelected).fadeIn(300);
	})
	// run click for first time and show content of active tab
	$(".tabs").each(function()
	{
		// if select one element as active select content of it
		if($(this).children('li.active').length == 1)
		{
			$(this).children('li.active').trigger("click");
			$('input[name="poll_type"], input[name="filter_type"]').val( $(this).children('li.active').data('tab') );
		}
		// else select first child
		else
		{
			$(this).children('li:first-child').trigger("click");
			$('input[name="poll_type"], input[name="filter_type"]').val($(this).children('li:first-child').data('tab'));
		}
	});

	// change poll_type on tabs click
	$('.tabs li').click(function(){
		$('input[name="poll_type"], input[name="filter_type"]').val( $(this).data('tab') );
	});

});


// -------------------------------------------------- Add Tag
function addTag(element_id)
{
	list  = $('#'+ element_id).attr('data-list');
	split = $('#'+ element_id).attr('data-split');

	var tag = $('#'+ element_id);
	var newTag = tag.val().trim();
	if (newTag)
	{
		var exist = false;
		$.each($('#'+ split).val().split(','), function (t, item)
		{
			if (item == newTag) { exist = t + 1; }
		});
		if (exist)
		{
			existEl = $("#" + list + " a:nth-child(" + exist + ")");
			existEl.addClass("tag-exist");
			setTimeout(function () { existEl.removeClass("tag-exist") }, 500);
		}
		else
		{
			$('#' + list).append("<a><i class='fa fa-times remove-tags' data-split='"+split+"'></i>" + newTag + "</a>");
			$('#' + split).val($('#' + split).val() + newTag + ',');
		}
	}
	tag.val('');
}


// --------------------------------- Sliders ---------------------------------

function pauseEvent(e)
{
	if (e.stopPropagation) e.stopPropagation();
	if (e.preventDefault) e.preventDefault();
	e.cancelBubble = true;
	e.returnValue = false;
	return false;
}

$(function()
{
	$('.input-slider').each(function(id, el){
		$(el).append($('<div style="width: 10px;height: 25px;background: #666;position: absolute;top: 5px;left: 100px;cursor:pointer"></div>'));

		var offset = $(el.children[0]).outerWidth(),
			inpEl = $(el.children[1]),
				maxim = inpEl.outerWidth() - 10,
				sl = $(el.children[2]);

		inpEl.css({height: 8, margin: '11px 0' });
		inpEl.on('mousedown, mouseup', function(ev) {
				setSlider(offset + ev.screenX - inpEl.offset().left);
				return pauseEvent(ev);
			});


		var dragStart = false, currentLeft;
		sl.on('mousedown', function (ev) {
			dragStart = ev.screenX;
			currentLeft = parseFloat(sl.css('left'));
			inpEl.focus();
			return pauseEvent(ev);
		});


		var minVal = inpEl.data('min'), maxVal = inpEl.data('max');
		if(minVal==undefined) minVal = 0;
		if(maxVal==undefined) maxVal = 100;

		$(document).on('mousemove', function (ev) {
			if (dragStart && ev.buttons !== 0) {
				setSlider(currentLeft + ev.screenX - dragStart);
				return pauseEvent(ev);
			}
		});

		function setSlider(val) {
			var newLeft = Math.max(Math.min(val, offset + maxim), offset);
			sl.css('left', newLeft);
			inpEl.val( parseInt((newLeft-offset)*(maxVal-minVal) / maxim)+minVal );
			inpEl.focus();
		}

		$(document).on('mouseup', function(){
			dragStart = false;
		});

		updateSlider();

		function updateSlider() {
			var val = parseFloat(inpEl.val());
			var newLeft = offset + ((val - minVal) / (maxVal - minVal)) * maxim;
			sl.css('left', newLeft);
		}
	});


});














// -------------------------------------------------- Features
var getDirection = function (ev, obj)
{
	var w = obj.offsetWidth,
		h = obj.offsetHeight,
		x = (ev.pageX - obj.offsetLeft - (w / 2) * (w > h ? (h / w) : 1)),
		y = (ev.pageY - obj.offsetTop - (h / 2) * (h > w ? (w / h) : 1)),
		d = Math.round( Math.atan2(y, x) / 1.57079633 + 5 ) % 4;

	return d;
};

var addClass = function ( ev, obj, state )
{
	var direction = getDirection( ev, obj ),
		class_suffix = "";

	obj.className = "";

	switch ( direction )
	{
			case 0 : class_suffix = '-top';    break;
			case 1 : class_suffix = '-right';  break;
			case 2 : class_suffix = '-bottom'; break;
			case 3 : class_suffix = '-left';   break;
	}

	obj.classList.add( state + class_suffix );
};




// contact form
route(/contact/, function()
{
	$('form').on('ajaxify:success',function(data, debug)
	{
		if(debug.status)
		{
			$('input').val('');
			$('textarea').val('');
		}
	});
});


runAllScripts();
/**
 * this function run all scripts and all subfunctions
 * @return {[type]} [description]
 */
function runAllScripts()
{
	// handle all shortkeys
	shortkey();
	// set all textarea resizable
	resizableTextarea();
	// run data-response detector
	runDataResponse();
	// open profile on getting focus
	openProfile();
	// allow to set fav
	setFav();


}


