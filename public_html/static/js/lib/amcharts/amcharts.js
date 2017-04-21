(function(){var d;window.AmCharts?d=window.AmCharts:(d={},window.AmCharts=d,d.themes={},d.maps={},d.inheriting={},d.charts=[],d.onReadyArray=[],d.useUTC=!1,d.updateRate=60,d.uid=0,d.lang={},d.translations={},d.mapTranslations={},d.windows={},d.initHandlers=[],d.amString="am",d.pmString="pm");d.Class=function(a){var b=function(){arguments[0]!==d.inheriting&&(this.events={},this.construct.apply(this,arguments))};a.inherits?(b.prototype=new a.inherits(d.inheriting),b.base=a.inherits.prototype,delete a.inherits):
(b.prototype.createEvents=function(){for(var a=0;a<arguments.length;a++)this.events[arguments[a]]=[]},b.prototype.listenTo=function(a,b,c){this.removeListener(a,b,c);a.events[b].push({handler:c,scope:this})},b.prototype.addListener=function(a,b,c){this.removeListener(this,a,b);a&&this.events[a]&&this.events[a].push({handler:b,scope:c})},b.prototype.removeListener=function(a,b,c){if(a&&a.events&&(a=a.events[b]))for(b=a.length-1;0<=b;b--)a[b].handler===c&&a.splice(b,1)},b.prototype.fire=function(a){for(var b=
this.events[a.type],c=0;c<b.length;c++){var d=b[c];d.handler.call(d.scope,a)}});for(var c in a)b.prototype[c]=a[c];return b};d.addChart=function(a){window.requestAnimationFrame?d.animationRequested||(d.animationRequested=!0,window.requestAnimationFrame(d.update)):d.updateInt||(d.updateInt=setInterval(function(){d.update()},Math.round(1E3/d.updateRate)));d.charts.push(a)};d.removeChart=function(a){for(var b=d.charts,c=b.length-1;0<=c;c--)b[c]==a&&b.splice(c,1);0===b.length&&(d.requestAnimation&&(window.cancelAnimationFrame(d.requestAnimation),
d.animationRequested=!1),d.updateInt&&(clearInterval(d.updateInt),d.updateInt=NaN))};d.isModern=!0;d.getIEVersion=function(){var a=0,b,c;"Microsoft Internet Explorer"==navigator.appName&&(b=navigator.userAgent,c=/MSIE ([0-9]{1,}[.0-9]{0,})/,null!==c.exec(b)&&(a=parseFloat(RegExp.$1)));return a};d.applyLang=function(a,b){var c=d.translations;b.dayNames=d.extend({},d.dayNames);b.shortDayNames=d.extend({},d.shortDayNames);b.monthNames=d.extend({},d.monthNames);b.shortMonthNames=d.extend({},d.shortMonthNames);
b.amString="am";b.pmString="pm";c&&(c=c[a])&&(d.lang=c,b.langObj=c,c.monthNames&&(b.dayNames=d.extend({},c.dayNames),b.shortDayNames=d.extend({},c.shortDayNames),b.monthNames=d.extend({},c.monthNames),b.shortMonthNames=d.extend({},c.shortMonthNames)),c.am&&(b.amString=c.am),c.pm&&(b.pmString=c.pm));d.amString=b.amString;d.pmString=b.pmString};d.IEversion=d.getIEVersion();9>d.IEversion&&0<d.IEversion&&(d.isModern=!1,d.isIE=!0);d.dx=0;d.dy=0;if(document.addEventListener||window.opera)d.isNN=!0,d.isIE=
!1,d.dx=.5,d.dy=.5;document.attachEvent&&(d.isNN=!1,d.isIE=!0,d.isModern||(d.dx=0,d.dy=0));window.chrome&&(d.chrome=!0);d.handleMouseUp=function(a){for(var b=d.charts,c=0;c<b.length;c++){var e=b[c];e&&e.handleReleaseOutside&&e.handleReleaseOutside(a)}};d.handleMouseMove=function(a){for(var b=d.charts,c=0;c<b.length;c++){var e=b[c];e&&e.handleMouseMove&&e.handleMouseMove(a)}};d.handleWheel=function(a){for(var b=d.charts,c=0;c<b.length;c++){var e=b[c];if(e&&e.mouseIsOver){(e.mouseWheelScrollEnabled||
e.mouseWheelZoomEnabled)&&e.handleWheel&&e.handleWheel(a);break}}};d.resetMouseOver=function(){for(var a=d.charts,b=0;b<a.length;b++){var c=a[b];c&&(c.mouseIsOver=!1)}};d.ready=function(a){d.onReadyArray.push(a)};d.handleLoad=function(){d.isReady=!0;for(var a=d.onReadyArray,b=0;b<a.length;b++){var c=a[b];isNaN(d.processDelay)?c():setTimeout(c,d.processDelay*b)}};d.addInitHandler=function(a,b){d.initHandlers.push({method:a,types:b})};d.callInitHandler=function(a){var b=d.initHandlers;if(d.initHandlers)for(var c=
0;c<b.length;c++){var e=b[c];e.types?d.isInArray(e.types,a.type)&&e.method(a):e.method(a)}};d.getUniqueId=function(){d.uid++;return"AmChartsEl-"+d.uid};d.isNN&&(document.addEventListener("mousemove",d.handleMouseMove),document.addEventListener("mouseup",d.handleMouseUp,!0),window.addEventListener("load",d.handleLoad,!0),window.addEventListener("DOMMouseScroll",d.handleWheel,!0),document.addEventListener("mousewheel",d.handleWheel,!0));d.isIE&&(document.attachEvent("onmousemove",d.handleMouseMove),
document.attachEvent("onmouseup",d.handleMouseUp),window.attachEvent("onload",d.handleLoad),document.attachEvent("onmousewheel",d.handleWheel));d.clear=function(){var a=d.charts;if(a)for(var b=a.length-1;0<=b;b--)a[b].clear();d.updateInt&&clearInterval(d.updateInt);d.requestAnimation&&window.cancelAnimationFrame(d.requestAnimation);d.charts=[];d.isNN&&(document.removeEventListener("mousemove",d.handleMouseMove,!0),document.removeEventListener("mouseup",d.handleMouseUp,!0),window.removeEventListener("load",
d.handleLoad,!0),window.removeEventListener("DOMMouseScroll",d.handleWheel,!0),document.removeEventListener("mousewheel",d.handleWheel,!0));d.isIE&&(document.detachEvent("onmousemove",d.handleMouseMove),document.detachEvent("onmouseup",d.handleMouseUp),window.detachEvent("onload",d.handleLoad))};d.makeChart=function(a,b,c){var e=b.type,h=b.theme;d.isString(h)&&(h=d.themes[h],b.theme=h);var f;switch(e){case "serial":f=new d.AmSerialChart(h);break;case "xy":f=new d.AmXYChart(h);break;case "pie":f=new d.AmPieChart(h);
break;case "radar":f=new d.AmRadarChart(h);break;case "gauge":f=new d.AmAngularGauge(h);break;case "funnel":f=new d.AmFunnelChart(h);break;case "map":f=new d.AmMap(h);break;case "stock":f=new d.AmStockChart(h);break;case "gantt":f=new d.AmGanttChart(h)}d.extend(f,b);d.isReady?isNaN(c)?f.write(a):setTimeout(function(){d.realWrite(f,a)},c):d.ready(function(){isNaN(c)?f.write(a):setTimeout(function(){d.realWrite(f,a)},c)});return f};d.realWrite=function(a,b){a.write(b)};d.updateCount=0;d.validateAt=
Math.round(d.updateRate/10);d.update=function(){var a=d.charts;d.updateCount++;var b=!1;d.updateCount==d.validateAt&&(b=!0,d.updateCount=0);if(a)for(var c=a.length-1;0<=c;c--)a[c].update&&a[c].update(),b&&(a[c].autoResize?a[c].validateSize&&a[c].validateSize():a[c].premeasure&&a[c].premeasure());window.requestAnimationFrame&&(d.requestAnimation=window.requestAnimationFrame(d.update))};d.bezierX=3;d.bezierY=6;"complete"==document.readyState&&d.handleLoad()})();(function(){var d=window.AmCharts;d.toBoolean=function(a,b){if(void 0===a)return b;switch(String(a).toLowerCase()){case "true":case "yes":case "1":return!0;case "false":case "no":case "0":case null:return!1;default:return!!a}};d.removeFromArray=function(a,b){var c;if(void 0!==b&&void 0!==a)for(c=a.length-1;0<=c;c--)a[c]==b&&a.splice(c,1)};d.getPath=function(){var a=document.getElementsByTagName("script");if(a)for(var b=0;b<a.length;b++){var c=a[b].src;if(-1!==c.search(/\/(amcharts|ammap)\.js/))return c.replace(/\/(amcharts|ammap)\.js.*/,
"/")}};d.normalizeUrl=function(a){return""!==a&&-1===a.search(/\/$/)?a+"/":a};d.isAbsolute=function(a){return 0===a.search(/^http[s]?:|^\//)};d.isInArray=function(a,b){for(var c=0;c<a.length;c++)if(a[c]==b)return!0;return!1};d.getDecimals=function(a){var b=0;isNaN(a)||(a=String(a),-1!=a.indexOf("e-")?b=Number(a.split("-")[1]):-1!=a.indexOf(".")&&(b=a.split(".")[1].length));return b};d.wordwrap=function(a,b,c,e){var h,f,g,k;a+="";if(1>b)return a;h=-1;for(a=(k=a.split(/\r\n|\n|\r/)).length;++h<a;k[h]+=
g){g=k[h];for(k[h]="";g.length>b;k[h]+=d.trim(g.slice(0,f))+((g=g.slice(f)).length?c:""))f=2==e||(f=g.slice(0,b+1).match(/\S*(\s)?$/))[1]?b:f.input.length-f[0].length||1==e&&b||f.input.length+(f=g.slice(b).match(/^\S*/))[0].length;g=d.trim(g)}return k.join(c)};d.trim=function(a){return a.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")};d.wrappedText=function(a,b,c,e,h,f,g,k){var l=d.text(a,b,c,e,h,f,g);if(l){var m=l.getBBox();if(m.width>k){var n="\n";d.isModern||(n="<br>");k=Math.floor(k/(m.width/
b.length));2<k&&(k-=2);b=d.wordwrap(b,k,n,!0);l.remove();l=d.text(a,b,c,e,h,f,g)}}return l};d.getStyle=function(a,b){var c="";if(document.defaultView&&document.defaultView.getComputedStyle)try{c=document.defaultView.getComputedStyle(a,"").getPropertyValue(b)}catch(e){}else a.currentStyle&&(b=b.replace(/\-(\w)/g,function(a,b){return b.toUpperCase()}),c=a.currentStyle[b]);return c};d.removePx=function(a){if(void 0!==a)return Number(a.substring(0,a.length-2))};d.getURL=function(a,b){if(a)if("_self"!=
b&&b)if("_top"==b&&window.top)window.top.location.href=a;else if("_parent"==b&&window.parent)window.parent.location.href=a;else if("_blank"==b)window.open(a);else{var c=document.getElementsByName(b)[0];c?c.src=a:(c=d.windows[b])?c.opener&&!c.opener.closed?c.location.href=a:d.windows[b]=window.open(a):d.windows[b]=window.open(a)}else window.location.href=a};d.ifArray=function(a){return a&&"object"==typeof a&&0<a.length?!0:!1};d.callMethod=function(a,b){var c;for(c=0;c<b.length;c++){var e=b[c];if(e){if(e[a])e[a]();
var d=e.length;if(0<d){var f;for(f=0;f<d;f++){var g=e[f];if(g&&g[a])g[a]()}}}}};d.toNumber=function(a){return"number"==typeof a?a:Number(String(a).replace(/[^0-9\-.]+/g,""))};d.toColor=function(a){if(""!==a&&void 0!==a)if(-1!=a.indexOf(",")){a=a.split(",");var b;for(b=0;b<a.length;b++){var c=a[b].substring(a[b].length-6,a[b].length);a[b]="#"+c}}else a=a.substring(a.length-6,a.length),a="#"+a;return a};d.toCoordinate=function(a,b,c){var e;void 0!==a&&(a=String(a),c&&c<b&&(b=c),e=Number(a),-1!=a.indexOf("!")&&
(e=b-Number(a.substr(1))),-1!=a.indexOf("%")&&(e=b*Number(a.substr(0,a.length-1))/100));return e};d.fitToBounds=function(a,b,c){a<b&&(a=b);a>c&&(a=c);return a};d.isDefined=function(a){return void 0===a?!1:!0};d.stripNumbers=function(a){return a.replace(/[0-9]+/g,"")};d.roundTo=function(a,b){if(0>b)return a;var c=Math.pow(10,b);return Math.round(a*c)/c};d.toFixed=function(a,b){var c=String(Math.round(a*Math.pow(10,b)));if(0<b){var e=c.length;if(e<b){var d;for(d=0;d<b-e;d++)c="0"+c}e=c.substring(0,
c.length-b);""===e&&(e=0);return e+"."+c.substring(c.length-b,c.length)}return String(c)};d.formatDuration=function(a,b,c,e,h,f){var g=d.intervals,k=f.decimalSeparator;if(a>=g[b].contains){var l=a-Math.floor(a/g[b].contains)*g[b].contains;"ss"==b?(l=d.formatNumber(l,f),1==l.split(k)[0].length&&(l="0"+l)):l=d.roundTo(l,f.precision);("mm"==b||"hh"==b)&&10>l&&(l="0"+l);c=l+""+e[b]+""+c;a=Math.floor(a/g[b].contains);b=g[b].nextInterval;return d.formatDuration(a,b,c,e,h,f)}"ss"==b&&(a=d.formatNumber(a,
f),1==a.split(k)[0].length&&(a="0"+a));("mm"==b||"hh"==b)&&10>a&&(a="0"+a);c=a+""+e[b]+""+c;if(g[h].count>g[b].count)for(a=g[b].count;a<g[h].count;a++)b=g[b].nextInterval,"ss"==b||"mm"==b||"hh"==b?c="00"+e[b]+""+c:"DD"==b&&(c="0"+e[b]+""+c);":"==c.charAt(c.length-1)&&(c=c.substring(0,c.length-1));return c};d.formatNumber=function(a,b,c,e,h){a=d.roundTo(a,b.precision);isNaN(c)&&(c=b.precision);var f=b.decimalSeparator;b=b.thousandsSeparator;var g;g=0>a?"-":"";a=Math.abs(a);var k=String(a),l=!1;-1!=
k.indexOf("e")&&(l=!0);0<=c&&!l&&(k=d.toFixed(a,c));var m="";if(l)m=k;else{var k=k.split("."),l=String(k[0]),n;for(n=l.length;0<=n;n-=3)m=n!=l.length?0!==n?l.substring(n-3,n)+b+m:l.substring(n-3,n)+m:l.substring(n-3,n);void 0!==k[1]&&(m=m+f+k[1]);void 0!==c&&0<c&&"0"!=m&&(m=d.addZeroes(m,f,c))}m=g+m;""===g&&!0===e&&0!==a&&(m="+"+m);!0===h&&(m+="%");return m};d.addZeroes=function(a,b,c){a=a.split(b);void 0===a[1]&&0<c&&(a[1]="0");return a[1].length<c?(a[1]+="0",d.addZeroes(a[0]+b+a[1],b,c)):void 0!==
a[1]?a[0]+b+a[1]:a[0]};d.scientificToNormal=function(a){var b;a=String(a).split("e");var c;if("-"==a[1].substr(0,1)){b="0.";for(c=0;c<Math.abs(Number(a[1]))-1;c++)b+="0";b+=a[0].split(".").join("")}else{var e=0;b=a[0].split(".");b[1]&&(e=b[1].length);b=a[0].split(".").join("");for(c=0;c<Math.abs(Number(a[1]))-e;c++)b+="0"}return b};d.toScientific=function(a,b){if(0===a)return"0";var c=Math.floor(Math.log(Math.abs(a))*Math.LOG10E),e=String(e).split(".").join(b);return String(e)+"e"+c};d.randomColor=
function(){return"#"+("00000"+(16777216*Math.random()<<0).toString(16)).substr(-6)};d.hitTest=function(a,b,c){var e=!1,h=a.x,f=a.x+a.width,g=a.y,k=a.y+a.height,l=d.isInRectangle;e||(e=l(h,g,b));e||(e=l(h,k,b));e||(e=l(f,g,b));e||(e=l(f,k,b));e||!0===c||(e=d.hitTest(b,a,!0));return e};d.isInRectangle=function(a,b,c){return a>=c.x-5&&a<=c.x+c.width+5&&b>=c.y-5&&b<=c.y+c.height+5?!0:!1};d.isPercents=function(a){if(-1!=String(a).indexOf("%"))return!0};d.formatValue=function(a,b,c,e,h,f,g,k){if(b){void 0===
h&&(h="");var l;for(l=0;l<c.length;l++){var m=c[l],n=b[m];void 0!==n&&(n=f?d.addPrefix(n,k,g,e):d.formatNumber(n,e),a=a.replace(new RegExp("\\[\\["+h+""+m+"\\]\\]","g"),n))}}return a};d.formatDataContextValue=function(a,b){if(a){var c=a.match(/\[\[.*?\]\]/g),e;for(e=0;e<c.length;e++){var d=c[e],d=d.substr(2,d.length-4);void 0!==b[d]&&(a=a.replace(new RegExp("\\[\\["+d+"\\]\\]","g"),b[d]))}}return a};d.massReplace=function(a,b){for(var c in b)if(b.hasOwnProperty(c)){var e=b[c];void 0===e&&(e="");a=
a.replace(c,e)}return a};d.cleanFromEmpty=function(a){return a.replace(/\[\[[^\]]*\]\]/g,"")};d.addPrefix=function(a,b,c,e,h){var f=d.formatNumber(a,e),g="",k,l,m;if(0===a)return"0";0>a&&(g="-");a=Math.abs(a);if(1<a)for(k=b.length-1;-1<k;k--){if(a>=b[k].number&&(l=a/b[k].number,m=Number(e.precision),1>m&&(m=1),c=d.roundTo(l,m),m=d.formatNumber(c,{precision:-1,decimalSeparator:e.decimalSeparator,thousandsSeparator:e.thousandsSeparator}),!h||l==c)){f=g+""+m+""+b[k].prefix;break}}else for(k=0;k<c.length;k++)if(a<=
c[k].number){l=a/c[k].number;m=Math.abs(Math.floor(Math.log(l)*Math.LOG10E));l=d.roundTo(l,m);f=g+""+l+""+c[k].prefix;break}return f};d.remove=function(a){a&&a.remove()};d.getEffect=function(a){">"==a&&(a="easeOutSine");"<"==a&&(a="easeInSine");"elastic"==a&&(a="easeOutElastic");return a};d.getObjById=function(a,b){var c,e;for(e=0;e<a.length;e++){var d=a[e];if(d.id==b){c=d;break}}return c};d.applyTheme=function(a,b,c){b||(b=d.theme);try{b=JSON.parse(JSON.stringify(b))}catch(e){}b&&b[c]&&d.extend(a,
b[c])};d.isString=function(a){return"string"==typeof a?!0:!1};d.extend=function(a,b,c){var e;a||(a={});for(e in b)c?a.hasOwnProperty(e)||(a[e]=b[e]):a[e]=b[e];return a};d.copyProperties=function(a,b){for(var c in a)a.hasOwnProperty(c)&&"events"!=c&&void 0!==a[c]&&"function"!=typeof a[c]&&"cname"!=c&&(b[c]=a[c])};d.processObject=function(a,b,c,e){if(!1===a instanceof b&&(a=e?d.extend(new b(c),a):d.extend(a,new b(c),!0),a.listeners))for(var h in a.listeners)b=a.listeners[h],a.addListener(b.event,b.method);
return a};d.fixNewLines=function(a){var b=RegExp("\\n","g");a&&(a=a.replace(b,"<br />"));return a};d.fixBrakes=function(a){if(d.isModern){var b=RegExp("<br>","g");a&&(a=a.replace(b,"\n"))}else a=d.fixNewLines(a);return a};d.deleteObject=function(a,b){if(a){if(void 0===b||null===b)b=20;if(0!==b)if("[object Array]"===Object.prototype.toString.call(a))for(var c=0;c<a.length;c++)d.deleteObject(a[c],b-1),a[c]=null;else if(a&&!a.tagName)try{for(c in a.theme=null,a)a[c]&&("object"==typeof a[c]&&d.deleteObject(a[c],
b-1),"function"!=typeof a[c]&&(a[c]=null))}catch(e){}}};d.bounce=function(a,b,c,e,d){return(b/=d)<1/2.75?7.5625*e*b*b+c:b<2/2.75?e*(7.5625*(b-=1.5/2.75)*b+.75)+c:b<2.5/2.75?e*(7.5625*(b-=2.25/2.75)*b+.9375)+c:e*(7.5625*(b-=2.625/2.75)*b+.984375)+c};d.easeInOutQuad=function(a,b,c,e,d){b/=d/2;if(1>b)return e/2*b*b+c;b--;return-e/2*(b*(b-2)-1)+c};d.easeInSine=function(a,b,c,e,d){return-e*Math.cos(b/d*(Math.PI/2))+e+c};d.easeOutSine=function(a,b,c,e,d){return e*Math.sin(b/d*(Math.PI/2))+c};d.easeOutElastic=
function(a,b,c,e,d){a=1.70158;var f=0,g=e;if(0===b)return c;if(1==(b/=d))return c+e;f||(f=.3*d);g<Math.abs(e)?(g=e,a=f/4):a=f/(2*Math.PI)*Math.asin(e/g);return g*Math.pow(2,-10*b)*Math.sin(2*(b*d-a)*Math.PI/f)+e+c};d.fixStepE=function(a){a=a.toExponential(0).split("e");var b=Number(a[1]);9==Number(a[0])&&b++;return d.generateNumber(1,b)};d.generateNumber=function(a,b){var c="",e;e=0>b?Math.abs(b)-1:Math.abs(b);var d;for(d=0;d<e;d++)c+="0";return 0>b?Number("0."+c+String(a)):Number(String(a)+c)};d.setCN=
function(a,b,c,e){if(a.addClassNames&&b&&(b=b.node)&&c){var d=b.getAttribute("class");a=a.classNamePrefix+"-";e&&(a="");d?b.setAttribute("class",d+" "+a+c):b.setAttribute("class",a+c)}};d.removeCN=function(a,b,c){b&&(b=b.node)&&c&&(b=b.classList)&&b.remove(a.classNamePrefix+"-"+c)};d.parseDefs=function(a,b){for(var c in a){var e=typeof a[c];if(0<a[c].length&&"object"==e)for(var h=0;h<a[c].length;h++)e=document.createElementNS(d.SVG_NS,c),b.appendChild(e),d.parseDefs(a[c][h],e);else"object"==e?(e=
document.createElementNS(d.SVG_NS,c),b.appendChild(e),d.parseDefs(a[c],e)):b.setAttribute(c,a[c])}}})();(function(){var d=window.AmCharts;d.AxisBase=d.Class({construct:function(a){this.createEvents("clickItem","rollOverItem","rollOutItem");this.titleDY=this.y=this.x=this.dy=this.dx=0;this.axisThickness=1;this.axisColor="#000000";this.axisAlpha=1;this.gridCount=this.tickLength=5;this.gridAlpha=.15;this.gridThickness=1;this.gridColor="#000000";this.dashLength=0;this.labelFrequency=1;this.showLastLabel=this.showFirstLabel=!0;this.fillColor="#FFFFFF";this.fillAlpha=0;this.labelsEnabled=!0;this.labelRotation=
0;this.autoGridCount=!0;this.offset=0;this.guides=[];this.visible=!0;this.counter=0;this.guides=[];this.ignoreAxisWidth=this.inside=!1;this.minHorizontalGap=75;this.minVerticalGap=35;this.titleBold=!0;this.minorGridEnabled=!1;this.minorGridAlpha=.07;this.autoWrap=!1;this.titleAlign="middle";this.labelOffset=0;this.bcn="axis-";this.centerLabels=!1;this.firstDayOfWeek=1;this.centerLabelOnFullPeriod=this.markPeriodChange=this.boldPeriodBeginning=!0;this.periods=[{period:"fff",count:1},{period:"fff",
count:5},{period:"fff",count:10},{period:"fff",count:50},{period:"fff",count:100},{period:"fff",count:500},{period:"ss",count:1},{period:"ss",count:5},{period:"ss",count:10},{period:"ss",count:30},{period:"mm",count:1},{period:"mm",count:5},{period:"mm",count:10},{period:"mm",count:30},{period:"hh",count:1},{period:"hh",count:3},{period:"hh",count:6},{period:"hh",count:12},{period:"DD",count:1},{period:"DD",count:2},{period:"DD",count:3},{period:"DD",count:4},{period:"DD",count:5},{period:"WW",count:1},
{period:"MM",count:1},{period:"MM",count:2},{period:"MM",count:3},{period:"MM",count:6},{period:"YYYY",count:1},{period:"YYYY",count:2},{period:"YYYY",count:5},{period:"YYYY",count:10},{period:"YYYY",count:50},{period:"YYYY",count:100}];this.dateFormats=[{period:"fff",format:"NN:SS.QQQ"},{period:"ss",format:"JJ:NN:SS"},{period:"mm",format:"JJ:NN"},{period:"hh",format:"JJ:NN"},{period:"DD",format:"MMM DD"},{period:"WW",format:"MMM DD"},{period:"MM",format:"MMM"},{period:"YYYY",format:"YYYY"}];this.nextPeriod=
{fff:"ss",ss:"mm",mm:"hh",hh:"DD",DD:"MM",MM:"YYYY"};d.applyTheme(this,a,"AxisBase")},zoom:function(a,b){this.start=a;this.end=b;this.dataChanged=!0;this.draw()},fixAxisPosition:function(){var a=this.position;"H"==this.orientation?("left"==a&&(a="bottom"),"right"==a&&(a="top")):("bottom"==a&&(a="left"),"top"==a&&(a="right"));this.position=a},init:function(){this.createBalloon()},draw:function(){var a=this.chart;this.prevBY=this.prevBX=NaN;this.allLabels=[];this.counter=0;this.destroy();this.fixAxisPosition();
this.setBalloonBounds();this.labels=[];var b=a.container,c=b.set();a.gridSet.push(c);this.set=c;b=b.set();a.axesLabelsSet.push(b);this.labelsSet=b;this.axisLine=new this.axisRenderer(this);this.autoGridCount?("V"==this.orientation?(a=this.height/this.minVerticalGap,3>a&&(a=3)):a=this.width/this.minHorizontalGap,this.gridCountR=Math.max(a,1)):this.gridCountR=this.gridCount;this.axisWidth=this.axisLine.axisWidth;this.addTitle()},setOrientation:function(a){this.orientation=a?"H":"V"},addTitle:function(){var a=
this.title;this.titleLabel=null;if(a){var b=this.chart,c=this.titleColor;void 0===c&&(c=b.color);var e=this.titleFontSize;isNaN(e)&&(e=b.fontSize+1);a=d.text(b.container,a,c,b.fontFamily,e,this.titleAlign,this.titleBold);d.setCN(b,a,this.bcn+"title");this.titleLabel=a}},positionTitle:function(){var a=this.titleLabel;if(a){var b,c,e=this.labelsSet,h={};0<e.length()?h=e.getBBox():(h.x=0,h.y=0,h.width=this.width,h.height=this.height,d.VML&&(h.y+=this.y,h.x+=this.x));e.push(a);var e=h.x,f=h.y;d.VML&&
(f-=this.y,e-=this.x);var g=h.width,h=h.height,k=this.width,l=this.height,m=0,n=a.getBBox().height/2,q=this.inside,p=this.titleAlign;switch(this.position){case "top":b="left"==p?-1:"right"==p?k:k/2;c=f-10-n;break;case "bottom":b="left"==p?-1:"right"==p?k:k/2;c=f+h+10+n;break;case "left":b=e-10-n;q&&(b-=5);m=-90;c=("left"==p?l+1:"right"==p?-1:l/2)+this.titleDY;break;case "right":b=e+g+10+n,q&&(b+=7),c=("left"==p?l+2:"right"==p?-2:l/2)+this.titleDY,m=-90}this.marginsChanged?(a.translate(b,c),this.tx=
b,this.ty=c):a.translate(this.tx,this.ty);this.marginsChanged=!1;isNaN(this.titleRotation)||(m=this.titleRotation);0!==m&&a.rotate(m)}},pushAxisItem:function(a,b){var c=this,e=a.graphics();0<e.length()&&(b?c.labelsSet.push(e):c.set.push(e));if(e=a.getLabel())c.labelsSet.push(e),e.click(function(b){c.handleMouse(b,a,"clickItem")}).touchend(function(b){c.handleMouse(b,a,"clickItem")}).mouseover(function(b){c.handleMouse(b,a,"rollOverItem")}).mouseout(function(b){c.handleMouse(b,a,"rollOutItem")})},
handleMouse:function(a,b,c){this.fire({type:c,value:b.value,serialDataItem:b.serialDataItem,axis:this,target:b.label,chart:this.chart,event:a})},addGuide:function(a){for(var b=this.guides,c=!1,e=b.length,h=0;h<b.length;h++)b[h]==a&&(c=!0,e=h);a=d.processObject(a,d.Guide,this.theme);a.id||(a.id="guideAuto"+e+"_"+(new Date).getTime());c||b.push(a)},removeGuide:function(a){var b=this.guides,c;for(c=0;c<b.length;c++)b[c]==a&&b.splice(c,1)},handleGuideOver:function(a){clearTimeout(this.chart.hoverInt);
var b=a.graphics.getBBox(),c=this.x+b.x+b.width/2,b=this.y+b.y+b.height/2,e=a.fillColor;void 0===e&&(e=a.lineColor);this.chart.showBalloon(a.balloonText,e,!0,c,b)},handleGuideOut:function(){this.chart.hideBalloon()},addEventListeners:function(a,b){var c=this;a.mouseover(function(){c.handleGuideOver(b)});a.touchstart(function(){c.handleGuideOver(b)});a.mouseout(function(){c.handleGuideOut(b)})},getBBox:function(){var a;this.labelsSet&&(a=this.labelsSet.getBBox());a?d.VML||(a={x:a.x+this.x,y:a.y+this.y,
width:a.width,height:a.height}):a={x:0,y:0,width:0,height:0};return a},destroy:function(){d.remove(this.set);d.remove(this.labelsSet);var a=this.axisLine;a&&d.remove(a.axisSet);d.remove(this.grid0)},chooseMinorFrequency:function(a){for(var b=10;0<b;b--)if(a/b==Math.round(a/b))return a/b},parseDatesDraw:function(){var a,b=this.chart,c=this.showFirstLabel,e=this.showLastLabel,h,f="",g=d.extractPeriod(this.minPeriod),k=d.getPeriodDuration(g.period,g.count),l,m,n,q,p,t=this.firstDayOfWeek,r=this.boldPeriodBeginning;
a=this.minorGridEnabled;var w,y=this.gridAlpha,x,u=this.choosePeriod(0),A=u.period,u=u.count,z=d.getPeriodDuration(A,u);z<k&&(A=g.period,u=g.count,z=k);g=A;"WW"==g&&(g="DD");this.stepWidth=this.getStepWidth(this.timeDifference);var B=Math.ceil(this.timeDifference/z)+5,D=l=d.resetDateToMin(new Date(this.startTime-z),A,u,t).getTime();if(g==A&&1==u&&this.centerLabelOnFullPeriod||this.autoWrap||this.centerLabels)n=z*this.stepWidth,this.autoWrap&&!this.centerLabels&&(n=-n);this.cellWidth=k*this.stepWidth;
q=Math.round(l/z);k=-1;q/2==Math.round(q/2)&&(k=-2,l-=z);q=this.firstTime;var C=0,K=0;a&&1<u&&(w=this.chooseMinorFrequency(u),x=d.getPeriodDuration(A,w),"DD"==A&&(x+=d.getPeriodDuration("hh")),"fff"==A&&(x=1));if(0<this.gridCountR)for(B-5-k>this.autoRotateCount&&!isNaN(this.autoRotateAngle)&&(this.labelRotationR=this.autoRotateAngle),a=k;a<=B;a++){p=q+z*(a+Math.floor((D-q)/z))-C;"DD"==A&&(p+=36E5);p=d.resetDateToMin(new Date(p),A,u,t).getTime();"MM"==A&&(h=(p-l)/z,1.5<=(p-l)/z&&(p=p-(h-1)*z+d.getPeriodDuration("DD",
3),p=d.resetDateToMin(new Date(p),A,1).getTime(),C+=z));h=(p-this.startTime)*this.stepWidth;if("radar"==b.type){if(h=this.axisWidth-h,0>h||h>this.axisWidth)continue}else this.rotate?"date"==this.type&&"middle"==this.gridPosition&&(K=-z*this.stepWidth/2):"date"==this.type&&(h=this.axisWidth-h);f=!1;this.nextPeriod[g]&&(f=this.checkPeriodChange(this.nextPeriod[g],1,p,l,g));l=!1;f&&this.markPeriodChange?(f=this.dateFormatsObject[this.nextPeriod[g]],this.twoLineMode&&(f=this.dateFormatsObject[g]+"\n"+
f,f=d.fixBrakes(f)),l=!0):f=this.dateFormatsObject[g];r||(l=!1);this.currentDateFormat=f;f=d.formatDate(new Date(p),f,b);if(a==k&&!c||a==B&&!e)f=" ";this.labelFunction&&(f=this.labelFunction(f,new Date(p),this,A,u,m).toString());this.boldLabels&&(l=!0);m=new this.axisItemRenderer(this,h,f,!1,n,K,!1,l);this.pushAxisItem(m);m=l=p;if(!isNaN(w))for(h=1;h<u;h+=w)this.gridAlpha=this.minorGridAlpha,f=p+x*h,f=d.resetDateToMin(new Date(f),A,w,t).getTime(),f=new this.axisItemRenderer(this,(f-this.startTime)*
this.stepWidth,void 0,void 0,void 0,void 0,void 0,void 0,void 0,!0),this.pushAxisItem(f);this.gridAlpha=y}},choosePeriod:function(a){var b=d.getPeriodDuration(this.periods[a].period,this.periods[a].count),c=this.periods;return this.timeDifference<b&&0<a?c[a-1]:Math.ceil(this.timeDifference/b)<=this.gridCountR?c[a]:a+1<c.length?this.choosePeriod(a+1):c[a]},getStepWidth:function(a){var b;this.startOnAxis?(b=this.axisWidth/(a-1),1==a&&(b=this.axisWidth)):b=this.axisWidth/a;return b},timeZoom:function(a,
b){this.startTime=a;this.endTime=b},minDuration:function(){var a=d.extractPeriod(this.minPeriod);return d.getPeriodDuration(a.period,a.count)},checkPeriodChange:function(a,b,c,e,h){c=new Date(c);var f=new Date(e),g=this.firstDayOfWeek;e=b;"DD"==a&&(b=1);c=d.resetDateToMin(c,a,b,g).getTime();b=d.resetDateToMin(f,a,b,g).getTime();return"DD"==a&&"hh"!=h&&c-b<d.getPeriodDuration(a,e)-d.getPeriodDuration("hh",1)?!1:c!=b?!0:!1},generateDFObject:function(){this.dateFormatsObject={};var a;for(a=0;a<this.dateFormats.length;a++){var b=
this.dateFormats[a];this.dateFormatsObject[b.period]=b.format}},hideBalloon:function(){this.balloon&&this.balloon.hide&&this.balloon.hide();this.prevBY=this.prevBX=NaN},formatBalloonText:function(a){return a},showBalloon:function(a,b,c,e){var d=this.offset;switch(this.position){case "bottom":b=this.height+d;break;case "top":b=-d;break;case "left":a=-d;break;case "right":a=this.width+d}c||(c=this.currentDateFormat);if("V"==this.orientation){if(0>b||b>this.height)return;if(isNaN(b)){this.hideBalloon();
return}b=this.adjustBalloonCoordinate(b,e);e=this.coordinateToValue(b)}else{if(0>a||a>this.width)return;if(isNaN(a)){this.hideBalloon();return}a=this.adjustBalloonCoordinate(a,e);e=this.coordinateToValue(a)}var f;if(d=this.chart.chartCursor)f=d.index;if(this.balloon&&void 0!==e&&this.balloon.enabled){if(this.balloonTextFunction){if("date"==this.type||!0===this.parseDates)e=new Date(e);e=this.balloonTextFunction(e)}else this.balloonText?e=this.formatBalloonText(this.balloonText,f,c):isNaN(e)||(e=this.formatValue(e,
c));if(a!=this.prevBX||b!=this.prevBY)this.balloon.setPosition(a,b),this.prevBX=a,this.prevBY=b,e&&this.balloon.showBalloon(e)}},adjustBalloonCoordinate:function(a){return a},createBalloon:function(){var a=this.chart,b=a.chartCursor;b&&(b=b.cursorPosition,"mouse"!=b&&(this.stickBalloonToCategory=!0),"start"==b&&(this.stickBalloonToStart=!0),"ValueAxis"==this.cname&&(this.stickBalloonToCategory=!1));this.balloon&&(this.balloon.destroy&&this.balloon.destroy(),d.extend(this.balloon,a.balloon,!0))},setBalloonBounds:function(){var a=
this.balloon;if(a){var b=this.chart;a.cornerRadius=0;a.shadowAlpha=0;a.borderThickness=1;a.borderAlpha=1;a.adjustBorderColor=!1;a.showBullet=!1;this.balloon=a;a.chart=b;a.mainSet=b.plotBalloonsSet;a.pointerWidth=this.tickLength;if(this.parseDates||"date"==this.type)a.pointerWidth=0;a.className=this.id;b="V";"V"==this.orientation&&(b="H");this.stickBalloonToCategory||(a.animationDuration=0);var c,e,d,f,g=this.inside,k=this.width,l=this.height;switch(this.position){case "bottom":c=0;e=k;g?(d=0,f=l):
(d=l,f=l+1E3);break;case "top":c=0;e=k;g?(d=0,f=l):(d=-1E3,f=0);break;case "left":d=0;f=l;g?(c=0,e=k):(c=-1E3,e=0);break;case "right":d=0,f=l,g?(c=0,e=k):(c=k,e=k+1E3)}a.drop||(a.pointerOrientation=b);a.setBounds(c,d,e,f)}}})})();(function(){var d=window.AmCharts;d.ValueAxis=d.Class({inherits:d.AxisBase,construct:function(a){this.cname="ValueAxis";this.createEvents("axisChanged","logarithmicAxisFailed","axisZoomed","axisIntZoomed");d.ValueAxis.base.construct.call(this,a);this.dataChanged=!0;this.stackType="none";this.position="left";this.unitPosition="right";this.includeAllValues=this.recalculateToPercents=this.includeHidden=this.includeGuidesInMinMax=this.integersOnly=!1;this.durationUnits={DD:"d. ",hh:":",mm:":",ss:""};
this.scrollbar=!1;this.baseValue=0;this.radarCategoriesEnabled=!0;this.axisFrequency=1;this.gridType="polygons";this.useScientificNotation=!1;this.axisTitleOffset=10;this.pointPosition="axis";this.minMaxMultiplier=1;this.logGridLimit=2;this.totalTextOffset=this.treatZeroAs=0;this.minPeriod="ss";this.relativeStart=0;this.relativeEnd=1;d.applyTheme(this,a,this.cname)},updateData:function(){0>=this.gridCountR&&(this.gridCountR=1);this.totals=[];this.data=this.chart.chartData;var a=this.chart;"xy"!=a.type&&
(this.stackGraphs("smoothedLine"),this.stackGraphs("line"),this.stackGraphs("column"),this.stackGraphs("step"));this.recalculateToPercents&&this.recalculate();this.synchronizationMultiplier&&this.synchronizeWith?(d.isString(this.synchronizeWith)&&(this.synchronizeWith=a.getValueAxisById(this.synchronizeWith)),this.synchronizeWith&&(this.synchronizeWithAxis(this.synchronizeWith),this.foundGraphs=!0)):(this.foundGraphs=!1,this.getMinMax(),0===this.start&&this.end==this.data.length-1&&isNaN(this.minZoom)&&
isNaN(this.maxZoom)&&(this.fullMin=this.min,this.fullMax=this.max,"date"!=this.type&&this.strictMinMax&&(isNaN(this.minimum)||(this.fullMin=this.minimum),isNaN(this.maximum)||(this.fullMax=this.maximum)),this.logarithmic&&(this.fullMin=this.logMin,0===this.fullMin&&(this.fullMin=this.treatZeroAs)),"date"==this.type&&(this.minimumDate||(this.fullMin=this.minRR),this.maximumDate||(this.fullMax=this.maxRR),this.strictMinMax&&(this.minimumDate&&(this.fullMin=this.minimumDate.getTime()),this.maximumDate&&
(this.fullMax=this.maximumDate.getTime())))))},draw:function(){d.ValueAxis.base.draw.call(this);var a=this.chart,b=this.set;this.labelRotationR=this.labelRotation;d.setCN(a,this.set,"value-axis value-axis-"+this.id);d.setCN(a,this.labelsSet,"value-axis value-axis-"+this.id);d.setCN(a,this.axisLine.axisSet,"value-axis value-axis-"+this.id);var c=this.type;"duration"==c&&(this.duration="ss");!0===this.dataChanged&&(this.updateData(),this.dataChanged=!1);"date"==c&&(this.logarithmic=!1,this.min=this.minRR,
this.max=this.maxRR,this.reversed=!1,this.getDateMinMax());if(this.logarithmic){var e=this.treatZeroAs,h=this.getExtremes(0,this.data.length-1).min;!isNaN(this.minimum)&&this.minimum<h&&(h=this.minimum);this.logMin=h;this.minReal<h&&(this.minReal=h);isNaN(this.minReal)&&(this.minReal=h);0<e&&0===h&&(this.minReal=h=e);if(0>=h||0>=this.minimum){this.fire({type:"logarithmicAxisFailed",chart:a});return}}this.grid0=null;var f,g,k=a.dx,l=a.dy,e=!1,h=this.logarithmic;if(isNaN(this.min)||isNaN(this.max)||
!this.foundGraphs||Infinity==this.min||-Infinity==this.max)e=!0;else{"date"==this.type&&this.min==this.max&&(this.max+=this.minDuration(),this.min-=this.minDuration());var m=this.labelFrequency,n=this.showFirstLabel,q=this.showLastLabel,p=1,t=0;this.minCalc=this.min;this.maxCalc=this.max;if(this.strictMinMax&&(isNaN(this.minimum)||(this.min=this.minimum),isNaN(this.maximum)||(this.max=this.maximum),this.min==this.max))return;isNaN(this.minZoom)||(this.minReal=this.min=this.minZoom);isNaN(this.maxZoom)||
(this.max=this.maxZoom);if(this.logarithmic){g=Math.log(this.fullMax)*Math.LOG10E-Math.log(this.fullMin)*Math.LOG10E;var r=Math.log(this.max)/Math.LN10-Math.log(this.fullMin)*Math.LOG10E;this.relativeStart=d.roundTo((Math.log(this.minReal)/Math.LN10-Math.log(this.fullMin)*Math.LOG10E)/g,5);this.relativeEnd=d.roundTo(r/g,5)}else this.relativeStart=d.roundTo(d.fitToBounds((this.min-this.fullMin)/(this.fullMax-this.fullMin),0,1),5),this.relativeEnd=d.roundTo(d.fitToBounds((this.max-this.fullMin)/(this.fullMax-
this.fullMin),0,1),5);var r=Math.round((this.maxCalc-this.minCalc)/this.step)+1,w;!0===h?(w=Math.log(this.max)*Math.LOG10E-Math.log(this.minReal)*Math.LOG10E,this.stepWidth=this.axisWidth/w,w>this.logGridLimit&&(r=Math.ceil(Math.log(this.max)*Math.LOG10E)+1,t=Math.round(Math.log(this.minReal)*Math.LOG10E),r>this.gridCountR&&(p=Math.ceil(r/this.gridCountR)))):this.stepWidth=this.axisWidth/(this.max-this.min);var y=0;1>this.step&&-1<this.step&&(y=d.getDecimals(this.step));this.integersOnly&&(y=0);y>
this.maxDecCount&&(y=this.maxDecCount);var x=this.precision;isNaN(x)||(y=x);isNaN(this.maxZoom)&&(this.max=d.roundTo(this.max,this.maxDecCount),this.min=d.roundTo(this.min,this.maxDecCount));g={};g.precision=y;g.decimalSeparator=a.nf.decimalSeparator;g.thousandsSeparator=a.nf.thousandsSeparator;this.numberFormatter=g;var u;this.exponential=!1;for(g=t;g<r;g+=p){var A=d.roundTo(this.step*g+this.min,y);-1!=String(A).indexOf("e")&&(this.exponential=!0)}this.duration&&(this.maxInterval=d.getMaxInterval(this.max,
this.duration));var y=this.step,z,A=this.minorGridAlpha;this.minorGridEnabled&&(z=this.getMinorGridStep(y,this.stepWidth*y));if(this.autoGridCount||0!==this.gridCount)if("date"==c)this.generateDFObject(),this.timeDifference=this.max-this.min,this.maxTime=this.lastTime=this.max,this.startTime=this.firstTime=this.min,this.parseDatesDraw();else for(r>=this.autoRotateCount&&!isNaN(this.autoRotateAngle)&&(this.labelRotationR=this.autoRotateAngle),c=this.minCalc,h&&(r++,c=this.maxCalc-r*y),this.gridCountReal=
r,g=this.startCount=t;g<r;g+=p)if(t=y*g+c,t=d.roundTo(t,this.maxDecCount+1),!this.integersOnly||Math.round(t)==t)if(isNaN(x)||Number(d.toFixed(t,x))==t){if(!0===h)if(w>this.logGridLimit)t=Math.pow(10,g);else if(0>=t&&(t=c+y*g+y/2,0>=t))continue;u=this.formatValue(t,!1,g);Math.round(g/m)!=g/m&&(u=void 0);if(0===g&&!n||g==r-1&&!q)u=" ";f=this.getCoordinate(t);var B;this.rotate&&this.autoWrap&&(B=this.stepWidth*y-10);u=new this.axisItemRenderer(this,f,u,void 0,B,void 0,void 0,this.boldLabels);this.pushAxisItem(u);
if(t==this.baseValue&&"radar"!=a.type){var D,C,K=this.width,H=this.height;"H"==this.orientation?0<=f&&f<=K+1&&(D=[f,f,f+k],C=[H,0,l]):0<=f&&f<=H+1&&(D=[0,K,K+k],C=[f,f,f+l]);D&&(f=d.fitToBounds(2*this.gridAlpha,0,1),isNaN(this.zeroGridAlpha)||(f=this.zeroGridAlpha),f=d.line(a.container,D,C,this.gridColor,f,1,this.dashLength),f.translate(this.x,this.y),this.grid0=f,a.axesSet.push(f),f.toBack(),d.setCN(a,f,this.bcn+"zero-grid-"+this.id),d.setCN(a,f,this.bcn+"zero-grid"))}if(!isNaN(z)&&0<A&&g<r-1){f=
y/z;h&&(z=y*(g+p)+this.minCalc,z=d.roundTo(z,this.maxDecCount+1),w>this.logGridLimit&&(z=Math.pow(10,g+p)),f=9,z=(z-t)/f);K=this.gridAlpha;this.gridAlpha=this.minorGridAlpha;for(H=1;H<f;H++){var Q=this.getCoordinate(t+z*H),Q=new this.axisItemRenderer(this,Q,"",!1,0,0,!1,!1,0,!0);this.pushAxisItem(Q)}this.gridAlpha=K}}w=this.guides;B=w.length;if(0<B){D=this.fillAlpha;for(g=this.fillAlpha=0;g<B;g++)C=w[g],k=NaN,z=C.above,isNaN(C.toValue)||(k=this.getCoordinate(C.toValue),u=new this.axisItemRenderer(this,
k,"",!0,NaN,NaN,C),this.pushAxisItem(u,z)),l=NaN,isNaN(C.value)||(l=this.getCoordinate(C.value),u=new this.axisItemRenderer(this,l,C.label,!0,NaN,(k-l)/2,C),this.pushAxisItem(u,z)),isNaN(k)&&(l-=3,k=l+3),C.balloonText&&u&&(m=u.label)&&this.addEventListeners(m,C),isNaN(k-l)||0>l&&0>k||(k=new this.guideFillRenderer(this,l,k,C),this.pushAxisItem(k,z),z=k.graphics(),C.graphics=z,C.balloonText&&this.addEventListeners(z,C));this.fillAlpha=D}u=this.baseValue;this.min>this.baseValue&&this.max>this.baseValue&&
(u=this.min);this.min<this.baseValue&&this.max<this.baseValue&&(u=this.max);h&&u<this.minReal&&(u=this.minReal);this.baseCoord=this.getCoordinate(u,!0);u={type:"axisChanged",target:this,chart:a};u.min=h?this.minReal:this.min;u.max=this.max;this.fire(u);this.axisCreated=!0}h=this.axisLine.set;u=this.labelsSet;b.translate(this.x,this.y);u.translate(this.x,this.y);this.positionTitle();"radar"!=a.type&&h.toFront();!this.visible||e?(b.hide(),h.hide(),u.hide()):(b.show(),h.show(),u.show());this.axisY=this.y;
this.axisX=this.x},getDateMinMax:function(){this.minimumDate&&(this.minimumDate instanceof Date||(this.minimumDate=d.getDate(this.minimumDate,this.chart.dataDateFormat,"fff")),this.min=this.minimumDate.getTime());this.maximumDate&&(this.maximumDate instanceof Date||(this.maximumDate=d.getDate(this.maximumDate,this.chart.dataDateFormat,"fff")),this.max=this.maximumDate.getTime())},formatValue:function(a,b,c){var e=this.exponential,h=this.logarithmic,f=this.numberFormatter,g=this.chart;if(f)return!0===
this.logarithmic&&(e=-1!=String(a).indexOf("e")?!0:!1),this.useScientificNotation&&(e=!0),this.usePrefixes&&(e=!1),e?(c=-1==String(a).indexOf("e")?a.toExponential(15):String(a),e=c.split("e"),c=Number(e[0]),e=Number(e[1]),c=d.roundTo(c,14),b||isNaN(this.precision)||(c=d.roundTo(c,this.precision)),10==c&&(c=1,e+=1),c=c+"e"+e,0===a&&(c="0"),1==a&&(c="1")):(h&&(e=String(a).split("."),e[1]?(f.precision=e[1].length,0>c&&(f.precision=Math.abs(c)),b&&1<a&&(f.precision=0),b||isNaN(this.precision)||(f.precision=
this.precision)):f.precision=-1),c=this.usePrefixes?d.addPrefix(a,g.prefixesOfBigNumbers,g.prefixesOfSmallNumbers,f,!b):d.formatNumber(a,f,f.precision)),this.duration&&(b&&(f.precision=0),c=d.formatDuration(a,this.duration,"",this.durationUnits,this.maxInterval,f)),"date"==this.type&&(c=d.formatDate(new Date(a),this.currentDateFormat,g)),this.recalculateToPercents?c+="%":(b=this.unit)&&(c="left"==this.unitPosition?b+c:c+b),this.labelFunction&&(c="date"==this.type?this.labelFunction(c,new Date(a),
this).toString():this.labelFunction(a,c,this).toString()),c},getMinorGridStep:function(a,b){var c=[5,4,2];60>b&&c.shift();for(var e=Math.floor(Math.log(Math.abs(a))*Math.LOG10E),d=0;d<c.length;d++){var f=a/c[d],g=Math.floor(Math.log(Math.abs(f))*Math.LOG10E);if(!(1<Math.abs(e-g)))if(1>a){if(g=Math.pow(10,-g)*f,g==Math.round(g))return f}else if(f==Math.round(f))return f}},stackGraphs:function(a){var b=this.stackType;"stacked"==b&&(b="regular");"line"==b&&(b="none");"100% stacked"==b&&(b="100%");this.stackType=
b;var c=[],e=[],h=[],f=[],g,k=this.chart.graphs,l,m,n,q,p,t=this.baseValue,r=!1;if("line"==a||"step"==a||"smoothedLine"==a)r=!0;if(r&&("regular"==b||"100%"==b))for(q=0;q<k.length;q++)n=k[q],n.stackGraph=null,n.hidden||(m=n.type,n.chart==this.chart&&n.valueAxis==this&&a==m&&n.stackable&&(l&&(n.stackGraph=l),l=n));n=this.start-10;l=this.end+10;q=this.data.length-1;n=d.fitToBounds(n,0,q);l=d.fitToBounds(l,0,q);for(p=n;p<=l;p++){var w=0;for(q=0;q<k.length;q++)if(n=k[q],n.hidden)n.newStack&&(h[p]=NaN,
e[p]=NaN);else if(m=n.type,n.chart==this.chart&&n.valueAxis==this&&a==m&&n.stackable)if(m=this.data[p].axes[this.id].graphs[n.id],g=m.values.value,isNaN(g))n.newStack&&(h[p]=NaN,e[p]=NaN);else{var y=d.getDecimals(g);w<y&&(w=y);isNaN(f[p])?f[p]=Math.abs(g):f[p]+=Math.abs(g);f[p]=d.roundTo(f[p],w);y=n.fillToGraph;r&&y&&(y=this.data[p].axes[this.id].graphs[y.id])&&(m.values.open=y.values.value);"regular"==b&&(r&&(isNaN(c[p])?(c[p]=g,m.values.close=g,m.values.open=this.baseValue):(isNaN(g)?m.values.close=
c[p]:m.values.close=g+c[p],m.values.open=c[p],c[p]=m.values.close)),"column"==a&&(n.newStack&&(h[p]=NaN,e[p]=NaN),m.values.close=g,0>g?(m.values.close=g,isNaN(e[p])?m.values.open=t:(m.values.close+=e[p],m.values.open=e[p]),e[p]=m.values.close):(m.values.close=g,isNaN(h[p])?m.values.open=t:(m.values.close+=h[p],m.values.open=h[p]),h[p]=m.values.close)))}}for(p=this.start;p<=this.end;p++)for(q=0;q<k.length;q++)(n=k[q],n.hidden)?n.newStack&&(h[p]=NaN,e[p]=NaN):(m=n.type,n.chart==this.chart&&n.valueAxis==
this&&a==m&&n.stackable&&(m=this.data[p].axes[this.id].graphs[n.id],g=m.values.value,isNaN(g)||(c=g/f[p]*100,m.values.percents=c,m.values.total=f[p],n.newStack&&(h[p]=NaN,e[p]=NaN),"100%"==b&&(isNaN(e[p])&&(e[p]=0),isNaN(h[p])&&(h[p]=0),0>c?(m.values.close=d.fitToBounds(c+e[p],-100,100),m.values.open=e[p],e[p]=m.values.close):(m.values.close=d.fitToBounds(c+h[p],-100,100),m.values.open=h[p],h[p]=m.values.close)))))},recalculate:function(){var a=this.chart,b=a.graphs,c;for(c=0;c<b.length;c++){var e=
b[c];if(e.valueAxis==this){var h="value";if("candlestick"==e.type||"ohlc"==e.type)h="open";var f,g,k=this.end+2,k=d.fitToBounds(this.end+1,0,this.data.length-1),l=this.start;0<l&&l--;var m;g=this.start;e.compareFromStart&&(g=0);if(!isNaN(a.startTime)&&(m=a.categoryAxis)){var n=m.minDuration(),n=new Date(a.startTime+n/2),q=d.resetDateToMin(new Date(a.startTime),m.minPeriod).getTime();d.resetDateToMin(new Date(n),m.minPeriod).getTime()>q&&g++}if(m=a.recalculateFromDate)m=d.getDate(m,a.dataDateFormat,
"fff"),g=a.getClosestIndex(a.chartData,"time",m.getTime(),!0,0,a.chartData.length),k=a.chartData.length-1;for(m=g;m<=k&&(g=this.data[m].axes[this.id].graphs[e.id],f=g.values[h],e.recalculateValue&&(f=g.dataContext[e.valueField+e.recalculateValue]),isNaN(f));m++);this.recBaseValue=f;for(h=l;h<=k;h++){g=this.data[h].axes[this.id].graphs[e.id];g.percents={};var l=g.values,p;for(p in l)g.percents[p]="percents"!=p?l[p]/f*100-100:l[p]}}}},getMinMax:function(){var a=!1,b=this.chart,c=b.graphs,e;for(e=0;e<
c.length;e++){var h=c[e].type;("line"==h||"step"==h||"smoothedLine"==h)&&this.expandMinMax&&(a=!0)}a&&(0<this.start&&this.start--,this.end<this.data.length-1&&this.end++);"serial"==b.type&&(!0!==b.categoryAxis.parseDates||a||this.end<this.data.length-1&&this.end++);this.includeAllValues&&(this.start=0,this.end=this.data.length-1);a=this.minMaxMultiplier;b=this.getExtremes(this.start,this.end);this.min=b.min;this.max=b.max;this.minRR=this.min;this.maxRR=this.max;a=(this.max-this.min)*(a-1);this.min-=
a;this.max+=a;a=this.guides.length;if(this.includeGuidesInMinMax&&0<a)for(b=0;b<a;b++)c=this.guides[b],c.toValue<this.min&&(this.min=c.toValue),c.value<this.min&&(this.min=c.value),c.toValue>this.max&&(this.max=c.toValue),c.value>this.max&&(this.max=c.value);isNaN(this.minimum)||(this.min=this.minimum);isNaN(this.maximum)||(this.max=this.maximum);"date"==this.type&&this.getDateMinMax();this.min>this.max&&(a=this.max,this.max=this.min,this.min=a);isNaN(this.minZoom)||(this.min=this.minZoom);isNaN(this.maxZoom)||
(this.max=this.maxZoom);this.minCalc=this.min;this.maxCalc=this.max;this.minReal=this.min;this.maxReal=this.max;0===this.min&&0===this.max&&(this.max=9);this.min>this.max&&(this.min=this.max-1);a=this.min;b=this.max;c=this.max-this.min;e=0===c?Math.pow(10,Math.floor(Math.log(Math.abs(this.max))*Math.LOG10E))/10:Math.pow(10,Math.floor(Math.log(Math.abs(c))*Math.LOG10E))/10;isNaN(this.maximum)&&(this.max=Math.ceil(this.max/e)*e+e);isNaN(this.minimum)&&(this.min=Math.floor(this.min/e)*e-e);0>this.min&&
0<=a&&(this.min=0);0<this.max&&0>=b&&(this.max=0);"100%"==this.stackType&&(this.min=0>this.min?-100:0,this.max=0>this.max?0:100);c=this.max-this.min;e=Math.pow(10,Math.floor(Math.log(Math.abs(c))*Math.LOG10E))/10;this.step=Math.ceil(c/this.gridCountR/e)*e;c=Math.pow(10,Math.floor(Math.log(Math.abs(this.step))*Math.LOG10E));c=d.fixStepE(c);e=Math.ceil(this.step/c);5<e&&(e=10);5>=e&&2<e&&(e=5);this.step=Math.ceil(this.step/(c*e))*c*e;isNaN(this.setStep)||(this.step=this.setStep);1>c?(this.maxDecCount=
Math.abs(Math.log(Math.abs(c))*Math.LOG10E),this.maxDecCount=Math.round(this.maxDecCount),this.step=d.roundTo(this.step,this.maxDecCount+1)):this.maxDecCount=0;this.min=this.step*Math.floor(this.min/this.step);this.max=this.step*Math.ceil(this.max/this.step);0>this.min&&0<=a&&(this.min=0);0<this.max&&0>=b&&(this.max=0);1<this.minReal&&1<this.max-this.minReal&&(this.minReal=Math.floor(this.minReal));c=Math.pow(10,Math.floor(Math.log(Math.abs(this.minReal))*Math.LOG10E));0===this.min&&(this.minReal=
c);0===this.min&&1<this.minReal&&(this.minReal=1);0<this.min&&0<this.minReal-this.step&&(this.minReal=this.min+this.step<this.minReal?this.min+this.step:this.min);this.logarithmic&&(2<Math.log(b)*Math.LOG10E-Math.log(a)*Math.LOG10E?(this.minReal=this.min=Math.pow(10,Math.floor(Math.log(Math.abs(a))*Math.LOG10E)),this.maxReal=this.max=Math.pow(10,Math.ceil(Math.log(Math.abs(b))*Math.LOG10E))):(a=Math.pow(10,Math.floor(Math.log(Math.abs(a))*Math.LOG10E))/10,Math.pow(10,Math.floor(Math.log(Math.abs(this.min))*
Math.LOG10E))/10<a&&(this.minReal=this.min=10*a)))},getExtremes:function(a,b){var c,e,d;for(d=a;d<=b;d++){var f=this.data[d].axes[this.id].graphs,g;for(g in f)if(f.hasOwnProperty(g)){var k=this.chart.graphsById[g];if(k.includeInMinMax&&(!k.hidden||this.includeHidden)){isNaN(c)&&(c=Infinity);isNaN(e)&&(e=-Infinity);this.foundGraphs=!0;k=f[g].values;this.recalculateToPercents&&(k=f[g].percents);var l;if(this.minMaxField)l=k[this.minMaxField],l<c&&(c=l),l>e&&(e=l);else for(var m in k)k.hasOwnProperty(m)&&
"percents"!=m&&"total"!=m&&"error"!=m&&(l=k[m],l<c&&(c=l),l>e&&(e=l))}}}return{min:c,max:e}},zoomOut:function(a){this.maxZoom=this.minZoom=NaN;this.zoomToRelativeValues(0,1,a)},zoomToRelativeValues:function(a,b,c){if(this.reversed){var e=a;a=1-b;b=1-e}var d=this.fullMax,e=this.fullMin,f=e+(d-e)*a,g=e+(d-e)*b;0<=this.minimum&&0>f&&(f=0);this.logarithmic&&(d=Math.log(d)*Math.LOG10E-Math.log(e)*Math.LOG10E,f=Math.pow(10,d*a+Math.log(e)*Math.LOG10E),g=Math.pow(10,d*b+Math.log(e)*Math.LOG10E));return this.zoomToValues(f,
g,c)},zoomToValues:function(a,b,c){if(b<a){var e=b;b=a;a=e}var h=this.fullMax,e=this.fullMin;this.relativeStart=(a-e)/(h-e);this.relativeEnd=(b-e)/(h-e);if(this.logarithmic){var h=Math.log(h)*Math.LOG10E-Math.log(e)*Math.LOG10E,f=Math.log(b)/Math.LN10-Math.log(e)*Math.LOG10E;this.relativeStart=(Math.log(a)/Math.LN10-Math.log(e)*Math.LOG10E)/h;this.relativeEnd=f/h}if(this.minZoom!=a||this.maxZoom!=b)return this.minZoom=a,this.maxZoom=b,e={type:"axisZoomed"},e.chart=this.chart,e.valueAxis=this,e.startValue=
a,e.endValue=b,e.relativeStart=this.relativeStart,e.relativeEnd=this.relativeEnd,this.prevStartValue==a&&this.prevEndValue==b||this.fire(e),this.prevStartValue=a,this.prevEndValue=b,c||(a={},d.copyProperties(e,a),a.type="axisIntZoomed",this.fire(a)),0===this.relativeStart&&1==this.relativeEnd&&(this.maxZoom=this.minZoom=NaN),!0},coordinateToValue:function(a){if(isNaN(a))return NaN;var b=this.axisWidth,c=this.stepWidth,e=this.reversed,d=this.rotate,f=this.min,g=this.minReal;return!0===this.logarithmic?
Math.pow(10,(d?!0===e?(b-a)/c:a/c:!0===e?a/c:(b-a)/c)+Math.log(g)*Math.LOG10E):!0===e?d?f-(a-b)/c:a/c+f:d?a/c+f:f-(a-b)/c},getCoordinate:function(a,b){if(isNaN(a))return NaN;var c=this.rotate,e=this.reversed,d=this.axisWidth,f=this.stepWidth,g=this.min,k=this.minReal;!0===this.logarithmic?(0===a&&(a=this.treatZeroAs),g=Math.log(a)*Math.LOG10E-Math.log(k)*Math.LOG10E,c=c?!0===e?d-f*g:f*g:!0===e?f*g:d-f*g):c=!0===e?c?d-f*(a-g):f*(a-g):c?f*(a-g):d-f*(a-g);1E7<Math.abs(c)&&(c=c/Math.abs(c)*1E7);b||(c=
Math.round(c));return c},synchronizeWithAxis:function(a){this.synchronizeWith=a;this.listenTo(this.synchronizeWith,"axisChanged",this.handleSynchronization)},handleSynchronization:function(){if(this.synchronizeWith){d.isString(this.synchronizeWith)&&(this.synchronizeWith=this.chart.getValueAxisById(this.synchronizeWith));var a=this.synchronizeWith,b=a.min,c=a.max,a=a.step,e=this.synchronizationMultiplier;e&&(this.min=b*e,this.max=c*e,this.step=a*e,b=Math.abs(Math.log(Math.abs(Math.pow(10,Math.floor(Math.log(Math.abs(this.step))*
Math.LOG10E))))*Math.LOG10E),this.maxDecCount=b=Math.round(b),this.draw())}}})})();(function(){var d=window.AmCharts;d.RecAxis=d.Class({construct:function(a){var b=a.chart,c=a.axisThickness,e=a.axisColor,h=a.axisAlpha,f=a.offset,g=a.dx,k=a.dy,l=a.x,m=a.y,n=a.height,q=a.width,p=b.container;"H"==a.orientation?(e=d.line(p,[0,q],[0,0],e,h,c),this.axisWidth=a.width,"bottom"==a.position?(k=c/2+f+n+m-1,c=l):(k=-c/2-f+m+k,c=g+l)):(this.axisWidth=a.height,"right"==a.position?(e=d.line(p,[0,0,-g],[0,n,n-k],e,h,c),k=m+k,c=c/2+f+g+q+l-1):(e=d.line(p,[0,0],[0,n],e,h,c),k=m,c=-c/2-f+l));e.translate(c,
k);c=b.container.set();c.push(e);b.axesSet.push(c);d.setCN(b,e,a.bcn+"line");this.axisSet=c;this.set=e}})})();(function(){var d=window.AmCharts;d.RecItem=d.Class({construct:function(a,b,c,e,h,f,g,k,l,m,n,q){b=Math.round(b);var p=a.chart;this.value=c;void 0==c&&(c="");l||(l=0);void 0==e&&(e=!0);var t=p.fontFamily,r=a.fontSize;void 0==r&&(r=p.fontSize);var w=a.color;void 0==w&&(w=p.color);void 0!==n&&(w=n);var y=a.chart.container,x=y.set();this.set=x;var u=a.axisThickness,A=a.axisColor,z=a.axisAlpha,B=a.tickLength,D=a.gridAlpha,C=a.gridThickness,K=a.gridColor,H=a.dashLength,Q=a.fillColor,M=a.fillAlpha,P=a.labelsEnabled;
n=a.labelRotationR;var ia=a.counter,I=a.inside,aa=a.labelOffset,ma=a.dx,na=a.dy,Pa=a.orientation,Z=a.position,da=a.previousCoord,X=a.height,xa=a.width,ea=a.offset,fa,Ba;g?(void 0!==g.id&&(q=p.classNamePrefix+"-guide-"+g.id),P=!0,isNaN(g.tickLength)||(B=g.tickLength),void 0!=g.lineColor&&(K=g.lineColor),void 0!=g.color&&(w=g.color),isNaN(g.lineAlpha)||(D=g.lineAlpha),isNaN(g.dashLength)||(H=g.dashLength),isNaN(g.lineThickness)||(C=g.lineThickness),!0===g.inside&&(I=!0,0<ea&&(ea=0)),isNaN(g.labelRotation)||
(n=g.labelRotation),isNaN(g.fontSize)||(r=g.fontSize),g.position&&(Z=g.position),void 0!==g.boldLabel&&(k=g.boldLabel),isNaN(g.labelOffset)||(aa=g.labelOffset)):""===c&&(B=0);m&&!isNaN(a.minorTickLength)&&(B=a.minorTickLength);var ga="start";0<h&&(ga="middle");a.centerLabels&&(ga="middle");var V=n*Math.PI/180,Y,Da,G=0,v=0,oa=0,ha=Y=0,Qa=0;"V"==Pa&&(n=0);var ca;P&&""!==c&&(ca=a.autoWrap&&0===n?d.wrappedText(y,c,w,t,r,ga,k,Math.abs(h),0):d.text(y,c,w,t,r,ga,k),ga=ca.getBBox(),ha=ga.width,Qa=ga.height);
if("H"==Pa){if(0<=b&&b<=xa+1&&(0<B&&0<z&&b+l<=xa+1&&(fa=d.line(y,[b+l,b+l],[0,B],A,z,C),x.push(fa)),0<D&&(Ba=d.line(y,[b,b+ma,b+ma],[X,X+na,na],K,D,C,H),x.push(Ba))),v=0,G=b,g&&90==n&&I&&(G-=r),!1===e?(ga="start",v="bottom"==Z?I?v+B:v-B:I?v-B:v+B,G+=3,0<h&&(G+=h/2-3,ga="middle"),0<n&&(ga="middle")):ga="middle",1==ia&&0<M&&!g&&!m&&da<xa&&(e=d.fitToBounds(b,0,xa),da=d.fitToBounds(da,0,xa),Y=e-da,0<Y&&(Da=d.rect(y,Y,a.height,Q,M),Da.translate(e-Y+ma,na),x.push(Da))),"bottom"==Z?(v+=X+r/2+ea,I?(0<n?(v=
X-ha/2*Math.sin(V)-B-3,a.centerRotatedLabels||(G+=ha/2*Math.cos(V)-4+2)):0>n?(v=X+ha*Math.sin(V)-B-3+2,G+=-ha*Math.cos(V)-Qa*Math.sin(V)-4):v-=B+r+3+3,v-=aa):(0<n?(v=X+ha/2*Math.sin(V)+B+3,a.centerRotatedLabels||(G-=ha/2*Math.cos(V))):0>n?(v=X+B+3-ha/2*Math.sin(V)+2,G+=ha/2*Math.cos(V)):v+=B+u+3+3,v+=aa)):(v+=na+r/2-ea,G+=ma,I?(0<n?(v=ha/2*Math.sin(V)+B+3,a.centerRotatedLabels||(G-=ha/2*Math.cos(V))):v+=B+3,v+=aa):(0<n?(v=-(ha/2)*Math.sin(V)-B-6,a.centerRotatedLabels||(G+=ha/2*Math.cos(V))):v-=B+
r+3+u+3,v-=aa)),"bottom"==Z?Y=(I?X-B-1:X+u-1)+ea:(oa=ma,Y=(I?na:na-B-u+1)-ea),f&&(G+=f),r=G,0<n&&(r+=ha/2*Math.cos(V)),ca&&(f=0,I&&(f=ha/2*Math.cos(V)),r+f>xa+2||0>r))ca.remove(),ca=null}else{0<=b&&b<=X+1&&(0<B&&0<z&&b+l<=X+1&&(fa=d.line(y,[0,B+1],[b+l,b+l],A,z,C),x.push(fa)),0<D&&(Ba=d.line(y,[0,ma,xa+ma],[b,b+na,b+na],K,D,C,H),x.push(Ba)));ga="end";if(!0===I&&"left"==Z||!1===I&&"right"==Z)ga="start";v=b-Qa/2+2;1==ia&&0<M&&!g&&!m&&(e=d.fitToBounds(b,0,X),da=d.fitToBounds(da,0,X),V=e-da,Da=d.polygon(y,
[0,a.width,a.width,0],[0,0,V,V],Q,M),Da.translate(ma,e-V+na),x.push(Da));v+=r/2;"right"==Z?(G+=ma+xa+ea,v+=na,I?(f||(v-=r/2+3),G=G-(B+4)-aa):(G+=B+4+u,v-=2,G+=aa)):I?(G+=B+4-ea,f||(v-=r/2+3),g&&(G+=ma,v+=na),G+=aa):(G+=-B-u-4-2-ea,v-=2,G-=aa);fa&&("right"==Z?(oa+=ma+ea+xa-1,Y+=na,oa=I?oa-u:oa+u):(oa-=ea,I||(oa-=B+u)));f&&(v+=f);I=-3;"right"==Z&&(I+=na);ca&&(v>X+1||v<I-r/10)&&(ca.remove(),ca=null)}fa&&(fa.translate(oa,Y),d.setCN(p,fa,a.bcn+"tick"),d.setCN(p,fa,q,!0),g&&d.setCN(p,fa,"guide"));!1===
a.visible&&(fa&&fa.remove(),ca&&(ca.remove(),ca=null));ca&&(ca.attr({"text-anchor":ga}),ca.translate(G,v,NaN,!0),0!==n&&ca.rotate(-n,a.chart.backgroundColor),a.allLabels.push(ca),this.label=ca,d.setCN(p,ca,a.bcn+"label"),d.setCN(p,ca,q,!0),g&&d.setCN(p,ca,"guide"));Ba&&(d.setCN(p,Ba,a.bcn+"grid"),d.setCN(p,Ba,q,!0),g&&d.setCN(p,Ba,"guide"));Da&&(d.setCN(p,Da,a.bcn+"fill"),d.setCN(p,Da,q,!0));m?Ba&&d.setCN(p,Ba,a.bcn+"grid-minor"):(a.counter=0===ia?1:0,a.previousCoord=b);0===this.set.node.childNodes.length&&
this.set.remove()},graphics:function(){return this.set},getLabel:function(){return this.label}})})();(function(){var d=window.AmCharts;d.RecFill=d.Class({construct:function(a,b,c,e){var h=a.dx,f=a.dy,g=a.orientation,k=0;if(c<b){var l=b;b=c;c=l}var m=e.fillAlpha;isNaN(m)&&(m=0);var l=a.chart.container,n=e.fillColor;"V"==g?(b=d.fitToBounds(b,0,a.height),c=d.fitToBounds(c,0,a.height)):(b=d.fitToBounds(b,0,a.width),c=d.fitToBounds(c,0,a.width));c-=b;isNaN(c)&&(c=4,k=2,m=0);0>c&&"object"==typeof n&&(n=n.join(",").split(",").reverse());"V"==g?(g=d.rect(l,a.width,c,n,m),g.translate(h,b-k+f)):(g=d.rect(l,
c,a.height,n,m),g.translate(b-k+h,f));d.setCN(a.chart,g,"guide-fill");e.id&&d.setCN(a.chart,g,"guide-fill-"+e.id);this.set=l.set([g])},graphics:function(){return this.set},getLabel:function(){}})})();(function(){var d=window.AmCharts;d.AmChart=d.Class({construct:function(a){this.svgIcons=this.tapToActivate=!0;this.theme=a;this.classNamePrefix="amcharts";this.addClassNames=!1;this.version="3.20.20";d.addChart(this);this.createEvents("buildStarted","dataUpdated","init","rendered","drawn","failed","resized","animationFinished");this.height=this.width="100%";this.dataChanged=!0;this.chartCreated=!1;this.previousWidth=this.previousHeight=0;this.backgroundColor="#FFFFFF";this.borderAlpha=this.backgroundAlpha=
0;this.color=this.borderColor="#000000";this.fontFamily="Verdana";this.fontSize=11;this.usePrefixes=!1;this.autoResize=!0;this.autoDisplay=!1;this.addCodeCredits=this.accessible=!0;this.touchStartTime=this.touchClickDuration=0;this.precision=-1;this.percentPrecision=2;this.decimalSeparator=".";this.thousandsSeparator=",";this.labels=[];this.allLabels=[];this.titles=[];this.marginRight=this.marginLeft=this.autoMarginOffset=0;this.timeOuts=[];this.creditsPosition="top-left";var b=document.createElement("div"),
c=b.style;c.overflow="hidden";c.position="relative";c.textAlign="left";this.chartDiv=b;b=document.createElement("div");c=b.style;c.overflow="hidden";c.position="relative";c.textAlign="left";this.legendDiv=b;this.titleHeight=0;this.hideBalloonTime=150;this.handDrawScatter=2;this.cssScale=this.handDrawThickness=1;this.cssAngle=0;this.prefixesOfBigNumbers=[{number:1E3,prefix:"k"},{number:1E6,prefix:"M"},{number:1E9,prefix:"G"},{number:1E12,prefix:"T"},{number:1E15,prefix:"P"},{number:1E18,prefix:"E"},
{number:1E21,prefix:"Z"},{number:1E24,prefix:"Y"}];this.prefixesOfSmallNumbers=[{number:1E-24,prefix:"y"},{number:1E-21,prefix:"z"},{number:1E-18,prefix:"a"},{number:1E-15,prefix:"f"},{number:1E-12,prefix:"p"},{number:1E-9,prefix:"n"},{number:1E-6,prefix:"\u03bc"},{number:.001,prefix:"m"}];this.panEventsEnabled=!0;this.product="amcharts";this.animations=[];this.balloon=new d.AmBalloon(this.theme);this.balloon.chart=this;this.processTimeout=0;this.processCount=1E3;this.animatable=[];this.langObj={};
d.applyTheme(this,a,"AmChart")},drawChart:function(){0<this.realWidth&&0<this.realHeight&&(this.drawBackground(),this.redrawLabels(),this.drawTitles(),this.brr(),this.renderFix(),this.chartDiv&&(this.boundingRect=this.chartDiv.getBoundingClientRect()))},makeAccessible:function(a,b,c){this.accessible&&a&&(c&&a.setAttr("role",c),a.setAttr("aria-label",b))},drawBackground:function(){d.remove(this.background);var a=this.container,b=this.backgroundColor,c=this.backgroundAlpha,e=this.set;d.isModern||0!==
c||(c=.001);var h=this.updateWidth();this.realWidth=h;var f=this.updateHeight();this.realHeight=f;b=d.polygon(a,[0,h-1,h-1,0],[0,0,f-1,f-1],b,c,1,this.borderColor,this.borderAlpha);d.setCN(this,b,"bg");this.background=b;e.push(b);if(b=this.backgroundImage)a=a.image(b,0,0,h,f),d.setCN(this,b,"bg-image"),this.bgImg=a,e.push(a)},drawTitles:function(a){var b=this.titles;this.titleHeight=0;if(d.ifArray(b)){var c=20,e;for(e=0;e<b.length;e++){var h=b[e],h=d.processObject(h,d.Title,this.theme);if(!1!==h.enabled){var f=
h.color;void 0===f&&(f=this.color);var g=h.size;isNaN(g)&&(g=this.fontSize+2);isNaN(h.alpha);var k=this.marginLeft,l=!0;void 0!==h.bold&&(l=h.bold);f=d.wrappedText(this.container,h.text,f,this.fontFamily,g,"middle",l,this.realWidth-35);f.translate(k+(this.realWidth-this.marginRight-k)/2,c);f.node.style.pointerEvents="none";h.sprite=f;void 0!==h.tabIndex&&f.setAttr("tabindex",h.tabIndex);d.setCN(this,f,"title");h.id&&d.setCN(this,f,"title-"+h.id);f.attr({opacity:h.alpha});c+=f.getBBox().height+5;a?
f.remove():this.freeLabelsSet.push(f)}}this.titleHeight=c-10}},write:function(a){var b=this;if(b.listeners)for(var c=0;c<b.listeners.length;c++){var e=b.listeners[c];b.addListener(e.event,e.method)}b.fire({type:"buildStarted",chart:b});b.afterWriteTO&&clearTimeout(b.afterWriteTO);0<b.processTimeout?b.afterWriteTO=setTimeout(function(){b.afterWrite.call(b,a)},b.processTimeout):b.afterWrite(a)},afterWrite:function(a){var b;if(b="object"!=typeof a?document.getElementById(a):a){for(;b.firstChild;)b.removeChild(b.firstChild);
this.div=b;b.style.overflow="hidden";b.style.textAlign="left";a=this.chartDiv;var c=this.legendDiv,e=this.legend,h=c.style,f=a.style;this.measure();this.previousHeight=this.divRealHeight;this.previousWidth=this.divRealWidth;var g,k=document.createElement("div");g=k.style;g.position="relative";this.containerDiv=k;k.className=this.classNamePrefix+"-main-div";a.className=this.classNamePrefix+"-chart-div";b.appendChild(k);(b=this.exportConfig)&&d.AmExport&&!this.AmExport&&(this.AmExport=new d.AmExport(this,
b));this.amExport&&d.AmExport&&(this.AmExport=d.extend(this.amExport,new d.AmExport(this),!0));this.AmExport&&this.AmExport.init&&this.AmExport.init();if(e){e=this.addLegend(e,e.divId);if(e.enabled)switch(h.left=null,h.top=null,h.right=null,f.left=null,f.right=null,f.top=null,h.position="relative",f.position="relative",g.width="100%",g.height="100%",e.position){case "bottom":k.appendChild(a);k.appendChild(c);break;case "top":k.appendChild(c);k.appendChild(a);break;case "absolute":h.position="absolute";
f.position="absolute";void 0!==e.left&&(h.left=e.left+"px");void 0!==e.right&&(h.right=e.right+"px");void 0!==e.top&&(h.top=e.top+"px");void 0!==e.bottom&&(h.bottom=e.bottom+"px");e.marginLeft=0;e.marginRight=0;k.appendChild(a);k.appendChild(c);break;case "right":h.position="relative";f.position="absolute";k.appendChild(a);k.appendChild(c);break;case "left":h.position="absolute";f.position="relative";k.appendChild(a);k.appendChild(c);break;case "outside":k.appendChild(a)}else k.appendChild(a);this.prevLegendPosition=
e.position}else k.appendChild(a);this.listenersAdded||(this.addListeners(),this.listenersAdded=!0);this.initChart()}},createLabelsSet:function(){d.remove(this.labelsSet);this.labelsSet=this.container.set();this.freeLabelsSet.push(this.labelsSet)},initChart:function(){this.balloon=d.processObject(this.balloon,d.AmBalloon,this.theme);window.AmCharts_path&&(this.path=window.AmCharts_path);void 0===this.path&&(this.path=d.getPath());void 0===this.path&&(this.path="amcharts/");this.path=d.normalizeUrl(this.path);
void 0===this.pathToImages&&(this.pathToImages=this.path+"images/");this.initHC||(d.callInitHandler(this),this.initHC=!0);d.applyLang(this.language,this);var a=this.numberFormatter;a&&(isNaN(a.precision)||(this.precision=a.precision),void 0!==a.thousandsSeparator&&(this.thousandsSeparator=a.thousandsSeparator),void 0!==a.decimalSeparator&&(this.decimalSeparator=a.decimalSeparator));(a=this.percentFormatter)&&!isNaN(a.precision)&&(this.percentPrecision=a.precision);this.nf={precision:this.precision,
thousandsSeparator:this.thousandsSeparator,decimalSeparator:this.decimalSeparator};this.pf={precision:this.percentPrecision,thousandsSeparator:this.thousandsSeparator,decimalSeparator:this.decimalSeparator};this.destroy();(a=this.container)?(a.container.innerHTML="",a.width=this.realWidth,a.height=this.realHeight,a.addDefs(this),this.chartDiv.appendChild(a.container)):a=new d.AmDraw(this.chartDiv,this.realWidth,this.realHeight,this);this.container=a;this.extension=".png";this.svgIcons&&d.SVG&&(this.extension=
".svg");this.checkDisplay();this.checkTransform(this.div);a.chart=this;d.VML||d.SVG?(a.handDrawn=this.handDrawn,a.handDrawScatter=this.handDrawScatter,a.handDrawThickness=this.handDrawThickness,d.remove(this.set),this.set=a.set(),d.remove(this.gridSet),this.gridSet=a.set(),d.remove(this.cursorLineSet),this.cursorLineSet=a.set(),d.remove(this.graphsBehindSet),this.graphsBehindSet=a.set(),d.remove(this.bulletBehindSet),this.bulletBehindSet=a.set(),d.remove(this.columnSet),this.columnSet=a.set(),d.remove(this.graphsSet),
this.graphsSet=a.set(),d.remove(this.trendLinesSet),this.trendLinesSet=a.set(),d.remove(this.axesSet),this.axesSet=a.set(),d.remove(this.cursorSet),this.cursorSet=a.set(),d.remove(this.scrollbarsSet),this.scrollbarsSet=a.set(),d.remove(this.bulletSet),this.bulletSet=a.set(),d.remove(this.freeLabelsSet),this.freeLabelsSet=a.set(),d.remove(this.axesLabelsSet),this.axesLabelsSet=a.set(),d.remove(this.balloonsSet),this.balloonsSet=a.set(),d.remove(this.plotBalloonsSet),this.plotBalloonsSet=a.set(),d.remove(this.zoomButtonSet),
this.zoomButtonSet=a.set(),d.remove(this.zbSet),this.zbSet=null,d.remove(this.linkSet),this.linkSet=a.set()):this.fire({type:"failed",chart:this})},premeasure:function(){var a=this.div;if(a){try{this.boundingRect=this.chartDiv.getBoundingClientRect()}catch(e){}var b=a.offsetWidth,c=a.offsetHeight;a.clientHeight&&(b=a.clientWidth,c=a.clientHeight);if(b!=this.mw||c!=this.mh)this.mw=b,this.mh=c,this.measure()}},measure:function(){var a=this.div;if(a){var b=this.chartDiv,c=a.offsetWidth,e=a.offsetHeight,
h=this.container;a.clientHeight&&(c=a.clientWidth,e=a.clientHeight);var e=Math.round(e),c=Math.round(c),a=Math.round(d.toCoordinate(this.width,c)),f=Math.round(d.toCoordinate(this.height,e));(c!=this.previousWidth||e!=this.previousHeight)&&0<a&&0<f&&(b.style.width=a+"px",b.style.height=f+"px",b.style.padding=0,h&&h.setSize(a,f),this.balloon=d.processObject(this.balloon,d.AmBalloon,this.theme));this.balloon&&this.balloon.setBounds&&this.balloon.setBounds(2,2,a-2,f);this.updateWidth();this.balloon.chart=
this;this.realWidth=a;this.realHeight=f;this.divRealWidth=c;this.divRealHeight=e}},checkDisplay:function(){if(this.autoDisplay&&this.container){var a=d.rect(this.container,10,10),b=a.getBBox();0===b.width&&0===b.height&&(this.divRealHeight=this.divRealWidth=this.realHeight=this.realWidth=0,this.previousWidth=this.previousHeight=NaN);a.remove()}},checkTransform:function(a){if(this.autoTransform&&window.getComputedStyle&&a){if(a.style){var b=window.getComputedStyle(a,null);if(b&&(b=b.getPropertyValue("-webkit-transform")||
b.getPropertyValue("-moz-transform")||b.getPropertyValue("-ms-transform")||b.getPropertyValue("-o-transform")||b.getPropertyValue("transform"))&&"none"!==b){var c=b.split("(")[1].split(")")[0].split(","),b=c[0],c=c[1],b=Math.sqrt(b*b+c*c);isNaN(b)||(this.cssScale*=b)}}a.parentNode&&this.checkTransform(a.parentNode)}},destroy:function(){this.chartDiv.innerHTML="";this.clearTimeOuts();this.legend&&this.legend.destroy()},clearTimeOuts:function(){var a=this.timeOuts;if(a){var b;for(b=0;b<a.length;b++)clearTimeout(a[b])}this.timeOuts=
[]},clear:function(a){try{document.removeEventListener("touchstart",this.docfn1,!0),document.removeEventListener("touchend",this.docfn2,!0)}catch(b){}d.callMethod("clear",[this.chartScrollbar,this.scrollbarV,this.scrollbarH,this.chartCursor]);this.chartCursor=this.scrollbarH=this.scrollbarV=this.chartScrollbar=null;this.clearTimeOuts();this.container&&(this.container.remove(this.chartDiv),this.container.remove(this.legendDiv));a||d.removeChart(this);if(a=this.div)for(;a.firstChild;)a.removeChild(a.firstChild);
this.legend&&this.legend.destroy();this.AmExport&&this.AmExport.clear&&this.AmExport.clear()},setMouseCursor:function(a){"auto"==a&&d.isNN&&(a="default");this.chartDiv.style.cursor=a;this.legendDiv.style.cursor=a},redrawLabels:function(){this.labels=[];var a=this.allLabels;this.createLabelsSet();var b;for(b=0;b<a.length;b++)this.drawLabel(a[b])},drawLabel:function(a){var b=this;if(b.container&&!1!==a.enabled){a=d.processObject(a,d.Label,b.theme);var c=a.y,e=a.text,h=a.align,f=a.size,g=a.color,k=a.rotation,
l=a.alpha,m=a.bold,n=d.toCoordinate(a.x,b.realWidth),c=d.toCoordinate(c,b.realHeight);n||(n=0);c||(c=0);void 0===g&&(g=b.color);isNaN(f)&&(f=b.fontSize);h||(h="start");"left"==h&&(h="start");"right"==h&&(h="end");"center"==h&&(h="middle",k?c=b.realHeight-c+c/2:n=b.realWidth/2-n);void 0===l&&(l=1);void 0===k&&(k=0);c+=f/2;e=d.text(b.container,e,g,b.fontFamily,f,h,m,l);e.translate(n,c);void 0!==a.tabIndex&&e.setAttr("tabindex",a.tabIndex);d.setCN(b,e,"label");a.id&&d.setCN(b,e,"label-"+a.id);0!==k&&
e.rotate(k);a.url?(e.setAttr("cursor","pointer"),e.click(function(){d.getURL(a.url,b.urlTarget)})):e.node.style.pointerEvents="none";b.labelsSet.push(e);b.labels.push(e)}},addLabel:function(a,b,c,e,d,f,g,k,l,m){a={x:a,y:b,text:c,align:e,size:d,color:f,alpha:k,rotation:g,bold:l,url:m,enabled:!0};this.container&&this.drawLabel(a);this.allLabels.push(a)},clearLabels:function(){var a=this.labels,b;for(b=a.length-1;0<=b;b--)a[b].remove();this.labels=[];this.allLabels=[]},updateHeight:function(){var a=
this.divRealHeight,b=this.legend;if(b){var c=this.legendDiv.offsetHeight,b=b.position;if("top"==b||"bottom"==b){a-=c;if(0>a||isNaN(a))a=0;this.chartDiv.style.height=a+"px"}}return a},updateWidth:function(){var a=this.divRealWidth,b=this.divRealHeight,c=this.legend;if(c){var e=this.legendDiv,d=e.offsetWidth;isNaN(c.width)||(d=c.width);c.ieW&&(d=c.ieW);var f=e.offsetHeight,e=e.style,g=this.chartDiv.style,k=c.position;if(("right"==k||"left"==k)&&void 0===c.divId){a-=d;if(0>a||isNaN(a))a=0;g.width=a+
"px";this.balloon.setBounds(2,2,a-2,this.realHeight);"left"==k?(g.left=d+"px",e.left="0px"):(g.left="0px",e.left=a+"px");b>f&&(e.top=(b-f)/2+"px")}}return a},getTitleHeight:function(){this.drawTitles(!0);return this.titleHeight},addTitle:function(a,b,c,e,d){isNaN(b)&&(b=this.fontSize+2);a={text:a,size:b,color:c,alpha:e,bold:d,enabled:!0};this.titles.push(a);return a},handleWheel:function(a){var b=0;a||(a=window.event);a.wheelDelta?b=a.wheelDelta/120:a.detail&&(b=-a.detail/3);b&&this.handleWheelReal(b,
a.shiftKey);a.preventDefault&&a.preventDefault()},handleWheelReal:function(){},handleDocTouchStart:function(){this.handleMouseMove();this.tmx=this.mouseX;this.tmy=this.mouseY;this.touchStartTime=(new Date).getTime()},handleDocTouchEnd:function(){-.5<this.tmx&&this.tmx<this.divRealWidth+1&&0<this.tmy&&this.tmy<this.divRealHeight?(this.handleMouseMove(),4>Math.abs(this.mouseX-this.tmx)&&4>Math.abs(this.mouseY-this.tmy)?(this.tapped=!0,this.panRequired&&this.panEventsEnabled&&this.chartDiv&&(this.chartDiv.style.msTouchAction=
"none",this.chartDiv.style.touchAction="none")):this.mouseIsOver||this.resetTouchStyle()):(this.tapped=!1,this.resetTouchStyle())},resetTouchStyle:function(){this.panEventsEnabled&&this.chartDiv&&(this.chartDiv.style.msTouchAction="auto",this.chartDiv.style.touchAction="auto")},checkTouchDuration:function(a){var b=this,c=(new Date).getTime();if(a)if(a.touches)b.isTouchEvent=!0;else if(!b.isTouchEvent)return!0;if(c-b.touchStartTime>b.touchClickDuration)return!0;setTimeout(function(){b.resetTouchDuration()},
300)},resetTouchDuration:function(){this.isTouchEvent=!1},checkTouchMoved:function(){if(4<Math.abs(this.mouseX-this.tmx)||4<Math.abs(this.mouseY-this.tmy))return!0},addListeners:function(){var a=this,b=a.chartDiv;document.addEventListener?("ontouchstart"in document.documentElement&&(b.addEventListener("touchstart",function(b){a.handleTouchStart.call(a,b)},!0),b.addEventListener("touchmove",function(b){a.handleMouseMove.call(a,b)},!0),b.addEventListener("touchend",function(b){a.handleTouchEnd.call(a,
b)},!0),a.docfn1=function(b){a.handleDocTouchStart.call(a,b)},a.docfn2=function(b){a.handleDocTouchEnd.call(a,b)},document.addEventListener("touchstart",a.docfn1,!0),document.addEventListener("touchend",a.docfn2,!0)),b.addEventListener("mousedown",function(b){a.mouseIsOver=!0;a.handleMouseMove.call(a,b);a.handleMouseDown.call(a,b);a.handleDocTouchStart.call(a,b)},!0),b.addEventListener("mouseover",function(b){a.handleMouseOver.call(a,b)},!0),b.addEventListener("mouseout",function(b){a.handleMouseOut.call(a,
b)},!0),b.addEventListener("mouseup",function(b){a.handleDocTouchEnd.call(a,b)},!0)):(b.attachEvent("onmousedown",function(b){a.handleMouseDown.call(a,b)}),b.attachEvent("onmouseover",function(b){a.handleMouseOver.call(a,b)}),b.attachEvent("onmouseout",function(b){a.handleMouseOut.call(a,b)}))},dispDUpd:function(){this.skipEvents||(this.dispatchDataUpdated&&(this.dispatchDataUpdated=!1,this.fire({type:"dataUpdated",chart:this})),this.chartCreated||(this.chartCreated=!0,this.fire({type:"init",chart:this})),
this.chartRendered||(this.fire({type:"rendered",chart:this}),this.chartRendered=!0),this.fire({type:"drawn",chart:this}));this.skipEvents=!1},validateSize:function(){var a=this;a.premeasure();a.checkDisplay();a.cssScale=1;a.cssAngle=0;a.checkTransform(a.div);if(a.divRealWidth!=a.previousWidth||a.divRealHeight!=a.previousHeight){var b=a.legend;if(0<a.realWidth&&0<a.realHeight){a.sizeChanged=!0;if(b){a.legendInitTO&&clearTimeout(a.legendInitTO);var c=setTimeout(function(){b.invalidateSize()},10);a.timeOuts.push(c);
a.legendInitTO=c}a.marginsUpdated=!1;clearTimeout(a.initTO);c=setTimeout(function(){a.initChart()},10);a.timeOuts.push(c);a.initTO=c}a.renderFix();b&&b.renderFix&&b.renderFix();clearTimeout(a.resizedTO);a.resizedTO=setTimeout(function(){a.fire({type:"resized",chart:a})},10);a.previousHeight=a.divRealHeight;a.previousWidth=a.divRealWidth}},invalidateSize:function(){this.previousHeight=this.previousWidth=NaN;this.invalidateSizeReal()},invalidateSizeReal:function(){var a=this;a.marginsUpdated=!1;clearTimeout(a.validateTO);
var b=setTimeout(function(){a.validateSize()},5);a.timeOuts.push(b);a.validateTO=b},validateData:function(a){this.chartCreated&&(this.dataChanged=!0,this.marginsUpdated=!1,this.initChart(a))},validateNow:function(a,b){this.initTO&&clearTimeout(this.initTO);a&&(this.dataChanged=!0,this.marginsUpdated=!1);this.skipEvents=b;this.chartRendered=!1;var c=this.legend;c&&c.position!=this.prevLegendPosition&&(this.previousWidth=this.mw=0,c.invalidateSize&&(c.invalidateSize(),this.validateSize()));this.write(this.div)},
showItem:function(a){a.hidden=!1;this.initChart()},hideItem:function(a){a.hidden=!0;this.initChart()},hideBalloon:function(){var a=this;clearTimeout(a.hoverInt);clearTimeout(a.balloonTO);a.hoverInt=setTimeout(function(){a.hideBalloonReal.call(a)},a.hideBalloonTime)},cleanChart:function(){},hideBalloonReal:function(){var a=this.balloon;a&&a.hide&&a.hide()},showBalloon:function(a,b,c,e,d){var f=this;clearTimeout(f.balloonTO);clearTimeout(f.hoverInt);f.balloonTO=setTimeout(function(){f.showBalloonReal.call(f,
a,b,c,e,d)},1)},showBalloonReal:function(a,b,c,e,d){this.handleMouseMove();var f=this.balloon;f.enabled&&(f.followCursor(!1),f.changeColor(b),!c||f.fixedPosition?(f.setPosition(e,d),isNaN(e)||isNaN(d)?f.followCursor(!0):f.followCursor(!1)):f.followCursor(!0),a&&f.showBalloon(a))},handleMouseOver:function(){this.outTO&&clearTimeout(this.outTO);d.resetMouseOver();this.mouseIsOver=!0},handleMouseOut:function(){var a=this;d.resetMouseOver();a.outTO&&clearTimeout(a.outTO);a.outTO=setTimeout(function(){a.handleMouseOutReal()},
10)},handleMouseOutReal:function(){this.mouseIsOver=!1},handleMouseMove:function(a){a||(a=window.event);this.mouse2Y=this.mouse2X=NaN;var b,c,e,d;if(a){if(a.touches){var f=a.touches.item(1);f&&this.panEventsEnabled&&this.boundingRect&&(e=f.clientX-this.boundingRect.left,d=f.clientY-this.boundingRect.top);a=a.touches.item(0);if(!a)return}else this.wasTouched=!1;this.boundingRect&&a.clientX&&(b=a.clientX-this.boundingRect.left,c=a.clientY-this.boundingRect.top);isNaN(e)?this.mouseX=b:(this.mouseX=Math.min(b,
e),this.mouse2X=Math.max(b,e));isNaN(d)?this.mouseY=c:(this.mouseY=Math.min(c,d),this.mouse2Y=Math.max(c,d));this.autoTransform&&(this.mouseX/=this.cssScale,this.mouseY/=this.cssScale)}},handleTouchStart:function(a){this.hideBalloonReal();a&&(a.touches&&this.tapToActivate&&!this.tapped||!this.panRequired)||(this.handleMouseMove(a),this.handleMouseDown(a))},handleTouchEnd:function(a){this.wasTouched=!0;this.handleMouseMove(a);d.resetMouseOver();this.handleReleaseOutside(a)},handleReleaseOutside:function(){this.handleDocTouchEnd.call(this)},
handleMouseDown:function(a){d.resetMouseOver();this.mouseIsOver=!0;a&&a.preventDefault&&(this.panEventsEnabled?a.preventDefault():a.touches||a.preventDefault())},addLegend:function(a,b){a=d.processObject(a,d.AmLegend,this.theme);a.divId=b;a.ieW=0;var c;c="object"!=typeof b&&b?document.getElementById(b):b;this.legend=a;a.chart=this;c?(a.div=c,a.position="outside",a.autoMargins=!1):a.div=this.legendDiv;return a},removeLegend:function(){this.legend=void 0;this.previousWidth=0;this.legendDiv.innerHTML=
""},handleResize:function(){(d.isPercents(this.width)||d.isPercents(this.height))&&this.invalidateSizeReal();this.renderFix()},renderFix:function(){if(!d.VML){var a=this.container;a&&a.renderFix()}},getSVG:function(){if(d.hasSVG)return this.container},animate:function(a,b,c,e,h,f,g){a["an_"+b]&&d.removeFromArray(this.animations,a["an_"+b]);c={obj:a,frame:0,attribute:b,from:c,to:e,time:h,effect:f,suffix:g};a["an_"+b]=c;this.animations.push(c);return c},setLegendData:function(a){var b=this.legend;b&&
b.setData(a)},stopAnim:function(a){d.removeFromArray(this.animations,a)},updateAnimations:function(){var a;this.container&&this.container.update();if(this.animations)for(a=this.animations.length-1;0<=a;a--){var b=this.animations[a],c=d.updateRate*b.time,e=b.frame+1,h=b.obj,f=b.attribute;if(e<=c){b.frame++;var g=Number(b.from),k=Number(b.to)-g,c=d[b.effect](0,e,g,k,c);0===k?(this.animations.splice(a,1),h.node.style[f]=Number(b.to)+b.suffix):h.node.style[f]=c+b.suffix}else h.node.style[f]=Number(b.to)+
b.suffix,h.animationFinished=!0,this.animations.splice(a,1)}},update:function(){this.updateAnimations();var a=this.animatable;if(0<a.length){for(var b=!0,c=a.length-1;0<=c;c--){var e=a[c];e&&(e.animationFinished?a.splice(c,1):b=!1)}b&&(this.fire({type:"animationFinished",chart:this}),this.animatable=[])}},inIframe:function(){try{return window.self!==window.top}catch(a){return!0}},brr:function(){if(!this.hideCredits){var a="amcharts.com",b=window.location.hostname.split("."),c;2<=b.length&&(c=b[b.length-
2]+"."+b[b.length-1]);this.amLink&&(b=this.amLink.parentNode)&&b.removeChild(this.amLink);b=this.creditsPosition;if(c!=a||!0===this.inIframe()){var a="http://www."+a,e=c=0,d=this.realWidth,f=this.realHeight,g=this.type;if("serial"==g||"xy"==g||"gantt"==g)c=this.marginLeftReal,e=this.marginTopReal,d=c+this.plotAreaWidth,f=e+this.plotAreaHeight;var g=a+"/javascript-charts/",k="JavaScript charts",l="JS chart by amCharts";"ammap"==this.product&&(g=a+"/javascript-maps/",k="Interactive JavaScript maps",
l="JS map by amCharts");a=document.createElement("a");l=document.createTextNode(l);a.setAttribute("href",g);a.setAttribute("title",k);this.urlTarget&&a.setAttribute("target",this.urlTarget);a.appendChild(l);this.chartDiv.appendChild(a);this.amLink=a;g=a.style;g.position="absolute";g.textDecoration="none";g.color=this.color;g.fontFamily=this.fontFamily;g.fontSize="11px";g.opacity=.7;g.display="block";var k=a.offsetWidth,a=a.offsetHeight,l=5+c,m=e+5;"bottom-left"==b&&(l=5+c,m=f-a-3);"bottom-right"==
b&&(l=d-k-5,m=f-a-3);"top-right"==b&&(l=d-k-5,m=e+5);g.left=l+"px";g.top=m+"px"}}}});d.Slice=d.Class({construct:function(){}});d.SerialDataItem=d.Class({construct:function(){}});d.GraphDataItem=d.Class({construct:function(){}});d.Guide=d.Class({construct:function(a){this.cname="Guide";d.applyTheme(this,a,this.cname)}});d.Title=d.Class({construct:function(a){this.cname="Title";d.applyTheme(this,a,this.cname)}});d.Label=d.Class({construct:function(a){this.cname="Label";d.applyTheme(this,a,this.cname)}})})();(function(){var d=window.AmCharts;d.AmGraph=d.Class({construct:function(a){this.cname="AmGraph";this.createEvents("rollOverGraphItem","rollOutGraphItem","clickGraphItem","doubleClickGraphItem","rightClickGraphItem","clickGraph","rollOverGraph","rollOutGraph");this.type="line";this.stackable=!0;this.columnCount=1;this.columnIndex=0;this.centerCustomBullets=this.showBalloon=!0;this.maxBulletSize=50;this.minBulletSize=4;this.balloonText="[[value]]";this.hidden=this.scrollbar=this.animationPlayed=!1;
this.pointPosition="middle";this.depthCount=1;this.includeInMinMax=!0;this.negativeBase=0;this.visibleInLegend=!0;this.showAllValueLabels=!1;this.showBulletsAt=this.showBalloonAt="close";this.lineThickness=1;this.dashLength=0;this.connect=!0;this.lineAlpha=1;this.bullet="none";this.bulletBorderThickness=2;this.bulletBorderAlpha=0;this.bulletAlpha=1;this.bulletSize=8;this.cornerRadiusTop=this.hideBulletsCount=this.bulletOffset=0;this.cursorBulletAlpha=1;this.gradientOrientation="vertical";this.dy=
this.dx=0;this.periodValue="";this.clustered=!0;this.periodSpan=1;this.accessibleLabel="[[title]] [[category]] [[value]]";this.accessibleSkipText="Press enter to skip [[title]]";this.y=this.x=0;this.switchable=!0;this.tcc=this.minDistance=1;this.labelRotation=0;this.labelAnchor="auto";this.labelOffset=3;this.bcn="graph-";this.dateFormat="MMM DD, YYYY";this.noRounding=!0;d.applyTheme(this,a,this.cname)},init:function(){this.createBalloon()},draw:function(){var a=this.chart;a.isRolledOverBullet=!1;
var b=a.type;if(a.drawGraphs){isNaN(this.precision)||(this.numberFormatter?this.numberFormatter.precision=this.precision:this.numberFormatter={precision:this.precision,decimalSeparator:a.decimalSeparator,thousandsSeparator:a.thousandsSeparator});var c=a.container;this.container=c;this.destroy();var e=c.set();this.set=e;e.translate(this.x,this.y);var h=c.set();this.bulletSet=h;h.translate(this.x,this.y);this.behindColumns?(a.graphsBehindSet.push(e),a.bulletBehindSet.push(h)):(a.graphsSet.push(e),a.bulletSet.push(h));
var f=this.bulletAxis;d.isString(f)&&(this.bulletAxis=a.getValueAxisById(f));c=c.set();d.remove(this.columnsSet);this.columnsSet=c;d.setCN(a,e,"graph-"+this.type);d.setCN(a,e,"graph-"+this.id);d.setCN(a,h,"graph-"+this.type);d.setCN(a,h,"graph-"+this.id);this.columnsArray=[];this.ownColumns=[];this.allBullets=[];this.animationArray=[];h=this.labelPosition;h||(f=this.valueAxis.stackType,h="top","column"==this.type&&(a.rotate&&(h="right"),"100%"==f||"regular"==f)&&(h="middle"),this.labelPosition=h);
d.ifArray(this.data)&&(a=!1,"xy"==b?this.xAxis.axisCreated&&this.yAxis.axisCreated&&(a=!0):this.valueAxis.axisCreated&&(a=!0),!this.hidden&&a&&this.createGraph());e.push(c)}},createGraph:function(){var a=this,b=a.chart;a.startAlpha=b.startAlpha;a.seqAn=b.sequencedAnimation;a.baseCoord=a.valueAxis.baseCoord;void 0===a.fillAlphas&&(a.fillAlphas=0);a.bulletColorR=a.bulletColor;void 0===a.bulletColorR&&(a.bulletColorR=a.lineColorR,a.bulletColorNegative=a.negativeLineColor);void 0===a.bulletAlpha&&(a.bulletAlpha=
a.lineAlpha);if("step"==c||d.VML)a.noRounding=!1;var c=b.type;"gantt"==c&&(c="serial");clearTimeout(a.playedTO);if(!isNaN(a.valueAxis.min)&&!isNaN(a.valueAxis.max)){switch(c){case "serial":a.categoryAxis&&(a.createSerialGraph(),"candlestick"==a.type&&1>a.valueAxis.minMaxMultiplier&&a.positiveClip(a.set));break;case "radar":a.createRadarGraph();break;case "xy":a.createXYGraph()}a.playedTO=setTimeout(function(){a.setAnimationPlayed.call(a)},500*a.chart.startDuration)}},setAnimationPlayed:function(){this.animationPlayed=
!0},createXYGraph:function(){var a=[],b=[],c=this.xAxis,e=this.yAxis;this.pmh=e.height;this.pmw=c.width;this.pmy=this.pmx=0;var d;for(d=this.start;d<=this.end;d++){var f=this.data[d].axes[c.id].graphs[this.id],g=f.values,k=g.x,l=g.y,g=c.getCoordinate(k,this.noRounding),m=e.getCoordinate(l,this.noRounding);if(!isNaN(k)&&!isNaN(l)&&(a.push(g),b.push(m),f.x=g,f.y=m,k=this.createBullet(f,g,m,d),l=this.labelText)){var l=this.createLabel(f,l),n=0;k&&(n=k.size);this.positionLabel(f,g,m,l,n)}}this.drawLineGraph(a,
b);this.launchAnimation()},createRadarGraph:function(){var a=this.valueAxis.stackType,b=[],c=[],e=[],d=[],f,g,k,l,m;for(m=this.start;m<=this.end;m++){var n=this.data[m].axes[this.valueAxis.id].graphs[this.id],q,p;"none"==a||"3d"==a?q=n.values.value:(q=n.values.close,p=n.values.open);if(isNaN(q))this.connect||(this.drawLineGraph(b,c,e,d),b=[],c=[],e=[],d=[]);else{var t=this.valueAxis.getCoordinate(q,this.noRounding)-this.height,t=t*this.valueAxis.rMultiplier,r=-360/(this.end-this.start+1)*m;"middle"==
this.valueAxis.pointPosition&&(r-=180/(this.end-this.start+1));q=t*Math.sin(r/180*Math.PI);t*=Math.cos(r/180*Math.PI);b.push(q);c.push(t);if(!isNaN(p)){var w=this.valueAxis.getCoordinate(p,this.noRounding)-this.height,w=w*this.valueAxis.rMultiplier,y=w*Math.sin(r/180*Math.PI),r=w*Math.cos(r/180*Math.PI);e.push(y);d.push(r);isNaN(k)&&(k=y);isNaN(l)&&(l=r)}r=this.createBullet(n,q,t,m);n.x=q;n.y=t;if(y=this.labelText)y=this.createLabel(n,y),w=0,r&&(w=r.size),this.positionLabel(n,q,t,y,w);isNaN(f)&&(f=
q);isNaN(g)&&(g=t)}}b.push(f);c.push(g);isNaN(k)||(e.push(k),d.push(l));this.drawLineGraph(b,c,e,d);this.launchAnimation()},positionLabel:function(a,b,c,e,d){if(e){var f=this.chart,g=this.valueAxis,k="middle",l=!1,m=this.labelPosition,n=e.getBBox(),q=this.chart.rotate,p=a.isNegative,t=this.fontSize;void 0===t&&(t=this.chart.fontSize);c-=n.height/2-t/2-1;void 0!==a.labelIsNegative&&(p=a.labelIsNegative);switch(m){case "right":m=q?p?"left":"right":"right";break;case "top":m=q?"top":p?"bottom":"top";
break;case "bottom":m=q?"bottom":p?"top":"bottom";break;case "left":m=q?p?"right":"left":"left"}var t=a.columnGraphics,r=0,w=0;t&&(r=t.x,w=t.y);var y=this.labelOffset;switch(m){case "right":k="start";b+=d/2+y;break;case "top":c=g.reversed?c+(d/2+n.height/2+y):c-(d/2+n.height/2+y);break;case "bottom":c=g.reversed?c-(d/2+n.height/2+y):c+(d/2+n.height/2+y);break;case "left":k="end";b-=d/2+y;break;case "inside":"column"==this.type&&(l=!0,q?p?(k="end",b=r-3-y):(k="start",b=r+3+y):c=p?w+7+y:w-10-y);break;
case "middle":"column"==this.type&&(l=!0,q?b-=(b-r)/2+y-3:c-=(c-w)/2+y-3)}"auto"!=this.labelAnchor&&(k=this.labelAnchor);e.attr({"text-anchor":k});this.labelRotation&&e.rotate(this.labelRotation);e.translate(b,c);!this.showAllValueLabels&&t&&l&&(n=e.getBBox(),n.height>a.columnHeight||n.width>a.columnWidth)&&(e.remove(),e=null);if(e&&"radar"!=f.type)if(q){if(0>c||c>this.height)e.remove(),e=null;!this.showAllValueLabels&&e&&(0>b||b>this.width)&&(e.remove(),e=null)}else{if(0>b||b>this.width)e.remove(),
e=null;!this.showAllValueLabels&&e&&(0>c||c>this.height)&&(e.remove(),e=null)}e&&this.allBullets.push(e);return e}},getGradRotation:function(){var a=270;"horizontal"==this.gradientOrientation&&(a=0);return this.gradientRotation=a},createSerialGraph:function(){this.dashLengthSwitched=this.fillColorsSwitched=this.lineColorSwitched=void 0;var a=this.chart,b=this.id,c=this.index,e=this.data,h=this.chart.container,f=this.valueAxis,g=this.type,k=this.columnWidthReal,l=this.showBulletsAt;isNaN(this.columnWidth)||
(k=this.columnWidth);isNaN(k)&&(k=.8);var m=this.useNegativeColorIfDown,n=this.width,q=this.height,p=this.y,t=this.rotate,r=this.columnCount,w=d.toCoordinate(this.cornerRadiusTop,k/2),y=this.connect,x=[],u=[],A,z,B,D,C=this.chart.graphs.length,K,H=this.dx/this.tcc,Q=this.dy/this.tcc,M=f.stackType,P=this.start,ia=this.end,I=this.scrollbar,aa="graph-column-";I&&(aa="scrollbar-graph-column-");var ma=this.categoryAxis,na=this.baseCoord,Pa=this.negativeBase,Z=this.columnIndex,da=this.lineThickness,X=this.lineAlpha,
xa=this.lineColorR,ea=this.dashLength,fa=this.set,Ba,ga=this.getGradRotation(),V=this.chart.columnSpacing,Y=ma.cellWidth,Da=(Y*k-r)/r;V>Da&&(V=Da);var G,v,oa,ha=q,Qa=n,ca=0,tb=0,ub,vb,gb,hb,wb=this.fillColorsR,Ra=this.negativeFillColors,Ja=this.negativeLineColor,Ya=this.fillAlphas,Za=this.negativeFillAlphas;"object"==typeof Ya&&(Ya=Ya[0]);"object"==typeof Za&&(Za=Za[0]);var xb=this.noRounding;"step"==g&&(xb=!1);var ib=f.getCoordinate(f.min);f.logarithmic&&(ib=f.getCoordinate(f.minReal));this.minCoord=
ib;this.resetBullet&&(this.bullet="none");if(!(I||"line"!=g&&"smoothedLine"!=g&&"step"!=g||(1==e.length&&"step"!=g&&"none"==this.bullet&&(this.bullet="round",this.resetBullet=!0),!Ra&&void 0==Ja||m))){var Ua=Pa;Ua>f.max&&(Ua=f.max);Ua<f.min&&(Ua=f.min);f.logarithmic&&(Ua=f.minReal);var Ka=f.getCoordinate(Ua),Mb=f.getCoordinate(f.max);t?(ha=q,Qa=Math.abs(Mb-Ka),ub=q,vb=Math.abs(ib-Ka),hb=tb=0,f.reversed?(ca=0,gb=Ka):(ca=Ka,gb=0)):(Qa=n,ha=Math.abs(Mb-Ka),vb=n,ub=Math.abs(ib-Ka),gb=ca=0,f.reversed?
(hb=p,tb=Ka):hb=Ka)}var La=Math.round;this.pmx=La(ca);this.pmy=La(tb);this.pmh=La(ha);this.pmw=La(Qa);this.nmx=La(gb);this.nmy=La(hb);this.nmh=La(ub);this.nmw=La(vb);d.isModern||(this.nmy=this.nmx=0,this.nmh=this.height);this.clustered||(r=1);k="column"==g?(Y*k-V*(r-1))/r:Y*k;1>k&&(k=1);var Nb=this.fixedColumnWidth;isNaN(Nb)||(k=Nb);var L;if("line"==g||"step"==g||"smoothedLine"==g){if(0<P){for(L=P-1;-1<L;L--)if(G=e[L],v=G.axes[f.id].graphs[b],oa=v.values.value,!isNaN(oa)){P=L;break}if(this.lineColorField)for(L=
P;-1<L;L--)if(G=e[L],v=G.axes[f.id].graphs[b],v.lineColor){this.lineColorSwitched=v.lineColor;void 0===this.bulletColor&&(this.bulletColorSwitched=this.lineColorSwitched);break}if(this.fillColorsField)for(L=P;-1<L;L--)if(G=e[L],v=G.axes[f.id].graphs[b],v.fillColors){this.fillColorsSwitched=v.fillColors;break}if(this.dashLengthField)for(L=P;-1<L;L--)if(G=e[L],v=G.axes[f.id].graphs[b],!isNaN(v.dashLength)){this.dashLengthSwitched=v.dashLength;break}}if(ia<e.length-1)for(L=ia+1;L<e.length;L++)if(G=e[L],
v=G.axes[f.id].graphs[b],oa=v.values.value,!isNaN(oa)){ia=L;break}}ia<e.length-1&&ia++;var T=[],U=[],Ma=!1;if("line"==g||"step"==g||"smoothedLine"==g)if(this.stackable&&"regular"==M||"100%"==M||this.fillToGraph)Ma=!0;var Ob=this.noStepRisers,jb=-1E3,kb=-1E3,lb=this.minDistance,Sa=!0,$a=!1;for(L=P;L<=ia;L++){G=e[L];v=G.axes[f.id].graphs[b];v.index=L;var ab,Ta=NaN;if(m&&void 0==this.openField)for(var yb=L+1;yb<e.length&&(!e[yb]||!(ab=e[L+1].axes[f.id].graphs[b])||!ab.values||(Ta=ab.values.value,isNaN(Ta)));yb++);
var S,R,J,ba,ja=NaN,E=NaN,F=NaN,O=NaN,N=NaN,qa=NaN,ra=NaN,sa=NaN,ta=NaN,ya=NaN,Ea=NaN,ka=NaN,la=NaN,W=NaN,zb=NaN,Ab=NaN,ua=NaN,va=void 0,Na=wb,Va=Ya,Ha=xa,Ca,za,Bb=this.proCandlesticks,mb=this.topRadius,Fa=q-1,pa=n-1,bb=this.pattern;void 0!=v.pattern&&(bb=v.pattern);isNaN(v.alpha)||(Va=v.alpha);isNaN(v.dashLength)||(ea=v.dashLength);var Ia=v.values;f.recalculateToPercents&&(Ia=v.percents);"none"==M&&(Z=isNaN(v.columnIndex)?this.columnIndex:v.columnIndex);if(Ia){W=this.stackable&&"none"!=M&&"3d"!=
M?Ia.close:Ia.value;if("candlestick"==g||"ohlc"==g)W=Ia.close,Ab=Ia.low,ra=f.getCoordinate(Ab),zb=Ia.high,ta=f.getCoordinate(zb);ua=Ia.open;F=f.getCoordinate(W,xb);isNaN(ua)||(N=f.getCoordinate(ua,xb),m&&"regular"!=M&&"100%"!=M&&(Ta=ua,ua=N=NaN));m&&(void 0==this.openField?ab&&(ab.isNegative=Ta<W?!0:!1,isNaN(Ta)&&(v.isNegative=!Sa)):v.isNegative=Ta>W?!0:!1);if(!I)switch(this.showBalloonAt){case "close":v.y=F;break;case "open":v.y=N;break;case "high":v.y=ta;break;case "low":v.y=ra}var ja=G.x[ma.id],
Wa=this.periodSpan-1;"step"!=g||isNaN(G.cellWidth)||(Y=G.cellWidth);var wa=Math.floor(Y/2)+Math.floor(Wa*Y/2),Ga=wa,nb=0;"left"==this.stepDirection&&(nb=(2*Y+Wa*Y)/2,ja-=nb);"center"==this.stepDirection&&(nb=Y/2,ja-=nb);"start"==this.pointPosition&&(ja-=Y/2+Math.floor(Wa*Y/2),wa=0,Ga=Math.floor(Y)+Math.floor(Wa*Y));"end"==this.pointPosition&&(ja+=Y/2+Math.floor(Wa*Y/2),wa=Math.floor(Y)+Math.floor(Wa*Y),Ga=0);if(Ob){var Cb=this.columnWidth;isNaN(Cb)||(wa*=Cb,Ga*=Cb)}I||(v.x=ja);-1E5>ja&&(ja=-1E5);
ja>n+1E5&&(ja=n+1E5);t?(E=F,O=N,N=F=ja,isNaN(ua)&&!this.fillToGraph&&(O=na),qa=ra,sa=ta):(O=E=ja,isNaN(ua)&&!this.fillToGraph&&(N=na));if(!Bb&&W<ua||Bb&&W<Ba)v.isNegative=!0,Ra&&(Na=Ra),Za&&(Va=Za),void 0!=Ja&&(Ha=Ja);$a=!1;isNaN(W)||(m?W>Ta?(Sa&&($a=!0),Sa=!1):(Sa||($a=!0),Sa=!0):v.isNegative=W<Pa?!0:!1,Ba=W);var Pb=!1;I&&a.chartScrollbar.ignoreCustomColors&&(Pb=!0);Pb||(void 0!=v.color&&(Na=v.color),v.fillColors&&(Na=v.fillColors));F=d.fitToBounds(F,-3E4,3E4);switch(g){case "line":if(isNaN(W))y||
(this.drawLineGraph(x,u,T,U),x=[],u=[],T=[],U=[]);else{if(Math.abs(E-jb)>=lb||Math.abs(F-kb)>=lb)x.push(E),u.push(F),jb=E,kb=F;ya=E;Ea=F;ka=E;la=F;!Ma||isNaN(N)||isNaN(O)||(T.push(O),U.push(N));if($a||void 0!=v.lineColor&&v.lineColor!=this.lineColorSwitched||void 0!=v.fillColors&&v.fillColors!=this.fillColorsSwitched||!isNaN(v.dashLength))this.drawLineGraph(x,u,T,U),x=[E],u=[F],T=[],U=[],!Ma||isNaN(N)||isNaN(O)||(T.push(O),U.push(N)),m?(Sa?(this.lineColorSwitched=xa,this.fillColorsSwitched=wb):(this.lineColorSwitched=
Ja,this.fillColorsSwitched=Ra),void 0===this.bulletColor&&(this.bulletColorSwitched=xa)):(this.lineColorSwitched=v.lineColor,this.fillColorsSwitched=v.fillColors,void 0===this.bulletColor&&(this.bulletColorSwitched=this.lineColorSwitched)),this.dashLengthSwitched=v.dashLength;v.gap&&(this.drawLineGraph(x,u,T,U),x=[],u=[],T=[],U=[])}break;case "smoothedLine":if(isNaN(W))y||(this.drawSmoothedGraph(x,u,T,U),x=[],u=[],T=[],U=[]);else{if(Math.abs(E-jb)>=lb||Math.abs(F-kb)>=lb)x.push(E),u.push(F),jb=E,
kb=F;ya=E;Ea=F;ka=E;la=F;!Ma||isNaN(N)||isNaN(O)||(T.push(O),U.push(N));void 0==v.lineColor&&void 0==v.fillColors&&isNaN(v.dashLength)||(this.drawSmoothedGraph(x,u,T,U),x=[E],u=[F],T=[],U=[],!Ma||isNaN(N)||isNaN(O)||(T.push(O),U.push(N)),this.lineColorSwitched=v.lineColor,this.fillColorsSwitched=v.fillColors,this.dashLengthSwitched=v.dashLength);v.gap&&(this.drawSmoothedGraph(x,u,T,U),x=[],u=[],T=[],U=[])}break;case "step":if(!isNaN(W)){t?(isNaN(A)||(x.push(A),u.push(F-wa)),u.push(F-wa),x.push(E),
u.push(F+Ga),x.push(E),!Ma||isNaN(N)||isNaN(O)||(isNaN(B)||(T.push(B),U.push(N-wa)),T.push(O),U.push(N-wa),T.push(O),U.push(N+Ga))):(isNaN(z)||(u.push(z),x.push(E-wa)),x.push(E-wa),u.push(F),x.push(E+Ga),u.push(F),!Ma||isNaN(N)||isNaN(O)||(isNaN(D)||(T.push(O-wa),U.push(D)),T.push(O-wa),U.push(N),T.push(O+Ga),U.push(N)));A=E;z=F;B=O;D=N;ya=E;Ea=F;ka=E;la=F;if($a||void 0!=v.lineColor||void 0!=v.fillColors||!isNaN(v.dashLength)){var Db=x[x.length-2],dc=u[u.length-2];x.pop();u.pop();T.pop();U.pop();
this.drawLineGraph(x,u,T,U);x=[Db];u=[dc];T=[];U=[];Ma&&(T=[Db,Db+wa+Ga],U=[D,D]);t?(u.push(F+Ga),x.push(E)):(x.push(E+Ga),u.push(F));this.lineColorSwitched=v.lineColor;this.fillColorsSwitched=v.fillColors;this.dashLengthSwitched=v.dashLength;m&&(Sa?(this.lineColorSwitched=xa,this.fillColorsSwitched=wb):(this.lineColorSwitched=Ja,this.fillColorsSwitched=Ra))}if(Ob||v.gap)A=z=NaN,this.drawLineGraph(x,u,T,U),x=[],u=[],T=[],U=[]}else if(!y){if(1>=this.periodSpan||1<this.periodSpan&&E-A>wa+Ga)A=z=NaN;
this.drawLineGraph(x,u,T,U);x=[];u=[];T=[];U=[]}break;case "column":Ca=Ha;void 0!=v.lineColor&&(Ca=v.lineColor);if(!isNaN(W)){m||(v.isNegative=W<Pa?!0:!1);v.isNegative&&(Ra&&(Na=Ra),void 0!=Ja&&(Ca=Ja));var Qb=f.min,Rb=f.max,ob=ua;isNaN(ob)&&(ob=Pa);if(!(W<Qb&&ob<Qb||W>Rb&&ob>Rb)){var Aa;if(t){"3d"==M?(R=F-(r/2-this.depthCount+1)*(k+V)+V/2+Q*Z,S=O+H*Z,Aa=Z):(R=Math.floor(F-(r/2-Z)*(k+V)+V/2),S=O,Aa=0);J=k;ya=E;Ea=R+k/2;ka=E;la=R+k/2;R+J>q+Aa*Q&&(J=q-R+Aa*Q);R<Aa*Q&&(J+=R,R=Aa*Q);ba=E-O;var ec=S;S=
d.fitToBounds(S,0,n);ba+=ec-S;ba=d.fitToBounds(ba,-S,n-S+H*Z);v.labelIsNegative=0>ba?!0:!1;0===ba&&1/W===1/-0&&(v.labelIsNegative=!0);isNaN(G.percentWidthValue)||(J=this.height*G.percentWidthValue/100,R=ja-J/2,Ea=R+J/2);J=d.roundTo(J,2);ba=d.roundTo(ba,2);R<q&&0<J&&(va=new d.Cuboid(h,ba,J,H-a.d3x,Q-a.d3y,Na,Va,da,Ca,X,ga,w,t,ea,bb,mb,aa),v.columnWidth=Math.abs(ba),v.columnHeight=Math.abs(J))}else{"3d"==M?(S=E-(r/2-this.depthCount+1)*(k+V)+V/2+H*Z,R=N+Q*Z,Aa=Z):(S=E-(r/2-Z)*(k+V)+V/2,R=N,Aa=0);J=k;
ya=S+k/2;Ea=F;ka=S+k/2;la=F;S+J>n+Aa*H&&(J=n-S+Aa*H);S<Aa*H&&(J+=S-Aa*H,S=Aa*H);ba=F-N;v.labelIsNegative=0<ba?!0:!1;0===ba&&1/W!==1/Math.abs(W)&&(v.labelIsNegative=!0);var fc=R;R=d.fitToBounds(R,this.dy,q);ba+=fc-R;ba=d.fitToBounds(ba,-R+Q*Aa,q-R);isNaN(G.percentWidthValue)||(J=this.width*G.percentWidthValue/100,S=ja-J/2,ya=S+J/2);J=d.roundTo(J,2);ba=d.roundTo(ba,2);S<n+Z*H&&0<J&&(this.showOnAxis&&(R-=Q/2),va=new d.Cuboid(h,J,ba,H-a.d3x,Q-a.d3y,Na,Va,da,Ca,this.lineAlpha,ga,w,t,ea,bb,mb,aa),v.columnHeight=
Math.abs(ba),v.columnWidth=Math.abs(J))}}if(va){za=va.set;d.setCN(a,va.set,"graph-"+this.type);d.setCN(a,va.set,"graph-"+this.id);v.className&&d.setCN(a,va.set,v.className,!0);v.columnGraphics=za;S=d.roundTo(S,2);R=d.roundTo(R,2);za.translate(S,R);(v.url||this.showHandOnHover)&&za.setAttr("cursor","pointer");if(!I){"none"==M&&(K=t?(this.end+1-L)*C-c:C*L+c);"3d"==M&&(t?(K=(this.end+1-L)*C-c-1E3*this.depthCount,ya+=H*Z,ka+=H*Z,v.y+=H*Z):(K=(C-c)*(L+1)+1E3*this.depthCount,Ea+=Q*Z,la+=Q*Z,v.y+=Q*Z));
if("regular"==M||"100%"==M)K=t?0<Ia.value?(this.end+1-L)*C+c:(this.end+1-L)*C-c:0<Ia.value?C*L+c:C*L-c;this.columnsArray.push({column:va,depth:K});v.x=t?R+J/2:S+J/2;this.ownColumns.push(va);this.animateColumns(va,L,E,O,F,N);this.addListeners(za,v);void 0!==this.tabIndex&&za.setAttr("tabindex",this.tabIndex)}this.columnsSet.push(za)}}break;case "candlestick":if(!isNaN(ua)&&!isNaN(W)){var Xa,cb;Ca=Ha;void 0!=v.lineColor&&(Ca=v.lineColor);ya=E;la=Ea=F;ka=E;if(t){"open"==l&&(ka=O);"high"==l&&(ka=sa);
"low"==l&&(ka=qa);E=d.fitToBounds(E,0,pa);O=d.fitToBounds(O,0,pa);qa=d.fitToBounds(qa,0,pa);sa=d.fitToBounds(sa,0,pa);if(0===E&&0===O&&0===qa&&0===sa)continue;if(E==pa&&O==pa&&qa==pa&&sa==pa)continue;R=F-k/2;S=O;J=k;R+J>q&&(J=q-R);0>R&&(J+=R,R=0);if(R<q&&0<J){var Eb,Fb;W>ua?(Eb=[E,sa],Fb=[O,qa]):(Eb=[O,sa],Fb=[E,qa]);!isNaN(sa)&&!isNaN(qa)&&F<q&&0<F&&(Xa=d.line(h,Eb,[F,F],Ca,X,da),cb=d.line(h,Fb,[F,F],Ca,X,da));ba=E-O;va=new d.Cuboid(h,ba,J,H,Q,Na,Ya,da,Ca,X,ga,w,t,ea,bb,mb,aa)}}else{"open"==l&&(la=
N);"high"==l&&(la=ta);"low"==l&&(la=ra);F=d.fitToBounds(F,0,Fa);N=d.fitToBounds(N,0,Fa);ra=d.fitToBounds(ra,0,Fa);ta=d.fitToBounds(ta,0,Fa);if(0===F&&0===N&&0===ra&&0===ta)continue;if(F==Fa&&N==Fa&&ra==Fa&&ta==Fa)continue;S=E-k/2;R=N+da/2;J=k;S+J>n&&(J=n-S);0>S&&(J+=S,S=0);ba=F-N;if(S<n&&0<J){Bb&&W>=ua&&(Va=0);var va=new d.Cuboid(h,J,ba,H,Q,Na,Va,da,Ca,X,ga,w,t,ea,bb,mb,aa),Gb,Hb;W>ua?(Gb=[F,ta],Hb=[N,ra]):(Gb=[N,ta],Hb=[F,ra]);!isNaN(ta)&&!isNaN(ra)&&E<n&&0<E&&(Xa=d.line(h,[E,E],Gb,Ca,X,da),cb=d.line(h,
[E,E],Hb,Ca,X,da),d.setCN(a,Xa,this.bcn+"line-high"),v.className&&d.setCN(a,Xa,v.className,!0),d.setCN(a,cb,this.bcn+"line-low"),v.className&&d.setCN(a,cb,v.className,!0))}}va&&(za=va.set,v.columnGraphics=za,fa.push(za),za.translate(S,R-da/2),(v.url||this.showHandOnHover)&&za.setAttr("cursor","pointer"),Xa&&(fa.push(Xa),fa.push(cb)),I||(v.x=t?R+J/2:S+J/2,this.animateColumns(va,L,E,O,F,N),this.addListeners(za,v),void 0!==this.tabIndex&&za.setAttr("tabindex",this.tabIndex)))}break;case "ohlc":if(!(isNaN(ua)||
isNaN(zb)||isNaN(Ab)||isNaN(W))){var Sb=h.set();fa.push(Sb);W<ua&&(v.isNegative=!0,void 0!=Ja&&(Ha=Ja));void 0!=v.lineColor&&(Ha=v.lineColor);var pb,qb,rb;if(t){la=F;ka=E;"open"==l&&(ka=O);"high"==l&&(ka=sa);"low"==l&&(ka=qa);qa=d.fitToBounds(qa,0,pa);sa=d.fitToBounds(sa,0,pa);if(0===E&&0===O&&0===qa&&0===sa)continue;if(E==pa&&O==pa&&qa==pa&&sa==pa)continue;var Ib=F-k/2,Ib=d.fitToBounds(Ib,0,q),Tb=d.fitToBounds(F,0,q),Jb=F+k/2,Jb=d.fitToBounds(Jb,0,q);0<=O&&O<=pa&&(qb=d.line(h,[O,O],[Ib,Tb],Ha,X,
da,ea));0<F&&F<q&&(pb=d.line(h,[qa,sa],[F,F],Ha,X,da,ea));0<=E&&E<=pa&&(rb=d.line(h,[E,E],[Tb,Jb],Ha,X,da,ea))}else{la=F;"open"==l&&(la=N);"high"==l&&(la=ta);"low"==l&&(la=ra);var ka=E,ra=d.fitToBounds(ra,0,Fa),ta=d.fitToBounds(ta,0,Fa),Kb=E-k/2,Kb=d.fitToBounds(Kb,0,n),Ub=d.fitToBounds(E,0,n),Lb=E+k/2,Lb=d.fitToBounds(Lb,0,n);0<=N&&N<=Fa&&(qb=d.line(h,[Kb,Ub],[N,N],Ha,X,da,ea));0<E&&E<n&&(pb=d.line(h,[E,E],[ra,ta],Ha,X,da,ea));0<=F&&F<=Fa&&(rb=d.line(h,[Ub,Lb],[F,F],Ha,X,da,ea))}fa.push(qb);fa.push(pb);
fa.push(rb);d.setCN(a,qb,this.bcn+"stroke-open");d.setCN(a,rb,this.bcn+"stroke-close");d.setCN(a,pb,this.bcn+"stroke");v.className&&d.setCN(a,Sb,v.className,!0);ya=E;Ea=F}}if(!I&&!isNaN(W)){var Vb=this.hideBulletsCount;if(this.end-this.start<=Vb||0===Vb){var Wb=this.createBullet(v,ka,la,L),Xb=this.labelText;if(Xb&&!isNaN(ya)&&!isNaN(ya)){var gc=this.createLabel(v,Xb),Yb=0;Wb&&(Yb=Wb.size);this.positionLabel(v,ya,Ea,gc,Yb)}if("regular"==M||"100%"==M){var Zb=f.totalText;if(Zb){var Oa=this.createLabel(v,
Zb,f.totalTextColor);d.setCN(a,Oa,this.bcn+"label-total");this.allBullets.push(Oa);if(Oa){var $b=Oa.getBBox(),ac=$b.width,bc=$b.height,db,eb,sb=f.totalTextOffset,cc=f.totals[L];cc&&cc.remove();var fb=0;"column"!=g&&(fb=this.bulletSize);t?(eb=Ea,db=0>W?E-ac/2-2-fb-sb:E+ac/2+3+fb+sb):(db=ya,eb=0>W?F+bc/2+fb+sb:F-bc/2-3-fb-sb);Oa.translate(db,eb);f.totals[L]=Oa;t?(0>eb||eb>q)&&Oa.remove():(0>db||db>n)&&Oa.remove()}}}}}}}this.lastDataItem=v;if("line"==g||"step"==g||"smoothedLine"==g)"smoothedLine"==g?
this.drawSmoothedGraph(x,u,T,U):this.drawLineGraph(x,u,T,U),I||this.launchAnimation();this.bulletsHidden&&this.hideBullets();this.customBulletsHidden&&this.hideCustomBullets()},animateColumns:function(a,b){var c=this,e=c.chart.startDuration;0<e&&!c.animationPlayed&&(c.seqAn?(a.set.hide(),c.animationArray.push(a),e=setTimeout(function(){c.animate.call(c)},e/(c.end-c.start+1)*(b-c.start)*1E3),c.timeOuts.push(e)):c.animate(a),c.chart.animatable.push(a))},createLabel:function(a,b,c){var e=this.chart,
h=a.labelColor;h||(h=this.color);h||(h=e.color);c&&(h=c);c=this.fontSize;void 0===c&&(this.fontSize=c=e.fontSize);var f=this.labelFunction;b=e.formatString(b,a);b=d.cleanFromEmpty(b);f&&(b=f(a,b));if(void 0!==b&&""!==b)return a=d.text(this.container,b,h,e.fontFamily,c),a.node.style.pointerEvents="none",d.setCN(e,a,this.bcn+"label"),this.bulletSet.push(a),a},positiveClip:function(a){a.clipRect(this.pmx,this.pmy,this.pmw,this.pmh)},negativeClip:function(a){a.clipRect(this.nmx,this.nmy,this.nmw,this.nmh)},
drawLineGraph:function(a,b,c,e){var h=this;if(1<a.length){var f=h.noRounding,g=h.set,k=h.chart,l=h.container,m=l.set(),n=l.set();g.push(n);g.push(m);var q=h.lineAlpha,p=h.lineThickness,g=h.fillAlphas,t=h.lineColorR,r=h.negativeLineAlpha;isNaN(r)&&(r=q);var w=h.lineColorSwitched;w&&(t=w);var w=h.fillColorsR,y=h.fillColorsSwitched;y&&(w=y);var x=h.dashLength;(y=h.dashLengthSwitched)&&(x=y);var y=h.negativeLineColor,u=h.negativeFillColors,A=h.negativeFillAlphas,z=h.baseCoord;0!==h.negativeBase&&(z=h.valueAxis.getCoordinate(h.negativeBase,
f),z>h.height&&(z=h.height),0>z&&(z=0));q=d.line(l,a,b,t,q,p,x,!1,!0,f);q.node.setAttribute("stroke-linejoin","round");d.setCN(k,q,h.bcn+"stroke");m.push(q);m.click(function(a){h.handleGraphEvent(a,"clickGraph")}).mouseover(function(a){h.handleGraphEvent(a,"rollOverGraph")}).mouseout(function(a){h.handleGraphEvent(a,"rollOutGraph")}).touchmove(function(a){h.chart.handleMouseMove(a)}).touchend(function(a){h.chart.handleTouchEnd(a)});void 0===y||h.useNegativeColorIfDown||(p=d.line(l,a,b,y,r,p,x,!1,
!0,f),p.node.setAttribute("stroke-linejoin","round"),d.setCN(k,p,h.bcn+"stroke"),d.setCN(k,p,h.bcn+"stroke-negative"),n.push(p));if(0<g||0<A)if(p=a.join(";").split(";"),r=b.join(";").split(";"),q=k.type,"serial"==q||"radar"==q?0<c.length?(c.reverse(),e.reverse(),p=a.concat(c),r=b.concat(e)):"radar"==q?(r.push(0),p.push(0)):h.rotate?(r.push(r[r.length-1]),p.push(z),r.push(r[0]),p.push(z),r.push(r[0]),p.push(p[0])):(p.push(p[p.length-1]),r.push(z),p.push(p[0]),r.push(z),p.push(a[0]),r.push(r[0])):"xy"==
q&&(b=h.fillToAxis)&&(d.isString(b)&&(b=k.getValueAxisById(b)),"H"==b.orientation?(z="top"==b.position?0:b.height,p.push(p[p.length-1]),r.push(z),p.push(p[0]),r.push(z),p.push(a[0]),r.push(r[0])):(z="left"==b.position?0:b.width,r.push(r[r.length-1]),p.push(z),r.push(r[0]),p.push(z),r.push(r[0]),p.push(p[0]))),a=h.gradientRotation,0<g&&(b=d.polygon(l,p,r,w,g,1,"#000",0,a,f),b.pattern(h.pattern,NaN,k.path),d.setCN(k,b,h.bcn+"fill"),m.push(b)),u||void 0!==y)isNaN(A)&&(A=g),u||(u=y),f=d.polygon(l,p,r,
u,A,1,"#000",0,a,f),d.setCN(k,f,h.bcn+"fill"),d.setCN(k,f,h.bcn+"fill-negative"),f.pattern(h.pattern,NaN,k.path),n.push(f),n.click(function(a){h.handleGraphEvent(a,"clickGraph")}).mouseover(function(a){h.handleGraphEvent(a,"rollOverGraph")}).mouseout(function(a){h.handleGraphEvent(a,"rollOutGraph")}).touchmove(function(a){h.chart.handleMouseMove(a)}).touchend(function(a){h.chart.handleTouchEnd(a)});h.applyMask(n,m)}},applyMask:function(a,b){var c=a.length();"serial"!=this.chart.type||this.scrollbar||
(this.positiveClip(b),0<c&&this.negativeClip(a))},drawSmoothedGraph:function(a,b,c,e){if(1<a.length){var h=this.set,f=this.chart,g=this.container,k=g.set(),l=g.set();h.push(l);h.push(k);var m=this.lineAlpha,n=this.lineThickness,h=this.dashLength,q=this.fillAlphas,p=this.lineColorR,t=this.fillColorsR,r=this.negativeLineColor,w=this.negativeFillColors,y=this.negativeFillAlphas,x=this.baseCoord,u=this.lineColorSwitched;u&&(p=u);(u=this.fillColorsSwitched)&&(t=u);var A=this.negativeLineAlpha;isNaN(A)&&
(A=m);u=this.getGradRotation();m=new d.Bezier(g,a,b,p,m,n,t,0,h,void 0,u);d.setCN(f,m,this.bcn+"stroke");k.push(m.path);void 0!==r&&(n=new d.Bezier(g,a,b,r,A,n,t,0,h,void 0,u),d.setCN(f,n,this.bcn+"stroke"),d.setCN(f,n,this.bcn+"stroke-negative"),l.push(n.path));0<q&&(m=a.join(";").split(";"),p=b.join(";").split(";"),n="",0<c.length?(c.push("M"),e.push("M"),c.reverse(),e.reverse(),m=a.concat(c),p=b.concat(e)):(this.rotate?(n+=" L"+x+","+b[b.length-1],n+=" L"+x+","+b[0]):(n+=" L"+a[a.length-1]+","+
x,n+=" L"+a[0]+","+x),n+=" L"+a[0]+","+b[0]),c=new d.Bezier(g,m,p,NaN,0,0,t,q,h,n,u),d.setCN(f,c,this.bcn+"fill"),c.path.pattern(this.pattern,NaN,f.path),k.push(c.path),w||void 0!==r)&&(y||(y=q),w||(w=r),a=new d.Bezier(g,a,b,NaN,0,0,w,y,h,n,u),a.path.pattern(this.pattern,NaN,f.path),d.setCN(f,a,this.bcn+"fill"),d.setCN(f,a,this.bcn+"fill-negative"),l.push(a.path));this.applyMask(l,k)}},launchAnimation:function(){var a=this,b=a.chart.startDuration;if(0<b&&!a.animationPlayed){var c=a.set,e=a.bulletSet;
d.VML||(c.attr({opacity:a.startAlpha}),e.attr({opacity:a.startAlpha}));c.hide();e.hide();a.seqAn?(b=setTimeout(function(){a.animateGraphs.call(a)},a.index*b*1E3),a.timeOuts.push(b)):a.animateGraphs()}},animateGraphs:function(){var a=this.chart,b=this.set,c=this.bulletSet,e=this.x,d=this.y;b.show();c.show();var f=a.startDuration,g=a.startEffect;b&&(this.rotate?(b.translate(-1E3,d),c.translate(-1E3,d)):(b.translate(e,-1E3),c.translate(e,-1E3)),b.animate({opacity:1,translate:e+","+d},f,g),c.animate({opacity:1,
translate:e+","+d},f,g),a.animatable.push(b))},animate:function(a){var b=this.chart,c=this.animationArray;!a&&0<c.length&&(a=c[0],c.shift());c=d[d.getEffect(b.startEffect)];b=b.startDuration;a&&(this.rotate?a.animateWidth(b,c):a.animateHeight(b,c),a.set.show())},legendKeyColor:function(){var a=this.legendColor,b=this.lineAlpha;void 0===a&&(a=this.lineColorR,0===b&&(b=this.fillColorsR)&&(a="object"==typeof b?b[0]:b));return a},legendKeyAlpha:function(){var a=this.legendAlpha;void 0===a&&(a=this.lineAlpha,
this.fillAlphas>a&&(a=this.fillAlphas),0===a&&(a=this.bulletAlpha),0===a&&(a=1));return a},createBullet:function(a,b,c){if(!isNaN(b)&&!isNaN(c)&&("none"!=this.bullet||this.customBullet||a.bullet||a.customBullet)){var e=this.chart,h=this.container,f=this.bulletOffset,g=this.bulletSize;isNaN(a.bulletSize)||(g=a.bulletSize);var k=a.values.value,l=this.maxValue,m=this.minValue,n=this.maxBulletSize,q=this.minBulletSize;isNaN(l)||(isNaN(k)||(g=(k-m)/(l-m)*(n-q)+q),m==l&&(g=n));l=g;this.bulletAxis&&(g=a.values.error,
isNaN(g)||(k=g),g=this.bulletAxis.stepWidth*k);g<this.minBulletSize&&(g=this.minBulletSize);this.rotate?b=a.isNegative?b-f:b+f:c=a.isNegative?c+f:c-f;q=this.bulletColorR;a.lineColor&&void 0===this.bulletColor&&(this.bulletColorSwitched=a.lineColor);this.bulletColorSwitched&&(q=this.bulletColorSwitched);a.isNegative&&void 0!==this.bulletColorNegative&&(q=this.bulletColorNegative);void 0!==a.color&&(q=a.color);var p;"xy"==e.type&&this.valueField&&(p=this.pattern,a.pattern&&(p=a.pattern));f=this.bullet;
a.bullet&&(f=a.bullet);var k=this.bulletBorderThickness,m=this.bulletBorderColorR,n=this.bulletBorderAlpha,t=this.bulletAlpha;m||(m=q);this.useLineColorForBulletBorder&&(m=this.lineColorR,a.isNegative&&this.negativeLineColor&&(m=this.negativeLineColor),this.lineColorSwitched&&(m=this.lineColorSwitched));var r=a.alpha;isNaN(r)||(t=r);p=d.bullet(h,f,g,q,t,k,m,n,l,0,p,e.path);l=this.customBullet;a.customBullet&&(l=a.customBullet);l&&(p&&p.remove(),"function"==typeof l?(l=new l,l.chart=e,a.bulletConfig&&
(l.availableSpace=c,l.graph=this,l.graphDataItem=a,l.bulletY=c,a.bulletConfig.minCoord=this.minCoord-c,l.bulletConfig=a.bulletConfig),l.write(h),p&&l.showBullet&&l.set.push(p),a.customBulletGraphics=l.cset,p=l.set):(p=h.set(),l=h.image(l,0,0,g,g),p.push(l),this.centerCustomBullets&&l.translate(-g/2,-g/2)));if(p){(a.url||this.showHandOnHover)&&p.setAttr("cursor","pointer");if("serial"==e.type||"gantt"==e.type)if(-.5>b||b>this.width||c<-g/2||c>this.height)p.remove(),p=null;p&&(this.bulletSet.push(p),
p.translate(b,c),this.addListeners(p,a),this.allBullets.push(p));a.bx=b;a.by=c;d.setCN(e,p,this.bcn+"bullet");a.className&&d.setCN(e,p,a.className,!0)}if(p){p.size=g||0;if(e=this.bulletHitAreaSize)h=d.circle(h,e,"#FFFFFF",.001,0),h.translate(b,c),a.hitBullet=h,this.bulletSet.push(h),this.addListeners(h,a);a.bulletGraphics=p;void 0!==this.tabIndex&&p.setAttr("tabindex",this.tabIndex)}else p={size:0};p.graphDataItem=a;return p}},showBullets:function(){var a=this.allBullets,b;this.bulletsHidden=!1;for(b=
0;b<a.length;b++)a[b].show()},hideBullets:function(){var a=this.allBullets,b;this.bulletsHidden=!0;for(b=0;b<a.length;b++)a[b].hide()},showCustomBullets:function(){var a=this.allBullets,b;this.customBulletsHidden=!1;for(b=0;b<a.length;b++){var c=a[b].graphDataItem;c.customBulletGraphics&&c.customBulletGraphics.show()}},hideCustomBullets:function(){var a=this.allBullets,b;this.customBulletsHidden=!0;for(b=0;b<a.length;b++){var c=a[b].graphDataItem;c.customBulletGraphics&&c.customBulletGraphics.hide()}},
addListeners:function(a,b){var c=this;a.mouseover(function(a){c.handleRollOver(b,a)}).mouseout(function(a){c.handleRollOut(b,a)}).touchend(function(a){c.handleRollOver(b,a);c.chart.panEventsEnabled&&c.handleClick(b,a)}).touchstart(function(a){c.handleRollOver(b,a)}).click(function(a){c.handleClick(b,a)}).dblclick(function(a){c.handleDoubleClick(b,a)}).contextmenu(function(a){c.handleRightClick(b,a)});var e=c.chart;if(e.accessible&&c.accessibleLabel){var d=e.formatString(c.accessibleLabel,b);e.makeAccessible(a,
d)}},handleRollOver:function(a,b){this.handleGraphEvent(b,"rollOverGraph");if(a){var c=this.chart;a.bulletConfig&&(c.isRolledOverBullet=!0);var e={type:"rollOverGraphItem",item:a,index:a.index,graph:this,target:this,chart:this.chart,event:b};this.fire(e);c.fire(e);clearTimeout(c.hoverInt);(c=c.chartCursor)&&c.valueBalloonsEnabled||this.showGraphBalloon(a,"V",!0)}},handleRollOut:function(a,b){var c=this.chart;if(a){var e={type:"rollOutGraphItem",item:a,index:a.index,graph:this,target:this,chart:this.chart,
event:b};this.fire(e);c.fire(e);c.isRolledOverBullet=!1}this.handleGraphEvent(b,"rollOutGraph");(c=c.chartCursor)&&c.valueBalloonsEnabled||this.hideBalloon()},handleClick:function(a,b){if(!this.chart.checkTouchMoved()&&this.chart.checkTouchDuration(b)){if(a){var c={type:"clickGraphItem",item:a,index:a.index,graph:this,target:this,chart:this.chart,event:b};this.fire(c);this.chart.fire(c);d.getURL(a.url,this.urlTarget)}this.handleGraphEvent(b,"clickGraph")}},handleGraphEvent:function(a,b){var c={type:b,
graph:this,target:this,chart:this.chart,event:a};this.fire(c);this.chart.fire(c)},handleRightClick:function(a,b){if(a){var c={type:"rightClickGraphItem",item:a,index:a.index,graph:this,target:this,chart:this.chart,event:b};this.fire(c);this.chart.fire(c)}},handleDoubleClick:function(a,b){if(a){var c={type:"doubleClickGraphItem",item:a,index:a.index,graph:this,target:this,chart:this.chart,event:b};this.fire(c);this.chart.fire(c)}},zoom:function(a,b){this.start=a;this.end=b;this.draw()},changeOpacity:function(a){var b=
this.set;b&&b.setAttr("opacity",a);if(b=this.ownColumns){var c;for(c=0;c<b.length;c++){var e=b[c].set;e&&e.setAttr("opacity",a)}}(b=this.bulletSet)&&b.setAttr("opacity",a)},destroy:function(){d.remove(this.set);d.remove(this.bulletSet);var a=this.timeOuts;if(a){var b;for(b=0;b<a.length;b++)clearTimeout(a[b])}this.timeOuts=[]},createBalloon:function(){var a=this.chart;this.balloon?this.balloon.destroy&&this.balloon.destroy():this.balloon={};var b=this.balloon;d.extend(b,a.balloon,!0);b.chart=a;b.mainSet=
a.plotBalloonsSet;b.className=this.id},hideBalloon:function(){var a=this,b=a.chart;b.chartCursor?b.chartCursor.valueBalloonsEnabled||b.hideBalloon():b.hideBalloon();clearTimeout(a.hoverInt);a.hoverInt=setTimeout(function(){a.hideBalloonReal.call(a)},b.hideBalloonTime)},hideBalloonReal:function(){this.balloon&&this.balloon.hide();this.fixBulletSize()},fixBulletSize:function(){if(d.isModern){var a=this.resizedDItem;if(a){var b=a.bulletGraphics;if(b&&!b.doNotScale){b.translate(a.bx,a.by,1);var c=this.bulletAlpha;
isNaN(a.alpha)||(c=a.alpha);b.setAttr("fill-opacity",c);b.setAttr("stroke-opacity",this.bulletBorderAlpha)}}this.resizedDItem=null}},showGraphBalloon:function(a,b,c,e,h){if(a){var f=this.chart,g=this.balloon,k=0,l=0,m=f.chartCursor,n=!0;m?m.valueBalloonsEnabled||(g=f.balloon,k=this.x,l=this.y,n=!1):(g=f.balloon,k=this.x,l=this.y,n=!1);clearTimeout(this.hoverInt);if(f.chartCursor&&(this.currentDataItem=a,"serial"==f.type&&f.isRolledOverBullet&&f.chartCursor.valueBalloonsEnabled)){this.hideBalloonReal();
return}this.resizeBullet(a,e,h);if(g&&g.enabled&&this.showBalloon&&!this.hidden){var m=f.formatString(this.balloonText,a,!0),q=this.balloonFunction;q&&(m=q(a,a.graph));m&&(m=d.cleanFromEmpty(m));m&&""!==m?(e=f.getBalloonColor(this,a),g.drop||(g.pointerOrientation=b),b=a.x,h=a.y,f.rotate&&(b=a.y,h=a.x),b+=k,h+=l,isNaN(b)||isNaN(h)?this.hideBalloonReal():(a=this.width,q=this.height,n&&g.setBounds(k,l,a+k,q+l),g.changeColor(e),g.setPosition(b,h),g.fixPrevious(),g.fixedPosition&&(c=!1),!c&&"radar"!=f.type&&
(b<k||b>a+k||h<l-.5||h>q+l)?(g.showBalloon(m),g.hide(0)):(g.followCursor(c),g.showBalloon(m)))):(this.hideBalloonReal(),g.hide(),this.resizeBullet(a,e,h))}else this.hideBalloonReal()}},resizeBullet:function(a,b,c){this.fixBulletSize();if(a&&d.isModern&&(1!=b||!isNaN(c))){var e=a.bulletGraphics;e&&!e.doNotScale&&(e.translate(a.bx,a.by,b),isNaN(c)||(e.setAttr("fill-opacity",c),e.setAttr("stroke-opacity",c)),this.resizedDItem=a)}}})})();(function(){var d=window.AmCharts;d.ChartCursor=d.Class({construct:function(a){this.cname="ChartCursor";this.createEvents("changed","zoomed","onHideCursor","onShowCursor","draw","selected","moved","panning","zoomStarted");this.enabled=!0;this.cursorAlpha=1;this.selectionAlpha=.2;this.cursorColor="#CC0000";this.categoryBalloonAlpha=1;this.color="#FFFFFF";this.type="cursor";this.zoomed=!1;this.zoomable=!0;this.pan=!1;this.categoryBalloonDateFormat="MMM DD, YYYY";this.categoryBalloonText="[[category]]";
this.categoryBalloonEnabled=this.valueBalloonsEnabled=!0;this.rolledOver=!1;this.cursorPosition="middle";this.bulletsEnabled=this.skipZoomDispatch=!1;this.bulletSize=8;this.selectWithoutZooming=this.oneBalloonOnly=!1;this.graphBulletSize=1.7;this.animationDuration=.3;this.zooming=!1;this.adjustment=0;this.avoidBalloonOverlapping=!0;this.leaveCursor=!1;this.leaveAfterTouch=!0;this.valueZoomable=!1;this.balloonPointerOrientation="horizontal";this.hLineEnabled=this.vLineEnabled=!0;this.vZoomEnabled=
this.hZoomEnabled=!1;d.applyTheme(this,a,this.cname)},draw:function(){this.destroy();var a=this.chart;a.panRequired=!0;var b=a.container;this.rotate=a.rotate;this.container=b;this.prevLineHeight=this.prevLineWidth=NaN;b=b.set();b.translate(this.x,this.y);this.set=b;a.cursorSet.push(b);this.createElements();d.isString(this.limitToGraph)&&(this.limitToGraph=d.getObjById(a.graphs,this.limitToGraph),this.fullWidth=!1,this.cursorPosition="middle");this.pointer=this.balloonPointerOrientation.substr(0,1).toUpperCase();
this.isHidden=!1;this.hideLines();this.valueLineAxis||(this.valueLineAxis=a.valueAxes[0])},createElements:function(){var a=this,b=a.chart,c=b.dx,e=b.dy,h=a.width,f=a.height,g,k,l=a.cursorAlpha,m=a.valueLineAlpha;a.rotate?(g=m,k=l):(k=m,g=l);"xy"==b.type&&(k=l,void 0!==m&&(k=m),g=l);a.vvLine=d.line(a.container,[c,0,0],[e,0,f],a.cursorColor,g,1);d.setCN(b,a.vvLine,"cursor-line");d.setCN(b,a.vvLine,"cursor-line-vertical");a.hhLine=d.line(a.container,[0,h,h+c],[0,0,e],a.cursorColor,k,1);d.setCN(b,a.hhLine,
"cursor-line");d.setCN(b,a.hhLine,"cursor-line-horizontal");a.vLine=a.rotate?a.vvLine:a.hhLine;a.set.push(a.vvLine);a.set.push(a.hhLine);a.set.node.style.pointerEvents="none";a.fullLines=a.container.set();b=b.cursorLineSet;b.push(a.fullLines);b.translate(a.x,a.y);b.clipRect(-1,-1,h+2,f+2);void 0!==a.tabIndex&&(b.setAttr("tabindex",a.tabIndex),b.keyup(function(b){a.handleKeys(b)}).focus(function(b){a.showCursor()}).blur(function(b){a.hideCursor()}));a.set.clipRect(0,0,h,f)},handleKeys:function(a){var b=
this.prevIndex,c=this.chart;if(c){var e=c.chartData;e&&(isNaN(b)&&(b=e.length-1),37!=a.keyCode&&40!=a.keyCode||b--,39!=a.keyCode&&38!=a.keyCode||b++,b=d.fitToBounds(b,c.startIndex,c.endIndex),(a=this.chart.chartData[b])&&this.setPosition(a.x.categoryAxis),this.prevIndex=b)}},update:function(){var a=this.chart;if(a){var b=a.mouseX-this.x,c=a.mouseY-this.y;this.mouseX=b;this.mouseY=c;this.mouse2X=a.mouse2X-this.x;this.mouse2Y=a.mouse2Y-this.y;var e;if(a.chartData&&0<a.chartData.length){this.mouseIsOver()?
(this.hideGraphBalloons=!1,this.rolledOver=e=!0,this.updateDrawing(),this.vvLine&&isNaN(this.fx)&&(a.rotate||!this.limitToGraph)&&this.vvLine.translate(b,0),!this.hhLine||!isNaN(this.fy)||a.rotate&&this.limitToGraph||this.hhLine.translate(0,c),isNaN(this.mouse2X)?this.dispatchMovedEvent(b,c):e=!1):this.forceShow||this.hideCursor();if(this.zooming){if(!isNaN(this.mouse2X)){isNaN(this.mouse2X0)||this.dispatchPanEvent();return}if(this.pan){this.dispatchPanEvent();return}(this.hZoomEnabled||this.vZoomEnabled)&&
this.zooming&&this.updateSelection()}e&&this.showCursor()}}},updateDrawing:function(){this.drawing&&this.chart.setMouseCursor("crosshair");if(this.drawingNow&&(d.remove(this.drawingLine),1<Math.abs(this.drawStartX-this.mouseX)||1<Math.abs(this.drawStartY-this.mouseY))){var a=this.chart,b=a.marginTop,a=a.marginLeft;this.drawingLine=d.line(this.container,[this.drawStartX+a,this.mouseX+a],[this.drawStartY+b,this.mouseY+b],this.cursorColor,1,1)}},fixWidth:function(a){if(this.fullWidth&&this.prevLineWidth!=
a){var b=this.vvLine,c=0;b&&(b.remove(),c=b.x);b=this.container.set();b.translate(c,0);c=d.rect(this.container,a,this.height,this.cursorColor,this.cursorAlpha,this.cursorAlpha,this.cursorColor);d.setCN(this.chart,c,"cursor-fill");c.translate(-a/2-1,0);b.push(c);this.vvLine=b;this.fullLines.push(b);this.prevLineWidth=a}},fixHeight:function(a){if(this.fullWidth&&this.prevLineHeight!=a){var b=this.hhLine,c=0;b&&(b.remove(),c=b.y);b=this.container.set();b.translate(0,c);c=d.rect(this.container,this.width,
a,this.cursorColor,this.cursorAlpha);c.translate(0,-a/2);b.push(c);this.fullLines.push(b);this.hhLine=b;this.prevLineHeight=a}},fixVLine:function(a,b){if(!isNaN(a)){if(isNaN(this.prevLineX)){var c=0,e=this.mouseX;if(this.limitToGraph){var d=this.chart.categoryAxis;d&&(this.chart.rotate||(c="bottom"==d.position?this.height:-this.height),e=a)}this.vvLine.translate(e,c)}else this.prevLineX!=a&&this.vvLine.translate(this.prevLineX,this.prevLineY);this.fx=a;this.prevLineX!=a&&(c=this.animationDuration,
this.zooming&&(c=0),this.vvLine.stop(),this.vvLine.animate({translate:a+","+b},c,"easeOutSine"),this.prevLineX=a,this.prevLineY=b)}},fixHLine:function(a,b){if(!isNaN(a)){if(isNaN(this.prevLineY)){var c=0,e=this.mouseY;if(this.limitToGraph){var d=this.chart.categoryAxis;d&&(this.chart.rotate&&(c="right"==d.position?this.width:-this.width),e=a)}this.hhLine.translate(c,e)}else this.prevLineY!=a&&this.hhLine.translate(this.prevLineX,this.prevLineY);this.fy=a;this.prevLineY!=a&&(c=this.animationDuration,
this.zooming&&(c=0),this.hhLine.stop(),this.hhLine.animate({translate:b+","+a},c,"easeOutSine"),this.prevLineY=a,this.prevLineX=b)}},hideCursor:function(a){this.forceShow=!1;this.chart.wasTouched&&this.leaveAfterTouch||this.isHidden||this.leaveCursor||(this.hideCursorReal(),a?this.chart.handleCursorHide():this.fire({target:this,chart:this.chart,type:"onHideCursor"}),this.chart.setMouseCursor("auto"))},hideCursorReal:function(){this.hideLines();this.isHidden=!0;this.index=this.prevLineY=this.prevLineX=
this.mouseY0=this.mouseX0=this.fy=this.fx=NaN},hideLines:function(){this.vvLine&&this.vvLine.hide();this.hhLine&&this.hhLine.hide();this.fullLines&&this.fullLines.hide();this.isHidden=!0;this.chart.handleCursorHide()},showCursor:function(a){!this.drawing&&this.enabled&&(this.vLineEnabled&&this.vvLine&&this.vvLine.show(),this.hLineEnabled&&this.hhLine&&this.hhLine.show(),this.isHidden=!1,this.updateFullLine(),a||this.fire({target:this,chart:this.chart,type:"onShowCursor"}),this.pan&&this.chart.setMouseCursor("move"))},
updateFullLine:function(){this.zooming&&this.fullWidth&&this.selection&&(this.rotate?0<this.selection.height&&this.hhLine.hide():0<this.selection.width&&this.vvLine.hide())},updateSelection:function(){if(!this.pan&&this.enabled){var a=this.mouseX,b=this.mouseY;isNaN(this.fx)||(a=this.fx);isNaN(this.fy)||(b=this.fy);this.clearSelection();var c=this.mouseX0,e=this.mouseY0,h=this.width,f=this.height,a=d.fitToBounds(a,0,h),b=d.fitToBounds(b,0,f),g;a<c&&(g=a,a=c,c=g);b<e&&(g=b,b=e,e=g);this.hZoomEnabled?
h=a-c:c=0;this.vZoomEnabled?f=b-e:e=0;isNaN(this.mouse2X)&&0<Math.abs(h)&&0<Math.abs(f)&&(a=this.chart,b=d.rect(this.container,h,f,this.cursorColor,this.selectionAlpha),d.setCN(a,b,"cursor-selection"),b.width=h,b.height=f,b.translate(c,e),this.set.push(b),this.selection=b);this.updateFullLine()}},mouseIsOver:function(){var a=this.mouseX,b=this.mouseY;if(this.justReleased)return this.justReleased=!1,!0;if(this.mouseIsDown)return!0;if(!this.chart.mouseIsOver)return this.handleMouseOut(),!1;if(0<a&&
a<this.width&&0<b&&b<this.height)return!0;this.handleMouseOut()},fixPosition:function(){this.prevY=this.prevX=NaN},handleMouseDown:function(){this.update();if(this.mouseIsOver())if(this.mouseIsDown=!0,this.mouseX0=this.mouseX,this.mouseY0=this.mouseY,this.mouse2X0=this.mouse2X,this.mouse2Y0=this.mouse2Y,this.drawing)this.drawStartY=this.mouseY,this.drawStartX=this.mouseX,this.drawingNow=!0;else if(this.dispatchMovedEvent(this.mouseX,this.mouseY),!this.pan&&isNaN(this.mouse2X0)&&(isNaN(this.fx)||(this.mouseX0=
this.fx),isNaN(this.fy)||(this.mouseY0=this.fy)),this.hZoomEnabled||this.vZoomEnabled){this.zooming=!0;var a={chart:this.chart,target:this,type:"zoomStarted"};a.x=this.mouseX/this.width;a.y=this.mouseY/this.height;this.index0=a.index=this.index;this.timestamp0=this.timestamp;this.fire(a)}},registerInitialMouse:function(){},handleReleaseOutside:function(){this.mouseIsDown=!1;if(this.drawingNow){this.drawingNow=!1;d.remove(this.drawingLine);var a=this.drawStartX,b=this.drawStartY,c=this.mouseX,e=this.mouseY,
h=this.chart;(2<Math.abs(a-c)||2<Math.abs(b-e))&&this.fire({type:"draw",target:this,chart:h,initialX:a,initialY:b,finalX:c,finalY:e})}this.zooming&&(this.zooming=!1,this.selectWithoutZooming?this.dispatchZoomEvent("selected"):(this.hZoomEnabled||this.vZoomEnabled)&&this.dispatchZoomEvent("zoomed"),this.rolledOver&&this.dispatchMovedEvent(this.mouseX,this.mouseY));this.mouse2Y0=this.mouse2X0=this.mouseY0=this.mouseX0=NaN},dispatchZoomEvent:function(a){if(!this.pan){var b=this.selection;if(b&&3<Math.abs(b.width)&&
3<Math.abs(b.height)){var c=Math.min(this.index,this.index0),e=Math.max(this.index,this.index0),d=c,f=e,g=this.chart,k=g.chartData,l=g.categoryAxis;l&&l.parseDates&&!l.equalSpacing&&(d=k[c]?k[c].time:Math.min(this.timestamp0,this.timestamp),f=k[e]?g.getEndTime(k[e].time):Math.max(this.timestamp0,this.timestamp));var b={type:a,chart:this.chart,target:this,end:f,start:d,startIndex:c,endIndex:e,selectionHeight:b.height,selectionWidth:b.width,selectionY:b.y,selectionX:b.x},m;this.hZoomEnabled&&4<Math.abs(this.mouseX0-
this.mouseX)&&(b.startX=this.mouseX0/this.width,b.endX=this.mouseX/this.width,m=!0);this.vZoomEnabled&&4<Math.abs(this.mouseY0-this.mouseY)&&(b.startY=1-this.mouseY0/this.height,b.endY=1-this.mouseY/this.height,m=!0);m&&(this.prevY=this.prevX=NaN,this.fire(b),"selected"!=a&&this.clearSelection());this.hideCursor()}}},dispatchMovedEvent:function(a,b,c,e){a=Math.round(a);b=Math.round(b);if(!this.isHidden&&(a!=this.prevX||b!=this.prevY||"changed"==c)){c||(c="moved");var d=this.fx,f=this.fy;isNaN(d)&&
(d=a);isNaN(f)&&(f=b);var g=!1;this.zooming&&this.pan&&(g=!0);g={hidden:this.isHidden,type:c,chart:this.chart,target:this,x:a,y:b,finalX:d,finalY:f,zooming:this.zooming,panning:g,mostCloseGraph:this.mostCloseGraph,index:this.index,skip:e,hideBalloons:this.hideGraphBalloons};this.prevIndex=this.index;this.rotate?(g.position=b,g.finalPosition=f):(g.position=a,g.finalPosition=d);this.prevX=a;this.prevY=b;e?this.chart.handleCursorMove(g):(this.fire(g),"changed"==c&&this.chart.fire(g))}},dispatchPanEvent:function(){if(this.mouseIsDown){var a=
d.roundTo((this.mouseX-this.mouseX0)/this.width,3),b=d.roundTo((this.mouseY-this.mouseY0)/this.height,3),c=d.roundTo((this.mouse2X-this.mouse2X0)/this.width,3),e=d.roundTo((this.mouse2Y-this.mouse2Y0)/this.height,2),h=!1;0!==Math.abs(a)&&0!==Math.abs(b)&&(h=!0);if(this.prevDeltaX==a||this.prevDeltaY==b)h=!1;isNaN(c)||isNaN(e)||(0!==Math.abs(c)&&0!==Math.abs(e)&&(h=!0),this.prevDelta2X!=c&&this.prevDelta2Y!=e)||(h=!1);h&&(this.hideLines(),this.fire({type:"panning",chart:this.chart,target:this,deltaX:a,
deltaY:b,delta2X:c,delta2Y:e,index:this.index}),this.prevDeltaX=a,this.prevDeltaY=b,this.prevDelta2X=c,this.prevDelta2Y=e)}},clearSelection:function(){var a=this.selection;a&&(a.width=0,a.height=0,a.remove())},destroy:function(){this.clear();d.remove(this.selection);this.selection=null;clearTimeout(this.syncTO);d.remove(this.set)},clear:function(){},setTimestamp:function(a){this.timestamp=a},setIndex:function(a,b){a!=this.index&&(this.index=a,b||this.isHidden||this.dispatchMovedEvent(this.mouseX,
this.mouseY,"changed"))},handleMouseOut:function(){this.enabled&&this.rolledOver&&(this.leaveCursor||this.setIndex(void 0),this.forceShow=!1,this.hideCursor(),this.rolledOver=!1)},showCursorAt:function(a){var b=this.chart.categoryAxis;b&&this.setPosition(b.categoryToCoordinate(a),a)},setPosition:function(a,b){var c=this.chart,e=c.categoryAxis;if(e){var d,f;void 0===b&&(b=e.coordinateToValue(a));e.showBalloonAt(b,a);this.forceShow=!0;e.stickBalloonToCategory?c.rotate?this.fixHLine(a,0):this.fixVLine(a,
0):(this.showCursor(),c.rotate?this.hhLine.translate(0,a):this.vvLine.translate(a,0));c.rotate?d=a:f=a;c.rotate?(this.vvLine&&this.vvLine.hide(),this.hhLine&&this.hhLine.show()):(this.hhLine&&this.hhLine.hide(),this.vvLine&&this.vvLine.show());this.updateFullLine();this.isHidden=!1;this.dispatchMovedEvent(f,d,"moved",!0)}},enableDrawing:function(a){this.enabled=!a;this.hideCursor();this.drawing=a},syncWithCursor:function(a,b){clearTimeout(this.syncTO);a&&(a.isHidden?this.hideCursor(!0):this.syncWithCursorReal(a,
b))},isZooming:function(a){this.zooming=a},syncWithCursorReal:function(a,b){var c=a.vvLine,e=a.hhLine;this.index=a.index;this.forceShow=!0;this.zooming&&this.pan||this.showCursor(!0);this.hideGraphBalloons=b;this.justReleased=a.justReleased;this.zooming=a.zooming;this.index0=a.index0;this.mouseX0=a.mouseX0;this.mouseY0=a.mouseY0;this.mouse2X0=a.mouse2X0;this.mouse2Y0=a.mouse2Y0;this.timestamp0=a.timestamp0;this.prevDeltaX=a.prevDeltaX;this.prevDeltaY=a.prevDeltaY;this.prevDelta2X=a.prevDelta2X;this.prevDelta2Y=
a.prevDelta2Y;this.fx=a.fx;this.fy=a.fy;a.zooming&&this.updateSelection();var d=a.mouseX,f=a.mouseY;this.rotate?(d=NaN,this.vvLine&&this.vvLine.hide(),this.hhLine&&e&&(isNaN(a.fy)?this.hhLine.translate(0,a.mouseY):this.fixHLine(a.fy,0))):(f=NaN,this.hhLine&&this.hhLine.hide(),this.vvLine&&c&&(isNaN(a.fx)?this.vvLine.translate(a.mouseX,0):this.fixVLine(a.fx,0)));this.dispatchMovedEvent(d,f,"moved",!0)}})})();(function(){var d=window.AmCharts;d.SimpleChartScrollbar=d.Class({construct:function(a){this.createEvents("zoomed","zoomStarted","zoomEnded");this.backgroundColor="#D4D4D4";this.backgroundAlpha=1;this.selectedBackgroundColor="#EFEFEF";this.scrollDuration=this.selectedBackgroundAlpha=1;this.resizeEnabled=!0;this.hideResizeGrips=!1;this.scrollbarHeight=20;this.updateOnReleaseOnly=!1;9>document.documentMode&&(this.updateOnReleaseOnly=!0);this.dragIconHeight=this.dragIconWidth=35;this.dragIcon="dragIconRoundBig";
this.dragCursorHover="cursor: move; cursor: grab; cursor: -moz-grab; cursor: -webkit-grab;";this.dragCursorDown="cursor: move; cursor: grab; cursor: -moz-grabbing; cursor: -webkit-grabbing;";this.vResizeCursor="ns-resize";this.hResizeCursor="ew-resize";this.enabled=!0;this.percentStart=this.offset=0;this.percentEnd=1;d.applyTheme(this,a,"SimpleChartScrollbar")},getPercents:function(){var a=this.getDBox(),b=a.x,c=a.y,e=a.width,a=a.height;this.rotate?(b=1-c/this.height,c=1-(c+a)/this.height):(c=b/this.width,
b=(b+e)/this.width);this.percentStart=c;this.percentEnd=b},draw:function(){var a=this;a.destroy();if(a.enabled){var b=a.chart.container,c=a.rotate,e=a.chart;e.panRequired=!0;var h=b.set();a.set=h;c?d.setCN(e,h,"scrollbar-vertical"):d.setCN(e,h,"scrollbar-horizontal");e.scrollbarsSet.push(h);var f,g;c?(f=a.scrollbarHeight,g=e.plotAreaHeight):(g=a.scrollbarHeight,f=e.plotAreaWidth);a.width=f;if((a.height=g)&&f){var k=d.rect(b,f,g,a.backgroundColor,a.backgroundAlpha,1,a.backgroundColor,a.backgroundAlpha);
d.setCN(e,k,"scrollbar-bg");a.bg=k;h.push(k);k=d.rect(b,f,g,"#000",.005);h.push(k);a.invisibleBg=k;k.click(function(){a.handleBgClick()}).mouseover(function(){a.handleMouseOver()}).mouseout(function(){a.handleMouseOut()}).touchend(function(){a.handleBgClick()});k=d.rect(b,f,g,a.selectedBackgroundColor,a.selectedBackgroundAlpha);d.setCN(e,k,"scrollbar-bg-selected");a.selectedBG=k;h.push(k);f=d.rect(b,f,g,"#000",.005);a.dragger=f;h.push(f);f.mousedown(function(b){a.handleDragStart(b)}).mouseup(function(){a.handleDragStop()}).mouseover(function(){a.handleDraggerOver()}).mouseout(function(){a.handleMouseOut()}).touchstart(function(b){a.handleDragStart(b)}).touchend(function(){a.handleDragStop()});
g=e.pathToImages;var l,k=a.dragIcon.replace(/\.[a-z]*$/i,"");d.isAbsolute(k)&&(g="");c?(l=g+k+"H"+e.extension,g=a.dragIconWidth,c=a.dragIconHeight):(l=g+k+e.extension,c=a.dragIconWidth,g=a.dragIconHeight);k=b.image(l,0,0,c,g);d.setCN(e,k,"scrollbar-grip-left");l=b.image(l,0,0,c,g);d.setCN(e,l,"scrollbar-grip-right");var m=10,n=20;e.panEventsEnabled&&(m=25,n=a.scrollbarHeight);var q=d.rect(b,m,n,"#000",.005),p=d.rect(b,m,n,"#000",.005);p.translate(-(m-c)/2,-(n-g)/2);q.translate(-(m-c)/2,-(n-g)/2);
c=b.set([k,p]);b=b.set([l,q]);a.iconLeft=c;h.push(a.iconLeft);a.iconRight=b;h.push(b);a.updateGripCursor(!1);e.makeAccessible(c,a.accessibleLabel);e.makeAccessible(b,a.accessibleLabel);e.makeAccessible(f,a.accessibleLabel);c.setAttr("role","menuitem");b.setAttr("role","menuitem");f.setAttr("role","menuitem");void 0!==a.tabIndex&&(c.setAttr("tabindex",a.tabIndex),c.keyup(function(b){a.handleKeys(b,1,0)}));void 0!==a.tabIndex&&(f.setAttr("tabindex",a.tabIndex),f.keyup(function(b){a.handleKeys(b,1,1)}));
void 0!==a.tabIndex&&(b.setAttr("tabindex",a.tabIndex),b.keyup(function(b){a.handleKeys(b,0,1)}));c.mousedown(function(){a.leftDragStart()}).mouseup(function(){a.leftDragStop()}).mouseover(function(){a.iconRollOver()}).mouseout(function(){a.iconRollOut()}).touchstart(function(){a.leftDragStart()}).touchend(function(){a.leftDragStop()});b.mousedown(function(){a.rightDragStart()}).mouseup(function(){a.rightDragStop()}).mouseover(function(){a.iconRollOver()}).mouseout(function(){a.iconRollOut()}).touchstart(function(){a.rightDragStart()}).touchend(function(){a.rightDragStop()});
d.ifArray(e.chartData)?h.show():h.hide();a.hideDragIcons();a.clipDragger(!1)}h.translate(a.x,a.y);h.node.style.msTouchAction="none";h.node.style.touchAction="none"}},handleKeys:function(a,b,c){this.getPercents();var e=this.percentStart,d=this.percentEnd;if(this.rotate)var f=d,d=e,e=f;if(37==a.keyCode||40==a.keyCode)e-=.02*b,d-=.02*c;if(39==a.keyCode||38==a.keyCode)e+=.02*b,d+=.02*c;this.rotate&&(a=d,d=e,e=a);isNaN(d)||isNaN(e)||this.percentZoom(e,d,!0)},updateScrollbarSize:function(a,b){if(!isNaN(a)&&
!isNaN(b)){a=Math.round(a);b=Math.round(b);var c=this.dragger,e,d,f,g,k;this.rotate?(e=0,d=a,f=this.width+1,g=b-a,c.setAttr("height",b-a),c.setAttr("y",d)):(e=a,d=0,f=b-a,g=this.height+1,k=b-a,c.setAttr("x",e),c.setAttr("width",k));this.clipAndUpdate(e,d,f,g)}},update:function(){var a,b=!1,c,e,d=this.x,f=this.y,g=this.dragger,k=this.getDBox();if(k){c=k.x+d;e=k.y+f;var l=k.width,k=k.height,m=this.rotate,n=this.chart,q=this.width,p=this.height,t=n.mouseX,n=n.mouseY;a=this.initialMouse;this.forceClip&&
this.clipDragger(!0);if(this.dragging){var r=this.initialCoord;m?(a=r+(n-a),0>a&&(a=0),r=p-k,a>r&&(a=r),g.setAttr("y",a)):(a=r+(t-a),0>a&&(a=0),r=q-l,a>r&&(a=r),g.setAttr("x",a));this.clipDragger(!0)}if(this.resizingRight){if(m)if(a=n-e,!isNaN(this.maxHeight)&&a>this.maxHeight&&(a=this.maxHeight),a+e>p+f&&(a=p-e+f),0>a)this.resizingRight=!1,b=this.resizingLeft=!0;else{if(0===a||isNaN(a))a=.1;g.setAttr("height",a)}else if(a=t-c,!isNaN(this.maxWidth)&&a>this.maxWidth&&(a=this.maxWidth),a+c>q+d&&(a=
q-c+d),0>a)this.resizingRight=!1,b=this.resizingLeft=!0;else{if(0===a||isNaN(a))a=.1;g.setAttr("width",a)}this.clipDragger(!0)}if(this.resizingLeft){if(m)if(c=e,e=n,e<f&&(e=f),isNaN(e)&&(e=f),e>p+f&&(e=p+f),a=!0===b?c-e:k+c-e,!isNaN(this.maxHeight)&&a>this.maxHeight&&(a=this.maxHeight,e=c),0>a)this.resizingRight=!0,this.resizingLeft=!1,g.setAttr("y",c+k-f);else{if(0===a||isNaN(a))a=.1;g.setAttr("y",e-f);g.setAttr("height",a)}else if(e=t,e<d&&(e=d),isNaN(e)&&(e=d),e>q+d&&(e=q+d),a=!0===b?c-e:l+c-e,
!isNaN(this.maxWidth)&&a>this.maxWidth&&(a=this.maxWidth,e=c),0>a)this.resizingRight=!0,this.resizingLeft=!1,g.setAttr("x",c+l-d);else{if(0===a||isNaN(a))a=.1;g.setAttr("x",e-d);g.setAttr("width",a)}this.clipDragger(!0)}}},stopForceClip:function(){this.animating=this.forceClip=!1},clipDragger:function(a){var b=this.getDBox();if(b){var c=b.x,e=b.y,d=b.width,b=b.height,f=!1;if(this.rotate){if(c=0,d=this.width+1,this.clipY!=e||this.clipH!=b)f=!0}else if(e=0,b=this.height+1,this.clipX!=c||this.clipW!=
d)f=!0;f&&(this.clipAndUpdate(c,e,d,b),a&&(this.updateOnReleaseOnly||this.dispatchScrollbarEvent()))}},maskGraphs:function(){},clipAndUpdate:function(a,b,c,e){this.clipX=a;this.clipY=b;this.clipW=c;this.clipH=e;this.selectedBG.setAttr("width",c);this.selectedBG.setAttr("height",e);this.selectedBG.translate(a,b);this.updateDragIconPositions();this.maskGraphs(a,b,c,e)},dispatchScrollbarEvent:function(){if(this.skipEvent)this.skipEvent=!1;else{var a=this.chart;a.hideBalloon();var b=this.getDBox(),c=
b.x,e=b.y,d=b.width,b=b.height;this.getPercents();this.rotate?(c=e,d=this.height/b):d=this.width/d;this.fire({type:"zoomed",position:c,chart:a,target:this,multiplier:d,relativeStart:this.percentStart,relativeEnd:this.percentEnd})}},updateDragIconPositions:function(){var a=this.getDBox(),b=a.x,c=a.y,e=this.iconLeft,d=this.iconRight,f,g,k=this.scrollbarHeight;this.rotate?(f=this.dragIconWidth,g=this.dragIconHeight,e.translate((k-g)/2,c-f/2),d.translate((k-g)/2,c+a.height-f/2)):(f=this.dragIconHeight,
g=this.dragIconWidth,e.translate(b-g/2,(k-f)/2),d.translate(b-g/2+a.width,(k-f)/2))},showDragIcons:function(){this.resizeEnabled&&(this.iconLeft.show(),this.iconRight.show())},hideDragIcons:function(){if(!this.resizingLeft&&!this.resizingRight&&!this.dragging){if(this.hideResizeGrips||!this.resizeEnabled)this.iconLeft.hide(),this.iconRight.hide();this.removeCursors()}},removeCursors:function(){this.chart.setMouseCursor("auto")},fireZoomEvent:function(a){this.fire({type:a,chart:this.chart,target:this})},
percentZoom:function(a,b,c){a=d.fitToBounds(a,0,b);b=d.fitToBounds(b,a,1);if(this.dragger&&this.enabled){this.dragger.stop();isNaN(a)&&(a=0);isNaN(b)&&(b=1);var e,h;this.rotate?(e=this.height,b=e-e*b,h=e-e*a):(e=this.width,h=e*b,b=e*a);this.updateScrollbarSize(b,h);this.clipDragger(!1);this.getPercents();c&&this.dispatchScrollbarEvent()}},destroy:function(){this.clear();d.remove(this.set);d.remove(this.iconRight);d.remove(this.iconLeft)},clear:function(){},handleDragStart:function(){if(this.enabled){this.fireZoomEvent("zoomStarted");
var a=this.chart;this.dragger.stop();this.removeCursors();d.isModern&&this.dragger.node.setAttribute("style",this.dragCursorDown);this.dragging=!0;var b=this.getDBox();this.rotate?(this.initialCoord=b.y,this.initialMouse=a.mouseY):(this.initialCoord=b.x,this.initialMouse=a.mouseX)}},handleDragStop:function(){this.updateOnReleaseOnly&&(this.update(),this.skipEvent=!1,this.dispatchScrollbarEvent());this.dragging=!1;this.mouseIsOver&&this.removeCursors();d.isModern&&this.dragger.node.setAttribute("style",
this.dragCursorHover);this.update();this.fireZoomEvent("zoomEnded")},handleDraggerOver:function(){this.handleMouseOver();d.isModern&&this.dragger.node.setAttribute("style",this.dragCursorHover)},leftDragStart:function(){this.fireZoomEvent("zoomStarted");this.dragger.stop();this.resizingLeft=!0;this.updateGripCursor(!0)},updateGripCursor:function(a){d.isModern&&(a=this.rotate?a?this.vResizeCursorDown:this.vResizeCursorHover:a?this.hResizeCursorDown:this.hResizeCursorHover)&&(this.iconRight&&this.iconRight.node.setAttribute("style",
a),this.iconLeft&&this.iconLeft.node.setAttribute("style",a))},leftDragStop:function(){this.resizingLeft&&(this.resizingLeft=!1,this.mouseIsOver||this.removeCursors(),this.updateOnRelease(),this.fireZoomEvent("zoomEnded"));this.updateGripCursor(!1)},rightDragStart:function(){this.fireZoomEvent("zoomStarted");this.dragger.stop();this.resizingRight=!0;this.updateGripCursor(!0)},rightDragStop:function(){this.resizingRight&&(this.resizingRight=!1,this.mouseIsOver||this.removeCursors(),this.updateOnRelease(),
this.fireZoomEvent("zoomEnded"));this.updateGripCursor(!1)},iconRollOut:function(){this.removeCursors()},iconRollOver:function(){this.rotate?this.vResizeCursor&&this.chart.setMouseCursor(this.vResizeCursor):this.hResizeCursor&&this.chart.setMouseCursor(this.hResizeCursor);this.handleMouseOver()},getDBox:function(){if(this.dragger)return this.dragger.getBBox()},handleBgClick:function(){var a=this;if(!a.resizingRight&&!a.resizingLeft){a.zooming=!0;var b,c,e=a.scrollDuration,h=a.dragger;b=a.getDBox();
var f=b.height,g=b.width;c=a.chart;var k=a.y,l=a.x,m=a.rotate;m?(b="y",c=c.mouseY-f/2-k,c=d.fitToBounds(c,0,a.height-f)):(b="x",c=c.mouseX-g/2-l,c=d.fitToBounds(c,0,a.width-g));a.updateOnReleaseOnly?(a.skipEvent=!1,h.setAttr(b,c),a.dispatchScrollbarEvent(),a.clipDragger()):(a.animating=!0,c=Math.round(c),m?h.animate({y:c},e,">"):h.animate({x:c},e,">"),a.forceClip=!0,clearTimeout(a.forceTO),a.forceTO=setTimeout(function(){a.stopForceClip.call(a)},5E3*e))}},updateOnRelease:function(){this.updateOnReleaseOnly&&
(this.update(),this.skipEvent=!1,this.dispatchScrollbarEvent())},handleReleaseOutside:function(){if(this.set){if(this.resizingLeft||this.resizingRight||this.dragging)this.dragging=this.resizingRight=this.resizingLeft=!1,this.updateOnRelease(),this.removeCursors();this.animating=this.mouseIsOver=!1;this.hideDragIcons();this.update()}},handleMouseOver:function(){this.mouseIsOver=!0;this.showDragIcons()},handleMouseOut:function(){this.mouseIsOver=!1;this.hideDragIcons();this.removeCursors()}})})();(function(){var d=window.AmCharts;d.ChartScrollbar=d.Class({inherits:d.SimpleChartScrollbar,construct:function(a){this.cname="ChartScrollbar";d.ChartScrollbar.base.construct.call(this,a);this.graphLineColor="#BBBBBB";this.graphLineAlpha=0;this.graphFillColor="#BBBBBB";this.graphFillAlpha=1;this.selectedGraphLineColor="#888888";this.selectedGraphLineAlpha=0;this.selectedGraphFillColor="#888888";this.selectedGraphFillAlpha=1;this.gridCount=0;this.gridColor="#FFFFFF";this.gridAlpha=.7;this.skipEvent=
this.autoGridCount=!1;this.color="#FFFFFF";this.scrollbarCreated=!1;this.oppositeAxis=!0;this.accessibleLabel="Zoom chart using cursor arrows";d.applyTheme(this,a,this.cname)},init:function(){var a=this.categoryAxis,b=this.chart,c=this.gridAxis;a||("CategoryAxis"==this.gridAxis.cname?(this.catScrollbar=!0,a=new d.CategoryAxis,a.id="scrollbar"):(a=new d.ValueAxis,a.data=b.chartData,a.id=c.id,a.type=c.type,a.maximumDate=c.maximumDate,a.minimumDate=c.minimumDate,a.minPeriod=c.minPeriod,a.minMaxField=
c.minMaxField),this.categoryAxis=a);a.chart=b;var e=b.categoryAxis;e&&(a.firstDayOfWeek=e.firstDayOfWeek);a.dateFormats=c.dateFormats;a.markPeriodChange=c.markPeriodChange;a.boldPeriodBeginning=c.boldPeriodBeginning;a.labelFunction=c.labelFunction;a.axisItemRenderer=d.RecItem;a.axisRenderer=d.RecAxis;a.guideFillRenderer=d.RecFill;a.inside=!0;a.fontSize=this.fontSize;a.tickLength=0;a.axisAlpha=0;d.isString(this.graph)&&(this.graph=d.getObjById(b.graphs,this.graph));(a=this.graph)&&this.catScrollbar&&
(c=this.valueAxis,c||(this.valueAxis=c=new d.ValueAxis,c.visible=!1,c.scrollbar=!0,c.axisItemRenderer=d.RecItem,c.axisRenderer=d.RecAxis,c.guideFillRenderer=d.RecFill,c.labelsEnabled=!1,c.chart=b),b=this.unselectedGraph,b||(b=new d.AmGraph,b.scrollbar=!0,this.unselectedGraph=b,b.negativeBase=a.negativeBase,b.noStepRisers=a.noStepRisers),b=this.selectedGraph,b||(b=new d.AmGraph,b.scrollbar=!0,this.selectedGraph=b,b.negativeBase=a.negativeBase,b.noStepRisers=a.noStepRisers));this.scrollbarCreated=!0},
draw:function(){var a=this;d.ChartScrollbar.base.draw.call(a);if(a.enabled){a.scrollbarCreated||a.init();var b=a.chart,c=b.chartData,e=a.categoryAxis,h=a.rotate,f=a.x,g=a.y,k=a.width,l=a.height,m=a.gridAxis,n=a.set;e.setOrientation(!h);e.parseDates=m.parseDates;"ValueAxis"==a.categoryAxis.cname&&(e.rotate=!h);e.equalSpacing=m.equalSpacing;e.minPeriod=m.minPeriod;e.startOnAxis=m.startOnAxis;e.width=k-1;e.height=l;e.gridCount=a.gridCount;e.gridColor=a.gridColor;e.gridAlpha=a.gridAlpha;e.color=a.color;
e.tickLength=0;e.axisAlpha=0;e.autoGridCount=a.autoGridCount;e.parseDates&&!e.equalSpacing&&e.timeZoom(b.firstTime,b.lastTime);e.minimum=a.gridAxis.fullMin;e.maximum=a.gridAxis.fullMax;e.strictMinMax=!0;e.zoom(0,c.length-1);if((m=a.graph)&&a.catScrollbar){var q=a.valueAxis,p=m.valueAxis;q.id=p.id;q.rotate=h;q.setOrientation(h);q.width=k;q.height=l;q.dataProvider=c;q.reversed=p.reversed;q.logarithmic=p.logarithmic;q.gridAlpha=0;q.axisAlpha=0;n.push(q.set);h?(q.y=g,q.x=0):(q.x=f,q.y=0);var f=Infinity,
g=-Infinity,t;for(t=0;t<c.length;t++){var r=c[t].axes[p.id].graphs[m.id].values,w;for(w in r)if(r.hasOwnProperty(w)&&"percents"!=w&&"total"!=w){var y=r[w];y<f&&(f=y);y>g&&(g=y)}}Infinity!=f&&(q.minimum=f);-Infinity!=g&&(q.maximum=g+.1*(g-f));f==g&&(--q.minimum,q.maximum+=1);void 0!==a.minimum&&(q.minimum=a.minimum);void 0!==a.maximum&&(q.maximum=a.maximum);q.zoom(0,c.length-1);w=a.unselectedGraph;w.id=m.id;w.bcn="scrollbar-graph-";w.rotate=h;w.chart=b;w.data=c;w.valueAxis=q;w.chart=m.chart;w.categoryAxis=
a.categoryAxis;w.periodSpan=m.periodSpan;w.valueField=m.valueField;w.openField=m.openField;w.closeField=m.closeField;w.highField=m.highField;w.lowField=m.lowField;w.lineAlpha=a.graphLineAlpha;w.lineColorR=a.graphLineColor;w.fillAlphas=a.graphFillAlpha;w.fillColorsR=a.graphFillColor;w.connect=m.connect;w.hidden=m.hidden;w.width=k;w.height=l;w.pointPosition=m.pointPosition;w.stepDirection=m.stepDirection;w.periodSpan=m.periodSpan;p=a.selectedGraph;p.id=m.id;p.bcn=w.bcn+"selected-";p.rotate=h;p.chart=
b;p.data=c;p.valueAxis=q;p.chart=m.chart;p.categoryAxis=e;p.periodSpan=m.periodSpan;p.valueField=m.valueField;p.openField=m.openField;p.closeField=m.closeField;p.highField=m.highField;p.lowField=m.lowField;p.lineAlpha=a.selectedGraphLineAlpha;p.lineColorR=a.selectedGraphLineColor;p.fillAlphas=a.selectedGraphFillAlpha;p.fillColorsR=a.selectedGraphFillColor;p.connect=m.connect;p.hidden=m.hidden;p.width=k;p.height=l;p.pointPosition=m.pointPosition;p.stepDirection=m.stepDirection;p.periodSpan=m.periodSpan;
b=a.graphType;b||(b=m.type);w.type=b;p.type=b;c=c.length-1;w.zoom(0,c);p.zoom(0,c);p.set.click(function(){a.handleBackgroundClick()}).mouseover(function(){a.handleMouseOver()}).mouseout(function(){a.handleMouseOut()});w.set.click(function(){a.handleBackgroundClick()}).mouseover(function(){a.handleMouseOver()}).mouseout(function(){a.handleMouseOut()});n.push(w.set);n.push(p.set)}n.push(e.set);n.push(e.labelsSet);a.bg.toBack();a.invisibleBg.toFront();a.dragger.toFront();a.iconLeft.toFront();a.iconRight.toFront()}},
timeZoom:function(a,b,c){this.startTime=a;this.endTime=b;this.timeDifference=b-a;this.skipEvent=!d.toBoolean(c);this.zoomScrollbar();this.dispatchScrollbarEvent()},zoom:function(a,b){this.start=a;this.end=b;this.skipEvent=!0;this.zoomScrollbar()},dispatchScrollbarEvent:function(){if(this.categoryAxis&&"ValueAxis"==this.categoryAxis.cname)d.ChartScrollbar.base.dispatchScrollbarEvent.call(this);else if(this.skipEvent)this.skipEvent=!1;else{var a=this.chart.chartData,b,c,e=this.dragger.getBBox();b=e.x;
var h=e.y,f=e.width,e=e.height,g=this.chart;this.rotate?(b=h,c=e):c=f;f={type:"zoomed",target:this};f.chart=g;var k=this.categoryAxis,l=this.stepWidth,h=g.minSelectedTime,e=g.maxSelectedTime,m=!1;if(k.parseDates&&!k.equalSpacing){if(a=g.lastTime,g=g.firstTime,k=Math.round(b/l)+g,b=this.dragging?k+this.timeDifference:Math.round((b+c)/l)+g,k>b&&(k=b),0<h&&b-k<h&&(b=Math.round(k+(b-k)/2),m=Math.round(h/2),k=b-m,b+=m,m=!0),0<e&&b-k>e&&(b=Math.round(k+(b-k)/2),m=Math.round(e/2),k=b-m,b+=m,m=!0),b>a&&(b=
a),b-h<k&&(k=b-h),k<g&&(k=g),k+h>b&&(b=k+h),k!=this.startTime||b!=this.endTime)this.startTime=k,this.endTime=b,f.start=k,f.end=b,f.startDate=new Date(k),f.endDate=new Date(b),this.fire(f)}else{k.startOnAxis||(b+=l/2);c-=this.stepWidth/2;h=k.xToIndex(b);b=k.xToIndex(b+c);if(h!=this.start||this.end!=b)k.startOnAxis&&(this.resizingRight&&h==b&&b++,this.resizingLeft&&h==b&&(0<h?h--:b=1)),this.start=h,this.end=this.dragging?this.start+this.difference:b,f.start=this.start,f.end=this.end,k.parseDates&&(a[this.start]&&
(f.startDate=new Date(a[this.start].time)),a[this.end]&&(f.endDate=new Date(a[this.end].time))),this.fire(f);this.percentStart=h;this.percentEnd=b}m&&this.zoomScrollbar(!0)}},zoomScrollbar:function(a){if((!(this.dragging||this.resizingLeft||this.resizingRight||this.animating)||a)&&this.dragger&&this.enabled){var b,c,e=this.chart;a=e.chartData;var d=this.categoryAxis;d.parseDates&&!d.equalSpacing?(a=d.stepWidth,c=e.firstTime,b=a*(this.startTime-c),c=a*(this.endTime-c)):(a[this.start]&&(b=a[this.start].x[d.id]),
a[this.end]&&(c=a[this.end].x[d.id]),a=d.stepWidth,d.startOnAxis||(e=a/2,b-=e,c+=e));this.stepWidth=a;isNaN(b)||isNaN(c)||this.updateScrollbarSize(b,c)}},maskGraphs:function(a,b,c,e){var d=this.selectedGraph;d&&d.set.clipRect(a,b,c,e)},handleDragStart:function(){d.ChartScrollbar.base.handleDragStart.call(this);this.difference=this.end-this.start;this.timeDifference=this.endTime-this.startTime;0>this.timeDifference&&(this.timeDifference=0)},handleBackgroundClick:function(){d.ChartScrollbar.base.handleBackgroundClick.call(this);
this.dragging||(this.difference=this.end-this.start,this.timeDifference=this.endTime-this.startTime,0>this.timeDifference&&(this.timeDifference=0))}})})();(function(){var d=window.AmCharts;d.AmBalloon=d.Class({construct:function(a){this.cname="AmBalloon";this.enabled=!0;this.fillColor="#FFFFFF";this.fillAlpha=.8;this.borderThickness=2;this.borderColor="#FFFFFF";this.borderAlpha=1;this.cornerRadius=0;this.maxWidth=220;this.horizontalPadding=8;this.verticalPadding=4;this.pointerWidth=6;this.pointerOrientation="V";this.color="#000000";this.adjustBorderColor=!0;this.show=this.follow=this.showBullet=!1;this.bulletSize=3;this.shadowAlpha=.4;this.shadowColor=
"#000000";this.fadeOutDuration=this.animationDuration=.3;this.fixedPosition=!0;this.offsetY=6;this.offsetX=1;this.textAlign="center";this.disableMouseEvents=!0;this.deltaSignX=this.deltaSignY=1;d.isModern||(this.offsetY*=1.5);this.sdy=this.sdx=0;d.applyTheme(this,a,this.cname)},draw:function(){var a=this.pointToX,b=this.pointToY;d.isModern||(this.drop=!1);var c=this.chart;d.VML&&(this.fadeOutDuration=0);this.xAnim&&c.stopAnim(this.xAnim);this.yAnim&&c.stopAnim(this.yAnim);this.sdy=this.sdx=0;if(!isNaN(a)){var e=
this.follow,h=c.container,f=this.set;d.remove(f);this.removeDiv();f=h.set();f.node.style.pointerEvents="none";this.set=f;this.mainSet?(this.mainSet.push(this.set),this.sdx=this.mainSet.x,this.sdy=this.mainSet.y):c.balloonsSet.push(f);if(this.show){var g=this.l,k=this.t,l=this.r,m=this.b,n=this.balloonColor,q=this.fillColor,p=this.borderColor,t=q;void 0!=n&&(this.adjustBorderColor?t=p=n:q=n);var r=this.horizontalPadding,w=this.verticalPadding,y=this.pointerWidth,x=this.pointerOrientation,u=this.cornerRadius,
A=c.fontFamily,z=this.fontSize;void 0==z&&(z=c.fontSize);var n=document.createElement("div"),B=c.classNamePrefix;n.className=B+"-balloon-div";this.className&&(n.className=n.className+" "+B+"-balloon-div-"+this.className);B=n.style;this.disableMouseEvents&&(B.pointerEvents="none");B.position="absolute";var D=this.minWidth,C="";isNaN(D)||(C="min-width:"+(D-2*r)+"px; ");n.innerHTML='<div style="text-align:'+this.textAlign+"; "+C+"max-width:"+this.maxWidth+"px; font-size:"+z+"px; color:"+this.color+"; font-family:"+
A+'">'+this.text+"</div>";c.chartDiv.appendChild(n);this.textDiv=n;var K=n.offsetWidth,H=n.offsetHeight;n.clientHeight&&(K=n.clientWidth,H=n.clientHeight);A=H+2*w;C=K+2*r;!isNaN(D)&&C<D&&(C=D);window.opera&&(A+=2);var Q=!1,z=this.offsetY;c.handDrawn&&(z+=c.handDrawScatter+2);"H"!=x?(D=a-C/2,b<k+A+10&&"down"!=x?(Q=!0,e&&(b+=z),z=b+y,this.deltaSignY=-1):(e&&(b-=z),z=b-A-y,this.deltaSignY=1)):(2*y>A&&(y=A/2),z=b-A/2,a<g+(l-g)/2?(D=a+y,this.deltaSignX=-1):(D=a-C-y,this.deltaSignX=1));z+A>=m&&(z=m-A);
z<k&&(z=k);D<g&&(D=g);D+C>l&&(D=l-C);var k=z+w,m=D+r,M=this.shadowAlpha,P=this.shadowColor,r=this.borderThickness,ia=this.bulletSize,I,w=this.fillAlpha,aa=this.borderAlpha;this.showBullet&&(I=d.circle(h,ia,t,w),f.push(I));this.drop?(g=C/1.6,l=0,"V"==x&&(x="down"),"H"==x&&(x="left"),"down"==x&&(D=a+1,z=b-g-g/3),"up"==x&&(l=180,D=a+1,z=b+g+g/3),"left"==x&&(l=270,D=a+g+g/3+2,z=b),"right"==x&&(l=90,D=a-g-g/3+2,z=b),k=z-H/2+1,m=D-K/2-1,q=d.drop(h,g,l,q,w,r,p,aa)):0<u||0===y?(0<M&&(a=d.rect(h,C,A,q,0,r+
1,P,M,u),d.isModern?a.translate(1,1):a.translate(4,4),f.push(a)),q=d.rect(h,C,A,q,w,r,p,aa,u)):(t=[],u=[],"H"!=x?(g=a-D,g>C-y&&(g=C-y),g<y&&(g=y),t=[0,g-y,a-D,g+y,C,C,0,0],u=Q?[0,0,b-z,0,0,A,A,0]:[A,A,b-z,A,A,0,0,A]):(x=b-z,x>A-y&&(x=A-y),x<y&&(x=y),u=[0,x-y,b-z,x+y,A,A,0,0],t=a<g+(l-g)/2?[0,0,D<a?0:a-D,0,0,C,C,0]:[C,C,D+C>a?C:a-D,C,C,0,0,C]),0<M&&(a=d.polygon(h,t,u,q,0,r,P,M),a.translate(1,1),f.push(a)),q=d.polygon(h,t,u,q,w,r,p,aa));this.bg=q;f.push(q);q.toFront();d.setCN(c,q,"balloon-bg");this.className&&
d.setCN(c,q,"balloon-bg-"+this.className);h=1*this.deltaSignX;m+=this.sdx;k+=this.sdy;B.left=m+"px";B.top=k+"px";f.translate(D-h,z,1,!0);q=q.getBBox();this.bottom=z+A+1;this.yPos=q.y+z;I&&I.translate(this.pointToX-D+h,b-z);b=this.animationDuration;0<this.animationDuration&&!e&&!isNaN(this.prevX)&&(f.translate(this.prevX,this.prevY,NaN,!0),f.animate({translate:D-h+","+z},b,"easeOutSine"),n&&(B.left=this.prevTX+"px",B.top=this.prevTY+"px",this.xAnim=c.animate({node:n},"left",this.prevTX,m,b,"easeOutSine",
"px"),this.yAnim=c.animate({node:n},"top",this.prevTY,k,b,"easeOutSine","px")));this.prevX=D-h;this.prevY=z;this.prevTX=m;this.prevTY=k}}},fixPrevious:function(){this.rPrevX=this.prevX;this.rPrevY=this.prevY;this.rPrevTX=this.prevTX;this.rPrevTY=this.prevTY},restorePrevious:function(){this.prevX=this.rPrevX;this.prevY=this.rPrevY;this.prevTX=this.rPrevTX;this.prevTY=this.rPrevTY},followMouse:function(){if(this.follow&&this.show){var a=this.chart.mouseX-this.offsetX*this.deltaSignX-this.sdx,b=this.chart.mouseY-
this.sdy;this.pointToX=a;this.pointToY=b;if(a!=this.previousX||b!=this.previousY)if(this.previousX=a,this.previousY=b,0===this.cornerRadius)this.draw();else{var c=this.set;if(c){var d=c.getBBox(),a=a-d.width/2,h=b-d.height-10;a<this.l&&(a=this.l);a>this.r-d.width&&(a=this.r-d.width);h<this.t&&(h=b+10);c.translate(a,h);b=this.textDiv.style;b.left=a+this.horizontalPadding+"px";b.top=h+this.verticalPadding+"px"}}}},changeColor:function(a){this.balloonColor=a},setBounds:function(a,b,c,d){this.l=a;this.t=
b;this.r=c;this.b=d;this.destroyTO&&clearTimeout(this.destroyTO)},showBalloon:function(a){if(this.text!=a||this.positionChanged)this.text=a,this.isHiding=!1,this.show=!0,this.destroyTO&&clearTimeout(this.destroyTO),a=this.chart,this.fadeAnim1&&a.stopAnim(this.fadeAnim1),this.fadeAnim2&&a.stopAnim(this.fadeAnim2),this.draw(),this.positionChanged=!1},hide:function(a){var b=this;b.text=void 0;isNaN(a)&&(a=b.fadeOutDuration);var c=b.chart;if(0<a&&!b.isHiding){b.isHiding=!0;b.destroyTO&&clearTimeout(b.destroyTO);
b.destroyTO=setTimeout(function(){b.destroy.call(b)},1E3*a);b.follow=!1;b.show=!1;var d=b.set;d&&(d.setAttr("opacity",b.fillAlpha),b.fadeAnim1=d.animate({opacity:0},a,"easeInSine"));b.textDiv&&(b.fadeAnim2=c.animate({node:b.textDiv},"opacity",1,0,a,"easeInSine",""))}else b.show=!1,b.follow=!1,b.destroy()},setPosition:function(a,b){if(a!=this.pointToX||b!=this.pointToY)this.previousX=this.pointToX,this.previousY=this.pointToY,this.pointToX=a,this.pointToY=b,this.positionChanged=!0},followCursor:function(a){var b=
this;b.follow=a;clearInterval(b.interval);var c=b.chart.mouseX-b.sdx,d=b.chart.mouseY-b.sdy;!isNaN(c)&&a&&(b.pointToX=c-b.offsetX*b.deltaSignX,b.pointToY=d,b.followMouse(),b.interval=setInterval(function(){b.followMouse.call(b)},40))},removeDiv:function(){if(this.textDiv){var a=this.textDiv.parentNode;a&&a.removeChild(this.textDiv)}},destroy:function(){clearInterval(this.interval);d.remove(this.set);this.removeDiv();this.set=null}})})();(function(){var d=window.AmCharts;d.AmCoordinateChart=d.Class({inherits:d.AmChart,construct:function(a){d.AmCoordinateChart.base.construct.call(this,a);this.theme=a;this.createEvents("rollOverGraphItem","rollOutGraphItem","clickGraphItem","doubleClickGraphItem","rightClickGraphItem","clickGraph","rollOverGraph","rollOutGraph");this.startAlpha=1;this.startDuration=0;this.startEffect="elastic";this.sequencedAnimation=!0;this.colors="#FF6600 #FCD202 #B0DE09 #0D8ECF #2A0CD0 #CD0D74 #CC0000 #00CC00 #0000CC #DDDDDD #999999 #333333 #990000".split(" ");
this.balloonDateFormat="MMM DD, YYYY";this.valueAxes=[];this.graphs=[];this.guides=[];this.gridAboveGraphs=!1;d.applyTheme(this,a,"AmCoordinateChart")},initChart:function(){d.AmCoordinateChart.base.initChart.call(this);this.drawGraphs=!0;var a=this.categoryAxis;a&&(this.categoryAxis=d.processObject(a,d.CategoryAxis,this.theme));this.processValueAxes();this.createValueAxes();this.processGraphs();this.processGuides();d.VML&&(this.startAlpha=1);this.setLegendData(this.graphs);this.gridAboveGraphs&&(this.gridSet.toFront(),
this.bulletSet.toFront(),this.balloonsSet.toFront())},createValueAxes:function(){if(0===this.valueAxes.length){var a=new d.ValueAxis;this.addValueAxis(a)}},parseData:function(){this.processValueAxes();this.processGraphs()},parseSerialData:function(a){this.chartData=[];if(a)if(0<this.processTimeout){1>this.processCount&&(this.processCount=1);var b=a.length/this.processCount;this.parseCount=Math.ceil(b)-1;for(var c=0;c<b;c++)this.delayParseSerialData(a,c)}else this.parseCount=0,this.parsePartSerialData(a,
0,a.length,0);else this.onDataUpdated()},delayParseSerialData:function(a,b){var c=this,d=c.processCount;setTimeout(function(){c.parsePartSerialData.call(c,a,b*d,(b+1)*d,b)},c.processTimeout)},parsePartSerialData:function(a,b,c,e){c>a.length&&(c=a.length);var h=this.graphs,f={},g=this.seriesIdField;g||(g=this.categoryField);var k=!1,l,m=this.categoryAxis,n,q,p;m&&(k=m.parseDates,n=m.forceShowField,p=m.classNameField,q=m.labelColorField,l=m.categoryFunction);var t,r,w={},y;k&&(t=d.extractPeriod(m.minPeriod),
r=t.period,t=t.count,y=d.getPeriodDuration(r,t));var x={};this.lookupTable=x;var u,A=this.dataDateFormat,z={};for(u=b;u<c;u++){var B={},D=a[u];b=D[this.categoryField];B.dataContext=D;B.category=l?l(b,D,m):String(b);n&&(B.forceShow=D[n]);p&&(B.className=D[p]);q&&(B.labelColor=D[q]);x[D[g]]=B;if(k&&(m.categoryFunction?b=m.categoryFunction(b,D,m):(!A||b instanceof Date||(b=b.toString()+" |"),b=d.getDate(b,A,m.minPeriod)),b=d.resetDateToMin(b,r,t,m.firstDayOfWeek),B.category=b,B.time=b.getTime(),isNaN(B.time)))continue;
var C=this.valueAxes;B.axes={};B.x={};var K;for(K=0;K<C.length;K++){var H=C[K].id;B.axes[H]={};B.axes[H].graphs={};var Q;for(Q=0;Q<h.length;Q++){b=h[Q];var M=b.id,P=1.1;isNaN(b.gapPeriod)||(P=b.gapPeriod);var ia=b.periodValue;if(b.valueAxis.id==H){B.axes[H].graphs[M]={};var I={};I.index=u;var aa=D;b.dataProvider&&(aa=f);I.values=this.processValues(aa,b,ia);if(!b.connect||b.forceGap&&!isNaN(b.gapPeriod))if(z&&z[M]&&0<P&&B.time-w[M]>=y*P&&(z[M].gap=!0),b.forceGap){var P=0,ma;for(ma in I.values)P++;
0<P&&(w[M]=B.time,z[M]=I)}else w[M]=B.time,z[M]=I;this.processFields(b,I,aa);I.category=B.category;I.serialDataItem=B;I.graph=b;B.axes[H].graphs[M]=I}}}this.chartData[u]=B}if(this.parseCount==e){for(a=0;a<h.length;a++)b=h[a],b.dataProvider&&this.parseGraphData(b);this.dataChanged=!1;this.dispatchDataUpdated=!0;this.onDataUpdated()}},processValues:function(a,b,c){var e={},h,f=!1;"candlestick"!=b.type&&"ohlc"!=b.type||""===c||(f=!0);for(var g="value error open close low high".split(" "),k=0;k<g.length;k++){var l=
g[k];"value"!=l&&"error"!=l&&f&&(c=l.charAt(0).toUpperCase()+l.slice(1));var m=a[b[l+"Field"]+c];null!==m&&(h=Number(m),isNaN(h)||(e[l]=h),"date"==b.valueAxis.type&&void 0!==m&&(h=d.getDate(m,b.chart.dataDateFormat),e[l]=h.getTime()))}return e},parseGraphData:function(a){var b=a.dataProvider,c=a.seriesIdField;c||(c=this.seriesIdField);c||(c=this.categoryField);var d;for(d=0;d<b.length;d++){var h=b[d],f=this.lookupTable[String(h[c])],g=a.valueAxis.id;f&&(g=f.axes[g].graphs[a.id],g.serialDataItem=f,
g.values=this.processValues(h,a,a.periodValue),this.processFields(a,g,h))}},addValueAxis:function(a){a.chart=this;this.valueAxes.push(a);this.validateData()},removeValueAxesAndGraphs:function(){var a=this.valueAxes,b;for(b=a.length-1;-1<b;b--)this.removeValueAxis(a[b])},removeValueAxis:function(a){var b=this.graphs,c;for(c=b.length-1;0<=c;c--){var d=b[c];d&&d.valueAxis==a&&this.removeGraph(d)}b=this.valueAxes;for(c=b.length-1;0<=c;c--)b[c]==a&&b.splice(c,1);this.validateData()},addGraph:function(a){this.graphs.push(a);
this.chooseGraphColor(a,this.graphs.length-1);this.validateData()},removeGraph:function(a){var b=this.graphs,c;for(c=b.length-1;0<=c;c--)b[c]==a&&(b.splice(c,1),a.destroy());this.validateData()},handleValueAxisZoom:function(){},processValueAxes:function(){var a=this.valueAxes,b;for(b=0;b<a.length;b++){var c=a[b],c=d.processObject(c,d.ValueAxis,this.theme);a[b]=c;c.chart=this;c.init();this.listenTo(c,"axisIntZoomed",this.handleValueAxisZoom);c.id||(c.id="valueAxisAuto"+b+"_"+(new Date).getTime());
void 0===c.usePrefixes&&(c.usePrefixes=this.usePrefixes)}},processGuides:function(){var a=this.guides,b=this.categoryAxis;if(a)for(var c=0;c<a.length;c++){var e=a[c];(void 0!==e.category||void 0!==e.date)&&b&&b.addGuide(e);e.id||(e.id="guideAuto"+c+"_"+(new Date).getTime());var h=e.valueAxis;h?(d.isString(h)&&(h=this.getValueAxisById(h)),h?h.addGuide(e):this.valueAxes[0].addGuide(e)):isNaN(e.value)||this.valueAxes[0].addGuide(e)}},processGraphs:function(){var a=this.graphs,b;this.graphsById={};for(b=
0;b<a.length;b++){var c=a[b],c=d.processObject(c,d.AmGraph,this.theme);a[b]=c;this.chooseGraphColor(c,b);c.chart=this;c.init();d.isString(c.valueAxis)&&(c.valueAxis=this.getValueAxisById(c.valueAxis));c.valueAxis||(c.valueAxis=this.valueAxes[0]);c.id||(c.id="graphAuto"+b+"_"+(new Date).getTime());this.graphsById[c.id]=c}},formatString:function(a,b,c){var e=b.graph,h=e.valueAxis;h.duration&&b.values.value&&(h=d.formatDuration(b.values.value,h.duration,"",h.durationUnits,h.maxInterval,h.numberFormatter),
a=a.split("[[value]]").join(h));a=d.massReplace(a,{"[[title]]":e.title,"[[description]]":b.description});a=c?d.fixNewLines(a):d.fixBrakes(a);return a=d.cleanFromEmpty(a)},getBalloonColor:function(a,b,c){var e=a.lineColor,h=a.balloonColor;c&&(h=e);c=a.fillColorsR;"object"==typeof c?e=c[0]:void 0!==c&&(e=c);b.isNegative&&(c=a.negativeLineColor,a=a.negativeFillColors,"object"==typeof a?c=a[0]:void 0!==a&&(c=a),void 0!==c&&(e=c));void 0!==b.color&&(e=b.color);void 0!==b.lineColor&&(e=b.lineColor);b=b.fillColors;
void 0!==b&&(e=b,d.ifArray(b)&&(e=b[0]));void 0===h&&(h=e);return h},getGraphById:function(a){return d.getObjById(this.graphs,a)},getValueAxisById:function(a){return d.getObjById(this.valueAxes,a)},processFields:function(a,b,c){if(a.itemColors){var e=a.itemColors,h=b.index;b.color=h<e.length?e[h]:d.randomColor()}e="lineColor color alpha fillColors description bullet customBullet bulletSize bulletConfig url labelColor dashLength pattern gap className columnIndex".split(" ");for(h=0;h<e.length;h++){var f=
e[h],g=a[f+"Field"];g&&(g=c[g],d.isDefined(g)&&(b[f]=g))}b.dataContext=c},chooseGraphColor:function(a,b){if(a.lineColor)a.lineColorR=a.lineColor;else{var c;c=this.colors.length>b?this.colors[b]:a.lineColorR?a.lineColorR:d.randomColor();a.lineColorR=c}a.fillColorsR=a.fillColors?a.fillColors:a.lineColorR;a.bulletBorderColorR=a.bulletBorderColor?a.bulletBorderColor:a.useLineColorForBulletBorder?a.lineColorR:a.bulletColor;a.bulletColorR=a.bulletColor?a.bulletColor:a.lineColorR;if(c=this.patterns)a.pattern=
c[b]},handleLegendEvent:function(a){var b=a.type;if(a=a.dataItem){var c=a.hidden,d=a.showBalloon;switch(b){case "clickMarker":this.textClickEnabled&&(d?this.hideGraphsBalloon(a):this.showGraphsBalloon(a));break;case "clickLabel":d?this.hideGraphsBalloon(a):this.showGraphsBalloon(a);break;case "rollOverItem":c||this.highlightGraph(a);break;case "rollOutItem":c||this.unhighlightGraph();break;case "hideItem":this.hideGraph(a);break;case "showItem":this.showGraph(a)}}},highlightGraph:function(a){var b=
this.graphs;if(b){var c,d=.2;this.legend&&(d=this.legend.rollOverGraphAlpha);if(1!=d)for(c=0;c<b.length;c++){var h=b[c];h!=a&&h.changeOpacity(d)}}},unhighlightGraph:function(){var a;this.legend&&(a=this.legend.rollOverGraphAlpha);if(1!=a){a=this.graphs;var b;for(b=0;b<a.length;b++)a[b].changeOpacity(1)}},showGraph:function(a){a.switchable&&(a.hidden=!1,this.dataChanged=!0,"xy"!=this.type&&(this.marginsUpdated=!1),this.chartCreated&&this.initChart())},hideGraph:function(a){a.switchable&&(this.dataChanged=
!0,"xy"!=this.type&&(this.marginsUpdated=!1),a.hidden=!0,this.chartCreated&&this.initChart())},hideGraphsBalloon:function(a){a.showBalloon=!1;this.updateLegend()},showGraphsBalloon:function(a){a.showBalloon=!0;this.updateLegend()},updateLegend:function(){this.legend&&this.legend.invalidateSize()},resetAnimation:function(){var a=this.graphs;if(a){var b;for(b=0;b<a.length;b++)a[b].animationPlayed=!1}},animateAgain:function(){this.resetAnimation();this.validateNow()}})})();(function(){var d=window.AmCharts;d.TrendLine=d.Class({construct:function(a){this.cname="TrendLine";this.createEvents("click");this.isProtected=!1;this.dashLength=0;this.lineColor="#00CC00";this.lineThickness=this.lineAlpha=1;d.applyTheme(this,a,this.cname)},draw:function(){var a=this;a.destroy();var b=a.chart,c=b.container,e,h,f,g,k=a.categoryAxis,l=a.initialDate,m=a.initialCategory,n=a.finalDate,q=a.finalCategory,p=a.valueAxis,t=a.valueAxisX,r=a.initialXValue,w=a.finalXValue,y=a.initialValue,x=
a.finalValue,u=p.recalculateToPercents,A=b.dataDateFormat;k&&(l&&(l=d.getDate(l,A,"fff"),a.initialDate=l,e=k.dateToCoordinate(l)),m&&(e=k.categoryToCoordinate(m)),n&&(n=d.getDate(n,A,"fff"),a.finalDate=n,h=k.dateToCoordinate(n)),q&&(h=k.categoryToCoordinate(q)));t&&!u&&(isNaN(r)||(e=t.getCoordinate(r)),isNaN(w)||(h=t.getCoordinate(w)));p&&!u&&(isNaN(y)||(f=p.getCoordinate(y)),isNaN(x)||(g=p.getCoordinate(x)));if(!(isNaN(e)||isNaN(h)||isNaN(f)||isNaN(f))){b.rotate?(k=[f,g],g=[e,h]):(k=[e,h],g=[f,g]);
l=a.lineColor;f=d.line(c,k,g,l,a.lineAlpha,a.lineThickness,a.dashLength);e=k;h=g;q=k[1]-k[0];p=g[1]-g[0];0===q&&(q=.01);0===p&&(p=.01);m=q/Math.abs(q);n=p/Math.abs(p);p=90*Math.PI/180-Math.asin(q/(q*p/Math.abs(q*p)*Math.sqrt(Math.pow(q,2)+Math.pow(p,2))));q=Math.abs(5*Math.cos(p));p=Math.abs(5*Math.sin(p));e.push(k[1]-m*p,k[0]-m*p);h.push(g[1]+n*q,g[0]+n*q);g=d.polygon(c,e,h,l,.005,0);c=c.set([g,f]);c.translate(b.marginLeftReal,b.marginTopReal);b.trendLinesSet.push(c);d.setCN(b,f,"trend-line");d.setCN(b,
f,"trend-line-"+a.id);a.line=f;a.set=c;if(f=a.initialImage)f=d.processObject(f,d.Image,a.theme),f.chart=b,f.draw(),f.translate(e[0]+f.offsetX,h[0]+f.offsetY),c.push(f.set);if(f=a.finalImage)f=d.processObject(f,d.Image,a.theme),f.chart=b,f.draw(),f.translate(e[1]+f.offsetX,h[1]+f.offsetY),c.push(f.set);g.mouseup(function(){a.handleLineClick()}).mouseover(function(){a.handleLineOver()}).mouseout(function(){a.handleLineOut()});g.touchend&&g.touchend(function(){a.handleLineClick()});c.clipRect(0,0,b.plotAreaWidth,
b.plotAreaHeight)}},handleLineClick:function(){this.fire({type:"click",trendLine:this,chart:this.chart})},handleLineOver:function(){var a=this.rollOverColor;void 0!==a&&this.line.attr({stroke:a});this.balloonText&&(clearTimeout(this.chart.hoverInt),a=this.line.getBBox(),this.chart.showBalloon(this.balloonText,this.lineColor,!0,this.x+a.x+a.width/2,this.y+a.y+a.height/2))},handleLineOut:function(){this.line.attr({stroke:this.lineColor});this.balloonText&&this.chart.hideBalloon()},destroy:function(){d.remove(this.set)}})})();(function(){var d=window.AmCharts;d.Image=d.Class({construct:function(a){this.cname="Image";this.height=this.width=20;this.rotation=this.offsetY=this.offsetX=0;this.balloonColor=this.color="#000000";this.opacity=1;d.applyTheme(this,a,this.cname)},draw:function(){var a=this;a.set&&a.set.remove();var b=a.chart.container;a.set=b.set();var c,d;a.url?(c=b.image(a.url,0,0,a.width,a.height),d=1):a.svgPath&&(c=b.path(a.svgPath),c.setAttr("fill",a.color),c.setAttr("stroke",a.outlineColor),b=c.getBBox(),d=
Math.min(a.width/b.width,a.height/b.height));c&&(c.setAttr("opacity",a.opacity),a.set.rotate(a.rotation),c.translate(-a.width/2,-a.height/2,d),a.balloonText&&c.mouseover(function(){a.chart.showBalloon(a.balloonText,a.balloonColor,!0)}).mouseout(function(){a.chart.hideBalloon()}).touchend(function(){a.chart.hideBalloon()}).touchstart(function(){a.chart.showBalloon(a.balloonText,a.balloonColor,!0)}),a.set.push(c))},translate:function(a,b){this.set&&this.set.translate(a,b)}})})();(function(){var d=window.AmCharts;d.circle=function(a,b,c,e,h,f,g,k,l){0>=b&&(b=.001);if(void 0==h||0===h)h=.01;void 0===f&&(f="#000000");void 0===g&&(g=0);e={fill:c,stroke:f,"fill-opacity":e,"stroke-width":h,"stroke-opacity":g};a=isNaN(l)?a.circle(0,0,b).attr(e):a.ellipse(0,0,b,l).attr(e);k&&a.gradient("radialGradient",[c,d.adjustLuminosity(c,-.6)]);return a};d.text=function(a,b,c,e,h,f,g,k){f||(f="middle");"right"==f&&(f="end");"left"==f&&(f="start");isNaN(k)&&(k=1);void 0!==b&&(b=String(b),d.isIE&&
!d.isModern&&(b=b.replace("&amp;","&"),b=b.replace("&","&amp;")));c={fill:c,"font-family":e,"font-size":h+"px",opacity:k};!0===g&&(c["font-weight"]="bold");c["text-anchor"]=f;return a.text(b,c)};d.polygon=function(a,b,c,e,h,f,g,k,l,m,n){isNaN(f)&&(f=.01);isNaN(k)&&(k=h);var q=e,p=!1;"object"==typeof q&&1<q.length&&(p=!0,q=q[0]);void 0===g&&(g=q);h={fill:q,stroke:g,"fill-opacity":h,"stroke-width":f,"stroke-opacity":k};void 0!==n&&0<n&&(h["stroke-dasharray"]=n);n=d.dx;f=d.dy;a.handDrawn&&(c=d.makeHD(b,
c,a.handDrawScatter),b=c[0],c=c[1]);g=Math.round;m&&(b[t]=d.roundTo(b[t],5),c[t]=d.roundTo(c[t],5),g=Number);k="M"+(g(b[0])+n)+","+(g(c[0])+f);for(var t=1;t<b.length;t++)m&&(b[t]=d.roundTo(b[t],5),c[t]=d.roundTo(c[t],5)),k+=" L"+(g(b[t])+n)+","+(g(c[t])+f);a=a.path(k+" Z").attr(h);p&&a.gradient("linearGradient",e,l);return a};d.rect=function(a,b,c,e,h,f,g,k,l,m,n){if(isNaN(b)||isNaN(c))return a.set();isNaN(f)&&(f=0);void 0===l&&(l=0);void 0===m&&(m=270);isNaN(h)&&(h=0);var q=e,p=!1;"object"==typeof q&&
(q=q[0],p=!0);void 0===g&&(g=q);void 0===k&&(k=h);b=Math.round(b);c=Math.round(c);var t=0,r=0;0>b&&(b=Math.abs(b),t=-b);0>c&&(c=Math.abs(c),r=-c);t+=d.dx;r+=d.dy;h={fill:q,stroke:g,"fill-opacity":h,"stroke-opacity":k};void 0!==n&&0<n&&(h["stroke-dasharray"]=n);a=a.rect(t,r,b,c,l,f).attr(h);p&&a.gradient("linearGradient",e,m);return a};d.bullet=function(a,b,c,e,h,f,g,k,l,m,n,q,p){var t;"circle"==b&&(b="round");switch(b){case "round":t=d.circle(a,c/2,e,h,f,g,k);break;case "square":t=d.polygon(a,[-c/
2,c/2,c/2,-c/2],[c/2,c/2,-c/2,-c/2],e,h,f,g,k,m-180,void 0,p);break;case "rectangle":t=d.polygon(a,[-c,c,c,-c],[c/2,c/2,-c/2,-c/2],e,h,f,g,k,m-180,void 0,p);break;case "diamond":t=d.polygon(a,[-c/2,0,c/2,0],[0,-c/2,0,c/2],e,h,f,g,k);break;case "triangleUp":t=d.triangle(a,c,0,e,h,f,g,k);break;case "triangleDown":t=d.triangle(a,c,180,e,h,f,g,k);break;case "triangleLeft":t=d.triangle(a,c,270,e,h,f,g,k);break;case "triangleRight":t=d.triangle(a,c,90,e,h,f,g,k);break;case "bubble":t=d.circle(a,c/2,e,h,
f,g,k,!0);break;case "line":t=d.line(a,[-c/2,c/2],[0,0],e,h,f,g,k);break;case "yError":t=a.set();t.push(d.line(a,[0,0],[-c/2,c/2],e,h,f));t.push(d.line(a,[-l,l],[-c/2,-c/2],e,h,f));t.push(d.line(a,[-l,l],[c/2,c/2],e,h,f));break;case "xError":t=a.set(),t.push(d.line(a,[-c/2,c/2],[0,0],e,h,f)),t.push(d.line(a,[-c/2,-c/2],[-l,l],e,h,f)),t.push(d.line(a,[c/2,c/2],[-l,l],e,h,f))}t&&t.pattern(n,NaN,q);return t};d.triangle=function(a,b,c,d,h,f,g,k){if(void 0===f||0===f)f=1;void 0===g&&(g="#000");void 0===
k&&(k=0);d={fill:d,stroke:g,"fill-opacity":h,"stroke-width":f,"stroke-opacity":k};b/=2;var l;0===c&&(l=" M"+-b+","+b+" L0,"+-b+" L"+b+","+b+" Z");180==c&&(l=" M"+-b+","+-b+" L0,"+b+" L"+b+","+-b+" Z");90==c&&(l=" M"+-b+","+-b+" L"+b+",0 L"+-b+","+b+" Z");270==c&&(l=" M"+-b+",0 L"+b+","+b+" L"+b+","+-b+" Z");return a.path(l).attr(d)};d.line=function(a,b,c,e,h,f,g,k,l,m,n){if(a.handDrawn&&!n)return d.handDrawnLine(a,b,c,e,h,f,g,k,l,m,n);f={fill:"none","stroke-width":f};void 0!==g&&0<g&&(f["stroke-dasharray"]=
g);isNaN(h)||(f["stroke-opacity"]=h);e&&(f.stroke=e);e=Math.round;m&&(e=Number,b[0]=d.roundTo(b[0],5),c[0]=d.roundTo(c[0],5));m=d.dx;h=d.dy;g="M"+(e(b[0])+m)+","+(e(c[0])+h);for(k=1;k<b.length;k++)b[k]=d.roundTo(b[k],5),c[k]=d.roundTo(c[k],5),g+=" L"+(e(b[k])+m)+","+(e(c[k])+h);if(d.VML)return a.path(g,void 0,!0).attr(f);l&&(g+=" M0,0 L0,0");return a.path(g).attr(f)};d.makeHD=function(a,b,c){for(var d=[],h=[],f=1;f<a.length;f++)for(var g=Number(a[f-1]),k=Number(b[f-1]),l=Number(a[f]),m=Number(b[f]),
n=Math.round(Math.sqrt(Math.pow(l-g,2)+Math.pow(m-k,2))/50)+1,l=(l-g)/n,m=(m-k)/n,q=0;q<=n;q++){var p=k+q*m+Math.random()*c;d.push(g+q*l+Math.random()*c);h.push(p)}return[d,h]};d.handDrawnLine=function(a,b,c,e,h,f,g,k,l,m){var n,q=a.set();for(n=1;n<b.length;n++)for(var p=[b[n-1],b[n]],t=[c[n-1],c[n]],t=d.makeHD(p,t,a.handDrawScatter),p=t[0],t=t[1],r=1;r<p.length;r++)q.push(d.line(a,[p[r-1],p[r]],[t[r-1],t[r]],e,h,f+Math.random()*a.handDrawThickness-a.handDrawThickness/2,g,k,l,m,!0));return q};d.doNothing=
function(a){return a};d.drop=function(a,b,c,d,h,f,g,k){var l=1/180*Math.PI,m=c-20,n=Math.sin(m*l)*b,q=Math.cos(m*l)*b,p=Math.sin((m+40)*l)*b,t=Math.cos((m+40)*l)*b,r=.8*b,w=-b/3,y=b/3;0===c&&(w=-w,y=0);180==c&&(y=0);90==c&&(w=0);270==c&&(w=0,y=-y);c={fill:d,stroke:g,"stroke-width":f,"stroke-opacity":k,"fill-opacity":h};b="M"+n+","+q+" A"+b+","+b+",0,1,1,"+p+","+t+(" A"+r+","+r+",0,0,0,"+(Math.sin((m+20)*l)*b+y)+","+(Math.cos((m+20)*l)*b+w));b+=" A"+r+","+r+",0,0,0,"+n+","+q;return a.path(b,void 0,
void 0,"1000,1000").attr(c)};d.wedge=function(a,b,c,e,h,f,g,k,l,m,n,q,p,t){var r=Math.round;f=r(f);g=r(g);k=r(k);var w=r(g/f*k),y=d.VML,x=359.5+f/100;359.94<x&&(x=359.94);h>=x&&(h=x);var u=1/180*Math.PI,x=b+Math.sin(e*u)*k,A=c-Math.cos(e*u)*w,z=b+Math.sin(e*u)*f,B=c-Math.cos(e*u)*g,D=b+Math.sin((e+h)*u)*f,C=c-Math.cos((e+h)*u)*g,K=b+Math.sin((e+h)*u)*k,u=c-Math.cos((e+h)*u)*w,H={fill:d.adjustLuminosity(m.fill,-.2),"stroke-opacity":0,"fill-opacity":m["fill-opacity"]},Q=0;180<Math.abs(h)&&(Q=1);e=a.set();
var M;y&&(x=r(10*x),z=r(10*z),D=r(10*D),K=r(10*K),A=r(10*A),B=r(10*B),C=r(10*C),u=r(10*u),b=r(10*b),l=r(10*l),c=r(10*c),f*=10,g*=10,k*=10,w*=10,1>Math.abs(h)&&1>=Math.abs(D-z)&&1>=Math.abs(C-B)&&(M=!0));h="";var P;q&&(H["fill-opacity"]=0,H["stroke-opacity"]=m["stroke-opacity"]/2,H.stroke=m.stroke);if(0<l){P=" M"+x+","+(A+l)+" L"+z+","+(B+l);y?(M||(P+=" A"+(b-f)+","+(l+c-g)+","+(b+f)+","+(l+c+g)+","+z+","+(B+l)+","+D+","+(C+l)),P+=" L"+K+","+(u+l),0<k&&(M||(P+=" B"+(b-k)+","+(l+c-w)+","+(b+k)+","+
(l+c+w)+","+K+","+(l+u)+","+x+","+(l+A)))):(P+=" A"+f+","+g+",0,"+Q+",1,"+D+","+(C+l)+" L"+K+","+(u+l),0<k&&(P+=" A"+k+","+w+",0,"+Q+",0,"+x+","+(A+l)));P+=" Z";var ia=l;y&&(ia/=10);for(var I=0;I<ia;I+=10){var aa=a.path(P,void 0,void 0,"1000,1000").attr(H);e.push(aa);aa.translate(0,-I)}P=a.path(" M"+x+","+A+" L"+x+","+(A+l)+" L"+z+","+(B+l)+" L"+z+","+B+" L"+x+","+A+" Z",void 0,void 0,"1000,1000").attr(H);l=a.path(" M"+D+","+C+" L"+D+","+(C+l)+" L"+K+","+(u+l)+" L"+K+","+u+" L"+D+","+C+" Z",void 0,
void 0,"1000,1000").attr(H);e.push(P);e.push(l)}y?(M||(h=" A"+r(b-f)+","+r(c-g)+","+r(b+f)+","+r(c+g)+","+r(z)+","+r(B)+","+r(D)+","+r(C)),g=" M"+r(x)+","+r(A)+" L"+r(z)+","+r(B)+h+" L"+r(K)+","+r(u)):g=" M"+x+","+A+" L"+z+","+B+(" A"+f+","+g+",0,"+Q+",1,"+D+","+C)+" L"+K+","+u;0<k&&(y?M||(g+=" B"+(b-k)+","+(c-w)+","+(b+k)+","+(c+w)+","+K+","+u+","+x+","+A):g+=" A"+k+","+w+",0,"+Q+",0,"+x+","+A);a.handDrawn&&(k=d.line(a,[x,z],[A,B],m.stroke,m.thickness*Math.random()*a.handDrawThickness,m["stroke-opacity"]),
e.push(k));a=a.path(g+" Z",void 0,void 0,"1000,1000").attr(m);if(n){k=[];for(w=0;w<n.length;w++)k.push(d.adjustLuminosity(m.fill,n[w]));"radial"!=t||d.isModern||(k=[]);0<k.length&&a.gradient(t+"Gradient",k)}d.isModern&&"radial"==t&&a.grad&&(a.grad.setAttribute("gradientUnits","userSpaceOnUse"),a.grad.setAttribute("r",f),a.grad.setAttribute("cx",b),a.grad.setAttribute("cy",c));a.pattern(q,NaN,p);e.wedge=a;e.push(a);return e};d.rgb2hex=function(a){return(a=a.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i))&&
4===a.length?"#"+("0"+parseInt(a[1],10).toString(16)).slice(-2)+("0"+parseInt(a[2],10).toString(16)).slice(-2)+("0"+parseInt(a[3],10).toString(16)).slice(-2):""};d.adjustLuminosity=function(a,b){a&&-1!=a.indexOf("rgb")&&(a=d.rgb2hex(a));a=String(a).replace(/[^0-9a-f]/gi,"");6>a.length&&(a=String(a[0])+String(a[0])+String(a[1])+String(a[1])+String(a[2])+String(a[2]));b=b||0;var c="#",e,h;for(h=0;3>h;h++)e=parseInt(a.substr(2*h,2),16),e=Math.round(Math.min(Math.max(0,e+e*b),255)).toString(16),c+=("00"+
e).substr(e.length);return c}})();(function(){var d=window.AmCharts;d.Bezier=d.Class({construct:function(a,b,c,e,h,f,g,k,l,m,n){var q,p;"object"==typeof g&&1<g.length&&(p=!0,q=g,g=g[0]);"object"==typeof k&&(k=k[0]);0===k&&(g="none");f={fill:g,"fill-opacity":k,"stroke-width":f};void 0!==l&&0<l&&(f["stroke-dasharray"]=l);isNaN(h)||(f["stroke-opacity"]=h);e&&(f.stroke=e);e="M"+Math.round(b[0])+","+Math.round(c[0]);h=[];for(l=0;l<b.length;l++)isNaN(b[l])||isNaN(c[l])?(e+=this.drawSegment(h),l<b.length-1&&(e+="L"+b[l+1]+","+c[l+1]),h=
[]):h.push({x:Number(b[l]),y:Number(c[l])});e+=this.drawSegment(h);m?e+=m:d.VML||(e+="M0,0 L0,0");this.path=a.path(e).attr(f);this.node=this.path.node;p&&this.path.gradient("linearGradient",q,n)},drawSegment:function(a){return 1<a.length?(a=this.interpolate(a),this.drawBeziers(a)):""},interpolate:function(a){var b=[];b.push({x:a[0].x,y:a[0].y});var c=a[1].x-a[0].x,e=a[1].y-a[0].y,h=d.bezierX,f=d.bezierY;b.push({x:a[0].x+c/h,y:a[0].y+e/f});var g;for(g=1;g<a.length-1;g++){var k=a[g-1],l=a[g],e=a[g+
1];isNaN(e.x)&&(e=l);isNaN(l.x)&&(l=k);isNaN(k.x)&&(k=l);c=e.x-l.x;e=e.y-k.y;k=l.x-k.x;k>c&&(k=c);b.push({x:l.x-k/h,y:l.y-e/f});b.push({x:l.x,y:l.y});b.push({x:l.x+k/h,y:l.y+e/f})}e=a[a.length-1].y-a[a.length-2].y;c=a[a.length-1].x-a[a.length-2].x;b.push({x:a[a.length-1].x-c/h,y:a[a.length-1].y-e/f});b.push({x:a[a.length-1].x,y:a[a.length-1].y});return b},drawBeziers:function(a){var b="",c;for(c=0;c<(a.length-1)/3;c++)b+=this.drawBezierMidpoint(a[3*c],a[3*c+1],a[3*c+2],a[3*c+3]);return b},drawBezierMidpoint:function(a,
b,c,d){var h=Math.round,f=this.getPointOnSegment(a,b,.75),g=this.getPointOnSegment(d,c,.75),k=(d.x-a.x)/16,l=(d.y-a.y)/16,m=this.getPointOnSegment(a,b,.375);a=this.getPointOnSegment(f,g,.375);a.x-=k;a.y-=l;b=this.getPointOnSegment(g,f,.375);b.x+=k;b.y+=l;c=this.getPointOnSegment(d,c,.375);k=this.getMiddle(m,a);f=this.getMiddle(f,g);g=this.getMiddle(b,c);m=" Q"+h(m.x)+","+h(m.y)+","+h(k.x)+","+h(k.y);m+=" Q"+h(a.x)+","+h(a.y)+","+h(f.x)+","+h(f.y);m+=" Q"+h(b.x)+","+h(b.y)+","+h(g.x)+","+h(g.y);return m+=
" Q"+h(c.x)+","+h(c.y)+","+h(d.x)+","+h(d.y)},getMiddle:function(a,b){return{x:(a.x+b.x)/2,y:(a.y+b.y)/2}},getPointOnSegment:function(a,b,c){return{x:a.x+(b.x-a.x)*c,y:a.y+(b.y-a.y)*c}}})})();(function(){var d=window.AmCharts;d.AmDraw=d.Class({construct:function(a,b,c,e){d.SVG_NS="http://www.w3.org/2000/svg";d.SVG_XLINK="http://www.w3.org/1999/xlink";d.hasSVG=!!document.createElementNS&&!!document.createElementNS(d.SVG_NS,"svg").createSVGRect;1>b&&(b=10);1>c&&(c=10);this.div=a;this.width=b;this.height=c;this.rBin=document.createElement("div");d.hasSVG?(d.SVG=!0,b=this.createSvgElement("svg"),a.appendChild(b),this.container=b,this.addDefs(e),this.R=new d.SVGRenderer(this)):d.isIE&&d.VMLRenderer&&
(d.VML=!0,d.vmlStyleSheet||(document.namespaces.add("amvml","urn:schemas-microsoft-com:vml"),31>document.styleSheets.length?(b=document.createStyleSheet(),b.addRule(".amvml","behavior:url(#default#VML); display:inline-block; antialias:true"),d.vmlStyleSheet=b):document.styleSheets[0].addRule(".amvml","behavior:url(#default#VML); display:inline-block; antialias:true")),this.container=a,this.R=new d.VMLRenderer(this,e),this.R.disableSelection(a))},createSvgElement:function(a){return document.createElementNS(d.SVG_NS,
a)},circle:function(a,b,c,e){var h=new d.AmDObject("circle",this);h.attr({r:c,cx:a,cy:b});this.addToContainer(h.node,e);return h},ellipse:function(a,b,c,e,h){var f=new d.AmDObject("ellipse",this);f.attr({rx:c,ry:e,cx:a,cy:b});this.addToContainer(f.node,h);return f},setSize:function(a,b){0<a&&0<b&&(this.container.style.width=a+"px",this.container.style.height=b+"px")},rect:function(a,b,c,e,h,f,g){var k=new d.AmDObject("rect",this);d.VML&&(h=Math.round(100*h/Math.min(c,e)),c+=2*f,e+=2*f,k.bw=f,k.node.style.marginLeft=
-f,k.node.style.marginTop=-f);1>c&&(c=1);1>e&&(e=1);k.attr({x:a,y:b,width:c,height:e,rx:h,ry:h,"stroke-width":f});this.addToContainer(k.node,g);return k},image:function(a,b,c,e,h,f){var g=new d.AmDObject("image",this);g.attr({x:b,y:c,width:e,height:h});this.R.path(g,a);this.addToContainer(g.node,f);return g},addToContainer:function(a,b){b||(b=this.container);b.appendChild(a)},text:function(a,b,c){return this.R.text(a,b,c)},path:function(a,b,c,e){var h=new d.AmDObject("path",this);e||(e="100,100");
h.attr({cs:e});c?h.attr({dd:a}):h.attr({d:a});this.addToContainer(h.node,b);return h},set:function(a){return this.R.set(a)},remove:function(a){if(a){var b=this.rBin;b.appendChild(a);b.innerHTML=""}},renderFix:function(){var a=this.container,b=a.style;b.top="0px";b.left="0px";try{var c=a.getBoundingClientRect(),d=c.left-Math.round(c.left),h=c.top-Math.round(c.top);d&&(b.left=d+"px");h&&(b.top=h+"px")}catch(f){}},update:function(){this.R.update()},addDefs:function(a){if(d.hasSVG){var b=this.createSvgElement("desc"),
c=this.container;c.setAttribute("version","1.1");c.style.position="absolute";this.setSize(this.width,this.height);if(a.accessibleTitle){var e=this.createSvgElement("text");c.appendChild(e);e.innerHTML=a.accessibleTitle;e.style.opacity=0}d.rtl&&(c.setAttribute("direction","rtl"),c.style.left="auto",c.style.right="0px");a&&(a.addCodeCredits&&b.appendChild(document.createTextNode("JavaScript chart by amCharts "+a.version)),c.appendChild(b),a.defs&&(b=this.createSvgElement("defs"),c.appendChild(b),d.parseDefs(a.defs,
b),this.defs=b))}}})})();(function(){var d=window.AmCharts;d.AmDObject=d.Class({construct:function(a,b){this.D=b;this.R=b.R;this.node=this.R.create(this,a);this.y=this.x=0;this.scale=1},attr:function(a){this.R.attr(this,a);return this},getAttr:function(a){return this.node.getAttribute(a)},setAttr:function(a,b){this.R.setAttr(this,a,b);return this},clipRect:function(a,b,c,d){this.R.clipRect(this,a,b,c,d)},translate:function(a,b,c,d){d||(a=Math.round(a),b=Math.round(b));this.R.move(this,a,b,c);this.x=a;this.y=b;this.scale=
c;this.angle&&this.rotate(this.angle)},rotate:function(a,b){this.R.rotate(this,a,b);this.angle=a},animate:function(a,b,c){for(var e in a)if(a.hasOwnProperty(e)){var h=e,f=a[e];c=d.getEffect(c);this.R.animate(this,h,f,b,c)}},push:function(a){if(a){var b=this.node;b.appendChild(a.node);var c=a.clipPath;c&&b.appendChild(c);(a=a.grad)&&b.appendChild(a)}},text:function(a){this.R.setText(this,a)},remove:function(){this.stop();this.R.remove(this)},clear:function(){var a=this.node;if(a.hasChildNodes())for(;1<=
a.childNodes.length;)a.removeChild(a.firstChild)},hide:function(){this.setAttr("visibility","hidden")},show:function(){this.setAttr("visibility","visible")},getBBox:function(){return this.R.getBBox(this)},toFront:function(){var a=this.node;if(a){this.prevNextNode=a.nextSibling;var b=a.parentNode;b&&b.appendChild(a)}},toPrevious:function(){var a=this.node;a&&this.prevNextNode&&(a=a.parentNode)&&a.insertBefore(this.prevNextNode,null)},toBack:function(){var a=this.node;if(a){this.prevNextNode=a.nextSibling;
var b=a.parentNode;if(b){var c=b.firstChild;c&&b.insertBefore(a,c)}}},mouseover:function(a){this.R.addListener(this,"mouseover",a);return this},mouseout:function(a){this.R.addListener(this,"mouseout",a);return this},click:function(a){this.R.addListener(this,"click",a);return this},dblclick:function(a){this.R.addListener(this,"dblclick",a);return this},mousedown:function(a){this.R.addListener(this,"mousedown",a);return this},mouseup:function(a){this.R.addListener(this,"mouseup",a);return this},touchmove:function(a){this.R.addListener(this,
"touchmove",a);return this},touchstart:function(a){this.R.addListener(this,"touchstart",a);return this},touchend:function(a){this.R.addListener(this,"touchend",a);return this},keyup:function(a){this.R.addListener(this,"keyup",a);return this},focus:function(a){this.R.addListener(this,"focus",a);return this},blur:function(a){this.R.addListener(this,"blur",a);return this},contextmenu:function(a){this.node.addEventListener?this.node.addEventListener("contextmenu",a,!0):this.R.addListener(this,"contextmenu",
a);return this},stop:function(){d.removeFromArray(this.R.animations,this.an_translate);d.removeFromArray(this.R.animations,this.an_y);d.removeFromArray(this.R.animations,this.an_x)},length:function(){return this.node.childNodes.length},gradient:function(a,b,c){this.R.gradient(this,a,b,c)},pattern:function(a,b,c){a&&this.R.pattern(this,a,b,c)}})})();(function(){var d=window.AmCharts;d.VMLRenderer=d.Class({construct:function(a,b){this.chart=b;this.D=a;this.cNames={circle:"oval",ellipse:"oval",rect:"roundrect",path:"shape"};this.styleMap={x:"left",y:"top",width:"width",height:"height","font-family":"fontFamily","font-size":"fontSize",visibility:"visibility"}},create:function(a,b){var c;if("group"==b)c=document.createElement("div"),a.type="div";else if("text"==b)c=document.createElement("div"),a.type="text";else if("image"==b)c=document.createElement("img"),
a.type="image";else{a.type="shape";a.shapeType=this.cNames[b];c=document.createElement("amvml:"+this.cNames[b]);var d=document.createElement("amvml:stroke");c.appendChild(d);a.stroke=d;var h=document.createElement("amvml:fill");c.appendChild(h);a.fill=h;h.className="amvml";d.className="amvml";c.className="amvml"}c.style.position="absolute";c.style.top=0;c.style.left=0;return c},path:function(a,b){a.node.setAttribute("src",b)},setAttr:function(a,b,c){if(void 0!==c){var e;8===document.documentMode&&
(e=!0);var h=a.node,f=a.type,g=h.style;"r"==b&&(g.width=2*c,g.height=2*c);"oval"==a.shapeType&&("rx"==b&&(g.width=2*c),"ry"==b&&(g.height=2*c));"roundrect"==a.shapeType&&("width"!=b&&"height"!=b||--c);"cursor"==b&&(g.cursor=c);"cx"==b&&(g.left=c-d.removePx(g.width)/2);"cy"==b&&(g.top=c-d.removePx(g.height)/2);var k=this.styleMap[b];"width"==k&&0>c&&(c=0);void 0!==k&&(g[k]=c);"text"==f&&("text-anchor"==b&&(a.anchor=c,k=h.clientWidth,"end"==c&&(g.marginLeft=-k+"px"),"middle"==c&&(g.marginLeft=-(k/2)+
"px",g.textAlign="center"),"start"==c&&(g.marginLeft="0px")),"fill"==b&&(g.color=c),"font-weight"==b&&(g.fontWeight=c));if(g=a.children)for(k=0;k<g.length;k++)g[k].setAttr(b,c);if("shape"==f){"cs"==b&&(h.style.width="100px",h.style.height="100px",h.setAttribute("coordsize",c));"d"==b&&h.setAttribute("path",this.svgPathToVml(c));"dd"==b&&h.setAttribute("path",c);f=a.stroke;a=a.fill;"stroke"==b&&(e?f.color=c:f.setAttribute("color",c));"stroke-width"==b&&(e?f.weight=c:f.setAttribute("weight",c));"stroke-opacity"==
b&&(e?f.opacity=c:f.setAttribute("opacity",c));"stroke-dasharray"==b&&(g="solid",0<c&&3>c&&(g="dot"),3<=c&&6>=c&&(g="dash"),6<c&&(g="longdash"),e?f.dashstyle=g:f.setAttribute("dashstyle",g));if("fill-opacity"==b||"opacity"==b)0===c?e?a.on=!1:a.setAttribute("on",!1):e?a.opacity=c:a.setAttribute("opacity",c);"fill"==b&&(e?a.color=c:a.setAttribute("color",c));"rx"==b&&(e?h.arcSize=c+"%":h.setAttribute("arcsize",c+"%"))}}},attr:function(a,b){for(var c in b)b.hasOwnProperty(c)&&this.setAttr(a,c,b[c])},
text:function(a,b,c){var e=new d.AmDObject("text",this.D),h=e.node;h.style.whiteSpace="pre";h.innerHTML=a;this.D.addToContainer(h,c);this.attr(e,b);return e},getBBox:function(a){return this.getBox(a.node)},getBox:function(a){var b=a.offsetLeft,c=a.offsetTop,d=a.offsetWidth,h=a.offsetHeight,f;if(a.hasChildNodes()){var g,k,l;for(l=0;l<a.childNodes.length;l++){f=this.getBox(a.childNodes[l]);var m=f.x;isNaN(m)||(isNaN(g)?g=m:m<g&&(g=m));var n=f.y;isNaN(n)||(isNaN(k)?k=n:n<k&&(k=n));m=f.width+m;isNaN(m)||
(d=Math.max(d,m));f=f.height+n;isNaN(f)||(h=Math.max(h,f))}0>g&&(b+=g);0>k&&(c+=k)}return{x:b,y:c,width:d,height:h}},setText:function(a,b){var c=a.node;c&&(c.innerHTML=b);this.setAttr(a,"text-anchor",a.anchor)},addListener:function(a,b,c){a.node["on"+b]=c},move:function(a,b,c){var e=a.node,h=e.style;"text"==a.type&&(c-=d.removePx(h.fontSize)/2-1);"oval"==a.shapeType&&(b-=d.removePx(h.width)/2,c-=d.removePx(h.height)/2);a=a.bw;isNaN(a)||(b-=a,c-=a);isNaN(b)||isNaN(c)||(e.style.left=b+"px",e.style.top=
c+"px")},svgPathToVml:function(a){var b=a.split(" ");a="";var c,d=Math.round,h;for(h=0;h<b.length;h++){var f=b[h],g=f.substring(0,1),f=f.substring(1),k=f.split(","),l=d(k[0])+","+d(k[1]);"M"==g&&(a+=" m "+l);"L"==g&&(a+=" l "+l);"Z"==g&&(a+=" x e");if("Q"==g){var m=c.length,n=c[m-1],q=k[0],p=k[1],l=k[2],t=k[3];c=d(c[m-2]/3+2/3*q);n=d(n/3+2/3*p);q=d(2/3*q+l/3);p=d(2/3*p+t/3);a+=" c "+c+","+n+","+q+","+p+","+l+","+t}"A"==g&&(a+=" wa "+f);"B"==g&&(a+=" at "+f);c=k}return a},animate:function(a,b,c,d,
h){var f=a.node,g=this.chart;a.animationFinished=!1;if("translate"==b){b=c.split(",");c=b[1];var k=f.offsetTop;g.animate(a,"left",f.offsetLeft,b[0],d,h,"px");g.animate(a,"top",k,c,d,h,"px")}},clipRect:function(a,b,c,d,h){a=a.node;0===b&&0===c?(a.style.width=d+"px",a.style.height=h+"px",a.style.overflow="hidden"):a.style.clip="rect("+c+"px "+(b+d)+"px "+(c+h)+"px "+b+"px)"},rotate:function(a,b,c){if(0!==Number(b)){var e=a.node;a=e.style;c||(c=this.getBGColor(e.parentNode));a.backgroundColor=c;a.paddingLeft=
1;c=b*Math.PI/180;var h=Math.cos(c),f=Math.sin(c),g=d.removePx(a.left),k=d.removePx(a.top),l=e.offsetWidth,e=e.offsetHeight;b/=Math.abs(b);a.left=g+l/2-l/2*Math.cos(c)-b*e/2*Math.sin(c)+3;a.top=k-b*l/2*Math.sin(c)+b*e/2*Math.sin(c);a.cssText=a.cssText+"; filter:progid:DXImageTransform.Microsoft.Matrix(M11='"+h+"', M12='"+-f+"', M21='"+f+"', M22='"+h+"', sizingmethod='auto expand');"}},getBGColor:function(a){var b="#FFFFFF";if(a.style){var c=a.style.backgroundColor;""!==c?b=c:a.parentNode&&(b=this.getBGColor(a.parentNode))}return b},
set:function(a){var b=new d.AmDObject("group",this.D);this.D.container.appendChild(b.node);if(a){var c;for(c=0;c<a.length;c++)b.push(a[c])}return b},gradient:function(a,b,c,d){var h="";"radialGradient"==b&&(b="gradientradial",c.reverse());"linearGradient"==b&&(b="gradient");var f;for(f=0;f<c.length;f++)h+=Math.round(100*f/(c.length-1))+"% "+c[f],f<c.length-1&&(h+=",");a=a.fill;90==d?d=0:270==d?d=180:180==d?d=90:0===d&&(d=270);8===document.documentMode?(a.type=b,a.angle=d):(a.setAttribute("type",b),
a.setAttribute("angle",d));h&&(a.colors.value=h)},remove:function(a){a.clipPath&&this.D.remove(a.clipPath);this.D.remove(a.node)},disableSelection:function(a){a.onselectstart=function(){return!1};a.style.cursor="default"},pattern:function(a,b,c,e){c=a.node;a=a.fill;var h="none";b.color&&(h=b.color);c.fillColor=h;b=b.url;d.isAbsolute(b)||(b=e+b);8===document.documentMode?(a.type="tile",a.src=b):(a.setAttribute("type","tile"),a.setAttribute("src",b))},update:function(){}})})();(function(){var d=window.AmCharts;d.SVGRenderer=d.Class({construct:function(a){this.D=a;this.animations=[]},create:function(a,b){return document.createElementNS(d.SVG_NS,b)},attr:function(a,b){for(var c in b)b.hasOwnProperty(c)&&this.setAttr(a,c,b[c])},setAttr:function(a,b,c){void 0!==c&&a.node.setAttribute(b,c)},animate:function(a,b,c,e,h){a.animationFinished=!1;var f=a.node;a["an_"+b]&&d.removeFromArray(this.animations,a["an_"+b]);"translate"==b?(f=(f=f.getAttribute("transform"))?String(f).substring(10,
f.length-1):"0,0",f=f.split(", ").join(" "),f=f.split(" ").join(","),0===f&&(f="0,0")):f=Number(f.getAttribute(b));c={obj:a,frame:0,attribute:b,from:f,to:c,time:e,effect:h};this.animations.push(c);a["an_"+b]=c},update:function(){var a,b=this.animations;for(a=b.length-1;0<=a;a--){var c=b[a],e=c.time*d.updateRate,h=c.frame+1,f=c.obj,g=c.attribute,k,l,m;if(h<=e){c.frame++;if("translate"==g){k=c.from.split(",");g=Number(k[0]);k=Number(k[1]);isNaN(k)&&(k=0);l=c.to.split(",");m=Number(l[0]);l=Number(l[1]);
m=0===m-g?m:Math.round(d[c.effect](0,h,g,m-g,e));c=0===l-k?l:Math.round(d[c.effect](0,h,k,l-k,e));g="transform";if(isNaN(m)||isNaN(c))continue;c="translate("+m+","+c+")"}else l=Number(c.from),k=Number(c.to),m=k-l,c=d[c.effect](0,h,l,m,e),isNaN(c)&&(c=k),0===m&&this.animations.splice(a,1);this.setAttr(f,g,c)}else"translate"==g?(l=c.to.split(","),m=Number(l[0]),l=Number(l[1]),f.translate(m,l)):(k=Number(c.to),this.setAttr(f,g,k)),f.animationFinished=!0,this.animations.splice(a,1)}},getBBox:function(a){if(a=
a.node)try{return a.getBBox()}catch(b){}return{width:0,height:0,x:0,y:0}},path:function(a,b){a.node.setAttributeNS(d.SVG_XLINK,"xlink:href",b)},clipRect:function(a,b,c,e,h){var f=a.node,g=a.clipPath;g&&this.D.remove(g);var k=f.parentNode;k&&(f=document.createElementNS(d.SVG_NS,"clipPath"),g=d.getUniqueId(),f.setAttribute("id",g),this.D.rect(b,c,e,h,0,0,f),k.appendChild(f),b="#",d.baseHref&&!d.isIE&&(b=this.removeTarget(window.location.href)+b),this.setAttr(a,"clip-path","url("+b+g+")"),this.clipPathC++,
a.clipPath=f)},text:function(a,b,c){var e=new d.AmDObject("text",this.D);a=String(a).split("\n");var h=d.removePx(b["font-size"]),f;for(f=0;f<a.length;f++){var g=this.create(null,"tspan");g.appendChild(document.createTextNode(a[f]));g.setAttribute("y",(h+2)*f+Math.round(h/2));g.setAttribute("x",0);e.node.appendChild(g)}e.node.setAttribute("y",Math.round(h/2));this.attr(e,b);this.D.addToContainer(e.node,c);return e},setText:function(a,b){var c=a.node;c&&(c.removeChild(c.firstChild),c.appendChild(document.createTextNode(b)))},
move:function(a,b,c,d){isNaN(b)&&(b=0);isNaN(c)&&(c=0);b="translate("+b+","+c+")";d&&(b=b+" scale("+d+")");this.setAttr(a,"transform",b)},rotate:function(a,b){var c=a.node.getAttribute("transform"),d="rotate("+b+")";c&&(d=c+" "+d);this.setAttr(a,"transform",d)},set:function(a){var b=new d.AmDObject("g",this.D);this.D.container.appendChild(b.node);if(a){var c;for(c=0;c<a.length;c++)b.push(a[c])}return b},addListener:function(a,b,c){a.node["on"+b]=c},gradient:function(a,b,c,e){var h=a.node,f=a.grad;
f&&this.D.remove(f);b=document.createElementNS(d.SVG_NS,b);f=d.getUniqueId();b.setAttribute("id",f);if(!isNaN(e)){var g=0,k=0,l=0,m=0;90==e?l=100:270==e?m=100:180==e?g=100:0===e&&(k=100);b.setAttribute("x1",g+"%");b.setAttribute("x2",k+"%");b.setAttribute("y1",l+"%");b.setAttribute("y2",m+"%")}for(e=0;e<c.length;e++)g=document.createElementNS(d.SVG_NS,"stop"),k=100*e/(c.length-1),0===e&&(k=0),g.setAttribute("offset",k+"%"),g.setAttribute("stop-color",c[e]),b.appendChild(g);h.parentNode.appendChild(b);
c="#";d.baseHref&&!d.isIE&&(c=this.removeTarget(window.location.href)+c);h.setAttribute("fill","url("+c+f+")");a.grad=b},removeTarget:function(a){return a.split("#")[0]},pattern:function(a,b,c,e){var h=a.node;isNaN(c)&&(c=1);var f=a.patternNode;f&&this.D.remove(f);var f=document.createElementNS(d.SVG_NS,"pattern"),g=d.getUniqueId(),k=b;b.url&&(k=b.url);d.isAbsolute(k)||-1!=k.indexOf("data:image")||(k=e+k);e=Number(b.width);isNaN(e)&&(e=4);var l=Number(b.height);isNaN(l)&&(l=4);e/=c;l/=c;c=b.x;isNaN(c)&&
(c=0);var m=-Math.random()*Number(b.randomX);isNaN(m)||(c=m);m=b.y;isNaN(m)&&(m=0);var n=-Math.random()*Number(b.randomY);isNaN(n)||(m=n);f.setAttribute("id",g);f.setAttribute("width",e);f.setAttribute("height",l);f.setAttribute("patternUnits","userSpaceOnUse");f.setAttribute("xlink:href",k);b.color&&(n=document.createElementNS(d.SVG_NS,"rect"),n.setAttributeNS(null,"height",e),n.setAttributeNS(null,"width",l),n.setAttributeNS(null,"fill",b.color),f.appendChild(n));this.D.image(k,0,0,e,l,f).translate(c,
m);k="#";d.baseHref&&!d.isIE&&(k=this.removeTarget(window.location.href)+k);h.setAttribute("fill","url("+k+g+")");a.patternNode=f;h.parentNode.appendChild(f)},remove:function(a){a.clipPath&&this.D.remove(a.clipPath);a.grad&&this.D.remove(a.grad);a.patternNode&&this.D.remove(a.patternNode);this.D.remove(a.node)}})})();(function(){var d=window.AmCharts;d.AmLegend=d.Class({construct:function(a){this.enabled=!0;this.cname="AmLegend";this.createEvents("rollOverMarker","rollOverItem","rollOutMarker","rollOutItem","showItem","hideItem","clickMarker","clickLabel");this.position="bottom";this.borderColor=this.color="#000000";this.borderAlpha=0;this.markerLabelGap=5;this.verticalGap=10;this.align="left";this.horizontalGap=0;this.spacing=10;this.markerDisabledColor="#AAB3B3";this.markerType="square";this.markerSize=16;this.markerBorderThickness=
this.markerBorderAlpha=1;this.marginBottom=this.marginTop=0;this.marginLeft=this.marginRight=20;this.autoMargins=!0;this.valueWidth=50;this.switchable=!0;this.switchType="x";this.switchColor="#FFFFFF";this.rollOverColor="#CC0000";this.reversedOrder=!1;this.labelText="[[title]]";this.valueText="[[value]]";this.accessibleLabel="[[title]]";this.useMarkerColorForLabels=!1;this.rollOverGraphAlpha=1;this.textClickEnabled=!1;this.equalWidths=!0;this.backgroundColor="#FFFFFF";this.backgroundAlpha=0;this.useGraphSettings=
!1;this.showEntries=!0;this.labelDx=0;d.applyTheme(this,a,this.cname)},setData:function(a){this.legendData=a;this.invalidateSize()},invalidateSize:function(){this.destroy();this.entries=[];this.valueLabels=[];var a=this.legendData;this.enabled&&(d.ifArray(a)||d.ifArray(this.data))&&this.drawLegend()},drawLegend:function(){var a=this.chart,b=this.position,c=this.width,e=a.divRealWidth,h=a.divRealHeight,f=this.div,g=this.legendData;this.data&&(g=this.combineLegend?this.legendData.concat(this.data):
this.data);isNaN(this.fontSize)&&(this.fontSize=a.fontSize);this.maxColumnsReal=this.maxColumns;if("right"==b||"left"==b)this.maxColumnsReal=1,this.autoMargins&&(this.marginLeft=this.marginRight=10);else if(this.autoMargins){this.marginRight=a.marginRight;this.marginLeft=a.marginLeft;var k=a.autoMarginOffset;"bottom"==b?(this.marginBottom=k,this.marginTop=0):(this.marginTop=k,this.marginBottom=0)}c=void 0!==c?d.toCoordinate(c,e):"right"!=b&&"left"!=b?a.realWidth:0<this.ieW?this.ieW:a.realWidth;"outside"==
b?(c=f.offsetWidth,h=f.offsetHeight,f.clientHeight&&(c=f.clientWidth,h=f.clientHeight)):(isNaN(c)||(f.style.width=c+"px"),f.className="amChartsLegend "+a.classNamePrefix+"-legend-div");this.divWidth=c;(b=this.container)?(b.container.innerHTML="",f.appendChild(b.container),b.width=c,b.height=h,b.setSize(c,h),b.addDefs(a)):b=new d.AmDraw(f,c,h,a);this.container=b;this.lx=0;this.ly=8;h=this.markerSize;h>this.fontSize&&(this.ly=h/2-1);0<h&&(this.lx+=h+this.markerLabelGap);this.titleWidth=0;if(h=this.title)h=
d.text(this.container,h,this.color,a.fontFamily,this.fontSize,"start",!0),d.setCN(a,h,"legend-title"),h.translate(this.marginLeft,this.marginTop+this.verticalGap+this.ly+1),a=h.getBBox(),this.titleWidth=a.width+15,this.titleHeight=a.height+6;this.index=this.maxLabelWidth=0;if(this.showEntries){for(a=0;a<g.length;a++)this.createEntry(g[a]);for(a=this.index=0;a<g.length;a++)this.createValue(g[a])}this.arrangeEntries();this.updateValues()},arrangeEntries:function(){var a=this.position,b=this.marginLeft+
this.titleWidth,c=this.marginRight,e=this.marginTop,h=this.marginBottom,f=this.horizontalGap,g=this.div,k=this.divWidth,l=this.maxColumnsReal,m=this.verticalGap,n=this.spacing,q=k-c-b,p=0,t=0,r=this.container;this.set&&this.set.remove();var w=r.set();this.set=w;var y=r.set();w.push(y);var x=this.entries,u,A;for(A=0;A<x.length;A++){u=x[A].getBBox();var z=u.width;z>p&&(p=z);u=u.height;u>t&&(t=u)}var z=t=0,B=f,D=0,C=0;for(A=0;A<x.length;A++){var K=x[A];this.reversedOrder&&(K=x[x.length-A-1]);u=K.getBBox();
var H;this.equalWidths?H=z*(p+n+this.markerLabelGap):(H=B,B=B+u.width+f+n);H+u.width>q&&0<A&&0!==z&&(t++,H=z=0,B=H+u.width+f+n,D=D+C+m,C=0);u.height>C&&(C=u.height);K.translate(H,D);z++;!isNaN(l)&&z>=l&&(z=0,t++,D=D+C+m,B=f,C=0);y.push(K)}u=y.getBBox();l=u.height+2*m-1;"left"==a||"right"==a?(n=u.width+2*f,k=n+b+c,g.style.width=k+"px",this.ieW=k):n=k-b-c-1;c=d.polygon(this.container,[0,n,n,0],[0,0,l,l],this.backgroundColor,this.backgroundAlpha,1,this.borderColor,this.borderAlpha);d.setCN(this.chart,
c,"legend-bg");w.push(c);w.translate(b,e);c.toBack();b=f;if("top"==a||"bottom"==a||"absolute"==a||"outside"==a)"center"==this.align?b=f+(n-u.width)/2:"right"==this.align&&(b=f+n-u.width);y.translate(b,m+1);this.titleHeight>l&&(l=this.titleHeight);e=l+e+h+1;0>e&&(e=0);"absolute"!=a&&"outside"!=a&&e>this.chart.divRealHeight&&(g.style.top="0px");g.style.height=Math.round(e)+"px";r.setSize(this.divWidth,e)},createEntry:function(a){if(!1!==a.visibleInLegend&&!a.hideFromLegend){var b=this,c=b.chart,e=b.useGraphSettings,
h=a.markerType;h&&(e=!1);a.legendEntryWidth=b.markerSize;h||(h=b.markerType);var f=a.color,g=a.alpha;a.legendKeyColor&&(f=a.legendKeyColor());a.legendKeyAlpha&&(g=a.legendKeyAlpha());var k;!0===a.hidden&&(k=f=b.markerDisabledColor);var l=a.pattern,m,n=a.customMarker;n||(n=b.customMarker);var q=b.container,p=b.markerSize,t=0,r=0,w=p/2;if(e){e=a.type;b.switchType=void 0;if("line"==e||"step"==e||"smoothedLine"==e||"ohlc"==e)m=q.set(),a.hidden||(f=a.lineColorR,k=a.bulletBorderColorR),t=d.line(q,[0,2*
p],[p/2,p/2],f,a.lineAlpha,a.lineThickness,a.dashLength),d.setCN(c,t,"graph-stroke"),m.push(t),a.bullet&&(a.hidden||(f=a.bulletColorR),t=d.bullet(q,a.bullet,a.bulletSize,f,a.bulletAlpha,a.bulletBorderThickness,k,a.bulletBorderAlpha))&&(d.setCN(c,t,"graph-bullet"),t.translate(p+1,p/2),m.push(t)),w=0,t=p,r=p/3;else{a.getGradRotation&&(m=a.getGradRotation(),0===m&&(m=180));t=a.fillColorsR;!0===a.hidden&&(t=f);if(m=b.createMarker("rectangle",t,a.fillAlphas,a.lineThickness,f,a.lineAlpha,m,l,a.dashLength))w=
p,m.translate(w,p/2);t=p}d.setCN(c,m,"graph-"+e);d.setCN(c,m,"graph-"+a.id)}else if(n)m=q.image(n,0,0,p,p);else{var y;isNaN(b.gradientRotation)||(y=180+b.gradientRotation);(m=b.createMarker(h,f,g,void 0,void 0,void 0,y,l))&&m.translate(p/2,p/2)}d.setCN(c,m,"legend-marker");b.addListeners(m,a);q=q.set([m]);b.switchable&&a.switchable&&q.setAttr("cursor","pointer");void 0!==a.id&&d.setCN(c,q,"legend-item-"+a.id);d.setCN(c,q,a.className,!0);k=b.switchType;var x;k&&"none"!=k&&0<p&&("x"==k?(x=b.createX(),
x.translate(p/2,p/2)):x=b.createV(),x.dItem=a,!0!==a.hidden?"x"==k?x.hide():x.show():"x"!=k&&x.hide(),b.switchable||x.hide(),b.addListeners(x,a),a.legendSwitch=x,q.push(x),d.setCN(c,x,"legend-switch"));k=b.color;a.showBalloon&&b.textClickEnabled&&void 0!==b.selectedColor&&(k=b.selectedColor);b.useMarkerColorForLabels&&!l&&(k=f);!0===a.hidden&&(k=b.markerDisabledColor);f=d.massReplace(b.labelText,{"[[title]]":a.title});void 0!==b.tabIndex&&(q.setAttr("tabindex",b.tabIndex),q.setAttr("role","menuitem"),
q.keyup(function(c){13==c.keyCode&&b.clickMarker(a,c)}));c.accessible&&b.accessibleLabel&&(l=d.massReplace(b.accessibleLabel,{"[[title]]":a.title}),c.makeAccessible(q,l));l=b.fontSize;m&&(p<=l&&(p=p/2+b.ly-l/2+(l+2-p)/2-r,m.translate(w,p),x&&x.translate(x.x,p)),a.legendEntryWidth=m.getBBox().width);var u;f&&(f=d.fixBrakes(f),a.legendTextReal=f,u=b.labelWidth,u=isNaN(u)?d.text(b.container,f,k,c.fontFamily,l,"start"):d.wrappedText(b.container,f,k,c.fontFamily,l,"start",!1,u,0),d.setCN(c,u,"legend-label"),
u.translate(b.lx+t,b.ly),q.push(u),b.labelDx=t,c=u.getBBox().width,b.maxLabelWidth<c&&(b.maxLabelWidth=c));b.entries[b.index]=q;a.legendEntry=b.entries[b.index];a.legendMarker=m;a.legendLabel=u;b.index++}},addListeners:function(a,b){var c=this;a&&a.mouseover(function(a){c.rollOverMarker(b,a)}).mouseout(function(a){c.rollOutMarker(b,a)}).click(function(a){c.clickMarker(b,a)})},rollOverMarker:function(a,b){this.switchable&&this.dispatch("rollOverMarker",a,b);this.dispatch("rollOverItem",a,b)},rollOutMarker:function(a,
b){this.switchable&&this.dispatch("rollOutMarker",a,b);this.dispatch("rollOutItem",a,b)},clickMarker:function(a,b){this.switchable&&(!0===a.hidden?this.dispatch("showItem",a,b):this.dispatch("hideItem",a,b));this.dispatch("clickMarker",a,b)},rollOverLabel:function(a,b){a.hidden||this.textClickEnabled&&a.legendLabel&&a.legendLabel.attr({fill:this.rollOverColor});this.dispatch("rollOverItem",a,b)},rollOutLabel:function(a,b){if(!a.hidden&&this.textClickEnabled&&a.legendLabel){var c=this.color;void 0!==
this.selectedColor&&a.showBalloon&&(c=this.selectedColor);this.useMarkerColorForLabels&&(c=a.lineColor,void 0===c&&(c=a.color));a.legendLabel.attr({fill:c})}this.dispatch("rollOutItem",a,b)},clickLabel:function(a,b){this.textClickEnabled?a.hidden||this.dispatch("clickLabel",a,b):this.switchable&&(!0===a.hidden?this.dispatch("showItem",a,b):this.dispatch("hideItem",a,b))},dispatch:function(a,b,c){a={type:a,dataItem:b,target:this,event:c,chart:this.chart};this.chart&&this.chart.handleLegendEvent(a);
this.fire(a)},createValue:function(a){var b=this,c=b.fontSize,e=b.chart;if(!1!==a.visibleInLegend&&!a.hideFromLegend){var h=b.maxLabelWidth;b.forceWidth&&(h=b.labelWidth);b.equalWidths||(b.valueAlign="left");"left"==b.valueAlign&&a.legendLabel&&(h=a.legendLabel.getBBox().width);var f=h;if(b.valueText&&0<b.valueWidth){var g=b.color;b.useMarkerColorForValues&&(g=a.color,a.legendKeyColor&&(g=a.legendKeyColor()));!0===a.hidden&&(g=b.markerDisabledColor);var k=b.valueText,h=h+b.lx+b.labelDx+b.markerLabelGap+
b.valueWidth,l="end";"left"==b.valueAlign&&(h-=b.valueWidth,l="start");g=d.text(b.container,k,g,b.chart.fontFamily,c,l);d.setCN(e,g,"legend-value");g.translate(h,b.ly);b.entries[b.index].push(g);f+=b.valueWidth+2*b.markerLabelGap;g.dItem=a;b.valueLabels.push(g)}b.index++;e=b.markerSize;e<c+7&&(e=c+7,d.VML&&(e+=3));c=b.container.rect(a.legendEntryWidth,0,f,e,0,0).attr({stroke:"none",fill:"#fff","fill-opacity":.005});c.dItem=a;b.entries[b.index-1].push(c);c.mouseover(function(c){b.rollOverLabel(a,c)}).mouseout(function(c){b.rollOutLabel(a,
c)}).click(function(c){b.clickLabel(a,c)})}},createV:function(){var a=this.markerSize;return d.polygon(this.container,[a/5,a/2,a-a/5,a/2],[a/3,a-a/5,a/5,a/1.7],this.switchColor)},createX:function(){var a=(this.markerSize-4)/2,b={stroke:this.switchColor,"stroke-width":3},c=this.container,e=d.line(c,[-a,a],[-a,a]).attr(b),a=d.line(c,[-a,a],[a,-a]).attr(b);return this.container.set([e,a])},createMarker:function(a,b,c,e,h,f,g,k,l){var m=this.markerSize,n=this.container;h||(h=this.markerBorderColor);h||
(h=b);isNaN(e)&&(e=this.markerBorderThickness);isNaN(f)&&(f=this.markerBorderAlpha);return d.bullet(n,a,m,b,c,e,h,f,m,g,k,this.chart.path,l)},validateNow:function(){this.invalidateSize()},updateValues:function(){var a=this.valueLabels,b=this.chart,c,e=this.data;if(a)for(c=0;c<a.length;c++){var h=a[c],f=h.dItem;f.periodDataItem=void 0;f.periodPercentDataItem=void 0;var g=" ";if(e)f.value?h.text(f.value):h.text("");else{var k=null;if(void 0!==f.type){var k=f.currentDataItem,l=this.periodValueText;f.legendPeriodValueText&&
(l=f.legendPeriodValueText);k?(g=this.valueText,f.legendValueText&&(g=f.legendValueText),g=b.formatString(g,k)):l&&b.formatPeriodString&&(l=d.massReplace(l,{"[[title]]":f.title}),g=b.formatPeriodString(l,f))}else g=b.formatString(this.valueText,f);l=f;k&&(l=k);var m=this.valueFunction;m&&(g=m(l,g,b.periodDataItem));var n;this.useMarkerColorForLabels&&!k&&f.lastDataItem&&(k=f.lastDataItem);k?n=b.getBalloonColor(f,k):f.legendKeyColor&&(n=f.legendKeyColor());f.legendColorFunction&&(n=f.legendColorFunction(l,
g,f.periodDataItem,f.periodPercentDataItem));h.text(g);if(!f.pattern&&(this.useMarkerColorForValues&&h.setAttr("fill",n),this.useMarkerColorForLabels)){if(h=f.legendMarker)h.setAttr("fill",n),h.setAttr("stroke",n);(f=f.legendLabel)&&f.setAttr("fill",n)}}}},renderFix:function(){if(!d.VML&&this.enabled){var a=this.container;a&&a.renderFix()}},destroy:function(){this.div.innerHTML="";d.remove(this.set)}})})();(function(){var d=window.AmCharts;d.formatMilliseconds=function(a,b){if(-1!=a.indexOf("fff")){var c=b.getMilliseconds(),d=String(c);10>c&&(d="00"+c);10<=c&&100>c&&(d="0"+c);a=a.replace(/fff/g,d)}return a};d.extractPeriod=function(a){var b=d.stripNumbers(a),c=1;b!=a&&(c=Number(a.slice(0,a.indexOf(b))));return{period:b,count:c}};d.getDate=function(a,b,c){return a instanceof Date?d.newDate(a,c):b&&isNaN(a)?d.stringToDate(a,b):new Date(a)};d.daysInMonth=function(a){return(new Date(a.getYear(),a.getMonth()+
1,0)).getDate()};d.newDate=function(a,b){return b&&-1==b.indexOf("fff")?new Date(a):new Date(a.getFullYear(),a.getMonth(),a.getDate(),a.getHours(),a.getMinutes(),a.getSeconds(),a.getMilliseconds())};d.resetDateToMin=function(a,b,c,e){void 0===e&&(e=1);var h,f,g,k,l,m,n;d.useUTC?(h=a.getUTCFullYear(),f=a.getUTCMonth(),g=a.getUTCDate(),k=a.getUTCHours(),l=a.getUTCMinutes(),m=a.getUTCSeconds(),n=a.getUTCMilliseconds(),a=a.getUTCDay()):(h=a.getFullYear(),f=a.getMonth(),g=a.getDate(),k=a.getHours(),l=
a.getMinutes(),m=a.getSeconds(),n=a.getMilliseconds(),a=a.getDay());switch(b){case "YYYY":h=Math.floor(h/c)*c;f=0;g=1;n=m=l=k=0;break;case "MM":f=Math.floor(f/c)*c;g=1;n=m=l=k=0;break;case "WW":g=a>=e?g-a+e:g-(7+a)+e;n=m=l=k=0;break;case "DD":n=m=l=k=0;break;case "hh":k=Math.floor(k/c)*c;n=m=l=0;break;case "mm":l=Math.floor(l/c)*c;n=m=0;break;case "ss":m=Math.floor(m/c)*c;n=0;break;case "fff":n=Math.floor(n/c)*c}d.useUTC?(a=new Date,a.setUTCFullYear(h,f,g),a.setUTCHours(k,l,m,n)):a=new Date(h,f,g,
k,l,m,n);return a};d.getPeriodDuration=function(a,b){void 0===b&&(b=1);var c;switch(a){case "YYYY":c=316224E5;break;case "MM":c=26784E5;break;case "WW":c=6048E5;break;case "DD":c=864E5;break;case "hh":c=36E5;break;case "mm":c=6E4;break;case "ss":c=1E3;break;case "fff":c=1}return c*b};d.intervals={s:{nextInterval:"ss",contains:1E3},ss:{nextInterval:"mm",contains:60,count:0},mm:{nextInterval:"hh",contains:60,count:1},hh:{nextInterval:"DD",contains:24,count:2},DD:{nextInterval:"",contains:Infinity,count:3}};
d.getMaxInterval=function(a,b){var c=d.intervals;return a>=c[b].contains?(a=Math.round(a/c[b].contains),b=c[b].nextInterval,d.getMaxInterval(a,b)):"ss"==b?c[b].nextInterval:b};d.dayNames="Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" ");d.shortDayNames="Sun Mon Tue Wed Thu Fri Sat".split(" ");d.monthNames="January February March April May June July August September October November December".split(" ");d.shortMonthNames="Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(" ");
d.getWeekNumber=function(a){a=new Date(a);a.setHours(0,0,0);a.setDate(a.getDate()+4-(a.getDay()||7));var b=new Date(a.getFullYear(),0,1);return Math.ceil(((a-b)/864E5+1)/7)};d.stringToDate=function(a,b){var c={},e=[{pattern:"YYYY",period:"year"},{pattern:"YY",period:"year"},{pattern:"MM",period:"month"},{pattern:"M",period:"month"},{pattern:"DD",period:"date"},{pattern:"D",period:"date"},{pattern:"JJ",period:"hours"},{pattern:"J",period:"hours"},{pattern:"HH",period:"hours"},{pattern:"H",period:"hours"},
{pattern:"KK",period:"hours"},{pattern:"K",period:"hours"},{pattern:"LL",period:"hours"},{pattern:"L",period:"hours"},{pattern:"NN",period:"minutes"},{pattern:"N",period:"minutes"},{pattern:"SS",period:"seconds"},{pattern:"S",period:"seconds"},{pattern:"QQQ",period:"milliseconds"},{pattern:"QQ",period:"milliseconds"},{pattern:"Q",period:"milliseconds"}],h=!0,f=b.indexOf("AA");-1!=f&&(a.substr(f,2),"pm"==a.toLowerCase&&(h=!1));var f=b,g,k,l;for(l=0;l<e.length;l++)k=e[l].period,c[k]=0,"date"==k&&(c[k]=
1);for(l=0;l<e.length;l++)if(g=e[l].pattern,k=e[l].period,-1!=b.indexOf(g)){var m=d.getFromDateString(g,a,f);b=b.replace(g,"");if("KK"==g||"K"==g||"LL"==g||"L"==g)h||(m+=12);c[k]=m}d.useUTC?(e=new Date,e.setUTCFullYear(c.year,c.month,c.date),e.setUTCHours(c.hours,c.minutes,c.seconds,c.milliseconds)):e=new Date(c.year,c.month,c.date,c.hours,c.minutes,c.seconds,c.milliseconds);return e};d.getFromDateString=function(a,b,c){if(void 0!==b)return c=c.indexOf(a),b=String(b),b=b.substr(c,a.length),"0"==b.charAt(0)&&
(b=b.substr(1,b.length-1)),b=Number(b),isNaN(b)&&(b=0),-1!=a.indexOf("M")&&b--,b};d.formatDate=function(a,b,c){c||(c=d);var e,h,f,g,k,l,m,n,q=d.getWeekNumber(a);d.useUTC?(e=a.getUTCFullYear(),h=a.getUTCMonth(),f=a.getUTCDate(),g=a.getUTCDay(),k=a.getUTCHours(),l=a.getUTCMinutes(),m=a.getUTCSeconds(),n=a.getUTCMilliseconds()):(e=a.getFullYear(),h=a.getMonth(),f=a.getDate(),g=a.getDay(),k=a.getHours(),l=a.getMinutes(),m=a.getSeconds(),n=a.getMilliseconds());var p=String(e).substr(2,2),t="0"+g;b=b.replace(/W/g,
q);q=k;24==q&&(q=0);var r=q;10>r&&(r="0"+r);b=b.replace(/JJ/g,r);b=b.replace(/J/g,q);r=k;0===r&&(r=24,-1!=b.indexOf("H")&&(f--,0===f&&(e=new Date(a),e.setDate(e.getDate()-1),h=e.getMonth(),f=e.getDate(),e=e.getFullYear())));a=h+1;9>h&&(a="0"+a);q=f;10>f&&(q="0"+f);var w=r;10>w&&(w="0"+w);b=b.replace(/HH/g,w);b=b.replace(/H/g,r);r=k;11<r&&(r-=12);w=r;10>w&&(w="0"+w);b=b.replace(/KK/g,w);b=b.replace(/K/g,r);r=k;0===r&&(r=12);12<r&&(r-=12);w=r;10>w&&(w="0"+w);b=b.replace(/LL/g,w);b=b.replace(/L/g,r);
r=l;10>r&&(r="0"+r);b=b.replace(/NN/g,r);b=b.replace(/N/g,l);l=m;10>l&&(l="0"+l);b=b.replace(/SS/g,l);b=b.replace(/S/g,m);m=n;10>m?m="00"+m:100>m&&(m="0"+m);l=n;10>l&&(l="00"+l);b=b.replace(/A/g,"@A@");b=b.replace(/QQQ/g,m);b=b.replace(/QQ/g,l);b=b.replace(/Q/g,n);b=b.replace(/YYYY/g,"@IIII@");b=b.replace(/YY/g,"@II@");b=b.replace(/MMMM/g,"@XXXX@");b=b.replace(/MMM/g,"@XXX@");b=b.replace(/MM/g,"@XX@");b=b.replace(/M/g,"@X@");b=b.replace(/DD/g,"@RR@");b=b.replace(/D/g,"@R@");b=b.replace(/EEEE/g,"@PPPP@");
b=b.replace(/EEE/g,"@PPP@");b=b.replace(/EE/g,"@PP@");b=b.replace(/E/g,"@P@");b=b.replace(/@IIII@/g,e);b=b.replace(/@II@/g,p);b=b.replace(/@XXXX@/g,c.monthNames[h]);b=b.replace(/@XXX@/g,c.shortMonthNames[h]);b=b.replace(/@XX@/g,a);b=b.replace(/@X@/g,h+1);b=b.replace(/@RR@/g,q);b=b.replace(/@R@/g,f);b=b.replace(/@PPPP@/g,c.dayNames[g]);b=b.replace(/@PPP@/g,c.shortDayNames[g]);b=b.replace(/@PP@/g,t);b=b.replace(/@P@/g,g);return b=12>k?b.replace(/@A@/g,c.amString):b.replace(/@A@/g,c.pmString)};d.changeDate=
function(a,b,c,e,h){if(d.useUTC)return d.changeUTCDate(a,b,c,e,h);var f=-1;void 0===e&&(e=!0);void 0===h&&(h=!1);!0===e&&(f=1);switch(b){case "YYYY":a.setFullYear(a.getFullYear()+c*f);e||h||a.setDate(a.getDate()+1);break;case "MM":b=a.getMonth();a.setMonth(a.getMonth()+c*f);a.getMonth()>b+c*f&&a.setDate(a.getDate()-1);e||h||a.setDate(a.getDate()+1);break;case "DD":a.setDate(a.getDate()+c*f);break;case "WW":a.setDate(a.getDate()+c*f*7);break;case "hh":a.setHours(a.getHours()+c*f);break;case "mm":a.setMinutes(a.getMinutes()+
c*f);break;case "ss":a.setSeconds(a.getSeconds()+c*f);break;case "fff":a.setMilliseconds(a.getMilliseconds()+c*f)}return a};d.changeUTCDate=function(a,b,c,d,h){var f=-1;void 0===d&&(d=!0);void 0===h&&(h=!1);!0===d&&(f=1);switch(b){case "YYYY":a.setUTCFullYear(a.getUTCFullYear()+c*f);d||h||a.setUTCDate(a.getUTCDate()+1);break;case "MM":b=a.getUTCMonth();a.setUTCMonth(a.getUTCMonth()+c*f);a.getUTCMonth()>b+c*f&&a.setUTCDate(a.getUTCDate()-1);d||h||a.setUTCDate(a.getUTCDate()+1);break;case "DD":a.setUTCDate(a.getUTCDate()+
c*f);break;case "WW":a.setUTCDate(a.getUTCDate()+c*f*7);break;case "hh":a.setUTCHours(a.getUTCHours()+c*f);break;case "mm":a.setUTCMinutes(a.getUTCMinutes()+c*f);break;case "ss":a.setUTCSeconds(a.getUTCSeconds()+c*f);break;case "fff":a.setUTCMilliseconds(a.getUTCMilliseconds()+c*f)}return a}})();

// serial.js
(function(){var f=window.AmCharts;f.AmRectangularChart=f.Class({inherits:f.AmCoordinateChart,construct:function(a){f.AmRectangularChart.base.construct.call(this,a);this.theme=a;this.createEvents("zoomed","changed");this.marginRight=this.marginBottom=this.marginTop=this.marginLeft=20;this.depth3D=this.angle=0;this.plotAreaFillColors="#FFFFFF";this.plotAreaFillAlphas=0;this.plotAreaBorderColor="#000000";this.plotAreaBorderAlpha=0;this.maxZoomFactor=20;this.zoomOutButtonImageSize=19;this.zoomOutButtonImage=
"lens";this.zoomOutText="Show all";this.zoomOutButtonColor="#e5e5e5";this.zoomOutButtonAlpha=0;this.zoomOutButtonRollOverAlpha=1;this.zoomOutButtonPadding=8;this.trendLines=[];this.autoMargins=!0;this.marginsUpdated=!1;this.autoMarginOffset=10;f.applyTheme(this,a,"AmRectangularChart")},initChart:function(){f.AmRectangularChart.base.initChart.call(this);this.updateDxy();!this.marginsUpdated&&this.autoMargins&&(this.resetMargins(),this.drawGraphs=!1);this.processScrollbars();this.updateMargins();this.updatePlotArea();
this.updateScrollbars();this.updateTrendLines();this.updateChartCursor();this.updateValueAxes();this.scrollbarOnly||this.updateGraphs()},drawChart:function(){f.AmRectangularChart.base.drawChart.call(this);this.drawPlotArea();if(f.ifArray(this.chartData)){var a=this.chartCursor;a&&a.draw()}},resetMargins:function(){var a={},b;if("xy"==this.type){var c=this.xAxes,d=this.yAxes;for(b=0;b<c.length;b++){var g=c[b];g.ignoreAxisWidth||(g.setOrientation(!0),g.fixAxisPosition(),a[g.position]=!0)}for(b=0;b<
d.length;b++)c=d[b],c.ignoreAxisWidth||(c.setOrientation(!1),c.fixAxisPosition(),a[c.position]=!0)}else{d=this.valueAxes;for(b=0;b<d.length;b++)c=d[b],c.ignoreAxisWidth||(c.setOrientation(this.rotate),c.fixAxisPosition(),a[c.position]=!0);(b=this.categoryAxis)&&!b.ignoreAxisWidth&&(b.setOrientation(!this.rotate),b.fixAxisPosition(),b.fixAxisPosition(),a[b.position]=!0)}a.left&&(this.marginLeft=0);a.right&&(this.marginRight=0);a.top&&(this.marginTop=0);a.bottom&&(this.marginBottom=0);this.fixMargins=
a},measureMargins:function(){var a=this.valueAxes,b,c=this.autoMarginOffset,d=this.fixMargins,g=this.realWidth,h=this.realHeight,e=c,f=c,k=g;b=h;var m;for(m=0;m<a.length;m++)a[m].handleSynchronization(),b=this.getAxisBounds(a[m],e,k,f,b),e=Math.round(b.l),k=Math.round(b.r),f=Math.round(b.t),b=Math.round(b.b);if(a=this.categoryAxis)b=this.getAxisBounds(a,e,k,f,b),e=Math.round(b.l),k=Math.round(b.r),f=Math.round(b.t),b=Math.round(b.b);d.left&&e<c&&(this.marginLeft=Math.round(-e+c),!isNaN(this.minMarginLeft)&&
this.marginLeft<this.minMarginLeft&&(this.marginLeft=this.minMarginLeft));d.right&&k>=g-c&&(this.marginRight=Math.round(k-g+c),!isNaN(this.minMarginRight)&&this.marginRight<this.minMarginRight&&(this.marginRight=this.minMarginRight));d.top&&f<c+this.titleHeight&&(this.marginTop=Math.round(this.marginTop-f+c+this.titleHeight),!isNaN(this.minMarginTop)&&this.marginTop<this.minMarginTop&&(this.marginTop=this.minMarginTop));d.bottom&&b>h-c&&(this.marginBottom=Math.round(this.marginBottom+b-h+c),!isNaN(this.minMarginBottom)&&
this.marginBottom<this.minMarginBottom&&(this.marginBottom=this.minMarginBottom));this.initChart()},getAxisBounds:function(a,b,c,d,g){if(!a.ignoreAxisWidth){var h=a.labelsSet,e=a.tickLength;a.inside&&(e=0);if(h)switch(h=a.getBBox(),a.position){case "top":a=h.y;d>a&&(d=a);break;case "bottom":a=h.y+h.height;g<a&&(g=a);break;case "right":a=h.x+h.width+e+3;c<a&&(c=a);break;case "left":a=h.x-e,b>a&&(b=a)}}return{l:b,t:d,r:c,b:g}},drawZoomOutButton:function(){var a=this;if(!a.zbSet){var b=a.container.set();
a.zoomButtonSet.push(b);var c=a.color,d=a.fontSize,g=a.zoomOutButtonImageSize,h=a.zoomOutButtonImage.replace(/\.[a-z]*$/i,""),e=a.langObj.zoomOutText||a.zoomOutText,l=a.zoomOutButtonColor,k=a.zoomOutButtonAlpha,m=a.zoomOutButtonFontSize,p=a.zoomOutButtonPadding;isNaN(m)||(d=m);(m=a.zoomOutButtonFontColor)&&(c=m);var m=a.zoomOutButton,n;m&&(m.fontSize&&(d=m.fontSize),m.color&&(c=m.color),m.backgroundColor&&(l=m.backgroundColor),isNaN(m.backgroundAlpha)||(a.zoomOutButtonRollOverAlpha=m.backgroundAlpha));
var u=m=0,u=a.pathToImages;if(h){if(f.isAbsolute(h)||void 0===u)u="";n=a.container.image(u+h+a.extension,0,0,g,g);f.setCN(a,n,"zoom-out-image");b.push(n);n=n.getBBox();m=n.width+5}void 0!==e&&(c=f.text(a.container,e,c,a.fontFamily,d,"start"),f.setCN(a,c,"zoom-out-label"),d=c.getBBox(),u=n?n.height/2-3:d.height/2,c.translate(m,u),b.push(c));n=b.getBBox();c=1;f.isModern||(c=0);l=f.rect(a.container,n.width+2*p+5,n.height+2*p-2,l,1,1,l,c);l.setAttr("opacity",k);l.translate(-p,-p);f.setCN(a,l,"zoom-out-bg");
b.push(l);l.toBack();a.zbBG=l;n=l.getBBox();b.translate(a.marginLeftReal+a.plotAreaWidth-n.width+p,a.marginTopReal+p);b.hide();b.mouseover(function(){a.rollOverZB()}).mouseout(function(){a.rollOutZB()}).click(function(){a.clickZB()}).touchstart(function(){a.rollOverZB()}).touchend(function(){a.rollOutZB();a.clickZB()});for(k=0;k<b.length;k++)b[k].attr({cursor:"pointer"});void 0!==a.zoomOutButtonTabIndex&&(b.setAttr("tabindex",a.zoomOutButtonTabIndex),b.setAttr("role","menuitem"),b.keyup(function(b){13==
b.keyCode&&a.clickZB()}));a.zbSet=b}},rollOverZB:function(){this.rolledOverZB=!0;this.zbBG.setAttr("opacity",this.zoomOutButtonRollOverAlpha)},rollOutZB:function(){this.rolledOverZB=!1;this.zbBG.setAttr("opacity",this.zoomOutButtonAlpha)},clickZB:function(){this.rolledOverZB=!1;this.zoomOut()},zoomOut:function(){this.zoomOutValueAxes()},drawPlotArea:function(){var a=this.dx,b=this.dy,c=this.marginLeftReal,d=this.marginTopReal,g=this.plotAreaWidth-1,h=this.plotAreaHeight-1,e=this.plotAreaFillColors,
l=this.plotAreaFillAlphas,k=this.plotAreaBorderColor,m=this.plotAreaBorderAlpha;"object"==typeof l&&(l=l[0]);e=f.polygon(this.container,[0,g,g,0,0],[0,0,h,h,0],e,l,1,k,m,this.plotAreaGradientAngle);f.setCN(this,e,"plot-area");e.translate(c+a,d+b);this.set.push(e);0!==a&&0!==b&&(e=this.plotAreaFillColors,"object"==typeof e&&(e=e[0]),e=f.adjustLuminosity(e,-.15),g=f.polygon(this.container,[0,a,g+a,g,0],[0,b,b,0,0],e,l,1,k,m),f.setCN(this,g,"plot-area-bottom"),g.translate(c,d+h),this.set.push(g),a=f.polygon(this.container,
[0,0,a,a,0],[0,h,h+b,b,0],e,l,1,k,m),f.setCN(this,a,"plot-area-left"),a.translate(c,d),this.set.push(a));(c=this.bbset)&&this.scrollbarOnly&&c.remove()},updatePlotArea:function(){var a=this.updateWidth(),b=this.updateHeight(),c=this.container;this.realWidth=a;this.realWidth=b;c&&this.container.setSize(a,b);var c=this.marginLeftReal,d=this.marginTopReal,a=a-c-this.marginRightReal-this.dx,b=b-d-this.marginBottomReal;1>a&&(a=1);1>b&&(b=1);this.plotAreaWidth=Math.round(a);this.plotAreaHeight=Math.round(b);
this.plotBalloonsSet.translate(c,d)},updateDxy:function(){this.dx=Math.round(this.depth3D*Math.cos(this.angle*Math.PI/180));this.dy=Math.round(-this.depth3D*Math.sin(this.angle*Math.PI/180));this.d3x=Math.round(this.columnSpacing3D*Math.cos(this.angle*Math.PI/180));this.d3y=Math.round(-this.columnSpacing3D*Math.sin(this.angle*Math.PI/180))},updateMargins:function(){var a=this.getTitleHeight();this.titleHeight=a;this.marginTopReal=this.marginTop-this.dy;this.fixMargins&&!this.fixMargins.top&&(this.marginTopReal+=
a);this.marginBottomReal=this.marginBottom;this.marginLeftReal=this.marginLeft;this.marginRightReal=this.marginRight},updateValueAxes:function(){var a=this.valueAxes,b;for(b=0;b<a.length;b++){var c=a[b];this.setAxisRenderers(c);this.updateObjectSize(c)}},setAxisRenderers:function(a){a.axisRenderer=f.RecAxis;a.guideFillRenderer=f.RecFill;a.axisItemRenderer=f.RecItem;a.marginsChanged=!0},updateGraphs:function(){var a=this.graphs,b;for(b=0;b<a.length;b++){var c=a[b];c.index=b;c.rotate=this.rotate;this.updateObjectSize(c)}},
updateObjectSize:function(a){a.width=this.plotAreaWidth-1;a.height=this.plotAreaHeight-1;a.x=this.marginLeftReal;a.y=this.marginTopReal;a.dx=this.dx;a.dy=this.dy},updateChartCursor:function(){var a=this.chartCursor;a&&(a=f.processObject(a,f.ChartCursor,this.theme),this.updateObjectSize(a),this.addChartCursor(a),a.chart=this)},processScrollbars:function(){var a=this.chartScrollbar;a&&(a=f.processObject(a,f.ChartScrollbar,this.theme),this.addChartScrollbar(a))},updateScrollbars:function(){},removeChartCursor:function(){f.callMethod("destroy",
[this.chartCursor]);this.chartCursor=null},zoomTrendLines:function(){var a=this.trendLines,b;for(b=0;b<a.length;b++){var c=a[b];c.valueAxis.recalculateToPercents?c.set&&c.set.hide():(c.x=this.marginLeftReal,c.y=this.marginTopReal,c.draw())}},handleCursorValueZoom:function(){},addTrendLine:function(a){this.trendLines.push(a)},zoomOutValueAxes:function(){for(var a=this.valueAxes,b=0;b<a.length;b++)a[b].zoomOut()},removeTrendLine:function(a){var b=this.trendLines,c;for(c=b.length-1;0<=c;c--)b[c]==a&&
b.splice(c,1)},adjustMargins:function(a,b){var c=a.position,d=a.scrollbarHeight+a.offset;a.enabled&&("top"==c?b?this.marginLeftReal+=d:this.marginTopReal+=d:b?this.marginRightReal+=d:this.marginBottomReal+=d)},getScrollbarPosition:function(a,b,c){var d="bottom",g="top";a.oppositeAxis||(g=d,d="top");a.position=b?"bottom"==c||"left"==c?d:g:"top"==c||"right"==c?d:g},updateChartScrollbar:function(a,b){if(a){a.rotate=b;var c=this.marginTopReal,d=this.marginLeftReal,g=a.scrollbarHeight,h=this.dx,e=this.dy,
f=a.offset;"top"==a.position?b?(a.y=c,a.x=d-g-f):(a.y=c-g+e-f,a.x=d+h):b?(a.y=c+e,a.x=d+this.plotAreaWidth+h+f):(a.y=c+this.plotAreaHeight+f,a.x=this.marginLeftReal)}},showZB:function(a){var b=this.zbSet;a&&(b=this.zoomOutText,""!==b&&b&&this.drawZoomOutButton());if(b=this.zbSet)this.zoomButtonSet.push(b),a?b.show():b.hide(),this.rollOutZB()},handleReleaseOutside:function(a){f.AmRectangularChart.base.handleReleaseOutside.call(this,a);(a=this.chartCursor)&&a.handleReleaseOutside&&a.handleReleaseOutside()},
handleMouseDown:function(a){f.AmRectangularChart.base.handleMouseDown.call(this,a);var b=this.chartCursor;b&&b.handleMouseDown&&!this.rolledOverZB&&b.handleMouseDown(a)},update:function(){f.AmRectangularChart.base.update.call(this);this.chartCursor&&this.chartCursor.update&&this.chartCursor.update()},handleScrollbarValueZoom:function(a){this.relativeZoomValueAxes(a.target.valueAxes,a.relativeStart,a.relativeEnd);this.zoomAxesAndGraphs()},zoomValueScrollbar:function(a){if(a&&a.enabled){var b=a.valueAxes[0],
c=b.relativeStart,d=b.relativeEnd;b.reversed&&(d=1-c,c=1-b.relativeEnd);a.percentZoom(c,d)}},zoomAxesAndGraphs:function(){if(!this.scrollbarOnly){var a=this.valueAxes,b;for(b=0;b<a.length;b++)a[b].zoom(this.start,this.end);a=this.graphs;for(b=0;b<a.length;b++)a[b].zoom(this.start,this.end);(b=this.chartCursor)&&b.clearSelection();this.zoomTrendLines()}},handleValueAxisZoomReal:function(a,b){var c=a.relativeStart,d=a.relativeEnd;if(c>d)var g=c,c=d,d=g;this.relativeZoomValueAxes(b,c,d);this.updateAfterValueZoom()},
updateAfterValueZoom:function(){this.zoomAxesAndGraphs();this.zoomScrollbar()},relativeZoomValueAxes:function(a,b,c){b=f.fitToBounds(b,0,1);c=f.fitToBounds(c,0,1);if(b>c){var d=b;b=c;c=d}var d=1/this.maxZoomFactor,g=f.getDecimals(d)+4;c-b<d&&(c=b+(c-b)/2,b=c-d/2,c+=d/2);b=f.roundTo(b,g);c=f.roundTo(c,g);d=!1;if(a){for(g=0;g<a.length;g++){var h=a[g].zoomToRelativeValues(b,c,!0);h&&(d=h)}this.showZB()}return d},addChartCursor:function(a){f.callMethod("destroy",[this.chartCursor]);a&&(this.listenTo(a,
"moved",this.handleCursorMove),this.listenTo(a,"zoomed",this.handleCursorZoom),this.listenTo(a,"zoomStarted",this.handleCursorZoomStarted),this.listenTo(a,"panning",this.handleCursorPanning),this.listenTo(a,"onHideCursor",this.handleCursorHide));this.chartCursor=a},handleCursorChange:function(){},handleCursorMove:function(a){var b,c=this.valueAxes;for(b=0;b<c.length;b++)if(!a.panning){var d=c[b];d&&d.showBalloon&&d.showBalloon(a.x,a.y)}},handleCursorZoom:function(a){if(this.skipZoomed)this.skipZoomed=
!1;else{var b=this.startX0,c=this.endX0,d=this.endY0,g=this.startY0,h=a.startX,e=a.endX,f=a.startY,k=a.endY;this.startX0=this.endX0=this.startY0=this.endY0=NaN;this.handleCursorZoomReal(b+h*(c-b),b+e*(c-b),g+f*(d-g),g+k*(d-g),a)}},handleCursorHide:function(){var a,b=this.valueAxes;for(a=0;a<b.length;a++)b[a].hideBalloon();b=this.graphs;for(a=0;a<b.length;a++)b[a].hideBalloonReal()}})})();(function(){var f=window.AmCharts;f.AmSerialChart=f.Class({inherits:f.AmRectangularChart,construct:function(a){this.type="serial";f.AmSerialChart.base.construct.call(this,a);this.cname="AmSerialChart";this.theme=a;this.columnSpacing=5;this.columnSpacing3D=0;this.columnWidth=.8;var b=new f.CategoryAxis(a);b.chart=this;this.categoryAxis=b;this.zoomOutOnDataUpdate=!0;this.mouseWheelZoomEnabled=this.mouseWheelScrollEnabled=this.rotate=this.skipZoom=!1;this.minSelectedTime=0;f.applyTheme(this,a,this.cname)},
initChart:function(){f.AmSerialChart.base.initChart.call(this);this.updateCategoryAxis(this.categoryAxis,this.rotate,"categoryAxis");if(this.dataChanged)this.parseData();else this.onDataUpdated();this.drawGraphs=!0},onDataUpdated:function(){var a=this.countColumns(),b=this.chartData,c=this.graphs,d;for(d=0;d<c.length;d++){var g=c[d];g.data=b;g.columnCount=a}0<b.length&&(this.firstTime=this.getStartTime(b[0].time),this.lastTime=this.getEndTime(b[b.length-1].time));this.drawChart();this.autoMargins&&
!this.marginsUpdated?(this.marginsUpdated=!0,this.measureMargins()):this.dispDUpd()},syncGrid:function(){if(this.synchronizeGrid){var a=this.valueAxes,b,c;if(0<a.length){var d=0;for(c=0;c<a.length;c++)b=a[c],d<b.gridCountReal&&(d=b.gridCountReal);var g=!1;for(c=0;c<a.length;c++)if(b=a[c],b.gridCountReal<d){var h=(d-b.gridCountReal)/2,e=g=h;0!==h-Math.round(h)&&(g-=.5,e+=.5);0<=b.min&&0>b.min-g*b.step&&(e+=g,g=0);0>=b.max&&0<b.max+e*b.step&&(g+=e,e=0);h=f.getDecimals(b.step);b.minimum=f.roundTo(b.min-
g*b.step,h);b.maximum=f.roundTo(b.max+e*b.step,h);b.setStep=b.step;g=b.strictMinMax=!0}g&&this.updateAfterValueZoom();for(c=0;c<a.length;c++)b=a[c],b.minimum=NaN,b.maximum=NaN,b.setStep=NaN,b.strictMinMax=!1}}},handleWheelReal:function(a,b){if(!this.wheelBusy){var c=this.categoryAxis,d=c.parseDates,g=c.minDuration(),h=1,e=1;this.mouseWheelZoomEnabled?b||(h=-1):b&&(h=-1);var f=this.chartCursor;if(f){var k=f.mouseX,f=f.mouseY;h!=e&&(k=this.rotate?f/this.plotAreaHeight:k/this.plotAreaWidth,h*=k,e*=1-
k);k=.05*(this.end-this.start);d&&(k=.05*(this.endTime-this.startTime)/g);1>k&&(k=1);h*=k;e*=k;if(!d||c.equalSpacing)h=Math.round(h),e=Math.round(e)}f=this.chartData.length;c=this.lastTime;k=this.firstTime;0>a?d?(f=this.endTime-this.startTime,d=this.startTime+h*g,g=this.endTime+e*g,0<e&&0<h&&g>=c&&(g=c,d=c-f),this.zoomToDates(new Date(d),new Date(g))):(0<e&&0<h&&this.end>=f-1&&(h=e=0),d=this.start+h,g=this.end+e,this.zoomToIndexes(d,g)):d?(f=this.endTime-this.startTime,d=this.startTime-h*g,g=this.endTime-
e*g,0<e&&0<h&&d<=k&&(d=k,g=k+f),this.zoomToDates(new Date(d),new Date(g))):(0<e&&0<h&&1>this.start&&(h=e=0),d=this.start-h,g=this.end-e,this.zoomToIndexes(d,g))}},validateData:function(a){this.marginsUpdated=!1;this.zoomOutOnDataUpdate&&!a&&(this.endTime=this.end=this.startTime=this.start=NaN);f.AmSerialChart.base.validateData.call(this)},drawChart:function(){if(0<this.realWidth&&0<this.realHeight){f.AmSerialChart.base.drawChart.call(this);var a=this.chartData;if(f.ifArray(a)){var b=this.chartScrollbar;
!b||!this.marginsUpdated&&this.autoMargins||b.draw();(b=this.valueScrollbar)&&b.draw();var b=a.length-1,c,d;c=this.categoryAxis;if(c.parseDates&&!c.equalSpacing){if(c=this.startTime,d=this.endTime,isNaN(c)||isNaN(d))c=this.firstTime,d=this.lastTime}else{c=this.start;d=this.end;if(isNaN(c)||isNaN(d))d=c=NaN;isNaN(c)&&(isNaN(this.startTime)||(c=this.getClosestIndex(a,"time",this.startTime,!0,0,a.length)));isNaN(d)&&(isNaN(this.endTime)||(d=this.getClosestIndex(a,"time",this.endTime,!1,0,a.length)));
if(isNaN(c)||isNaN(d))c=0,d=b}this.endTime=this.startTime=this.end=this.start=void 0;this.zoom(c,d)}}else this.cleanChart()},cleanChart:function(){f.callMethod("destroy",[this.valueAxes,this.graphs,this.categoryAxis,this.chartScrollbar,this.chartCursor,this.valueScrollbar])},updateCategoryAxis:function(a,b,c){a.chart=this;a.id=c;a.rotate=b;a.setOrientation(!this.rotate);a.init();this.setAxisRenderers(a);this.updateObjectSize(a)},updateValueAxes:function(){f.AmSerialChart.base.updateValueAxes.call(this);
var a=this.valueAxes,b;for(b=0;b<a.length;b++){var c=a[b],d=this.rotate;c.rotate=d;c.setOrientation(d);d=this.categoryAxis;if(!d.startOnAxis||d.parseDates)c.expandMinMax=!0}},getStartTime:function(a){var b=this.categoryAxis;return f.resetDateToMin(new Date(a),b.minPeriod,1,b.firstDayOfWeek).getTime()},getEndTime:function(a){var b=f.extractPeriod(this.categoryAxis.minPeriod);return f.changeDate(new Date(a),b.period,b.count,!0).getTime()-1},updateMargins:function(){f.AmSerialChart.base.updateMargins.call(this);
var a=this.chartScrollbar;a&&(this.getScrollbarPosition(a,this.rotate,this.categoryAxis.position),this.adjustMargins(a,this.rotate));if(a=this.valueScrollbar)this.getScrollbarPosition(a,!this.rotate,this.valueAxes[0].position),this.adjustMargins(a,!this.rotate)},updateScrollbars:function(){f.AmSerialChart.base.updateScrollbars.call(this);this.updateChartScrollbar(this.chartScrollbar,this.rotate);this.updateChartScrollbar(this.valueScrollbar,!this.rotate)},zoom:function(a,b){var c=this.categoryAxis;
c.parseDates&&!c.equalSpacing?this.timeZoom(a,b):this.indexZoom(a,b);isNaN(a)&&this.zoomOutValueAxes();(c=this.chartCursor)&&(c.pan||c.hideCursorReal());this.updateLegendValues()},timeZoom:function(a,b){var c=this.maxSelectedTime;isNaN(c)||(b!=this.endTime&&b-a>c&&(a=b-c),a!=this.startTime&&b-a>c&&(b=a+c));var d=this.minSelectedTime;if(0<d&&b-a<d){var g=Math.round(a+(b-a)/2),d=Math.round(d/2);a=g-d;b=g+d}d=this.chartData;g=this.categoryAxis;if(f.ifArray(d)&&(a!=this.startTime||b!=this.endTime)){var h=
g.minDuration(),e=this.firstTime,l=this.lastTime;a||(a=e,isNaN(c)||(a=l-c));b||(b=l);a>l&&(a=l);b<e&&(b=e);a<e&&(a=e);b>l&&(b=l);b<a&&(b=a+h);b-a<h/5&&(b<l?b=a+h/5:a=b-h/5);this.startTime=a;this.endTime=b;c=d.length-1;h=this.getClosestIndex(d,"time",a,!0,0,c);d=this.getClosestIndex(d,"time",b,!1,h,c);g.timeZoom(a,b);g.zoom(h,d);this.start=f.fitToBounds(h,0,c);this.end=f.fitToBounds(d,0,c);this.zoomAxesAndGraphs();this.zoomScrollbar();this.fixCursor();this.showZB();this.syncGrid();this.updateColumnsDepth();
this.dispatchTimeZoomEvent()}},showZB:function(){var a,b=this.categoryAxis;b&&b.parseDates&&!b.equalSpacing&&(this.startTime>this.firstTime&&(a=!0),this.endTime<this.lastTime&&(a=!0));0<this.start&&(a=!0);this.end<this.chartData.length-1&&(a=!0);if(b=this.valueAxes)b=b[0],isNaN(b.relativeStart)||(0!==f.roundTo(b.relativeStart,3)&&(a=!0),1!=f.roundTo(b.relativeEnd,3)&&(a=!0));f.AmSerialChart.base.showZB.call(this,a)},updateAfterValueZoom:function(){f.AmSerialChart.base.updateAfterValueZoom.call(this);
this.updateColumnsDepth()},indexZoom:function(a,b){var c=this.maxSelectedSeries,d=!1;isNaN(c)||(b!=this.end&&b-a>c&&(a=b-c,d=!0),a!=this.start&&b-a>c&&(b=a+c,d=!0));if(d&&(d=this.chartScrollbar)&&d.dragger){var g=d.dragger.getBBox();d.maxWidth=g.width;d.maxHeight=g.height}if(a!=this.start||b!=this.end)d=this.chartData.length-1,isNaN(a)&&(a=0,isNaN(c)||(a=d-c)),isNaN(b)&&(b=d),b<a&&(b=a),b>d&&(b=d),a>d&&(a=d-1),0>a&&(a=0),this.start=a,this.end=b,this.categoryAxis.zoom(a,b),this.zoomAxesAndGraphs(),
this.zoomScrollbar(),this.fixCursor(),0!==a||b!=this.chartData.length-1?this.showZB(!0):this.showZB(!1),this.syncGrid(),this.updateColumnsDepth(),this.dispatchIndexZoomEvent()},updateGraphs:function(){f.AmSerialChart.base.updateGraphs.call(this);var a=this.graphs,b;for(b=0;b<a.length;b++){var c=a[b];c.columnWidthReal=this.columnWidth;c.categoryAxis=this.categoryAxis;f.isString(c.fillToGraph)&&(c.fillToGraph=this.graphsById[c.fillToGraph])}},zoomAxesAndGraphs:function(){f.AmSerialChart.base.zoomAxesAndGraphs.call(this);
this.updateColumnsDepth()},updateColumnsDepth:function(){if(0!==this.depth3D||0!==this.angle){var a,b=this.graphs,c;this.columnsArray=[];for(a=0;a<b.length;a++){c=b[a];var d=c.columnsArray;if(d){var g;for(g=0;g<d.length;g++)this.columnsArray.push(d[g])}}this.columnsArray.sort(this.compareDepth);b=this.columnsSet;if(0<this.columnsArray.length){d=this.container.set();this.columnSet.push(d);for(a=0;a<this.columnsArray.length;a++)d.push(this.columnsArray[a].column.set);c&&d.translate(c.x,c.y);this.columnsSet=
d}f.remove(b)}},compareDepth:function(a,b){return a.depth>b.depth?1:-1},zoomScrollbar:function(){var a=this.chartScrollbar,b=this.categoryAxis;if(a){if(!this.zoomedByScrollbar){var c=a.dragger;c&&c.stop()}this.zoomedByScrollbar=!1;b.parseDates&&!b.equalSpacing?a.timeZoom(this.startTime,this.endTime):a.zoom(this.start,this.end)}this.zoomValueScrollbar(this.valueScrollbar)},updateTrendLines:function(){var a=this.trendLines,b;for(b=0;b<a.length;b++){var c=a[b],c=f.processObject(c,f.TrendLine,this.theme);
a[b]=c;c.chart=this;c.id||(c.id="trendLineAuto"+b+"_"+(new Date).getTime());f.isString(c.valueAxis)&&(c.valueAxis=this.getValueAxisById(c.valueAxis));c.valueAxis||(c.valueAxis=this.valueAxes[0]);c.categoryAxis=this.categoryAxis}},countColumns:function(){var a=0,b=this.valueAxes.length,c=this.graphs.length,d,g,f=!1,e,l;for(l=0;l<b;l++){g=this.valueAxes[l];var k=g.stackType,m=0;if("100%"==k||"regular"==k)for(f=!1,e=0;e<c;e++)d=this.graphs[e],d.tcc=1,d.valueAxis==g&&"column"==d.type&&(!f&&d.stackable&&
(a++,f=!0),(!d.stackable&&d.clustered||d.newStack&&0!==m)&&a++,d.columnIndex=a-1,d.clustered||(d.columnIndex=0),m++);if("none"==k||"3d"==k){f=!1;for(e=0;e<c;e++)d=this.graphs[e],d.valueAxis==g&&"column"==d.type&&(d.clustered?(d.tcc=1,d.newStack&&(a=0),d.hidden||(d.columnIndex=a,a++)):d.hidden||(f=!0,d.tcc=1,d.columnIndex=0));f&&0===a&&(a=1)}if("3d"==k){g=1;for(l=0;l<c;l++)d=this.graphs[l],d.newStack&&g++,d.depthCount=g,d.tcc=a;a=g}}return a},parseData:function(){f.AmSerialChart.base.parseData.call(this);
this.parseSerialData(this.dataProvider)},getCategoryIndexByValue:function(a){var b=this.chartData,c;for(c=0;c<b.length;c++)if(b[c].category==a)return c},handleScrollbarZoom:function(a){this.zoomedByScrollbar=!0;this.zoom(a.start,a.end)},dispatchTimeZoomEvent:function(){if(this.drawGraphs&&(this.prevStartTime!=this.startTime||this.prevEndTime!=this.endTime)){var a={type:"zoomed"};a.startDate=new Date(this.startTime);a.endDate=new Date(this.endTime);a.startIndex=this.start;a.endIndex=this.end;this.startIndex=
this.start;this.endIndex=this.end;this.startDate=a.startDate;this.endDate=a.endDate;this.prevStartTime=this.startTime;this.prevEndTime=this.endTime;var b=this.categoryAxis,c=f.extractPeriod(b.minPeriod).period,b=b.dateFormatsObject[c];a.startValue=f.formatDate(a.startDate,b,this);a.endValue=f.formatDate(a.endDate,b,this);a.chart=this;a.target=this;this.fire(a)}},dispatchIndexZoomEvent:function(){if(this.drawGraphs&&(this.prevStartIndex!=this.start||this.prevEndIndex!=this.end)){this.startIndex=this.start;
this.endIndex=this.end;var a=this.chartData;if(f.ifArray(a)&&!isNaN(this.start)&&!isNaN(this.end)){var b={chart:this,target:this,type:"zoomed"};b.startIndex=this.start;b.endIndex=this.end;b.startValue=a[this.start].category;b.endValue=a[this.end].category;this.categoryAxis.parseDates&&(this.startTime=a[this.start].time,this.endTime=a[this.end].time,b.startDate=new Date(this.startTime),b.endDate=new Date(this.endTime));this.prevStartIndex=this.start;this.prevEndIndex=this.end;this.fire(b)}}},updateLegendValues:function(){this.legend&&
this.legend.updateValues()},getClosestIndex:function(a,b,c,d,g,f){0>g&&(g=0);f>a.length-1&&(f=a.length-1);var e=g+Math.round((f-g)/2),l=a[e][b];return c==l?e:1>=f-g?d?g:Math.abs(a[g][b]-c)<Math.abs(a[f][b]-c)?g:f:c==l?e:c<l?this.getClosestIndex(a,b,c,d,g,e):this.getClosestIndex(a,b,c,d,e,f)},zoomToIndexes:function(a,b){var c=this.chartData;if(c){var d=c.length;0<d&&(0>a&&(a=0),b>d-1&&(b=d-1),d=this.categoryAxis,d.parseDates&&!d.equalSpacing?this.zoom(c[a].time,this.getEndTime(c[b].time)):this.zoom(a,
b))}},zoomToDates:function(a,b){var c=this.chartData;if(c)if(this.categoryAxis.equalSpacing){var d=this.getClosestIndex(c,"time",a.getTime(),!0,0,c.length);b=f.resetDateToMin(b,this.categoryAxis.minPeriod,1);c=this.getClosestIndex(c,"time",b.getTime(),!1,0,c.length);this.zoom(d,c)}else this.zoom(a.getTime(),b.getTime())},zoomToCategoryValues:function(a,b){this.chartData&&this.zoom(this.getCategoryIndexByValue(a),this.getCategoryIndexByValue(b))},formatPeriodString:function(a,b){if(b){b.periodDataItem=
{};b.periodPercentDataItem={};var c=["value","open","low","high","close"],d="value open low high close average sum count".split(" "),g=b.valueAxis,h=this.chartData,e=b.numberFormatter;e||(e=this.nf);for(var l=0;l<c.length;l++){for(var k=c[l],m=0,p=0,n=0,u=0,v,x,E,t,r,B,q,w,y,C,F=this.start;F<=this.end;F++){var D=h[F];if(D){var A=D.axes[g.id].graphs[b.id];if(A){if(A.values){var z=A.values[k],D=D.x.categoryAxis;if(this.rotate){if(0>D||D>A.graph.height)z=NaN}else if(0>D||D>A.graph.width)z=NaN;if(!isNaN(z)){isNaN(v)&&
(v=z);x=z;if(isNaN(E)||E>z)E=z;if(isNaN(t)||t<z)t=z;r=f.getDecimals(m);D=f.getDecimals(z);m+=z;m=f.roundTo(m,Math.max(r,D));p++;r=m/p}}if(A.percents&&(A=A.percents[k],!isNaN(A))){isNaN(B)&&(B=A);q=A;if(isNaN(w)||w>A)w=A;if(isNaN(y)||y<A)y=A;C=f.getDecimals(n);z=f.getDecimals(A);n+=A;n=f.roundTo(n,Math.max(C,z));u++;C=n/u}}}}m={open:v,close:x,high:t,low:E,average:r,sum:m,count:p};n={open:B,close:q,high:y,low:w,average:C,sum:n,count:u};a=f.formatValue(a,m,d,e,k+"\\.",this.usePrefixes,this.prefixesOfSmallNumbers,
this.prefixesOfBigNumbers);a=f.formatValue(a,n,d,this.pf,"percents\\."+k+"\\.");b.periodDataItem[k]=m;b.periodPercentDataItem[k]=n}}return a=f.cleanFromEmpty(a)},formatString:function(a,b,c){if(b){var d=b.graph;if(void 0!==a){if(-1!=a.indexOf("[[category]]")){var g=b.serialDataItem.category;if(this.categoryAxis.parseDates){var h=this.balloonDateFormat,e=this.chartCursor;e&&e.categoryBalloonDateFormat&&(h=e.categoryBalloonDateFormat);h=f.formatDate(g,h,this);-1!=h.indexOf("fff")&&(h=f.formatMilliseconds(h,
g));g=h}a=a.replace(/\[\[category\]\]/g,String(g.replace("$","$$$")))}g=d.numberFormatter;g||(g=this.nf);h=b.graph.valueAxis;(e=h.duration)&&!isNaN(b.values.value)&&(e=f.formatDuration(b.values.value,e,"",h.durationUnits,h.maxInterval,g),a=a.replace(RegExp("\\[\\[value\\]\\]","g"),e));"date"==h.type&&(h=f.formatDate(new Date(b.values.value),d.dateFormat,this),e=RegExp("\\[\\[value\\]\\]","g"),a=a.replace(e,h),h=f.formatDate(new Date(b.values.open),d.dateFormat,this),e=RegExp("\\[\\[open\\]\\]","g"),
a=a.replace(e,h));d="value open low high close total".split(" ");h=this.pf;a=f.formatValue(a,b.percents,d,h,"percents\\.");a=f.formatValue(a,b.values,d,g,"",this.usePrefixes,this.prefixesOfSmallNumbers,this.prefixesOfBigNumbers);a=f.formatValue(a,b.values,["percents"],h);-1!=a.indexOf("[[")&&(a=f.formatDataContextValue(a,b.dataContext));-1!=a.indexOf("[[")&&b.graph.customData&&(a=f.formatDataContextValue(a,b.graph.customData));a=f.AmSerialChart.base.formatString.call(this,a,b,c)}return a}},updateChartCursor:function(){f.AmSerialChart.base.updateChartCursor.call(this);
var a=this.chartCursor,b=this.categoryAxis;if(a){var c=a.categoryBalloonAlpha,d=a.categoryBalloonColor,g=a.color;void 0===d&&(d=a.cursorColor);var h=a.valueZoomable,e=a.zoomable,l=a.valueLineEnabled;this.rotate?(a.vLineEnabled=l,a.hZoomEnabled=h,a.vZoomEnabled=e):(a.hLineEnabled=l,a.vZoomEnabled=h,a.hZoomEnabled=e);if(a.valueLineBalloonEnabled)for(l=0;l<this.valueAxes.length;l++)h=this.valueAxes[l],(e=h.balloon)||(e={}),e=f.extend(e,this.balloon,!0),e.fillColor=d,e.balloonColor=d,e.fillAlpha=c,e.borderColor=
d,e.color=g,h.balloon=e;else for(e=0;e<this.valueAxes.length;e++)h=this.valueAxes[e],h.balloon&&(h.balloon=null);b&&(b.balloonTextFunction=a.categoryBalloonFunction,a.categoryLineAxis=b,b.balloonText=a.categoryBalloonText,a.categoryBalloonEnabled&&((e=b.balloon)||(e={}),e=f.extend(e,this.balloon,!0),e.fillColor=d,e.balloonColor=d,e.fillAlpha=c,e.borderColor=d,e.color=g,b.balloon=e),b.balloon&&(b.balloon.enabled=a.categoryBalloonEnabled))}},addChartScrollbar:function(a){f.callMethod("destroy",[this.chartScrollbar]);
a&&(a.chart=this,this.listenTo(a,"zoomed",this.handleScrollbarZoom));this.rotate?void 0===a.width&&(a.width=a.scrollbarHeight):void 0===a.height&&(a.height=a.scrollbarHeight);a.gridAxis=this.categoryAxis;this.chartScrollbar=a},addValueScrollbar:function(a){f.callMethod("destroy",[this.valueScrollbar]);a&&(a.chart=this,this.listenTo(a,"zoomed",this.handleScrollbarValueZoom),this.listenTo(a,"zoomStarted",this.handleCursorZoomStarted));var b=a.scrollbarHeight;this.rotate?void 0===a.height&&(a.height=
b):void 0===a.width&&(a.width=b);a.gridAxis||(a.gridAxis=this.valueAxes[0]);a.valueAxes=this.valueAxes;this.valueScrollbar=a},removeChartScrollbar:function(){f.callMethod("destroy",[this.chartScrollbar]);this.chartScrollbar=null},removeValueScrollbar:function(){f.callMethod("destroy",[this.valueScrollbar]);this.valueScrollbar=null},handleReleaseOutside:function(a){f.AmSerialChart.base.handleReleaseOutside.call(this,a);f.callMethod("handleReleaseOutside",[this.chartScrollbar,this.valueScrollbar])},
update:function(){f.AmSerialChart.base.update.call(this);this.chartScrollbar&&this.chartScrollbar.update&&this.chartScrollbar.update();this.valueScrollbar&&this.valueScrollbar.update&&this.valueScrollbar.update()},processScrollbars:function(){f.AmSerialChart.base.processScrollbars.call(this);var a=this.valueScrollbar;a&&(a=f.processObject(a,f.ChartScrollbar,this.theme),a.id="valueScrollbar",this.addValueScrollbar(a))},handleValueAxisZoom:function(a){this.handleValueAxisZoomReal(a,this.valueAxes)},
zoomOut:function(){f.AmSerialChart.base.zoomOut.call(this);this.zoom();this.syncGrid()},getNextItem:function(a){var b=a.index,c=this.chartData,d=a.graph;if(b+1<c.length)for(b+=1;b<c.length;b++)if(a=c[b])if(a=a.axes[d.valueAxis.id].graphs[d.id],!isNaN(a.y))return a},handleCursorZoomReal:function(a,b,c,d,g){var f=g.target,e,l;this.rotate?(isNaN(a)||isNaN(b)||this.relativeZoomValueAxes(this.valueAxes,a,b)&&this.updateAfterValueZoom(),f.vZoomEnabled&&(e=g.start,l=g.end)):(isNaN(c)||isNaN(d)||this.relativeZoomValueAxes(this.valueAxes,
c,d)&&this.updateAfterValueZoom(),f.hZoomEnabled&&(e=g.start,l=g.end));isNaN(e)||isNaN(l)||(a=this.categoryAxis,a.parseDates&&!a.equalSpacing?this.zoomToDates(new Date(e),new Date(l)):this.zoomToIndexes(e,l))},handleCursorZoomStarted:function(){var a=this.valueAxes;if(a){var a=a[0],b=a.relativeStart,c=a.relativeEnd;a.reversed&&(b=1-a.relativeEnd,c=1-a.relativeStart);this.rotate?(this.startX0=b,this.endX0=c):(this.startY0=b,this.endY0=c)}this.categoryAxis&&(this.start0=this.start,this.end0=this.end,
this.startTime0=this.startTime,this.endTime0=this.endTime)},fixCursor:function(){this.chartCursor&&this.chartCursor.fixPosition();this.prevCursorItem=null},handleCursorMove:function(a){f.AmSerialChart.base.handleCursorMove.call(this,a);var b=a.target,c=this.categoryAxis;if(a.panning)this.handleCursorHide(a);else if(this.chartData&&!b.isHidden){var d=this.graphs;if(d){var g;g=c.xToIndex(this.rotate?a.y:a.x);if(g=this.chartData[g]){var h,e,l,k;if(b.oneBalloonOnly&&b.valueBalloonsEnabled){var m=Infinity;
for(h=d.length-1;0<=h;h--)if(e=d[h],e.balloon.enabled&&e.showBalloon&&!e.hidden){l=e.valueAxis.id;l=g.axes[l].graphs[e.id];if(b.showNextAvailable&&isNaN(l.y)&&(l=this.getNextItem(l),!l))continue;l=l.y;"top"==e.showBalloonAt&&(l=0);"bottom"==e.showBalloonAt&&(l=this.height);var p=b.mouseX,n=b.mouseY;l=this.rotate?Math.abs(p-l):Math.abs(n-l);l<m&&(m=l,k=e)}b.mostCloseGraph=k}if(this.prevCursorItem!=g||k!=this.prevMostCloseGraph){m=[];for(h=0;h<d.length;h++){e=d[h];l=e.valueAxis.id;l=g.axes[l].graphs[e.id];
if(b.showNextAvailable&&isNaN(l.y)&&(l=this.getNextItem(l),!l&&e.balloon)){e.balloon.hide();continue}k&&e!=k?(e.showGraphBalloon(l,b.pointer,!1,b.graphBulletSize,b.graphBulletAlpha),e.balloon.hide(0)):b.valueBalloonsEnabled?(e.balloon.showBullet=b.bulletsEnabled,e.balloon.bulletSize=b.bulletSize/2,a.hideBalloons||(e.showGraphBalloon(l,b.pointer,!1,b.graphBulletSize,b.graphBulletAlpha),e.balloon.set&&m.push({balloon:e.balloon,y:e.balloon.pointToY}))):(e.currentDataItem=l,e.resizeBullet(l,b.graphBulletSize,
b.graphBulletAlpha))}b.avoidBalloonOverlapping&&this.arrangeBalloons(m);this.prevCursorItem=g}this.prevMostCloseGraph=k}}c.showBalloon(a.x,a.y,b.categoryBalloonDateFormat,a.skip);this.updateLegendValues()}},handleCursorHide:function(a){f.AmSerialChart.base.handleCursorHide.call(this,a);a=this.categoryAxis;this.prevCursorItem=null;this.updateLegendValues();a&&a.hideBalloon();a=this.graphs;var b;for(b=0;b<a.length;b++)a[b].currentDataItem=null},handleCursorPanning:function(a){var b=a.target,c,d=a.deltaX,
g=a.deltaY,h=a.delta2X,e=a.delta2Y;a=!1;if(this.rotate){isNaN(h)&&(h=d,a=!0);var l=this.endX0;c=this.startX0;var k=l-c,l=l-k*h,m=k;a||(m=0);a=f.fitToBounds(c-k*d,0,1-m)}else isNaN(e)&&(e=g,a=!0),l=this.endY0,c=this.startY0,k=l-c,l+=k*g,m=k,a||(m=0),a=f.fitToBounds(c+k*e,0,1-m);c=f.fitToBounds(l,m,1);var p;b.valueZoomable&&(p=this.relativeZoomValueAxes(this.valueAxes,a,c));var n;c=this.categoryAxis;this.rotate&&(d=g,h=e);a=!1;isNaN(h)&&(h=d,a=!0);if(b.zoomable&&(0<Math.abs(d)||0<Math.abs(h)))if(c.parseDates&&
!c.equalSpacing){if(e=this.startTime0,g=this.endTime0,c=g-e,h*=c,k=this.firstTime,l=this.lastTime,m=c,a||(m=0),a=Math.round(f.fitToBounds(e-c*d,k,l-m)),h=Math.round(f.fitToBounds(g-h,k+m,l)),this.startTime!=a||this.endTime!=h)n={chart:this,target:b,type:"zoomed",start:a,end:h},this.skipZoomed=!0,b.fire(n),this.zoom(a,h),n=!0}else if(e=this.start0,g=this.end0,c=g-e,d=Math.round(c*d),h=Math.round(c*h),k=this.chartData.length-1,a||(c=0),a=f.fitToBounds(e-d,0,k-c),c=f.fitToBounds(g-h,c,k),this.start!=
a||this.end!=c)this.skipZoomed=!0,b.fire({chart:this,target:b,type:"zoomed",start:a,end:c}),this.zoom(a,c),n=!0;!n&&p&&this.updateAfterValueZoom()},arrangeBalloons:function(a){var b=this.plotAreaHeight;a.sort(this.compareY);var c,d,f,h=this.plotAreaWidth,e=a.length;for(c=0;c<e;c++)d=a[c].balloon,d.setBounds(0,0,h,b),d.restorePrevious(),d.draw(),b=d.yPos-3;a.reverse();for(c=0;c<e;c++){d=a[c].balloon;var b=d.bottom,l=d.bottom-d.yPos;0<c&&b-l<f+3&&(d.setBounds(0,f+3,h,f+l+3),d.restorePrevious(),d.draw());
d.set&&d.set.show();f=d.bottom}},compareY:function(a,b){return a.y<b.y?1:-1}})})();(function(){var f=window.AmCharts;f.Cuboid=f.Class({construct:function(a,b,c,d,f,h,e,l,k,m,p,n,u,v,x,E,t){this.set=a.set();this.container=a;this.h=Math.round(c);this.w=Math.round(b);this.dx=d;this.dy=f;this.colors=h;this.alpha=e;this.bwidth=l;this.bcolor=k;this.balpha=m;this.dashLength=v;this.topRadius=E;this.pattern=x;this.rotate=u;this.bcn=t;u?0>b&&0===p&&(p=180):0>c&&270==p&&(p=90);this.gradientRotation=p;0===d&&0===f&&(this.cornerRadius=n);this.draw()},draw:function(){var a=this.set;a.clear();
var b=this.container,c=b.chart,d=this.w,g=this.h,h=this.dx,e=this.dy,l=this.colors,k=this.alpha,m=this.bwidth,p=this.bcolor,n=this.balpha,u=this.gradientRotation,v=this.cornerRadius,x=this.dashLength,E=this.pattern,t=this.topRadius,r=this.bcn,B=l,q=l;"object"==typeof l&&(B=l[0],q=l[l.length-1]);var w,y,C,F,D,A,z,L,M,Q=k;E&&(k=0);var G,H,I,J,K=this.rotate;if(0<Math.abs(h)||0<Math.abs(e))if(isNaN(t))z=q,q=f.adjustLuminosity(B,-.2),q=f.adjustLuminosity(B,-.2),w=f.polygon(b,[0,h,d+h,d,0],[0,e,e,0,0],
q,k,1,p,0,u),0<n&&(M=f.line(b,[0,h,d+h],[0,e,e],p,n,m,x)),y=f.polygon(b,[0,0,d,d,0],[0,g,g,0,0],q,k,1,p,0,u),y.translate(h,e),0<n&&(C=f.line(b,[h,h],[e,e+g],p,n,m,x)),F=f.polygon(b,[0,0,h,h,0],[0,g,g+e,e,0],q,k,1,p,0,u),D=f.polygon(b,[d,d,d+h,d+h,d],[0,g,g+e,e,0],q,k,1,p,0,u),0<n&&(A=f.line(b,[d,d+h,d+h,d],[0,e,g+e,g],p,n,m,x)),q=f.adjustLuminosity(z,.2),z=f.polygon(b,[0,h,d+h,d,0],[g,g+e,g+e,g,g],q,k,1,p,0,u),0<n&&(L=f.line(b,[0,h,d+h],[g,g+e,g+e],p,n,m,x));else{var N,O,P;K?(N=g/2,q=h/2,P=g/2,O=
d+h/2,H=Math.abs(g/2),G=Math.abs(h/2)):(q=d/2,N=e/2,O=d/2,P=g+e/2+1,G=Math.abs(d/2),H=Math.abs(e/2));I=G*t;J=H*t;.1<G&&.1<G&&(w=f.circle(b,G,B,k,m,p,n,!1,H),w.translate(q,N));.1<I&&.1<I&&(z=f.circle(b,I,f.adjustLuminosity(B,.5),k,m,p,n,!1,J),z.translate(O,P))}k=Q;1>Math.abs(g)&&(g=0);1>Math.abs(d)&&(d=0);!isNaN(t)&&(0<Math.abs(h)||0<Math.abs(e))?(l=[B],l={fill:l,stroke:p,"stroke-width":m,"stroke-opacity":n,"fill-opacity":k},K?(k="M0,0 L"+d+","+(g/2-g/2*t),m=" B",0<d&&(m=" A"),f.VML?(k+=m+Math.round(d-
I)+","+Math.round(g/2-J)+","+Math.round(d+I)+","+Math.round(g/2+J)+","+d+",0,"+d+","+g,k=k+(" L0,"+g)+(m+Math.round(-G)+","+Math.round(g/2-H)+","+Math.round(G)+","+Math.round(g/2+H)+",0,"+g+",0,0")):(k+="A"+I+","+J+",0,0,0,"+d+","+(g-g/2*(1-t))+"L0,"+g,k+="A"+G+","+H+",0,0,1,0,0"),G=90):(m=d/2-d/2*t,k="M0,0 L"+m+","+g,f.VML?(k="M0,0 L"+m+","+g,m=" B",0>g&&(m=" A"),k+=m+Math.round(d/2-I)+","+Math.round(g-J)+","+Math.round(d/2+I)+","+Math.round(g+J)+",0,"+g+","+d+","+g,k+=" L"+d+",0",k+=m+Math.round(d/
2+G)+","+Math.round(H)+","+Math.round(d/2-G)+","+Math.round(-H)+","+d+",0,0,0"):(k+="A"+I+","+J+",0,0,0,"+(d-d/2*(1-t))+","+g+"L"+d+",0",k+="A"+G+","+H+",0,0,1,0,0"),G=180),b=b.path(k).attr(l),b.gradient("linearGradient",[B,f.adjustLuminosity(B,-.3),f.adjustLuminosity(B,-.3),B],G),K?b.translate(h/2,0):b.translate(0,e/2)):b=0===g?f.line(b,[0,d],[0,0],p,n,m,x):0===d?f.line(b,[0,0],[0,g],p,n,m,x):0<v?f.rect(b,d,g,l,k,m,p,n,v,u,x):f.polygon(b,[0,0,d,d,0],[0,g,g,0,0],l,k,m,p,n,u,!1,x);d=isNaN(t)?0>g?[w,
M,y,C,F,D,A,z,L,b]:[z,L,y,C,F,D,w,M,A,b]:K?0<d?[w,b,z]:[z,b,w]:0>g?[w,b,z]:[z,b,w];f.setCN(c,b,r+"front");f.setCN(c,y,r+"back");f.setCN(c,z,r+"top");f.setCN(c,w,r+"bottom");f.setCN(c,F,r+"left");f.setCN(c,D,r+"right");for(w=0;w<d.length;w++)if(y=d[w])a.push(y),f.setCN(c,y,r+"element");E&&b.pattern(E,NaN,c.path)},width:function(a){isNaN(a)&&(a=0);this.w=Math.round(a);this.draw()},height:function(a){isNaN(a)&&(a=0);this.h=Math.round(a);this.draw()},animateHeight:function(a,b){var c=this;c.animationFinished=
!1;c.easing=b;c.totalFrames=a*f.updateRate;c.rh=c.h;c.frame=0;c.height(1);setTimeout(function(){c.updateHeight.call(c)},1E3/f.updateRate)},updateHeight:function(){var a=this;a.frame++;var b=a.totalFrames;a.frame<=b?(b=a.easing(0,a.frame,1,a.rh-1,b),a.height(b),window.requestAnimationFrame?window.requestAnimationFrame(function(){a.updateHeight.call(a)}):setTimeout(function(){a.updateHeight.call(a)},1E3/f.updateRate)):(a.height(a.rh),a.animationFinished=!0)},animateWidth:function(a,b){var c=this;c.animationFinished=
!1;c.easing=b;c.totalFrames=a*f.updateRate;c.rw=c.w;c.frame=0;c.width(1);setTimeout(function(){c.updateWidth.call(c)},1E3/f.updateRate)},updateWidth:function(){var a=this;a.frame++;var b=a.totalFrames;a.frame<=b?(b=a.easing(0,a.frame,1,a.rw-1,b),a.width(b),window.requestAnimationFrame?window.requestAnimationFrame(function(){a.updateWidth.call(a)}):setTimeout(function(){a.updateWidth.call(a)},1E3/f.updateRate)):(a.width(a.rw),a.animationFinished=!0)}})})();(function(){var f=window.AmCharts;f.CategoryAxis=f.Class({inherits:f.AxisBase,construct:function(a){this.cname="CategoryAxis";f.CategoryAxis.base.construct.call(this,a);this.minPeriod="DD";this.equalSpacing=this.parseDates=!1;this.position="bottom";this.startOnAxis=!1;this.gridPosition="middle";this.safeDistance=30;this.stickBalloonToCategory=!1;f.applyTheme(this,a,this.cname)},draw:function(){f.CategoryAxis.base.draw.call(this);this.generateDFObject();var a=this.chart.chartData;this.data=a;this.labelRotationR=
this.labelRotation;this.type=null;if(f.ifArray(a)){var b,c=this.chart;"scrollbar"!=this.id?(f.setCN(c,this.set,"category-axis"),f.setCN(c,this.labelsSet,"category-axis"),f.setCN(c,this.axisLine.axisSet,"category-axis")):this.bcn=this.id+"-";var d=this.start,g=this.labelFrequency,h=0,e=this.end-d+1,l=this.gridCountR,k=this.showFirstLabel,m=this.showLastLabel,p,n="",n=f.extractPeriod(this.minPeriod),u=f.getPeriodDuration(n.period,n.count),v,x,E,t,r,B=this.rotate,q=this.firstDayOfWeek,w=this.boldPeriodBeginning;
b=f.resetDateToMin(new Date(a[a.length-1].time+1.05*u),this.minPeriod,1,q).getTime();this.firstTime=c.firstTime;this.endTime>b&&(this.endTime=b);r=this.minorGridEnabled;x=this.gridAlpha;var y=0,C=0;if(this.widthField)for(b=this.start;b<=this.end;b++)if(t=this.data[b]){var F=Number(this.data[b].dataContext[this.widthField]);isNaN(F)||(y+=F,t.widthValue=F)}if(this.parseDates&&!this.equalSpacing)this.lastTime=a[a.length-1].time,this.maxTime=f.resetDateToMin(new Date(this.lastTime+1.05*u),this.minPeriod,
1,q).getTime(),this.timeDifference=this.endTime-this.startTime,this.parseDatesDraw();else if(!this.parseDates){if(this.cellWidth=this.getStepWidth(e),e<l&&(l=e),h+=this.start,this.stepWidth=this.getStepWidth(e),0<l)for(q=Math.floor(e/l),t=this.chooseMinorFrequency(q),e=h,e/2==Math.round(e/2)&&e--,0>e&&(e=0),w=0,this.widthField&&(e=this.start),this.end-e+1>=this.autoRotateCount&&(this.labelRotationR=this.autoRotateAngle),b=e;b<=this.end+2;b++){l=!1;0<=b&&b<this.data.length?(v=this.data[b],n=v.category,
l=v.forceShow):n="";if(r&&!isNaN(t))if(b/t==Math.round(b/t)||l)b/q==Math.round(b/q)||l||(this.gridAlpha=this.minorGridAlpha,n=void 0);else continue;else if(b/q!=Math.round(b/q)&&!l)continue;e=this.getCoordinate(b-h);l=0;"start"==this.gridPosition&&(e-=this.cellWidth/2,l=this.cellWidth/2);p=!0;E=l;"start"==this.tickPosition&&(E=0,p=!1,l=0);if(b==d&&!k||b==this.end&&!m)n=void 0;Math.round(w/g)!=w/g&&(n=void 0);w++;a=this.cellWidth;B&&(a=NaN,this.ignoreAxisWidth||!c.autoMargins)&&(a="right"==this.position?
c.marginRight:c.marginLeft,a-=this.tickLength+10);this.labelFunction&&v&&(n=this.labelFunction(n,v,this));n=f.fixBrakes(n);u=!1;this.boldLabels&&(u=!0);b>this.end&&"start"==this.tickPosition&&(n=" ");this.rotate&&this.inside&&(l-=2);isNaN(v.widthValue)||(v.percentWidthValue=v.widthValue/y*100,a=this.rotate?this.height*v.widthValue/y:this.width*v.widthValue/y,e=C,C+=a,l=a/2);p=new this.axisItemRenderer(this,e,n,p,a,l,void 0,u,E,!1,v.labelColor,v.className);p.serialDataItem=v;this.pushAxisItem(p);this.gridAlpha=
x}}else if(this.parseDates&&this.equalSpacing){h=this.start;this.startTime=this.data[this.start].time;this.endTime=this.data[this.end].time;this.timeDifference=this.endTime-this.startTime;b=this.choosePeriod(0);g=b.period;v=b.count;b=f.getPeriodDuration(g,v);b<u&&(g=n.period,v=n.count,b=u);x=g;"WW"==x&&(x="DD");this.currentDateFormat=this.dateFormatsObject[x];this.stepWidth=this.getStepWidth(e);l=Math.ceil(this.timeDifference/b)+1;n=f.resetDateToMin(new Date(this.startTime-b),g,v,q).getTime();this.cellWidth=
this.getStepWidth(e);e=Math.round(n/b);d=-1;e/2==Math.round(e/2)&&(d=-2,n-=b);e=this.start;e/2==Math.round(e/2)&&e--;0>e&&(e=0);C=this.end+2;C>=this.data.length&&(C=this.data.length);a=!1;a=!k;this.previousPos=-1E3;20<this.labelRotationR&&(this.safeDistance=5);F=e;if(this.data[e].time!=f.resetDateToMin(new Date(this.data[e].time),g,v,q).getTime()){var u=0,D=n;for(b=e;b<C;b++)t=this.data[b].time,this.checkPeriodChange(g,v,t,D)&&(u++,2<=u&&(F=b,b=C),D=t)}r&&1<v&&(t=this.chooseMinorFrequency(v),f.getPeriodDuration(g,
t));if(0<this.gridCountR)for(b=e;b<C;b++)if(t=this.data[b].time,this.checkPeriodChange(g,v,t,n)&&b>=F){e=this.getCoordinate(b-this.start);r=!1;this.nextPeriod[x]&&(r=this.checkPeriodChange(this.nextPeriod[x],1,t,n,x))&&f.resetDateToMin(new Date(t),this.nextPeriod[x],1,q).getTime()!=t&&(r=!1);u=!1;r&&this.markPeriodChange?(r=this.dateFormatsObject[this.nextPeriod[x]],u=!0):r=this.dateFormatsObject[x];n=f.formatDate(new Date(t),r,c);if(b==d&&!k||b==l&&!m)n=" ";a?a=!1:(w||(u=!1),e-this.previousPos>this.safeDistance*
Math.cos(this.labelRotationR*Math.PI/180)&&(this.labelFunction&&(n=this.labelFunction(n,new Date(t),this,g,v,E)),this.boldLabels&&(u=!0),p=new this.axisItemRenderer(this,e,n,void 0,void 0,void 0,void 0,u),r=p.graphics(),this.pushAxisItem(p),r=r.getBBox().width,f.isModern||(r-=e),this.previousPos=e+r));E=n=t}}for(b=k=0;b<this.data.length;b++)if(t=this.data[b])this.parseDates&&!this.equalSpacing?(m=t.time,d=this.cellWidth,"MM"==this.minPeriod&&(d=864E5*f.daysInMonth(new Date(m))*this.stepWidth,t.cellWidth=
d),m=Math.round((m-this.startTime)*this.stepWidth+d/2)):m=this.getCoordinate(b-h),t.x[this.id]=m;if(this.widthField)for(b=this.start;b<=this.end;b++)t=this.data[b],d=t.widthValue,t.percentWidthValue=d/y*100,this.rotate?(m=this.height*d/y/2+k,k=this.height*d/y+k):(m=this.width*d/y/2+k,k=this.width*d/y+k),t.x[this.id]=m;y=this.guides.length;for(b=0;b<y;b++)if(k=this.guides[b],q=q=q=r=d=NaN,m=k.above,k.toCategory&&(q=c.getCategoryIndexByValue(k.toCategory),isNaN(q)||(d=this.getCoordinate(q-h),k.expand&&
(d+=this.cellWidth/2),p=new this.axisItemRenderer(this,d,"",!0,NaN,NaN,k),this.pushAxisItem(p,m))),k.category&&(q=c.getCategoryIndexByValue(k.category),isNaN(q)||(r=this.getCoordinate(q-h),k.expand&&(r-=this.cellWidth/2),q=(d-r)/2,p=new this.axisItemRenderer(this,r,k.label,!0,NaN,q,k),this.pushAxisItem(p,m))),w=c.dataDateFormat,k.toDate&&(!w||k.toDate instanceof Date||(k.toDate=k.toDate.toString()+" |"),k.toDate=f.getDate(k.toDate,w),this.equalSpacing?(q=c.getClosestIndex(this.data,"time",k.toDate.getTime(),
!1,0,this.data.length-1),isNaN(q)||(d=this.getCoordinate(q-h))):d=(k.toDate.getTime()-this.startTime)*this.stepWidth,p=new this.axisItemRenderer(this,d,"",!0,NaN,NaN,k),this.pushAxisItem(p,m)),k.date&&(!w||k.date instanceof Date||(k.date=k.date.toString()+" |"),k.date=f.getDate(k.date,w),this.equalSpacing?(q=c.getClosestIndex(this.data,"time",k.date.getTime(),!1,0,this.data.length-1),isNaN(q)||(r=this.getCoordinate(q-h))):r=(k.date.getTime()-this.startTime)*this.stepWidth,q=(d-r)/2,p=!0,k.toDate&&
(p=!1),p="H"==this.orientation?new this.axisItemRenderer(this,r,k.label,p,2*q,NaN,k):new this.axisItemRenderer(this,r,k.label,!1,NaN,q,k),this.pushAxisItem(p,m)),k.balloonText&&p&&(q=p.label)&&this.addEventListeners(q,k),0<d||0<r){q=!1;if(this.rotate){if(d<this.height||r<this.height)q=!0}else if(d<this.width||r<this.width)q=!0;q&&(d=new this.guideFillRenderer(this,r,d,k),r=d.graphics(),this.pushAxisItem(d,m),k.graphics=r,r.index=b,k.balloonText&&this.addEventListeners(r,k))}if(c=c.chartCursor)B?c.fixHeight(this.cellWidth):
(c.fixWidth(this.cellWidth),c.fullWidth&&this.balloon&&(this.balloon.minWidth=this.cellWidth));this.previousHeight=A}this.axisCreated=!0;this.set.translate(this.x,this.y);this.labelsSet.translate(this.x,this.y);this.labelsSet.show();this.positionTitle();(B=this.axisLine.set)&&B.toFront();var A=this.getBBox().height;2<A-this.previousHeight&&this.autoWrap&&!this.parseDates&&(this.axisCreated=this.chart.marginsUpdated=!1)},xToIndex:function(a){var b=this.data,c=this.chart,d=c.rotate,g=this.stepWidth,
h;if(this.parseDates&&!this.equalSpacing)a=this.startTime+Math.round(a/g)-this.minDuration()/2,h=c.getClosestIndex(b,"time",a,!1,this.start,this.end+1);else if(this.widthField)for(c=Infinity,g=this.start;g<=this.end;g++){var e=this.data[g];e&&(e=Math.abs(e.x[this.id]-a),e<c&&(c=e,h=g))}else this.startOnAxis||(a-=g/2),h=this.start+Math.round(a/g);h=f.fitToBounds(h,0,b.length-1);var l;b[h]&&(l=b[h].x[this.id]);d?l>this.height+1&&h--:l>this.width+1&&h--;0>l&&h++;return h=f.fitToBounds(h,0,b.length-1)},
dateToCoordinate:function(a){return this.parseDates&&!this.equalSpacing?(a.getTime()-this.startTime)*this.stepWidth:this.parseDates&&this.equalSpacing?(a=this.chart.getClosestIndex(this.data,"time",a.getTime(),!1,0,this.data.length-1),this.getCoordinate(a-this.start)):NaN},categoryToCoordinate:function(a){if(this.chart){if(this.parseDates)return this.dateToCoordinate(new Date(a));a=this.chart.getCategoryIndexByValue(a);if(!isNaN(a))return this.getCoordinate(a-this.start)}else return NaN},coordinateToDate:function(a){return this.equalSpacing?
(a=this.xToIndex(a),new Date(this.data[a].time)):new Date(this.startTime+a/this.stepWidth)},coordinateToValue:function(a){a=this.xToIndex(a);if(a=this.data[a])return this.parseDates?a.time:a.category},getCoordinate:function(a){a*=this.stepWidth;this.startOnAxis||(a+=this.stepWidth/2);return Math.round(a)},formatValue:function(a,b){b||(b=this.currentDateFormat);this.parseDates&&(a=f.formatDate(new Date(a),b,this.chart));return a},showBalloonAt:function(a,b){void 0===b&&(b=this.parseDates?this.dateToCoordinate(new Date(a)):
this.categoryToCoordinate(a));return this.adjustBalloonCoordinate(b)},formatBalloonText:function(a,b,c){var d="",g="",h=this.chart,e=this.data[b];if(e)if(this.parseDates)d=f.formatDate(e.category,c,h),b=f.changeDate(new Date(e.category),this.minPeriod,1),g=f.formatDate(b,c,h),-1!=d.indexOf("fff")&&(d=f.formatMilliseconds(d,e.category),g=f.formatMilliseconds(g,b));else{var l;this.data[b+1]&&(l=this.data[b+1]);d=f.fixNewLines(e.category);l&&(g=f.fixNewLines(l.category))}a=a.replace(/\[\[category\]\]/g,
String(d));return a=a.replace(/\[\[toCategory\]\]/g,String(g))},adjustBalloonCoordinate:function(a,b){var c=this.xToIndex(a),d=this.chart.chartCursor;if(this.stickBalloonToCategory){var f=this.data[c];f&&(a=f.x[this.id]);this.stickBalloonToStart&&(a-=this.cellWidth/2);var h=0;if(d){var e=d.limitToGraph;if(e){var l=e.valueAxis.id;e.hidden||(h=f.axes[l].graphs[e.id].y)}this.rotate?("left"==this.position?(e&&(h-=d.width),0<h&&(h=0)):0>h&&(h=0),d.fixHLine(a,h)):("top"==this.position?(e&&(h-=d.height),
0<h&&(h=0)):0>h&&(h=0),d.fixVLine(a,h))}}d&&!b&&(d.setIndex(c),this.parseDates&&d.setTimestamp(this.coordinateToDate(a).getTime()));return a}})})();

(function(){var k=window.AmCharts;k.AmSlicedChart=k.Class({inherits:k.AmChart,construct:function(a){this.createEvents("rollOverSlice","rollOutSlice","clickSlice","pullOutSlice","pullInSlice","rightClickSlice");k.AmSlicedChart.base.construct.call(this,a);this.colors="#FF0F00 #FF6600 #FF9E01 #FCD202 #F8FF01 #B0DE09 #04D215 #0D8ECF #0D52D1 #2A0CD0 #8A0CCF #CD0D74 #754DEB #DDDDDD #999999 #333333 #000000 #57032A #CA9726 #990000 #4B0C25".split(" ");this.alpha=1;this.groupPercent=0;this.groupedTitle="Other";
this.groupedPulled=!1;this.groupedAlpha=1;this.marginLeft=0;this.marginBottom=this.marginTop=10;this.marginRight=0;this.hoverAlpha=1;this.outlineColor="#FFFFFF";this.outlineAlpha=0;this.outlineThickness=1;this.startAlpha=0;this.startDuration=1;this.startEffect="bounce";this.sequencedAnimation=!0;this.pullOutDuration=1;this.pullOutEffect="bounce";this.pullOnHover=this.pullOutOnlyOne=!1;this.labelsEnabled=!0;this.labelTickColor="#000000";this.labelTickAlpha=.2;this.hideLabelsPercent=0;this.urlTarget=
"_self";this.autoMarginOffset=10;this.gradientRatio=[];this.maxLabelWidth=200;this.accessibleLabel="[[title]]: [[percents]]% [[value]] [[description]]";k.applyTheme(this,a,"AmSlicedChart")},initChart:function(){k.AmSlicedChart.base.initChart.call(this);this.dataChanged&&(this.parseData(),this.dispatchDataUpdated=!0,this.dataChanged=!1,this.setLegendData(this.chartData));this.drawChart()},handleLegendEvent:function(a){var b=a.type,c=a.dataItem,d=this.legend;if(c.wedge&&c){var g=c.hidden;a=a.event;
switch(b){case "clickMarker":g||d.switchable||this.clickSlice(c,a);break;case "clickLabel":g||this.clickSlice(c,a,!1);break;case "rollOverItem":g||this.rollOverSlice(c,!1,a);break;case "rollOutItem":g||this.rollOutSlice(c,a);break;case "hideItem":this.hideSlice(c,a);break;case "showItem":this.showSlice(c,a)}}},invalidateVisibility:function(){this.recalculatePercents();this.initChart();var a=this.legend;a&&a.invalidateSize()},addEventListeners:function(a,b){var c=this;a.mouseover(function(a){c.rollOverSlice(b,
!0,a)}).mouseout(function(a){c.rollOutSlice(b,a)}).touchend(function(a){c.rollOverSlice(b,a)}).mouseup(function(a){c.clickSlice(b,a)}).contextmenu(function(a){c.handleRightClick(b,a)})},formatString:function(a,b,c){a=k.formatValue(a,b,["value"],this.nf,"",this.usePrefixes,this.prefixesOfSmallNumbers,this.prefixesOfBigNumbers);var d=this.pf.precision;isNaN(this.tempPrec)||(this.pf.precision=this.tempPrec);a=k.formatValue(a,b,["percents"],this.pf);a=k.massReplace(a,{"[[title]]":b.title,"[[description]]":b.description});
this.pf.precision=d;-1!=a.indexOf("[[")&&(a=k.formatDataContextValue(a,b.dataContext));a=c?k.fixNewLines(a):k.fixBrakes(a);return a=k.cleanFromEmpty(a)},startSlices:function(){var a;for(a=0;a<this.chartData.length;a++)0<this.startDuration&&this.sequencedAnimation?this.setStartTO(a):this.startSlice(this.chartData[a])},setStartTO:function(a){var b=this;a=setTimeout(function(){b.startSequenced.call(b)},b.startDuration/b.chartData.length*500*a);b.timeOuts.push(a)},pullSlices:function(a){var b=this.chartData,
c;for(c=0;c<b.length;c++){var d=b[c];d.pulled&&this.pullSlice(d,1,a)}},startSequenced:function(){var a=this.chartData,b;for(b=0;b<a.length;b++)if(!a[b].started){this.startSlice(this.chartData[b]);break}},startSlice:function(a){a.started=!0;var b=a.wedge,c=this.startDuration,d=a.labelSet;b&&0<c&&(0<a.alpha&&b.show(),b.translate(a.startX,a.startY),this.animatable.push(b),b.animate({opacity:1,translate:"0,0"},c,this.startEffect));d&&0<c&&(0<a.alpha&&d.show(),d.translate(a.startX,a.startY),d.animate({opacity:1,
translate:"0,0"},c,this.startEffect))},showLabels:function(){var a=this.chartData,b;for(b=0;b<a.length;b++){var c=a[b];if(0<c.alpha){var d=c.label;d&&d.show();(c=c.tick)&&c.show()}}},showSlice:function(a){isNaN(a)?a.hidden=!1:this.chartData[a].hidden=!1;this.invalidateVisibility()},hideSlice:function(a){isNaN(a)?a.hidden=!0:this.chartData[a].hidden=!0;this.hideBalloon();this.invalidateVisibility()},rollOverSlice:function(a,b,c){isNaN(a)||(a=this.chartData[a]);clearTimeout(this.hoverInt);if(!a.hidden){this.pullOnHover&&
this.pullSlice(a,1);1>this.hoverAlpha&&a.wedge&&a.wedge.attr({opacity:this.hoverAlpha});var d=a.balloonX,g=a.balloonY;a.pulled&&(d+=a.pullX,g+=a.pullY);var f=this.formatString(this.balloonText,a,!0),h=this.balloonFunction;h&&(f=h(a,f));h=k.adjustLuminosity(a.color,-.15);f?this.showBalloon(f,h,b,d,g):this.hideBalloon();0===a.value&&this.hideBalloon();this.fire({type:"rollOverSlice",dataItem:a,chart:this,event:c})}},rollOutSlice:function(a,b){isNaN(a)||(a=this.chartData[a]);a.wedge&&a.wedge.attr({opacity:1});
this.hideBalloon();this.fire({type:"rollOutSlice",dataItem:a,chart:this,event:b})},clickSlice:function(a,b,c){this.checkTouchDuration(b)&&(isNaN(a)||(a=this.chartData[a]),a.pulled?this.pullSlice(a,0):this.pullSlice(a,1),k.getURL(a.url,this.urlTarget),c||this.fire({type:"clickSlice",dataItem:a,chart:this,event:b}))},handleRightClick:function(a,b){isNaN(a)||(a=this.chartData[a]);this.fire({type:"rightClickSlice",dataItem:a,chart:this,event:b})},drawTicks:function(){var a=this.chartData,b;for(b=0;b<
a.length;b++){var c=a[b];if(c.label&&!c.skipTick){var d=c.ty,d=k.line(this.container,[c.tx0,c.tx,c.tx2],[c.ty0,d,d],this.labelTickColor,this.labelTickAlpha);k.setCN(this,d,this.type+"-tick");k.setCN(this,d,c.className,!0);c.tick=d;c.wedge.push(d);"AmFunnelChart"==this.cname&&d.toBack()}}},initialStart:function(){var a=this,b=a.startDuration,c=setTimeout(function(){a.showLabels.call(a)},1E3*b);a.timeOuts.push(c);a.chartCreated?a.pullSlices(!0):(a.startSlices(),0<b?(b=setTimeout(function(){a.pullSlices.call(a)},
1200*b),a.timeOuts.push(b)):a.pullSlices(!0))},pullSlice:function(a,b,c){var d=this.pullOutDuration;!0===c&&(d=0);if(c=a.wedge)0<d?(c.animate({translate:b*a.pullX+","+b*a.pullY},d,this.pullOutEffect),a.labelSet&&a.labelSet.animate({translate:b*a.pullX+","+b*a.pullY},d,this.pullOutEffect)):(a.labelSet&&a.labelSet.translate(b*a.pullX,b*a.pullY),c.translate(b*a.pullX,b*a.pullY));1==b?(a.pulled=!0,this.pullOutOnlyOne&&this.pullInAll(a.index),a={type:"pullOutSlice",dataItem:a,chart:this}):(a.pulled=!1,
a={type:"pullInSlice",dataItem:a,chart:this});this.fire(a)},pullInAll:function(a){var b=this.chartData,c;for(c=0;c<this.chartData.length;c++)c!=a&&b[c].pulled&&this.pullSlice(b[c],0)},pullOutAll:function(){var a=this.chartData,b;for(b=0;b<a.length;b++)a[b].pulled||this.pullSlice(a[b],1)},parseData:function(){var a=[];this.chartData=a;var b=this.dataProvider;isNaN(this.pieAlpha)||(this.alpha=this.pieAlpha);if(void 0!==b){var c=b.length,d=0,g,f,h;for(g=0;g<c;g++){f={};var e=b[g];f.dataContext=e;null!==
e[this.valueField]&&(f.value=Number(e[this.valueField]));(h=e[this.titleField])||(h="");f.title=h;f.pulled=k.toBoolean(e[this.pulledField],!1);(h=e[this.descriptionField])||(h="");f.description=h;f.labelRadius=Number(e[this.labelRadiusField]);f.switchable=!0;f.className=e[this.classNameField];f.url=e[this.urlField];h=e[this.patternField];!h&&this.patterns&&(h=this.patterns[g]);f.pattern=h;f.visibleInLegend=k.toBoolean(e[this.visibleInLegendField],!0);h=e[this.alphaField];f.alpha=void 0!==h?Number(h):
this.alpha;h=e[this.colorField];void 0!==h&&(f.color=h);f.labelColor=k.toColor(e[this.labelColorField]);d+=f.value;f.hidden=!1;a[g]=f}for(g=b=0;g<c;g++)f=a[g],f.percents=f.value/d*100,f.percents<this.groupPercent&&b++;1<b&&(this.groupValue=0,this.removeSmallSlices(),a.push({title:this.groupedTitle,value:this.groupValue,percents:this.groupValue/d*100,pulled:this.groupedPulled,color:this.groupedColor,url:this.groupedUrl,description:this.groupedDescription,alpha:this.groupedAlpha,pattern:this.groupedPattern,
className:this.groupedClassName,dataContext:{}}));c=this.baseColor;c||(c=this.pieBaseColor);d=this.brightnessStep;d||(d=this.pieBrightnessStep);for(g=0;g<a.length;g++)c?h=k.adjustLuminosity(c,g*d/100):(h=this.colors[g],void 0===h&&(h=k.randomColor())),void 0===a[g].color&&(a[g].color=h);this.recalculatePercents()}},recalculatePercents:function(){var a=this.chartData,b=0,c,d;for(c=0;c<a.length;c++)d=a[c],!d.hidden&&0<d.value&&(b+=d.value);for(c=0;c<a.length;c++)d=this.chartData[c],d.percents=!d.hidden&&
0<d.value?100*d.value/b:0},removeSmallSlices:function(){var a=this.chartData,b;for(b=a.length-1;0<=b;b--)a[b].percents<this.groupPercent&&(this.groupValue+=a[b].value,a.splice(b,1))},animateAgain:function(){var a=this;a.startSlices();for(var b=0;b<a.chartData.length;b++){var c=a.chartData[b];c.started=!1;var d=c.wedge;d&&(d.setAttr("opacity",a.startAlpha),d.translate(c.startX,c.startY));if(d=c.labelSet)d.setAttr("opacity",a.startAlpha),d.translate(c.startX,c.startY)}b=a.startDuration;0<b?(b=setTimeout(function(){a.pullSlices.call(a)},
1200*b),a.timeOuts.push(b)):a.pullSlices()},measureMaxLabel:function(){var a=this.chartData,b=0,c;for(c=0;c<a.length;c++){var d=a[c],g=this.formatString(this.labelText,d),f=this.labelFunction;f&&(g=f(d,g));d=k.text(this.container,g,this.color,this.fontFamily,this.fontSize);g=d.getBBox().width;g>b&&(b=g);d.remove()}return b}})})();(function(){var k=window.AmCharts;k.AmPieChart=k.Class({inherits:k.AmSlicedChart,construct:function(a){this.type="pie";k.AmPieChart.base.construct.call(this,a);this.cname="AmPieChart";this.pieBrightnessStep=30;this.minRadius=10;this.depth3D=0;this.startAngle=90;this.angle=this.innerRadius=0;this.startRadius="500%";this.pullOutRadius="20%";this.labelRadius=20;this.labelText="[[title]]: [[percents]]%";this.balloonText="[[title]]: [[percents]]% ([[value]])\n[[description]]";this.previousScale=1;this.adjustPrecision=
!1;this.gradientType="radial";k.applyTheme(this,a,this.cname)},drawChart:function(){k.AmPieChart.base.drawChart.call(this);var a=this.chartData;if(k.ifArray(a)){if(0<this.realWidth&&0<this.realHeight){k.VML&&(this.startAlpha=1);var b=this.startDuration,c=this.container,d=this.updateWidth();this.realWidth=d;var g=this.updateHeight();this.realHeight=g;var f=k.toCoordinate,h=f(this.marginLeft,d),e=f(this.marginRight,d),z=f(this.marginTop,g)+this.getTitleHeight(),n=f(this.marginBottom,g)+this.depth3D,
A,B,m,w=k.toNumber(this.labelRadius),q=this.measureMaxLabel();q>this.maxLabelWidth&&(q=this.maxLabelWidth);this.labelText&&this.labelsEnabled||(w=q=0);A=void 0===this.pieX?(d-h-e)/2+h:f(this.pieX,this.realWidth);B=void 0===this.pieY?(g-z-n)/2+z:f(this.pieY,g);m=f(this.radius,d,g);m||(d=0<=w?d-h-e-2*q:d-h-e,g=g-z-n,m=Math.min(d,g),g<d&&(m/=1-this.angle/90,m>d&&(m=d)),g=k.toCoordinate(this.pullOutRadius,m),m=(0<=w?m-1.8*(w+g):m-1.8*g)/2);m<this.minRadius&&(m=this.minRadius);g=f(this.pullOutRadius,m);
z=k.toCoordinate(this.startRadius,m);f=f(this.innerRadius,m);f>=m&&(f=m-1);n=k.fitToBounds(this.startAngle,0,360);0<this.depth3D&&(n=270<=n?270:90);n-=90;360<n&&(n-=360);d=m-m*this.angle/90;for(h=q=0;h<a.length;h++)e=a[h],!0!==e.hidden&&(q+=k.roundTo(e.percents,this.pf.precision));q=k.roundTo(q,this.pf.precision);this.tempPrec=NaN;this.adjustPrecision&&100!=q&&(this.tempPrec=this.pf.precision+1);for(var E,h=0;h<a.length;h++)if(e=a[h],!0!==e.hidden&&(this.showZeroSlices||0!==e.percents)){var r=360*
e.percents/100,q=Math.sin((n+r/2)/180*Math.PI),C=d/m*-Math.cos((n+r/2)/180*Math.PI),p=this.outlineColor;p||(p=e.color);var u=this.alpha;isNaN(e.alpha)||(u=e.alpha);p={fill:e.color,stroke:p,"stroke-width":this.outlineThickness,"stroke-opacity":this.outlineAlpha,"fill-opacity":u};e.url&&(p.cursor="pointer");p=k.wedge(c,A,B,n,r,m,d,f,this.depth3D,p,this.gradientRatio,e.pattern,this.path,this.gradientType);k.setCN(this,p,"pie-item");k.setCN(this,p.wedge,"pie-slice");k.setCN(this,p,e.className,!0);this.addEventListeners(p,
e);e.startAngle=n;a[h].wedge=p;0<b&&(this.chartCreated||p.setAttr("opacity",this.startAlpha));e.ix=q;e.iy=C;e.wedge=p;e.index=h;e.label=null;u=c.set();if(this.labelsEnabled&&this.labelText&&e.percents>=this.hideLabelsPercent){var l=n+r/2;0>l&&(l+=360);360<l&&(l-=360);var t=w;isNaN(e.labelRadius)||(t=e.labelRadius,0>t&&(e.skipTick=!0));var r=A+q*(m+t),D=B+C*(m+t),x,v=0;isNaN(E)&&350<l&&1<a.length-h&&(E=h-1+Math.floor((a.length-h)/2));if(0<=t){var y;90>=l&&0<=l?(y=0,x="start",v=8):90<=l&&180>l?(y=1,
x="start",v=8):180<=l&&270>l?(y=2,x="end",v=-8):270<=l&&354>=l?(y=3,x="end",v=-8):354<=l&&(h>E?(y=0,x="start",v=8):(y=3,x="end",v=-8));e.labelQuarter=y}else x="middle";l=this.formatString(this.labelText,e);(t=this.labelFunction)&&(l=t(e,l));t=e.labelColor;t||(t=this.color);""!==l&&(l=k.wrappedText(c,l,t,this.fontFamily,this.fontSize,x,!1,this.maxLabelWidth),k.setCN(this,l,"pie-label"),k.setCN(this,l,e.className,!0),l.translate(r+1.5*v,D),0>w&&(l.node.style.pointerEvents="none"),l.node.style.cursor=
"default",e.ty=D,e.textX=r+1.5*v,u.push(l),this.axesSet.push(u),e.labelSet=u,e.label=l,this.addEventListeners(u,e));e.tx=r;e.tx2=r+v;e.tx0=A+q*m;e.ty0=B+C*m}r=f+(m-f)/2;e.pulled&&(r+=g);this.accessible&&this.accessibleLabel&&(D=this.formatString(this.accessibleLabel,e),this.makeAccessible(p,D));void 0!==this.tabIndex&&p.setAttr("tabindex",this.tabIndex);e.balloonX=q*r+A;e.balloonY=C*r+B;e.startX=Math.round(q*z);e.startY=Math.round(C*z);e.pullX=Math.round(q*g);e.pullY=Math.round(C*g);this.graphsSet.push(p);
if(0===e.alpha||0<b&&!this.chartCreated)p.hide(),u&&u.hide();n+=360*e.percents/100;360<n&&(n-=360)}0<w&&this.arrangeLabels();this.pieXReal=A;this.pieYReal=B;this.radiusReal=m;this.innerRadiusReal=f;0<w&&this.drawTicks();this.initialStart();this.setDepths()}(a=this.legend)&&a.invalidateSize()}else this.cleanChart();this.dispDUpd()},setDepths:function(){var a=this.chartData,b;for(b=0;b<a.length;b++){var c=a[b],d=c.wedge,c=c.startAngle;0<=c&&180>c?d.toFront():180<=c&&d.toBack()}},arrangeLabels:function(){var a=
this.chartData,b=a.length,c,d;for(d=b-1;0<=d;d--)c=a[d],0!==c.labelQuarter||c.hidden||this.checkOverlapping(d,c,0,!0,0);for(d=0;d<b;d++)c=a[d],1!=c.labelQuarter||c.hidden||this.checkOverlapping(d,c,1,!1,0);for(d=b-1;0<=d;d--)c=a[d],2!=c.labelQuarter||c.hidden||this.checkOverlapping(d,c,2,!0,0);for(d=0;d<b;d++)c=a[d],3!=c.labelQuarter||c.hidden||this.checkOverlapping(d,c,3,!1,0)},checkOverlapping:function(a,b,c,d,g){var f,h,e=this.chartData,k=e.length,n=b.label;if(n){if(!0===d)for(h=a+1;h<k;h++)e[h].labelQuarter==
c&&(f=this.checkOverlappingReal(b,e[h],c))&&(h=k);else for(h=a-1;0<=h;h--)e[h].labelQuarter==c&&(f=this.checkOverlappingReal(b,e[h],c))&&(h=0);!0===f&&200>g&&isNaN(b.labelRadius)&&(f=b.ty+3*b.iy,b.ty=f,n.translate(b.textX,f),this.checkOverlapping(a,b,c,d,g+1))}},checkOverlappingReal:function(a,b,c){var d=!1,g=a.label,f=b.label;a.labelQuarter!=c||a.hidden||b.hidden||!f||(g=g.getBBox(),c={},c.width=g.width,c.height=g.height,c.y=a.ty,c.x=a.tx,a=f.getBBox(),f={},f.width=a.width,f.height=a.height,f.y=
b.ty,f.x=b.tx,k.hitTest(c,f)&&(d=!0));return d}})})();


// AmCharts.themes.sarshomar={themeName:"sarshomar",AmChart:{color:"#000000",backgroundColor:"#FFFFFF"},AmCoordinateChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"]},AmStockChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"]},AmSlicedChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"],outlineAlpha:1,outlineThickness:2,labelTickColor:"#000000",labelTickAlpha:0.3},AmRectangularChart:{zoomOutButtonColor:'#000000',zoomOutButtonRollOverAlpha:0.15,zoomOutButtonImage:"lens"},AxisBase:{axisColor:"#000000",axisAlpha:0.3,gridAlpha:0.1,gridColor:"#000000"},ChartScrollbar:{backgroundColor:"#000000",backgroundAlpha:0.12,graphFillAlpha:0.5,graphLineAlpha:0,selectedBackgroundColor:"#FFFFFF",selectedBackgroundAlpha:0.4,gridAlpha:0.15},ChartCursor:{cursorColor:"#000000",color:"#FFFFFF",cursorAlpha:0.5},AmLegend:{color:"#000000"},AmGraph:{lineAlpha:0.9},GaugeArrow:{color:"#000000",alpha:0.8,nailAlpha:0,innerRadius:"40%",nailRadius:15,startWidth:15,borderAlpha:0.8,nailBorderAlpha:0},GaugeAxis:{tickColor:"#000000",tickAlpha:1,tickLength:15,minorTickLength:8,axisThickness:3,axisColor:'#000000',axisAlpha:1,bandAlpha:0.8},TrendLine:{lineColor:"#c03246",lineAlpha:0.8},AreasSettings:{alpha:0.8,color:"#67b7dc",colorSolid:"#003767",unlistedAreasAlpha:0.4,unlistedAreasColor:"#000000",outlineColor:"#FFFFFF",outlineAlpha:0.5,outlineThickness:0.5,rollOverColor:"#3c5bdc",rollOverOutlineColor:"#FFFFFF",selectedOutlineColor:"#FFFFFF",selectedColor:"#f15135",unlistedAreasOutlineColor:"#FFFFFF",unlistedAreasOutlineAlpha:0.5},LinesSettings:{color:"#000000",alpha:0.8},ImagesSettings:{alpha:0.8,labelColor:"#000000",color:"#000000",labelRollOverColor:"#3c5bdc"},ZoomControl:{buttonFillAlpha:0.7,buttonIconColor:"#a7a7a7"},SmallMap:{mapColor:"#00667a",rectangleColor:"#f15135",backgroundColor:"#FFFFFF",backgroundAlpha:0.7,borderThickness:0.3,borderAlpha:0.8},PeriodSelector:{color:"#000000"},PeriodButton:{color:"#000000",background:"transparent",opacity:0.7,border:"1px solid rgba(0, 0, 0, .3)",MozBorderRadius:"5px",borderRadius:"5px",margin:"1px",outline:"none",boxSizing:"border-box"},PeriodButtonSelected:{color:"#000000",backgroundColor:"#b9cdf5",border:"1px solid rgba(0, 0, 0, .3)",MozBorderRadius:"5px",borderRadius:"5px",margin:"1px",outline:"none",opacity:1,boxSizing:"border-box"},PeriodInputField:{color:"#000000",background:"transparent",border:"1px solid rgba(0, 0, 0, .3)",outline:"none"},DataSetSelector:{color:"#000000",selectedBackgroundColor:"#b9cdf5",rollOverBackgroundColor:"#a8b0e4"},DataSetCompareList:{color:"#000000",lineHeight:"100%",boxSizing:"initial",webkitBoxSizing:"initial",border:"1px solid rgba(0, 0, 0, .3)"},DataSetSelect:{border:"1px solid rgba(0, 0, 0, .3)",outline:"none"}};
AmCharts.themes.sarshomar={themeName:"sarshomar",
AmChart:{color:"#000000",backgroundColor:"#FFFFFF"},
AmCoordinateChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"]},
AmStockChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"]},
AmSlicedChart:{colors:["#67b7dc","#fdd400","#84b761","#cc4748","#cd82ad","#2f4074","#448e4d","#b7b83f","#b9783f","#b93e3d","#913167"],outlineAlpha:1,outlineThickness:2,labelTickColor:"#000000",labelTickAlpha:0.3},
AmRectangularChart:{zoomOutButtonColor:'#000000',zoomOutButtonRollOverAlpha:0.15,zoomOutButtonImage:"lens"},
AxisBase:{axisColor:"#000000",axisAlpha:0.3,gridAlpha:0.1,gridColor:"#000000"},
ChartScrollbar:{backgroundColor:"#000000",backgroundAlpha:0.12,graphFillAlpha:0.5,graphLineAlpha:0,selectedBackgroundColor:"#FFFFFF",selectedBackgroundAlpha:0.4,gridAlpha:0.15},
ChartCursor:{cursorColor:"#000000",color:"#FFFFFF",cursorAlpha:0.5},
AmLegend:{color:"#000000"},
AmGraph:{lineAlpha:0.9},
GaugeArrow:{color:"#000000",alpha:0.8,nailAlpha:0,innerRadius:"40%",nailRadius:15,startWidth:15,borderAlpha:0.8,nailBorderAlpha:0},
GaugeAxis:{tickColor:"#000000",tickAlpha:1,tickLength:15,minorTickLength:8,axisThickness:3,axisColor:'#000000',axisAlpha:1,bandAlpha:0.8},
TrendLine:{lineColor:"#c03246",lineAlpha:0.8},
AreasSettings:{alpha:0.8,color:"#67b7dc",colorSolid:"#083942",unlistedAreasAlpha:0.1,unlistedAreasColor:"#000000",outlineColor:"#f4f4f4",outlineAlpha:0.5,outlineThickness:0.4,rollOverColor:"#e5a428",rollOverOutlineColor:"#FFFFFF",selectedOutlineColor:"#FFFFFF",selectedColor:"#f15135",unlistedAreasOutlineColor:"#FFFFFF",unlistedAreasOutlineAlpha:0.5},
// AreasSettings:{alpha:0.8,color:"#67b7dc",colorSolid:"#003767",unlistedAreasAlpha:0.4,unlistedAreasColor:"#000000",outlineColor:"#FFFFFF",outlineAlpha:0.5,outlineThickness:0.5,rollOverColor:"#e5a428",rollOverOutlineColor:"#FFFFFF",selectedOutlineColor:"#FFFFFF",selectedColor:"#f15135",unlistedAreasOutlineColor:"#FFFFFF",unlistedAreasOutlineAlpha:0.5},
LinesSettings:{color:"#000000",alpha:0.8},
ImagesSettings:{alpha:0.8,labelColor:"#000000",color:"#000000",labelRollOverColor:"#3c5bdc"},
ZoomControl:{buttonFillAlpha:0.7,buttonIconColor:"#a7a7a7"},
SmallMap:{mapColor:"#00667a",rectangleColor:"#f15135",backgroundColor:"#FFFFFF",backgroundAlpha:0.7,borderThickness:0.3,borderAlpha:0.8},
PeriodSelector:{color:"#000000"},
PeriodButton:{color:"#000000",background:"transparent",opacity:0.7,border:"1px solid rgba(0, 0, 0, .3)",MozBorderRadius:"5px",borderRadius:"5px",margin:"1px",outline:"none",boxSizing:"border-box"},
PeriodButtonSelected:{color:"#000000",backgroundColor:"#b9cdf5",border:"1px solid rgba(0, 0, 0, .3)",MozBorderRadius:"5px",borderRadius:"5px",margin:"1px",outline:"none",opacity:1,boxSizing:"border-box"},
PeriodInputField:{color:"#000000",background:"transparent",border:"1px solid rgba(0, 0, 0, .3)",outline:"none"},
DataSetSelector:{color:"#000000",selectedBackgroundColor:"#b9cdf5",rollOverBackgroundColor:"#a8b0e4"},
DataSetCompareList:{color:"#000000",lineHeight:"100%",boxSizing:"initial",webkitBoxSizing:"initial",border:"1px solid rgba(0, 0, 0, .3)"},
DataSetSelect:{border:"1px solid rgba(0, 0, 0, .3)",outline:"none"}};



/**
 * [drawChart description]
 * @return {[type]} [description]
 */
function drawChart()
{
	AmCharts.addInitHandler(function(chart)
	{
		// check if there are graphs with autoColor: true set
		for(var i = 0; i < chart.graphs.length; i++)
		{
			var graph = chart.graphs[i];
			if (graph.autoColor !== true)
			{
				continue;
			}
			// var colorKey = "autoColor-"+i;
			colorPallet = pallet();
			colorKey    = colorPallet[i];
			graph.lineColorField = colorKey;
			graph.fillColorsField = colorKey;
			for(var x = 0; x < chart.dataProvider.length; x++)
			{
				// var color = chart.colors[x]
				var color = colorPallet[x];
				chart.dataProvider[x][colorKey] = color;
			}
		}

	}, ["serial"]);


	$('.chart:not([data-draw])').each(function()
	{
		createChartOption(this);
		$(this).attr('data-draw', true);
	});
}


/**
 * [createChartOption description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function createChartOption(_this, _chartName)
{
		// declare variables
		$this              = $(_this);
		var attrType       = $this.attr('data-type');
		var attrPlace      = $this.attr('data-place');
		var attrVals       = $this.attr('data-vals');
		var attrFormat     = $this.attr('data-format');
		var attrColor      = $this.attr('data-color');
		var attrCat        = $this.attr('data-catField');
		var attrWhat       = $this.attr('data-what');
		var attrWhatVals   = $this.attr('data-vals-' + attrWhat);
		var chartContainer = $this;
		var attrAutoColor  = false;

		if(attrWhatVals)
		{
			attrVals = attrWhatVals;
		}

		// if not set replace them
		if(!attrType)
		{
			attrType = 'column';
		}
		// set color of chart data
		if(attrColor === 'auto')
		{
			attrAutoColor = true;
			attrColor     = false;
		}
		else if(attrColor)
		{
			attrColor = JSON.parse(attrColor);
		}
		else
		{
			attrColor = ["#83c3e1"];
		}
		if(!attrVals)
		{
			attrVals = [];
		}
		else
		{
			attrVals = JSON.parse(attrVals);
		}
		if(attrPlace && chartContainer.find(attrPlace).length)
		{
			chartContainer = chartContainer.find(attrPlace);
		}
		else
		{
			chartContainer = chartContainer[0];
		}
		// generate global options
		// var myChartOptions =
		// {
		// 	chart: {type: attrType},
		// 	title: {text: ''},
		// 	xAxis:{categories:attrCats},
		// 	yAxis: {min: 0,title: {enabled: false},labels: {enabled: false},gridLineWidth: 0,minorGridLineWidth: 0},
		// 	plotOptions: {column: {pointPadding: 0,groupPadding: 0.15,},series: {dataLabels: {enabled: false,format: '{point.y:f}%'}},inside: true},
		// 	legend: {enabled: false},
		// 	credits: {enabled: false},
		// 	tooltip: {enabled: false},
		// 	series: attrVals
		// };

		var myChartOptions          = {};
		myChartOptions              = getChartOption('default', {"color":attrColor, "val":attrVals})
		myChartOptions.categoryAxis = getChartOption('categoryAxis');
		myChartOptions.valueAxes    = getChartOption('valueAxes');
		myChartOptions.graphs       = getChartOption('graphs', attrAutoColor);

		// allow to give special catfield
		if(attrCat)
		{
			myChartOptions.categoryField = attrCat;
		}

		// else if has format call it
		if(attrFormat && typeof window[attrFormat] == 'function')
		{
			myChartOptions = window[attrFormat](myChartOptions, attrVals, $this);
		}

		// console.log(myChartOptions);
		var chartData = AmCharts.makeChart( chartContainer, myChartOptions);
		// if(_chartName)
		// {
		// 	console.log(_chartName);

		// 	$(chartContainer).data('chart-' + _chartName, chartData);
		// 	chartData.invalidateSize();
		// 	// console.log(_chartName);
		// 	console.log($(chartContainer).data('chart-' + _chartName));
		// }
		// else
		{
			$(chartContainer).data('chart', chartData);
		}
}


/**
 * [getChartOption description]
 * @param  {[type]} $_what [description]
 * @param  {[type]} _arg   [description]
 * @return {[type]}        [description]
 */
function getChartOption($_what, _arg)
{
	var prop = {};
	switch ($_what)
	{
		case 'categoryAxis':
			prop =
			{
				"gridPosition": "start",
				"gridAlpha": 0,
				"tickPosition": "start",
				"twoLineMode": true,
				"tickLength": 5,
				"axisAlpha": 0.2,
				"autoWrap":false,
				// "labelRotation": 45,
				"autoRotateCount": 6,
				"autoRotateAngle": 45,
				"labelFunction": function(value)
				{
					return fitNumber(value, false);
				},
			};
			break;


		case 'valueAxes':
			if(_arg === undefined)
			{
				prop =
				[
					{
						"gridColor": "#FFFFFF",
						"gridAlpha": 0.1,
						"dashLength": 0,
						// "axisAlpha": 0.2,
						"labelsEnabled": false,
						"axisAlpha": 0,
						"integersOnly": true,
						"labelFunction": function(value)
						{
							return fitNumber(value, false);
						},
					}
				];

			}
			else
			{
				prop =
				[
					{
						"stackType": "regular",
						"axisAlpha": 0.1,
						"gridAlpha": 0,
						"minimum": 0,
						"integersOnly": true,
						// "position" : "right",
						"labelFunction": function(value)
						{
							return fitNumber(value, false);
						},
					}
				];

				if($(document).attr('dir') === 'rtl')
				{
					prop[0].position = 'right';
				}
			}
			break;

		case 'graphs':
			prop =
			[{
				"valueField": "value",
				"colorField": "color",
				"lineColorField": "color",
				"type": "column",
				"fillAlphas": 0.9,
				"lineAlpha": 0.2,
				"customBulletField": "bullet",
				"bulletSize": 40,
				"autoColor": _arg,
				"labelFunction" : function(item)
				{
					return fitNumber(Math.abs(item.values.value));
				},
				"balloonFunction": function(item)
				{
					return item.category + " <b>" + fitNumber(item.values.value) + "</b>";
				},
			}];
			break;

		case 'legend':
			prop =
			{
				"horizontalGap": 0,
				"verticalGap": 0,
				// "maxColumns": 1,
				"autoMargins":true,
				"position": "top",
				"useGraphSettings": true,
				"markerSize": 9,
			};
			break;

		case 'chartCursor':
			prop =
			{
				"fullWidth": true,
				"cursorAlpha": 0.1,
				"categoryBalloonColor": "#00667a",
				"cursorColor": "#00667a",
				"zoomable": false,
			};
			break;

		case 'default':
		default:
			prop =
			{
				"type": "serial",
				"colors": _arg.color,
				"dataProvider": _arg.val,
				"categoryField": "key",
				// "colorField": "color",
				// "labelColorField": "color",
				// "fillColorsField": "color",

				"gridAboveGraphs": true,
				"startDuration": 0.3,
				"startEffect":"easeOutSine",
				"startAlpha": 0.5,
			}
			break;
	}

	return prop;
}


/**
 * remove all exisiting graphgs
 * @return {[type]} [description]
 */
function removeChartGraphs(_chartData)
{
	if(!_chartData || !_chartData.graphs)
	{
		return false;
	}
	var graphLen = _chartData.graphs.length;

	for (var i=0; i<graphLen; i++)
	{
		var myGraph = _chartData.graphs.pop();
		_chartData.removeGraph(myGraph);
	}
}

/**
 * [createChartGraphs description]
 * @param  {[type]} _chartData [description]
 * @param  {[type]} _name      [description]
 * @param  {[type]} _title     [description]
 * @param  {[type]} _value     [description]
 * @param  {[type]} _balloon   [description]
 * @return {[type]}            [description]
 */
function createChartGraphs(_chartData, _group, _title, _value, _balloon)
{
	// Add mael graph attached to the axis
	var myNewGraph         = new AmCharts.AmGraph();
	myNewGraph.type        = "column";
	myNewGraph.title       = _title;
	myNewGraph.valueField  = _value;
	myNewGraph.labelText   = "[[value]]";
	myNewGraph.fillAlphas  = 0.9;
	myNewGraph.lineAlpha   = 0;

	if(_value === 'unknown')
	{
		myNewGraph.hidden = true;
	}

	myNewGraph.labelFunction = function(item)
	{
		return fitNumber(Math.abs(item.values.value));
	};

	myNewGraph.labelFunction = function(item)
	{
		// Calculate total of values across all columns in the graph
		var total = 0;
		for( var i = 0; i < _chartData.dataProvider.length; i++ )
		{
			total += _chartData.dataProvider[ i ][ item.graph.valueField ];
		}

		// Calculate percet value of this label
		var percent = Math.round( ( item.values.value / total ) * 1000 ) / 10;

		return fitNumber(percent) + "%";
	};

	if(!_balloon)
	{
		myNewGraph.balloonFunction = function(item)
		{
			var balloonText = item.category + " [" + _title + "] <br/><b>" + fitNumber(item.values.value) + "</b>";
			if(_chartData.trans && _chartData.trans.vote)
			{
				balloonText += " " + _chartData.trans.vote;
			}
			return balloonText;
		};
	}
	else
	{
		myNewGraph.balloonText = _balloon;
	}

	// set color if exist
	var myColor = getBestColorForGroup(_group);
	if(myColor)
	{
		myNewGraph.fillColors = myColor;
	}
	// add graph to chart data
	if(typeof _chartData.addGraph === "function")
	{
		_chartData.addGraph(myNewGraph);
	}
	else
	{
		_chartData.graph = myNewGraph;
	}
}


/**
 * [getBestColorForGroup description]
 * @param  {[type]} _group [description]
 * @return {[type]}        [description]
 */
function getBestColorForGroup(_group)
{
	console.log(_group);
	var myColor = null;
	// ["#83c3e1", "#e97197", "#e7e7e7"];
	switch(_group)
	{
		// gender
		case 'gender_male':
			myColor = '#83c3e1';
			break;

		case 'gender_female':
			myColor = '#e97197';
			break;


		// marittal
		case 'marrital_single':
			myColor = '#f8a13f';
			break;

		case 'marrital_married':
			myColor = '#c0453c';
			break;


		case 'result_value_reliable':
			myColor = '#8ec8e3';
			break;

		// graduation
		case 'graduation_undergraduate':
			myColor = '#6d9e73';
			break;


		// age range
		case 'range_-13':
			myColor = '#c1cee6';
			break;

		case 'range_14-17':
			myColor = '#8ca3d1';
			break;
		case 'range_18-24':
			myColor = '#7691c8';
			break;

		case 'range_25-30':
			myColor = '#3a62b0';
			break;

		case 'range_31-44':
			myColor = '#325bad';
			break;

		case 'range_45-59':
			myColor = '#4167b3';
			break;

		case 'range_60+':
			myColor = '#1f4ca5';
			break;

		// disable vote style
		case 'result_value_unreliable':
		case 'range_unknown':
		case 'gender_unknown':
		case 'marrital_unknown':
		case 'graduation_unknown':
		case 'employmentstatus_unknown':
			myColor = '#dbdbdb';
			break;

	}

	return myColor;
}


/**
 * [pollTotal description]
 * @param  {[type]} _option [description]
 * @param  {[type]} _vals   [description]
 * @param  {[type]} _this   [description]
 * @return {[type]}         [description]
 */
function pollTotal_default(_option, _answers, _el)
{
	transText = _el.attr('data-trans');
	if(transText)
	{
		transText = JSON.parse(transText);
		_option.trans = transText;
	}

	_option.colors = ["#83c3e1", "#d8d8d8" ];
	_option.legend = getChartOption('legend');
	_option.legend.equalWidths = false;
	_option.valueAxes = getChartOption('valueAxes', true);

	if($(document).attr('dir') === 'rtl')
	{
		_option.legend.align = 'right';
	}

	console.log(_option);

	// setChartOption_result(_option, null, true);

	// _option.graphs =
	// [
	// 	{
	// 		"type": "column",
	// 		"title": transText.reliable,
	// 		"valueField": "value_reliable",
	// 		"balloonText": "[[title]] [" + transText.reliable + "] <br/><b>[[value]]</b> " + transText.vote,
	// 		"labelText": "[[value]]",
	// 		"fillAlphas": 0.9,
	// 		"lineAlpha": 0,
	// 		"labelFunction" : function(item)
	// 		{
	// 			return fitNumber(Math.abs(item.values.value));
	// 		},
	// 		"balloonFunction": function(item)
	// 		{
	// 			return item.category + " [" + transText.reliable + "] <br/><b>" + fitNumber(item.values.value) + "</b> " + transText.vote;
	// 		},
	// 	},
	// 	{
	// 		"type": "column",
	// 		"title": transText.unreliable,
	// 		"valueField": "value_unreliable",
	// 		"labelText": "[[value]]",
	// 		"fillAlphas": 0.5,
	// 		"lineAlpha": 0,
	// 		"clustered":false,
	// 		"labelFunction" : function(item)
	// 		{
	// 			return fitNumber(Math.abs(item.values.value));
	// 		},
	// 		"balloonFunction": function(item)
	// 		{
	// 			return item.category + " [" + transText.reliable + "] <br/><b>" + fitNumber(item.values.value) + "</b> " + transText.vote;
	// 		},
	// 	},
	// ];

	_option.graphs =
	[
		{
			"type": "column",
			"title": transText.vote,
			"valueField": "value",
			"balloonText": "[[title]]<br/><b>[[value]]</b> " + transText.vote,
			"labelText": "[[value]]",
			"fillAlphas": 1,
			"lineAlpha": 0,
			"fillColors": '#98cce4',
			// "labelFunction" : function(item)
			// {
			// 	return fitNumber(Math.abs(item.values.value));
			// },
			"labelFunction": function( item )
			{
				// Calculate total of values across all columns in the graph
				var total = 0;
				for( var i = 0; i < _option.dataProvider.length; i++ )
				{
					total += _option.dataProvider[ i ][ item.graph.valueField ];
				}

				// Calculate percet value of this label
				var percent = Math.round( ( item.values.value / total ) * 1000 ) / 10;

				return fitNumber(percent) + "%";
			},

			"balloonFunction": function(item)
			{
				return item.category + "<br/><b>" + fitNumber(item.values.value) + "</b> " + transText.vote;
			},
		},
	];

	_option.categoryField = "title";
	// console.log(_option);

	// show all data on hover of column
	_option.chartCursor = {};
	_option.chartCursor.cursorAlpha = 0;
	_option.chartCursor.categoryBalloonColor = '#e7a429';

	return _option;
}


/**
 * [setChartOption description]
 * @param {[type]} _chartData [description]
 * @param {[type]} _type      [description]
 * @param {[type]} _trans     [description]
 * @param {[type]} _init      [description]
 */
function setChartOption(_chartData, _type, _trans, _init)
{
	// remove all exisiting graphgs
	removeChartGraphs(_chartData);

	switch(_type)
	{
		case 'gender':
			// set color and axis type
			// _chartData.colors = ["#83c3e1", "#e97197", "#e7e7e7"];
			break;

		case 'result':
			// _chartData.colors = ["#83c3e1", "#d8d8d8"];
			break;

		default:
			// _chartData.colors = pallet();
			// _chartData.colors = [];
			break;
	}
	// change effect on other types of chart
	// _chartData.startEffect = "elastic";
	// _chartData.startDuration = 0.3;

	// show all data on hover of column
	_chartData.chartCursor = {};
	_chartData.chartCursor.cursorAlpha = 0.1;
	_chartData.chartCursor.categoryBalloonColor = '#e7a429';

	if(_trans.length === 0)
	{
		console.log(_trans.length);
		_trans = {"value_reliable": "Reliable", "value_unreliable": "Unreliable"};
	}
	// add each graph if exist
	$.each(_trans, function($val, $title)
	{
		createChartGraphs(_chartData, _type+ '_'+ $val, $title, $val);
	});
}


/**
 * [redrawChart description]
 * @return {[type]} [description]
 */
function redrawPollChart(_data)
{
	if(!_data)
	{
		_data = {};
		_data.chart = true;

		var chartType = $('input[name="chart_result"]:checked').val();
		if(chartType)
		{
			_data.chart = chartType;
		}

	}
	// try to abort old request
	try { xhr.abort(); } catch(e){}
	// try to get new list from server
	var myAddr = $(location).attr('href');
	var xhr    = $.getJSON(myAddr, _data, function(_response)
	{
		var newChartData = clearJson(_response);
		// if(newChartData && newChartData.valid)
		if(newChartData)
		{
			var myChart     = $('.chart');
			var drawData    = newChartData['answers'];
			var myChartData = myChart.data('chart');

			if(newChartData && newChartData.summary && newChartData.summary.total)
			{
				$('.isDataNotExist').fadeOut();
			}
			console.log(drawData);

			// if this chart has extra setting set it
			// call func to customize data for this type of chart
			if(chartType && typeof window['setChartOption'] == 'function')
			{
				var trans = {};
				if(newChartData['trans'])
				{
					trans = newChartData['trans'];
				}
				window['setChartOption'](myChartData, chartType, trans);
			}

			//Setting the new data to the graph
			myChartData.dataProvider = drawData;
			myChartData.animateAgain();
			//Updating the graph to show the new data
			myChartData.validateData();
			// myChartData.validateNow();


			// myChartData.animateData(drawData,
			// {
			// 	duration: 1000,
			// 	complete: function ()
			// 	{
			// 		console.log('complete')
			// 		// setTimeout(loop, 2000);
			// 	}
			// });

		}
	});
}


/**
 * customized for gender chart
 * @param  {[type]} _option [description]
 * @return {[type]}         [description]
 */
function homepageGender(_option)
{
	_option.colors = ["#83c3e1", "#e97197" ];
	_option.rotate = true;
	_option.marginBottom = 50;
	_option.startDuration = 0.5;
	_option.startEffect = 'easeOutSine';
	_option.graphs =
	[
		{
			"fillAlphas": 0.8,
			"lineAlpha": 0.2,
			"type": "column",
			"valueField": "male",
			"title": "مرد",
			// "labelText": "[[value]]",
			"clustered": false,
			"labelFunction": function(item)
			{
				return fitNumber(Math.abs(item.values.value));
			},
			"balloonFunction": function(item)
			{
				return item.category + " [" + item.graph.title + "]<br/><b>"  + fitNumber(Math.abs(item.values.value)) + "%</b>";
			}
		},
		{
			"fillAlphas": 0.8,
			"lineAlpha": 0.2,
			"type": "column",
			"valueField": "female",
			"title": "زن",
			// "labelText": "[[value]]",
			"clustered": false,
			"labelFunction": function(item)
			{
				return fitNumber(Math.abs(item.values.value));
			},
			"balloonFunction": function(item)
			{
				return item.category + " [" + item.graph.title + "]<br/><b>"  + fitNumber(Math.abs(item.values.value)) + "%</b>";
			}
		}
	];
	_option.categoryAxis =
	{
		"gridPosition": "start",
		"gridAlpha": 0.02,
		"axisAlpha": 0,
		"position": "right",
		"labelFunction": function(value)
		{
			return fitNumber(value, false);
		},
	};
	_option.valueAxes =
	[
		{
			"gridAlpha": 0,
			"axisAlpha": 0.2,
			"ignoreAxisWidth": true,
			"labelFunction": function(value)
			{
				return fitNumber(Math.abs(value)) + '%';
			},
			"guides":
			[{
				"value": 0,
				"lineAlpha": 0.1
			}]
		}
	]
	_option.balloon = {"fixedPosition": true};
	_option.chartCursor =
	{
		"valueBalloonsEnabled": false,
		"cursorColor":"#00667a",
		"cursorAlpha": 0.05,
		"fullWidth": true,
		"zoomable": false,
		"categoryBalloonFunction": function(value)
		{
			return fitNumber(value, false);
		},
	};
	// _option.allLabels =
	// [
	// 	{
	// 		"text": "مرد",
	// 		"x": "28%",
	// 		"y": "0%",
	// 		"bold": true,
	// 		"align": "top"
	// 	},
	// 	{
	// 		"text": "زن",
	// 		"x": "75%",
	// 		"y": "0%",
	// 		"bold": true,
	// 		"align": "middle"
	// 	}
	// ];

	return _option;
}


/**
 * [dashboardPie description]
 * @param  {[type]} _option [description]
 * @return {[type]}         [description]
 */
function dashboardPie(_option, _vals)
{
	_option.type            = "pie";
	_option.colors          = ["#67b7dc", "#00677a", "#ff80c0"];
	// _option.dataProvider = _vals;
	_option.valueField      =  "value";
	_option.titleField      =  "key";
	_option.startDuration   = 0.5;
	_option.startEffect     = "easeOutSine";
	// empty inner box of chart
	_option.radius          = "20%";
	_option.innerRadius     = "60%";
	return _option;
}


/**
 * [admin_smoothedLine description]
 * @param  {[type]} _option [description]
 * @return {[type]}         [description]
 */
function admin_smoothedLine(_option)
{
	_option = admin_line(_option, true);
	return _option;
}


/**
 * [admin_line description]
 * @param  {[type]} _option       [description]
 * @param  {[type]} _smoothedLine [description]
 * @return {[type]}               [description]
 */
function admin_line(_option, _smoothedLine)
{
	var myGraph = _option.graphs[0];

	if(_smoothedLine === true)
	{
		myGraph.type = 'smoothedLine';
	}
	else
	{
		myGraph.type = 'line';
	}

	myGraph.fillAlphas            = 0.3;
	myGraph.fillColorsField       = "lineColor";
	myGraph.lineColor             = "#00667a";
	myGraph.bullet                = "round";
	myGraph.bulletSize            = 5;
	myGraph.bulletBorderAlpha     = 0.2;
	myGraph.bulletBorderThickness = 0.1;

	_option.chartCursor = getChartOption('chartCursor');
	_option.valueAxes   = getChartOption('valueAxes', true);

	return _option;
}


/**
 * pre defined color pallet
 * @param  {[type]} _index [description]
 * @return {[type]}        [description]
 */
function pallet(_index)
{
	if(!_index)
	{
		_index = Math.floor(Math.random() * 7) + 1;
	}
	var pt = ['f4f4f4'];

	switch (_index)
	{
		case 1:
			pt = ['#FA4D58', '#FB574C', '#FB574C', '#FC7F33', '#FD9526', '#FEAA18', '#FEBD0D', '#FFCC04',];
			break;

		case 2:
			pt = ['#CC4F00', '#DC6000', '#F9943F', '#FDB072', '#FCC496', '#FACDA6', '#F9D8BB', '#F9E3D0', '#087B34',];
			break;

		case 3:
			pt = ['#1E852F', '#3C9128', '#5E9F1F', '#82AE17', '#A5BD0F', '#C4CA08', '#DCD402',];
			break;

		case 4:
			pt = ['#00C28B', '#00B6A1', '#00B2AA', '#00ACB3', '#00A7BD', '#00A3C6', '#009ECE', '#0694C9',];
			break;

		case 5:
			pt = ['#00C18E', '#00B68F', '#00A790', '#009591', '#008392', '#007192', '#006293', '#005594',];
			break;

		case 6:
			pt = ['#F5C909', '#DBBF18', '#B9B32B', '#93A540', '#6B9756', '#45896C', '#237D7F', '#0A758C',];
			break;

		case 7:
			pt = ['#F24A57', '#D84C5E', '#B64D66', '#904F70', '#68507A', '#425283', '#2D639F', '#095592',];
			break;

		default:
			break;
	}

	return pt;
}