var __extends=this&&this.__extends||function(){var a=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(a,b){a.__proto__=b}||function(a,b){for(var c in b)b.hasOwnProperty(c)&&(a[c]=b[c])};return function(b,c){function d(){this.constructor=b}a(b,c),b.prototype=null===c?Object.create(c):(d.prototype=c.prototype,new d)}}();define(["require","exports","es6-promise","underscore","gsap/TimelineLite","./model","json!ekyna-cms/slide-types"],function(a,b,c,d,e,f,g){"use strict";c.polyfill();var h=c.Promise,i=function(){function b(){}return b.initialize=function(a){b.config=a,b.types={}},b.getType=function(c){return new h(function(d,e){if(b.types.hasOwnProperty(c))d(b.types[c]);else if(b.config.hasOwnProperty(c))try{a([b.config[c]],function(a){b.types[c]=new a,d(b.types[c])})}catch(f){e(f)}else e("Undefined type.")})},b}();i.initialize(g);var j=function(){function a(a){this.timeline=a}return a.prototype.show=function(){this.timeline.play(0,!1)},a.prototype.hide=function(a){if(void 0===a&&(a=!1),a)this.timeline.seek(0,!1),this.timeline.pause();else{var b=void 0;(b=this.timeline.eventCallback("onReverse"))&&b(),this.timeline.reverse()}},a}(),k={id:null,types:{},ui:!0,uiSpeed:.2,auto:!0,autoDelay:7,debug:!1},l=function(a){function b(){return null!==a&&a.apply(this,arguments)||this}return __extends(b,a),b.create=function(a){var c=new b;return c.init(a),c},b.prototype.init=function(a){var b=this;if(this.initialized=!1,this.root=document.getElementById(a.id),!this.root)return void console.log("Slide show's root element not found.");var c={};this.root.hasAttribute("data-config")&&(c=JSON.parse(this.root.getAttribute("data-config"))),this.options=d.defaults({},a,c,k),this.slides=[],this.busy=!1,this.autoPlaying=!0;for(var e=this.root.getElementsByClassName("cms-slides").item(0).children,f=[],g=function(a){var c=e.item(a);c.style.setProperty("display","none"),f.push(i.getType(c.getAttribute("data-type")).then(function(a){return new j(a.build(c,b))}))},l=0;l<e.length;l++)g(l);h.all(f).then(function(a){b.slides=a,b.buildUi(),b.transitionTo(0),b.initialized=!0,b.trigger("ekyna_cms.slide_show.initialized"),b.on("ekyna_cms.slide.timeout",function(){b.nextSlide()}),b.log("initialized")},function(a){console.log(a)})},b.prototype.autoNext=function(){this.options.auto&&this.autoPlaying&&this.timerTimeline.restart(!1,!1)},b.prototype.transitionTo=function(a,b){var c=this;if(void 0===b&&(b=!1),(b||!this.busy)&&this.index!==a){if(!this.initialized)return void this.once("ekyna_cms.slide_show.initialized",function(){c.transitionTo(a,b)});this.index=a,this.log("show "+this.index);var d=this.slides[this.index];b?(this.current&&this.current.hide(!0),this.showSlide(d)):this.current?this.hideSlide(this.current,d):this.showSlide(d)}},b.prototype.nextSlide=function(){if(this.log("next"),!this.busy){var a=this.index;a++,a>=this.slides.length&&(a=0),this.transitionTo(a)}},b.prototype.prevSlide=function(){if(this.log("prev"),!this.busy){var a=this.index;a--,a<0&&(a=this.slides.length-1),this.transitionTo(a)}},b.prototype.hideSlide=function(a,b){var c=this;this.busy=!0,this.once("ekyna_cms.slide.hidden",function(){c.showSlide(b)}),a.hide()},b.prototype.showSlide=function(a){var b=this;if(this.busy=!0,this.once("ekyna_cms.slide.shown",function(){b.busy=!1,b.autoNext()}),this.current=a,this.current.show(),this.options.ui){for(var c=0;c<this.nav.children.length;c++){var d=this.nav.children.item(c);d.classList.remove("active")}this.nav.children.item(this.index).classList.add("active")}},b.prototype.buildUi=function(){var a=this;if(this.options.ui&&!(1>=this.slides.length)){this.uiTimeline=new e,this.nav=document.createElement("ul");for(var b=.1,c=function(c){var e=document.createElement("li"),f=document.createElement("a");f.href="javascript: void(0)",f.classList.add("slide"),f.addEventListener("click",function(){a.busy||(a.timerTimeline.pause(),a.transitionTo(c))}),e.appendChild(f),d.nav.appendChild(e),d.uiTimeline.from(e,d.options.uiSpeed,{y:20,opacity:0,ease:Back.easeOut},b*c)},d=this,f=0;f<this.slides.length;f++)c(f);if(this.options.auto){var g=document.createElement("li"),h=document.createElement("a");h.href="javascript: void(0)",h.classList.add("play"),h.classList.add("pause"),h.addEventListener("click",function(){a.busy||(a.autoPlaying=!a.autoPlaying,a.autoPlaying?(h.classList.add("pause"),a.timerTimeline.resume()):(h.classList.remove("pause"),a.timerTimeline.pause()))}),g.appendChild(h),this.nav.appendChild(g),this.uiTimeline.from(g,this.options.uiSpeed,{y:20,opacity:0,ease:Back.easeOut},b*this.slides.length)}var i=document.createElement("div");i.classList.add("cms-slide-show-nav"),i.appendChild(this.nav),this.root.appendChild(i);var j=document.createElement("div");j.classList.add("cms-slide-show-timer"),this.root.appendChild(j),this.timerTimeline=new e,this.timerTimeline.to(j,this.options.autoDelay,{width:"100%",ease:Power0.easeNone,onComplete:function(){a.trigger("ekyna_cms.slide.timeout")}}),this.timerTimeline.to(j,.15,{opacity:0}),this.timerTimeline.pause();var k=document.createElement("a"),l=document.createElement("a");k.classList.add("cms-slide-show-prev"),k.href="javascript: void(0)",k.addEventListener("click",function(){a.timerTimeline.pause(),a.prevSlide()}),this.root.appendChild(k),this.uiTimeline.from(k,this.options.uiSpeed,{x:"-100% 0"},0),l.classList.add("cms-slide-show-next"),l.href="javascript: void(0)",l.addEventListener("click",function(){a.timerTimeline.pause(),a.nextSlide()}),this.root.appendChild(l),this.uiTimeline.from(l,this.options.uiSpeed,{x:"100% 0"},0),this.uiTimeline.pause(),this.root.addEventListener("mouseenter",function(){a.uiTimeline.play()}),this.root.addEventListener("mouseleave",function(){a.uiTimeline.reverse()})}},b.prototype.log=function(a){this.options.debug&&console.log("[SlideShow]",a)},b}(f.Dispatcher);return l});