var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","jquery","aos","es6-promise","ekyna-modal","../../dispatcher","../base-plugin","../../document-manager"],function(a,b,c,d,e,f,g,h,i){"use strict";e.polyfill();var j=(e.Promise,function(a){function b(){a.apply(this,arguments)}return __extends(b,a),b.prototype.edit=function(){var b=this;a.prototype.edit.call(this),this.modal=new f,this.modal.load({url:i.BlockManager.generateUrl(this.$element,"ekyna_cms_editor_block_edit"),method:"GET"}),c(this.modal).on("ekyna.modal.response",function(a){if("json"==a.contentType&&(a.preventDefault(),a.content.hasOwnProperty("blocks"))){i.BlockManager.parse(a.content.blocks);var c=new i.SelectionEvent;c.$element=b.$element,g["default"].trigger("document_manager.select",c),d.refresh()}})},b.prototype.destroy=function(){var b=this;return this.save().then(function(){return b.modal&&(b.modal.close(),b.modal=null),a.prototype.destroy.call(b)})},b}(h.BasePlugin));return j});