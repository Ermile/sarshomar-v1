// intro js v2.4.0
(function(C,m){"object"===typeof exports?m(exports):"function"===typeof define&&define.amd?define(["exports"],m):m(C)})(this,function(C){function m(a){this._targetElement=a;this._introItems=[];this._options={nextLabel:"Next &rarr;",prevLabel:"&larr; Back",skipLabel:"Skip",doneLabel:"Done",hidePrev:!1,hideNext:!1,tooltipPosition:"bottom",tooltipClass:"",highlightClass:"",exitOnEsc:!0,exitOnOverlayClick:!0,showStepNumbers:!0,keyboardNavigation:!0,showButtons:!0,showBullets:!0,showProgress:!1,scrollToElement:!0,
overlayOpacity:0.8,scrollPadding:30,positionPrecedence:["bottom","top","right","left"],disableInteraction:!1,hintPosition:"top-middle",hintButtonLabel:"Got it",hintAnimation:!0}}function V(a){var b=[],c=this;if(this._options.steps)for(var d=0,e=this._options.steps.length;d<e;d++){var f=y(this._options.steps[d]);f.step=b.length+1;"string"===typeof f.element&&(f.element=document.querySelector(f.element));if("undefined"===typeof f.element||null==f.element){var g=document.querySelector(".introjsFloatingElement");
null==g&&(g=document.createElement("div"),g.className="introjsFloatingElement",document.body.appendChild(g));f.element=g;f.position="floating"}null!=f.element&&b.push(f)}else{e=a.querySelectorAll("*[data-intro]");if(1>e.length)return!1;d=0;for(f=e.length;d<f;d++)if(g=e[d],"none"!=g.style.display){var k=parseInt(g.getAttribute("data-step"),10);0<k&&(b[k-1]={element:g,intro:g.getAttribute("data-intro"),step:parseInt(g.getAttribute("data-step"),10),tooltipClass:g.getAttribute("data-tooltipClass"),highlightClass:g.getAttribute("data-highlightClass"),
position:g.getAttribute("data-position")||this._options.tooltipPosition})}d=k=0;for(f=e.length;d<f;d++)if(g=e[d],null==g.getAttribute("data-step")){for(;"undefined"!=typeof b[k];)k++;b[k]={element:g,intro:g.getAttribute("data-intro"),step:k+1,tooltipClass:g.getAttribute("data-tooltipClass"),highlightClass:g.getAttribute("data-highlightClass"),position:g.getAttribute("data-position")||this._options.tooltipPosition}}}d=[];for(e=0;e<b.length;e++)b[e]&&d.push(b[e]);b=d;b.sort(function(a,b){return a.step-
b.step});c._introItems=b;W.call(c,a)&&(x.call(c),a.querySelector(".introjs-skipbutton"),a.querySelector(".introjs-nextbutton"),c._onKeyDown=function(b){if(27===b.keyCode&&!0==c._options.exitOnEsc)z.call(c,a);else if(37===b.keyCode)E.call(c);else if(39===b.keyCode)x.call(c);else if(13===b.keyCode){var d=b.target||b.srcElement;d&&0<d.className.indexOf("introjs-prevbutton")?E.call(c):d&&0<d.className.indexOf("introjs-skipbutton")?(c._introItems.length-1==c._currentStep&&"function"===typeof c._introCompleteCallback&&
c._introCompleteCallback.call(c),z.call(c,a)):x.call(c);b.preventDefault?b.preventDefault():b.returnValue=!1}},c._onResize=function(a){t.call(c,document.querySelector(".introjs-helperLayer"));t.call(c,document.querySelector(".introjs-tooltipReferenceLayer"))},window.addEventListener?(this._options.keyboardNavigation&&window.addEventListener("keydown",c._onKeyDown,!0),window.addEventListener("resize",c._onResize,!0)):document.attachEvent&&(this._options.keyboardNavigation&&document.attachEvent("onkeydown",
c._onKeyDown),document.attachEvent("onresize",c._onResize)));return!1}function y(a){if(null==a||"object"!=typeof a||"undefined"!=typeof a.nodeType)return a;var b={},c;for(c in a)b[c]="undefined"!=typeof jQuery&&a[c]instanceof jQuery?a[c]:y(a[c]);return b}function x(){this._direction="forward";if("undefined"!==typeof this._currentStepNumber)for(var a=0,b=this._introItems.length;a<b;a++)this._introItems[a].step===this._currentStepNumber&&(this._currentStep=a-1,this._currentStepNumber=void 0);"undefined"===
typeof this._currentStep?this._currentStep=0:++this._currentStep;this._introItems.length<=this._currentStep?("function"===typeof this._introCompleteCallback&&this._introCompleteCallback.call(this),z.call(this,this._targetElement)):(a=this._introItems[this._currentStep],"undefined"!==typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,a.element),N.call(this,a))}function E(){this._direction="backward";if(0===this._currentStep)return!1;var a=this._introItems[--this._currentStep];
"undefined"!==typeof this._introBeforeChangeCallback&&this._introBeforeChangeCallback.call(this,a.element);N.call(this,a)}function z(a){var b=a.querySelectorAll(".introjs-overlay");if(b&&0<b.length)for(var c=b.length-1;0<=c;c--){var d=b[c];d.style.opacity=0;setTimeout(function(){this.parentNode&&this.parentNode.removeChild(this)}.bind(d),500)}(c=a.querySelector(".introjs-helperLayer"))&&c.parentNode.removeChild(c);(c=a.querySelector(".introjs-tooltipReferenceLayer"))&&c.parentNode.removeChild(c);
(a=a.querySelector(".introjs-disableInteraction"))&&a.parentNode.removeChild(a);(a=document.querySelector(".introjsFloatingElement"))&&a.parentNode.removeChild(a);O();if((a=document.querySelectorAll(".introjs-fixParent"))&&0<a.length)for(c=a.length-1;0<=c;c--)a[c].className=a[c].className.replace(/introjs-fixParent/g,"").replace(/^\s+|\s+$/g,"");window.removeEventListener?window.removeEventListener("keydown",this._onKeyDown,!0):document.detachEvent&&document.detachEvent("onkeydown",this._onKeyDown);
void 0!=this._introExitCallback&&this._introExitCallback.call(self);this._currentStep=void 0}function F(a,b,c,d,e){var f="",g,k;e=e||!1;b.style.top=null;b.style.right=null;b.style.bottom=null;b.style.left=null;b.style.marginLeft=null;b.style.marginTop=null;c.style.display="inherit";"undefined"!=typeof d&&null!=d&&(d.style.top=null,d.style.left=null);if(this._introItems[this._currentStep]){f=this._introItems[this._currentStep];f="string"===typeof f.tooltipClass?f.tooltipClass:this._options.tooltipClass;
b.className=("introjs-tooltip "+f).replace(/^\s+|\s+$/g,"");k=this._introItems[this._currentStep].position;if(("auto"==k||"auto"==this._options.tooltipPosition)&&"floating"!=k){f=k;g=this._options.positionPrecedence.slice();k=G();var w=u(b).height+10,n=u(b).width+20,h=u(a),l="floating";h.left+n>k.width||0>h.left+h.width/2-n?(s(g,"bottom"),s(g,"top")):(h.height+h.top+w>k.height&&s(g,"bottom"),0>h.top-w&&s(g,"top"));h.width+h.left+n>k.width&&s(g,"right");0>h.left-n&&s(g,"left");0<g.length&&(l=g[0]);
f&&"auto"!=f&&-1<g.indexOf(f)&&(l=f);k=l}f=u(a);a=u(b);g=G();switch(k){case "top":c.className="introjs-arrow bottom";H(f,e?0:15,a,g,b);b.style.bottom=f.height+20+"px";break;case "right":b.style.left=f.width+20+"px";f.top+a.height>g.height?(c.className="introjs-arrow left-bottom",b.style.top="-"+(a.height-f.height-20)+"px"):c.className="introjs-arrow left";break;case "left":e||!0!=this._options.showStepNumbers||(b.style.top="15px");f.top+a.height>g.height?(b.style.top="-"+(a.height-f.height-20)+"px",
c.className="introjs-arrow right-bottom"):c.className="introjs-arrow right";b.style.right=f.width+20+"px";break;case "floating":c.style.display="none";b.style.left="50%";b.style.top="50%";b.style.marginLeft="-"+a.width/2+"px";b.style.marginTop="-"+a.height/2+"px";"undefined"!=typeof d&&null!=d&&(d.style.left="-"+(a.width/2+18)+"px",d.style.top="-"+(a.height/2+18)+"px");break;case "bottom-right-aligned":c.className="introjs-arrow top-right";P(f,0,a,b);b.style.top=f.height+20+"px";break;case "bottom-middle-aligned":c.className=
"introjs-arrow top-middle";c=f.width/2-a.width/2;e&&(c+=5);P(f,c,a,b)&&(b.style.right=null,H(f,c,a,g,b));b.style.top=f.height+20+"px";break;default:c.className="introjs-arrow top",H(f,0,a,g,b),b.style.top=f.height+20+"px"}}}function H(a,b,c,d,e){if(a.left+b+c.width>d.width)return e.style.left=d.width-c.width-a.left+"px",!1;e.style.left=b+"px";return!0}function P(a,b,c,d){if(0>a.left+a.width-b-c.width)return d.style.left=-a.left+"px",!1;d.style.right=b+"px";return!0}function s(a,b){-1<a.indexOf(b)&&
a.splice(a.indexOf(b),1)}function t(a){if(a&&this._introItems[this._currentStep]){var b=this._introItems[this._currentStep],c=u(b.element),d=10;I(b.element)?a.className+=" introjs-fixedTooltip":a.className=a.className.replace(" introjs-fixedTooltip","");"floating"==b.position&&(d=0);a.setAttribute("style","width: "+(c.width+d)+"px; height:"+(c.height+d)+"px; top:"+(c.top-5)+"px;left: "+(c.left-5)+"px;")}}function X(){var a=document.querySelector(".introjs-disableInteraction");null===a&&(a=document.createElement("div"),
a.className="introjs-disableInteraction",this._targetElement.appendChild(a));t.call(this,a)}function D(a){a.setAttribute("role","button");a.tabIndex=0}function N(a){"undefined"!==typeof this._introChangeCallback&&this._introChangeCallback.call(this,a.element);var b=this,c=document.querySelector(".introjs-helperLayer"),d=document.querySelector(".introjs-tooltipReferenceLayer"),e="introjs-helperLayer";u(a.element);"string"===typeof a.highlightClass&&(e+=" "+a.highlightClass);"string"===typeof this._options.highlightClass&&
(e+=" "+this._options.highlightClass);if(null!=c){var f=d.querySelector(".introjs-helperNumberLayer"),g=d.querySelector(".introjs-tooltiptext"),k=d.querySelector(".introjs-arrow"),w=d.querySelector(".introjs-tooltip"),n=d.querySelector(".introjs-skipbutton"),h=d.querySelector(".introjs-prevbutton"),l=d.querySelector(".introjs-nextbutton");c.className=e;w.style.opacity=0;w.style.display="none";if(null!=f){var p=this._introItems[0<=a.step-2?a.step-2:0];if(null!=p&&"forward"==this._direction&&"floating"==
p.position||"backward"==this._direction&&"floating"==a.position)f.style.opacity=0}t.call(b,c);t.call(b,d);if((p=document.querySelectorAll(".introjs-fixParent"))&&0<p.length)for(e=p.length-1;0<=e;e--)p[e].className=p[e].className.replace(/introjs-fixParent/g,"").replace(/^\s+|\s+$/g,"");O();b._lastShowElementTimer&&clearTimeout(b._lastShowElementTimer);b._lastShowElementTimer=setTimeout(function(){null!=f&&(f.innerHTML=a.step);g.innerHTML=a.intro;w.style.display="block";F.call(b,a.element,w,k,f);b._options.showBullets&&
(d.querySelector(".introjs-bullets li > a.active").className="",d.querySelector('.introjs-bullets li > a[data-stepnumber="'+a.step+'"]').className="active");d.querySelector(".introjs-progress .introjs-progressbar").setAttribute("style","width:"+Q.call(b)+"%;");w.style.opacity=1;f&&(f.style.opacity=1);-1===l.tabIndex?n.focus():l.focus()},350)}else{var m=document.createElement("div"),h=document.createElement("div"),c=document.createElement("div"),q=document.createElement("div"),r=document.createElement("div"),
s=document.createElement("div"),v=document.createElement("div"),A=document.createElement("div");m.className=e;h.className="introjs-tooltipReferenceLayer";t.call(b,m);t.call(b,h);this._targetElement.appendChild(m);this._targetElement.appendChild(h);c.className="introjs-arrow";r.className="introjs-tooltiptext";r.innerHTML=a.intro;s.className="introjs-bullets";!1===this._options.showBullets&&(s.style.display="none");for(var m=document.createElement("ul"),e=0,C=this._introItems.length;e<C;e++){var y=
document.createElement("li"),B=document.createElement("a");B.onclick=function(){b.goToStep(this.getAttribute("data-stepnumber"))};e===a.step-1&&(B.className="active");D(B);B.innerHTML="&nbsp;";B.setAttribute("data-stepnumber",this._introItems[e].step);y.appendChild(B);m.appendChild(y)}s.appendChild(m);v.className="introjs-progress";!1===this._options.showProgress&&(v.style.display="none");e=document.createElement("div");e.className="introjs-progressbar";e.setAttribute("style","width:"+Q.call(this)+
"%;");v.appendChild(e);A.className="introjs-tooltipbuttons";!1===this._options.showButtons&&(A.style.display="none");q.className="introjs-tooltip";q.appendChild(r);q.appendChild(s);q.appendChild(v);!0==this._options.showStepNumbers&&(p=document.createElement("span"),p.className="introjs-helperNumberLayer",p.innerHTML=a.step,h.appendChild(p));q.appendChild(c);h.appendChild(q);l=document.createElement("a");l.onclick=function(){b._introItems.length-1!=b._currentStep&&x.call(b)};D(l);l.innerHTML=this._options.nextLabel;
h=document.createElement("a");h.onclick=function(){0!=b._currentStep&&E.call(b)};D(h);h.innerHTML=this._options.prevLabel;n=document.createElement("a");n.className="introjs-button introjs-skipbutton";D(n);n.innerHTML=this._options.skipLabel;n.onclick=function(){b._introItems.length-1==b._currentStep&&"function"===typeof b._introCompleteCallback&&b._introCompleteCallback.call(b);z.call(b,b._targetElement)};A.appendChild(n);1<this._introItems.length&&(A.appendChild(h),A.appendChild(l));q.appendChild(A);
F.call(b,a.element,q,c,p)}!0===this._options.disableInteraction&&X.call(b);h.removeAttribute("tabIndex");l.removeAttribute("tabIndex");0==this._currentStep&&1<this._introItems.length?(l.className="introjs-button introjs-nextbutton",!0==this._options.hidePrev?(h.className="introjs-button introjs-prevbutton introjs-hidden",l.className+=" introjs-fullbutton"):h.className="introjs-button introjs-prevbutton introjs-disabled",h.tabIndex="-1",n.innerHTML=this._options.skipLabel):this._introItems.length-
1==this._currentStep||1==this._introItems.length?(n.innerHTML=this._options.doneLabel,n.className="introjs-button introjs-donebutton",h.className="introjs-button introjs-prevbutton",!0==this._options.hideNext?(l.className="introjs-button introjs-nextbutton introjs-hidden",h.className+=" introjs-fullbutton"):l.className="introjs-button introjs-nextbutton introjs-disabled",l.tabIndex="-1"):(h.className="introjs-button introjs-prevbutton",l.className="introjs-button introjs-nextbutton",n.innerHTML=this._options.skipLabel);
l.focus();Y(a);Z(a.element)||!0!==this._options.scrollToElement||(q=a.element.getBoundingClientRect(),p=G().height,c=q.bottom-(q.bottom-q.top),q=q.bottom-p,0>c||a.element.clientHeight>p?window.scrollBy(0,c-this._options.scrollPadding):window.scrollBy(0,q+70+this._options.scrollPadding));"undefined"!==typeof this._introAfterChangeCallback&&this._introAfterChangeCallback.call(this,a.element)}function O(){for(var a=document.querySelectorAll(".introjs-showElement"),b=0,c=a.length;b<c;b++){var d=a[b],
e=/introjs-[a-zA-Z]+/g;if(d instanceof SVGElement){var f=d.getAttribute("class")||"";d.setAttribute("class",f.replace(e,"").replace(/^\s+|\s+$/g,""))}else d.className=d.className.replace(e,"").replace(/^\s+|\s+$/g,"")}}function Y(a){if(a.element instanceof SVGElement)for(var b=a.element.parentNode;null!=a.element.parentNode&&b.tagName&&"body"!==b.tagName.toLowerCase();)"svg"===b.tagName.toLowerCase()&&J(b,"introjs-showElement introjs-relativePosition"),b=b.parentNode;J(a.element,"introjs-showElement");
b=r(a.element,"position");"absolute"!==b&&("relative"!==b&&"fixed"!==b)&&J(a.element,"introjs-relativePosition");for(b=a.element.parentNode;null!=b&&b.tagName&&"body"!==b.tagName.toLowerCase();){a=r(b,"z-index");var c=parseFloat(r(b,"opacity")),d=r(b,"transform")||r(b,"-webkit-transform")||r(b,"-moz-transform")||r(b,"-ms-transform")||r(b,"-o-transform");if(/[0-9]+/.test(a)||1>c||"none"!==d&&void 0!==d)b.className+=" introjs-fixParent";b=b.parentNode}}function J(a,b){if(a instanceof SVGElement){var c=
a.getAttribute("class")||"";a.setAttribute("class",c+" "+b)}else a.className+=" "+b}function r(a,b){var c="";a.currentStyle?c=a.currentStyle[b]:document.defaultView&&document.defaultView.getComputedStyle&&(c=document.defaultView.getComputedStyle(a,null).getPropertyValue(b));return c&&c.toLowerCase?c.toLowerCase():c}function I(a){var b=a.parentNode;return b&&"HTML"!==b.nodeName?"fixed"==r(a,"position")?!0:I(b):!1}function G(){if(void 0!=window.innerWidth)return{width:window.innerWidth,height:window.innerHeight};
var a=document.documentElement;return{width:a.clientWidth,height:a.clientHeight}}function Z(a){a=a.getBoundingClientRect();return 0<=a.top&&0<=a.left&&a.bottom+80<=window.innerHeight&&a.right<=window.innerWidth}function W(a){var b=document.createElement("div"),c="",d=this;b.className="introjs-overlay";if(a.tagName&&"body"!==a.tagName.toLowerCase()){var e=u(a);e&&(c+="width: "+e.width+"px; height:"+e.height+"px; top:"+e.top+"px;left: "+e.left+"px;",b.setAttribute("style",c))}else c+="top: 0;bottom: 0; left: 0;right: 0;position: fixed;",
b.setAttribute("style",c);a.appendChild(b);b.onclick=function(){!0==d._options.exitOnOverlayClick&&z.call(d,a)};setTimeout(function(){c+="opacity: "+d._options.overlayOpacity.toString()+";";b.setAttribute("style",c)},10);return!0}function v(){var a=this._targetElement.querySelector(".introjs-hintReference");if(a){var b=a.getAttribute("data-step");a.parentNode.removeChild(a);return b}}function R(a){this._introItems=[];if(this._options.hints){a=0;for(var b=this._options.hints.length;a<b;a++){var c=
y(this._options.hints[a]);"string"===typeof c.element&&(c.element=document.querySelector(c.element));c.hintPosition=c.hintPosition||this._options.hintPosition;c.hintAnimation=c.hintAnimation||this._options.hintAnimation;null!=c.element&&this._introItems.push(c)}}else{c=a.querySelectorAll("*[data-hint]");if(1>c.length)return!1;a=0;for(b=c.length;a<b;a++){var d=c[a],e=d.getAttribute("data-hintAnimation"),e=e?"true"==e:this._options.hintAnimation;this._introItems.push({element:d,hint:d.getAttribute("data-hint"),
hintPosition:d.getAttribute("data-hintPosition")||this._options.hintPosition,hintAnimation:e,tooltipClass:d.getAttribute("data-tooltipClass"),position:d.getAttribute("data-position")||this._options.tooltipPosition})}}$.call(this);document.addEventListener?(document.addEventListener("click",v.bind(this),!1),window.addEventListener("resize",K.bind(this),!0)):document.attachEvent&&(document.attachEvent("onclick",v.bind(this)),document.attachEvent("onresize",K.bind(this)))}function K(){for(var a=0,b=
this._introItems.length;a<b;a++){var c=this._introItems[a];"undefined"!=typeof c.targetElement&&S.call(this,c.hintPosition,c.element,c.targetElement)}}function L(a){v.call(this);var b=this._targetElement.querySelector('.introjs-hint[data-step="'+a+'"]');b&&(b.className+=" introjs-hidehint");"undefined"!==typeof this._hintCloseCallback&&this._hintCloseCallback.call(this,a)}function T(a){if(a=this._targetElement.querySelector('.introjs-hint[data-step="'+a+'"]'))a.className=a.className.replace(/introjs\-hidehint/g,
"")}function U(a){(a=this._targetElement.querySelector('.introjs-hint[data-step="'+a+'"]'))&&a.parentNode.removeChild(a)}function $(){var a=this,b=document.querySelector(".introjs-hints");null==b&&(b=document.createElement("div"),b.className="introjs-hints");for(var c=0,d=this._introItems.length;c<d;c++){var e=this._introItems[c];if(!document.querySelector('.introjs-hint[data-step="'+c+'"]')){var f=document.createElement("a");D(f);(function(b,c,d){b.onclick=function(e){e=e?e:window.event;e.stopPropagation&&
e.stopPropagation();null!=e.cancelBubble&&(e.cancelBubble=!0);aa.call(a,b,c,d)}})(f,e,c);f.className="introjs-hint";e.hintAnimation||(f.className+=" introjs-hint-no-anim");I(e.element)&&(f.className+=" introjs-fixedhint");var g=document.createElement("div");g.className="introjs-hint-dot";var k=document.createElement("div");k.className="introjs-hint-pulse";f.appendChild(g);f.appendChild(k);f.setAttribute("data-step",c);e.targetElement=e.element;e.element=f;S.call(this,e.hintPosition,f,e.targetElement);
b.appendChild(f)}}document.body.appendChild(b);"undefined"!==typeof this._hintsAddedCallback&&this._hintsAddedCallback.call(this)}function S(a,b,c){c=u.call(this,c);switch(a){default:case "top-left":b.style.left=c.left+"px";b.style.top=c.top+"px";break;case "top-right":b.style.left=c.left+c.width+"px";b.style.top=c.top+"px";break;case "bottom-left":b.style.left=c.left+"px";b.style.top=c.top+c.height+"px";break;case "bottom-right":b.style.left=c.left+c.width+"px";b.style.top=c.top+c.height+"px";break;
case "bottom-middle":b.style.left=c.left+c.width/2+"px";b.style.top=c.top+c.height+"px";break;case "top-middle":b.style.left=c.left+c.width/2+"px",b.style.top=c.top+"px"}}function aa(a,b,c){"undefined"!==typeof this._hintClickCallback&&this._hintClickCallback.call(this,a,b,c);var d=v.call(this);if(parseInt(d,10)!=c){var d=document.createElement("div"),e=document.createElement("div"),f=document.createElement("div"),g=document.createElement("div");d.className="introjs-tooltip";d.onclick=function(a){a.stopPropagation?
a.stopPropagation():a.cancelBubble=!0};e.className="introjs-tooltiptext";var k=document.createElement("p");k.innerHTML=b.hint;b=document.createElement("a");b.className="introjs-button";b.innerHTML=this._options.hintButtonLabel;b.onclick=L.bind(this,c);e.appendChild(k);e.appendChild(b);f.className="introjs-arrow";d.appendChild(f);d.appendChild(e);this._currentStep=a.getAttribute("data-step");g.className="introjs-tooltipReferenceLayer introjs-hintReference";g.setAttribute("data-step",a.getAttribute("data-step"));
t.call(this,g);g.appendChild(d);document.body.appendChild(g);F.call(this,a,d,f,null,!0)}}function u(a){var b={},c=document.body,d=document.documentElement,e=window.pageYOffset||d.scrollTop||c.scrollTop,c=window.pageXOffset||d.scrollLeft||c.scrollLeft;if(a instanceof SVGElement)a=a.getBoundingClientRect(),b.top=a.top+e,b.width=a.width,b.height=a.height,b.left=a.left+c;else{b.width=a.offsetWidth;b.height=a.offsetHeight;for(c=e=0;a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop);)e+=a.offsetLeft,c+=a.offsetTop,
a=a.offsetParent;b.top=c;b.left=e}return b}function Q(){return 100*(parseInt(this._currentStep+1,10)/this._introItems.length)}var M=function(a){if("object"===typeof a)return new m(a);if("string"===typeof a){if(a=document.querySelector(a))return new m(a);throw Error("There is no element with given selector.");}return new m(document.body)};M.version="2.5.0";M.fn=m.prototype={clone:function(){return new m(this)},setOption:function(a,b){this._options[a]=b;return this},setOptions:function(a){var b=this._options,
c={},d;for(d in b)c[d]=b[d];for(d in a)c[d]=a[d];this._options=c;return this},start:function(){V.call(this,this._targetElement);return this},goToStep:function(a){this._currentStep=a-2;"undefined"!==typeof this._introItems&&x.call(this);return this},addStep:function(a){this._options.steps||(this._options.steps=[]);this._options.steps.push(a);return this},addSteps:function(a){if(a.length){for(var b=0;b<a.length;b++)this.addStep(a[b]);return this}},goToStepNumber:function(a){this._currentStepNumber=
a;"undefined"!==typeof this._introItems&&x.call(this);return this},nextStep:function(){x.call(this);return this},previousStep:function(){E.call(this);return this},exit:function(){z.call(this,this._targetElement);return this},refresh:function(){t.call(this,document.querySelector(".introjs-helperLayer"));t.call(this,document.querySelector(".introjs-tooltipReferenceLayer"));K.call(this);return this},onbeforechange:function(a){if("function"===typeof a)this._introBeforeChangeCallback=a;else throw Error("Provided callback for onbeforechange was not a function");
return this},onchange:function(a){if("function"===typeof a)this._introChangeCallback=a;else throw Error("Provided callback for onchange was not a function.");return this},onafterchange:function(a){if("function"===typeof a)this._introAfterChangeCallback=a;else throw Error("Provided callback for onafterchange was not a function");return this},oncomplete:function(a){if("function"===typeof a)this._introCompleteCallback=a;else throw Error("Provided callback for oncomplete was not a function.");return this},
onhintsadded:function(a){if("function"===typeof a)this._hintsAddedCallback=a;else throw Error("Provided callback for onhintsadded was not a function.");return this},onhintclick:function(a){if("function"===typeof a)this._hintClickCallback=a;else throw Error("Provided callback for onhintclick was not a function.");return this},onhintclose:function(a){if("function"===typeof a)this._hintCloseCallback=a;else throw Error("Provided callback for onhintclose was not a function.");return this},onexit:function(a){if("function"===
typeof a)this._introExitCallback=a;else throw Error("Provided callback for onexit was not a function.");return this},addHints:function(){R.call(this,this._targetElement);return this},hideHint:function(a){L.call(this,a);return this},hideHints:function(){var a=this._targetElement.querySelectorAll(".introjs-hint");if(a&&0<a.length)for(var b=0;b<a.length;b++)L.call(this,a[b].getAttribute("data-step"));return this},showHint:function(a){T.call(this,a);return this},showHints:function(){var a=this._targetElement.querySelectorAll(".introjs-hint");
if(a&&0<a.length)for(var b=0;b<a.length;b++)T.call(this,a[b].getAttribute("data-step"));else R.call(this,this._targetElement);return this},removeHints:function(){var a=this._targetElement.querySelectorAll(".introjs-hint");if(a&&0<a.length)for(var b=0;b<a.length;b++)U.call(this,a[b].getAttribute("data-step"));return this},removeHint:function(a){U.call(this,a);return this}};return C.introJs=M});



function runHelp(_firstTime)
{
	// on first time load css
	if(_firstTime)
	{
		importCSS('/static/css/lib/introjs.css');
	}
	console.log("Need help?");
	var intro = introJs();
	// get below json from
	// intro.setOptions(
	// {
	// 	steps:
	// 	[
	// 		{
	// 			intro: "Hello world!"
	// 		},
	// 		{
	// 			element: document.querySelector('#step1'),
	// 			intro: "This is a tooltip."
	// 		},
	// 		{
	// 			element: document.querySelectorAll('#step2')[0],
	// 			intro: "Ok, wasn't that fun?",
	// 			position: 'right'
	// 		},
	// 		{
	// 			element: '#step3',
	// 			intro: 'More features, more fun.',
	// 			position: 'left'
	// 		},
	// 		{
	// 			element: '#step4',
	// 			intro: "Another step.",
	// 			position: 'bottom'
	// 		},
	// 		{
	// 			element: '#step5',
	// 			intro: 'Get it, use it.'
	// 		}
	// 	]
	// }
	// );

	intro.start();
}