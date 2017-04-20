var __extends=this&&this.__extends||function(){var a=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(a,b){a.__proto__=b}||function(a,b){for(var c in b)b.hasOwnProperty(c)&&(a[c]=b[c])};return function(b,c){function d(){this.constructor=b}a(b,c),b.prototype=null===c?Object.create(c):(d.prototype=c.prototype,new d)}}();define(["require","exports","jquery","underscore","es6-promise","routing","./dispatcher","./ui","./layout/bootstrap3","./layout/common","jquery-ui/widgets/slider"],function(a,b,c,d,e,f,g,h,i,j){"use strict";Object.defineProperty(b,"__esModule",{value:!0}),e.polyfill();var k=e.Promise,l={edit:!1},m={edit:!1,layout:!1,change_type:!1,move_left:!1,move_right:!1,move_up:!1,move_down:!1,add:!1,remove:!1},n={layout:!1,move_up:!1,move_down:!1,add:!1,remove:!1},o={layout:!1,move_up:!1,move_down:!1,add:!1,remove:!1},p=function(){function a(){}return a.setContentWindow=function(a){this.contentWindow=a},a.getContentWindow=function(){if(!this.contentWindow)throw"Window is not defined.";return this.contentWindow},a.setContentDocument=function(a){this.$contentDocument=a;var b=a.find("html").data("cms-editor-document");if(!b)throw"Undefined document data.\nDid you forget to use the cms_document_data() twig function in your template ?";this.setDocumentData(b)},a.getContentDocument=function(){if(!this.$contentDocument)throw"Document is not defined.";return this.$contentDocument},a.setDocumentData=function(a){this.documentData=a},a.getDocumentData=function(){return this.documentData},a.getDocumentLocale=function(){if(!this.documentData)throw"Content data is not defined.";return this.documentData.locale},a.clear=function(){this.contentWindow=null,this.$contentDocument=null,this.documentData=null},a.findElementById=function(a){return this.$contentDocument.find("#"+a)},a.createElement=function(a,b){if(!b)throw"Undefined parent.";return c("<div></div>").attr("id",a).appendTo(b)},a.findOrCreateElement=function(a,b){var c=this.findElementById(a);return 0==c.length&&(c=this.createElement(a,b)),c},a.setElementAttributes=function(b,c){for(var d=b.get(0),e=d.attributes,f=e.length;f--;)d.removeAttributeNode(e[f]);for(var g in c)"data"!=g?b.removeAttr(g).attr(g,c[g]):b.removeAttr("data-cms").data("cms",c[g]);a.appendHelpers(b)},a.sortChildren=function(a,b){var d=a.find(b);d.detach().get().sort(function(a,b){var d=c(a).data("cms").position,e=c(b).data("cms").position;return d==e?0:d>e?1:-1}).forEach(function(b){a.append(b)})},a.generateUrl=function(b,c){return f.generate(b,d.extend({},{_document_locale:a.getDocumentLocale()},c||{}))},a.request=function(b){g["default"].trigger("editor.set_busy"),b=d.extend({},b,{method:"POST"}),b.data||(b.data={}),b.data.cms_viewport_width=a.getContentWindow().innerWidth;var e=c.ajax(b);return e.done(function(b){a.parse(b)}),e.fail(function(a,b,c){console.log("Editor request failed.")}),e.always(function(){g["default"].trigger("editor.unset_busy")}),e},a.parse=function(b){if(b.hasOwnProperty("error"))return void alert(b.error);b.hasOwnProperty("removed")&&b.removed.forEach(function(b){a.findElementById(b).remove()}),b.hasOwnProperty("content")?q.parse(b.content):b.hasOwnProperty("containers")?r.parse(b.containers):b.hasOwnProperty("rows")?s.parse(b.rows):b.hasOwnProperty("blocks")?t.parse(b.blocks):b.hasOwnProperty("widgets")&&u.parse(b.widgets);var c=new A;b.hasOwnProperty("created")&&(c.$element=a.findElementById(b.created)),g["default"].trigger("base_manager.response_parsed",c)},a.appendHelpers=function(a){void 0!=a&&0!=a.length||null==this.$contentDocument||(a=this.$contentDocument.find(".cms-block, .cms-row, .cms-container")),void 0!=a&&0!=a.length&&a.filter(".cms-block, .cms-row, .cms-container").each(function(a,b){var d=c(b);0==d.find("i.cms-helper").length&&d.prepend('<i class="cms-helper"></i>')})},a}();b.BaseManager=p;var q=function(){function a(){}return a.parse=function(a){if(!a.hasOwnProperty("attributes"))throw"Unexpected content data";var b=p.findElementById(a.attributes.id);if(0==b.length)throw"Content not found.";p.setElementAttributes(b,a.attributes),a.hasOwnProperty("containers")&&r.parse(a.containers,b),p.sortChildren(b,"> .cms-container")},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return p.generateUrl(b,d.extend({},{contentId:e},c||{}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),p.request(d)},a}();b.ContentManager=q;var r=function(){function a(){}return a.parse=function(a,b){a.forEach(function(d,e){if(!d.hasOwnProperty("attributes"))throw"Unexpected container data";var f=p.findOrCreateElement(d.attributes.id,b);p.setElementAttributes(f,d.attributes);var g=f.find("> .cms-inner-container"),h=d.hasOwnProperty("content")?d.content:null;h&&0<h.length&&(g.detach(),f.html(h).append(g)),d.hasOwnProperty("innerAttributes")&&(g.each(function(a,b){c(b).attr("id")!==d.innerAttributes.id&&c(b).remove()}),g=p.findOrCreateElement(d.innerAttributes.id,f),p.setElementAttributes(g,d.innerAttributes));var i=d.hasOwnProperty("innerContent")?d.innerContent:null;i&&0<i.length?g.html(i):(g.children().not(".cms-row").remove(),d.hasOwnProperty("rows")&&s.parse(d.rows,g),p.sortChildren(g,"> .cms-row"),b||e!=a.length-1||p.sortChildren(f.closest(".cms-content"),"> .cms-container"))})},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return p.generateUrl(b,d.extend({},{containerId:e},c||{}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),p.request(d)},a.edit=function(a){w.createContainerPlugin(a.data("cms").type,a)},a.changeType=function(b,c){a.request(b,"admin_ekyna_cms_editor_container_change_type",null,{data:{type:c}})},a.remove=function(b){a.request(b,"admin_ekyna_cms_editor_container_remove")},a.add=function(a,b){var c=a.closest(".cms-content");if(1!=c.length)throw"Container content not found.";q.request(c,"admin_ekyna_cms_editor_content_create_container",null,{data:{type:b}})},a.moveUp=function(b){a.request(b,"admin_ekyna_cms_editor_container_move_up")},a.moveDown=function(b){a.request(b,"admin_ekyna_cms_editor_container_move_down")},a}();b.ContainerManager=r;var s=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){if(!c.hasOwnProperty("attributes"))throw"Unexpected row data";var e=p.findOrCreateElement(c.attributes.id,b);p.setElementAttributes(e,c.attributes),c.hasOwnProperty("blocks")&&t.parse(c.blocks,e),p.sortChildren(e,"> .cms-block"),b||d!=a.length-1||p.sortChildren(e.closest(".cms-inner-container"),"> .cms-row")})},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return p.generateUrl(b,d.extend({},{rowId:e},c||{}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),p.request(d)},a.remove=function(b){a.request(b,"admin_ekyna_cms_editor_row_remove")},a.add=function(a){var b=a.closest(".cms-container");if(1!=b.length)throw"Row container not found.";r.request(b,"admin_ekyna_cms_editor_container_create_row")},a.moveUp=function(b){a.request(b,"admin_ekyna_cms_editor_row_move_up")},a.moveDown=function(b){a.request(b,"admin_ekyna_cms_editor_row_move_down")},a}();b.RowManager=s;var t=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){if(!c.hasOwnProperty("attributes"))throw"Unexpected block data";var e=p.findOrCreateElement(c.attributes.id,b);p.setElementAttributes(e,c.attributes),c.hasOwnProperty("widgets")&&u.parse(c.widgets,e),p.sortChildren(e,"> .cms-widget"),b||d!=a.length-1||p.sortChildren(e.closest(".cms-row"),"> .cms-block")})},a.generateUrl=function(a,b,c){var e=a.data("cms");if(!e.id)throw"Invalid id";return p.generateUrl(b,d.extend({},{blockId:e.id,widgetType:e.type},c||{}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),p.request(d)},a.edit=function(a){w.createBlockPlugin(a.data("cms").type,a)},a.changeType=function(b,c){a.request(b,"admin_ekyna_cms_editor_block_change_type",null,{data:{type:c}})},a.remove=function(b){a.request(b,"admin_ekyna_cms_editor_block_remove")},a.add=function(a,b){var c=a.closest(".cms-row");if(1!=c.length)throw"Block row not found.";s.request(c,"admin_ekyna_cms_editor_row_create_block",null,{data:{type:b}})},a.moveUp=function(b){a.request(b,"admin_ekyna_cms_editor_block_move_up")},a.moveDown=function(b){a.request(b,"admin_ekyna_cms_editor_block_move_down")},a.moveLeft=function(b){a.request(b,"admin_ekyna_cms_editor_block_move_left")},a.moveRight=function(b){a.request(b,"admin_ekyna_cms_editor_block_move_right")},a}();b.BlockManager=t;var u=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){if(!c.hasOwnProperty("attributes"))throw"Unexpected block data";var e=p.findOrCreateElement(c.attributes.id,b);p.setElementAttributes(e,c.attributes),c.hasOwnProperty("content")&&e.html(c.content),b||d!=a.length-1||p.sortChildren(e.closest(".cms-block"),"> .cms-widget")})},a}();b.WidgetManager=u;var v=function(){function a(){}return a.setUp=function(b,c){this.$element=b,this.data={};var d=new A;d.$element=b,d.origin={top:c.clientY,left:c.clientX},z.createLayoutToolbar(d);var e=z.getToolbar(),f=p.getContentWindow().innerWidth;this.adapters=[new j.CommonAdapter(this.data,this.$element),new i.Bootstrap3Adapter(this.data,this.$element)],this.adapters.forEach(function(a){a.initialize(),a.onResize(f,e)}),this.backup=JSON.parse(JSON.stringify(this.data)),g["default"].on("viewport.resize",a.onResizeHandler)},a.tearDown=function(){g["default"].off("viewport.resize",a.onResizeHandler),this.data=null,this.backup=null,this.$element=null,this.adapters=[]},a.hasChanges=function(){return this.data!=this.backup},a.setData=function(a,b){this.adapters.forEach(function(c){c.setData(a,b)}),this.apply()},a.apply=function(){var a=this;this.adapters.forEach(function(b){b.apply(a.data)})},a.submit=function(){var a=this;if(!this.hasChanges())return void this.cancel();g["default"].trigger("editor.set_busy");var b=null,c={},d={"cms-container":"container","cms-row":"row","cms-block":"block"};for(var e in d)if(this.$element.hasClass(e)){b="admin_ekyna_cms_editor_"+d[e]+"_layout",c[d[e]+"Id"]=this.$element.data("cms").id;break}if(null===b)throw"Unexpected element type.";var h=p.request({url:f.generate(b,c),data:{data:this.data}});h.done(function(){a.tearDown(),z.clearToolbar()}),h.fail(function(){a.cancel()}),h.always(function(){g["default"].trigger("editor.unset_busy")})},a.cancel=function(){var b=this;this.adapters.forEach(function(a){a.apply(b.backup)}),a.tearDown(),z.clearToolbar()},a}();v.onResizeHandler=function(a){var b=z.getToolbar();if("layout"!=b.model.getName())throw"Unexpected toolbar";v.adapters.forEach(function(c){c.onResize(a.size.width,b)}),b.render()},g["default"].on("toolbar.remove",function(a){"layout"==a.toolbar.getName()&&v.hasChanges()&&(confirm("Souhaitez-vous appliquer les changements ?")?v.submit():v.cancel())}),g["default"].on("layout.change",function(a){v.setData(a.getName(),a.getValue())}),g["default"].on("layout.submit",function(){return v.submit()}),g["default"].on("layout.cancel",function(){return v.cancel()}),g["default"].on("block.edit",function(a){return t.edit(a.get("data").$block)}),g["default"].on("block.layout",function(a,b){return v.setUp(a.get("data").$block,b)}),g["default"].on("block.change-type",function(a,b){return t.changeType(a.get("data").$block,b.data.type)}),g["default"].on("block.move-up",function(a){return t.moveUp(a.get("data").$block)}),g["default"].on("block.move-down",function(a){return t.moveDown(a.get("data").$block)}),g["default"].on("block.move-left",function(a){return t.moveLeft(a.get("data").$block)}),g["default"].on("block.move-right",function(a){return t.moveRight(a.get("data").$block)}),g["default"].on("block.remove",function(a){return t.remove(a.get("data").$block)}),g["default"].on("block.add",function(a,b){return t.add(a.get("data").$block,b.data.type)}),g["default"].on("row.layout",function(a,b){return v.setUp(a.get("data").$row,b)}),g["default"].on("row.move-up",function(a){return s.moveUp(a.get("data").$row)}),g["default"].on("row.move-down",function(a){return s.moveDown(a.get("data").$row)}),g["default"].on("row.remove",function(a){return s.remove(a.get("data").$row)}),g["default"].on("row.add",function(a){return s.add(a.get("data").$row)}),g["default"].on("container.edit",function(a){return r.edit(a.get("data").$container)}),g["default"].on("container.layout",function(a,b){return v.setUp(a.get("data").$container,b)}),g["default"].on("container.change-type",function(a,b){return r.changeType(a.get("data").$container,b.data.type)}),g["default"].on("container.move-up",function(a){return r.moveUp(a.get("data").$container)}),g["default"].on("container.move-down",function(a){return r.moveDown(a.get("data").$container)}),g["default"].on("container.remove",function(a){return r.remove(a.get("data").$container)}),g["default"].on("container.add",function(a,b){return r.add(a.get("data").$container,b.data.type)});var w=function(){function b(){}return b.load=function(a){this.registry=a},b.getActivePlugin=function(){if(!this.hasActivePlugin())throw"Active plugin is not set";return this.activePlugin},b.hasActivePlugin=function(){return!!this.activePlugin},b.clearActivePlugin=function(){var a=this;return this.hasActivePlugin()?this.activePlugin.destroy().then(function(){a.activePlugin=null}):k.resolve()},b.createPlugin=function(b,c,d){var e=this;this.clearActivePlugin().then(function(){throw b.forEach(function(b){if(b.name===c)return void a([b.path],function(a){e.activePlugin=new a(d,p.getContentWindow()),e.activePlugin.edit()})}),'Plugin "'+c+'" not found.'})},b.createBlockPlugin=function(a,b){this.createPlugin(this.getBlockPluginsConfig(),a,b)},b.createContainerPlugin=function(a,b){this.createPlugin(this.getContainerPluginsConfig(),a,b)},b.getBlockPluginsConfig=function(){if(!this.registry)throw"Plugins registry is not configured";return this.registry.block},b.getContainerPluginsConfig=function(){if(!this.registry)throw"Plugins registry is not configured";return this.registry.container},b}();b.PluginManager=w;var x=function(){function a(){this.defaultPrevented=!1}return a.prototype.preventDefault=function(){this.defaultPrevented=!0},a.prototype.isDefaultPrevented=function(){return this.defaultPrevented},a}(),y=function(a){function b(){return null!==a&&a.apply(this,arguments)||this}return __extends(b,a),b}(x),z=function(){function a(){}return a.getToolbar=function(){if(!a.hasToolbar())throw"Toolbar is not set";return a.toolbar},a.hasToolbar=function(){return null!=a.toolbar},a.clearToolbar=function(){if(a.hasToolbar()){var b=new y;if(b.toolbar=a.toolbar.model,g["default"].trigger("toolbar.remove",b),b.isDefaultPrevented())return!1;a.toolbar.remove(),a.toolbar=null}return!0},a.createToolbar=function(b){a.clearToolbar()&&(a.toolbar=new h.ToolbarView({model:b}),c(document).find("body").append(this.toolbar.$el),a.toolbar.render())},a.createWidgetToolbar=function(b){var c=b.$element,e=new h.Toolbar({name:"widget",classes:["vertical","widget-toolbar"],origin:b.origin}),f=d.extend(l,c.data("cms").actions);e.addControl("default",new h.Button({name:"edit",title:"Edit",icon:"content",disabled:!f.edit,event:"block.edit",data:{$block:c}})),a.createToolbar(e)},a.createBlockToolbar=function(b){var c=b.$element,e=c.closest(".cms-row"),f=new h.Toolbar({name:"block",classes:["vertical","block-toolbar"],origin:b.origin}),g=d.extend(m,c.data("cms").actions);g.edit&&f.addControl("default",new h.Button({name:"edit",title:"Edit",icon:"content",event:"block.edit",data:{$block:c}})),f.addControl("default",new h.Button({name:"layout",title:"Layout",icon:"layout",disabled:!g.layout,event:"block.layout",data:{$block:c}}));var i=[];w.getBlockPluginsConfig().forEach(function(a){i.push({name:a.name,title:a.title,confirm:"Êtes-vous sûr de vouloir changer le type de ce bloc ? (Le contenu actuel sera définitivement perdu).",data:{type:a.name}})}),f.addControl("default",new h.Button({name:"change-type",title:"Change type",icon:"change-type",disabled:!g.change_type&&1<i.length,event:"block.change-type",data:{$block:c},choices:i})),1==e.length&&(f.addControl("vertical",new h.Button({name:"move-up",title:"Move up",icon:"move-up",disabled:!g.move_up,event:"block.move-up",data:{$block:c}})),f.addControl("vertical",new h.Button({name:"move-down",title:"Move down",icon:"move-down",disabled:!g.move_down,event:"block.move-down",data:{$block:c}})),f.addControl("horizontal",new h.Button({name:"move-left",title:"Move left",icon:"move-left",disabled:!g.move_left,event:"block.move-left",data:{$block:c}})),f.addControl("horizontal",new h.Button({name:"move-right",title:"Move right",icon:"move-right",disabled:!g.move_right,event:"block.move-right",data:{$block:c}})),f.addControl("add",new h.Button({name:"remove",title:"Remove",icon:"remove-block",disabled:!g.remove,confirm:"Êtes-vous sûr de vouloir supprimer ce bloc ?",event:"block.remove",data:{$block:c}})),i=[],w.getBlockPluginsConfig().forEach(function(a){i.push({name:a.name,title:a.title,data:{type:a.name}})}),f.addControl("add",new h.Button({name:"add",title:"Create a new block after this one",icon:"add-block",disabled:!g.add,event:"block.add",data:{$block:c},choices:i}))),a.createToolbar(f)},a.createRowToolbar=function(b){var c=b.$element,e=c.closest(".cms-inner-container"),f=new h.Toolbar({name:"row",classes:["vertical","row-toolbar"],origin:b.origin}),g=d.extend(n,c.data("cms").actions);f.addControl("default",new h.Button({name:"layout",title:"Layout",icon:"layout",disabled:!g.layout,event:"row.layout",data:{$row:c}})),0<e.length&&(f.addControl("move",new h.Button({name:"move-up",title:"Move up",icon:"move-up",disabled:!g.move_up,event:"row.move-up",data:{$row:c}})),f.addControl("move",new h.Button({name:"move-down",title:"Move down",icon:"move-down",disabled:!g.move_down,event:"row.move-down",data:{$row:c}})),f.addControl("default",new h.Button({name:"remove",title:"Remove",icon:"remove-row",disabled:!g.remove,confirm:"Êtes-vous sûr de vouloir supprimer cette ligne ?",event:"row.remove",data:{$row:c}})),f.addControl("default",new h.Button({name:"add",title:"Create a new row",icon:"add-row",disabled:!g.add,event:"row.add",data:{$row:c}}))),a.createToolbar(f)},a.createContainerToolbar=function(b){var c=b.$element,e=c.closest(".cms-content"),f=new h.Toolbar({name:"container",classes:["vertical","container-toolbar"],origin:b.origin}),g=d.extend(o,c.data("cms").actions);f.addControl("default",new h.Button({name:"edit",title:"Edit",icon:"content",disabled:!g.edit,event:"container.edit",data:{$container:c}})),f.addControl("default",new h.Button({name:"layout",title:"Layout",icon:"layout",disabled:!g.layout,event:"container.layout",data:{$container:c}}));var i=[];w.getContainerPluginsConfig().forEach(function(a){i.push({name:a.name,title:a.title,confirm:"Êtes-vous sûr de vouloir changer le type de ce contener ? (Le contenu actuel sera définitivement perdu).",data:{type:a.name}})}),f.addControl("default",new h.Button({name:"change-type",title:"Change type",icon:"change-type",disabled:!g.change_type&&1<i.length,event:"container.change-type",data:{$container:c},choices:i})),1==e.length&&(f.addControl("move",new h.Button({name:"move-up",title:"Move up",icon:"move-up",disabled:!g.move_up,event:"container.move-up",data:{$container:c}})),f.addControl("move",new h.Button({name:"move-down",title:"Move down",icon:"move-down",disabled:!g.move_down,event:"container.move-down",data:{$container:c}})),f.addControl("add",new h.Button({name:"remove",title:"Remove",icon:"remove-container",disabled:!g.remove,confirm:"Êtes-vous sûr de vouloir supprimer ce conteneur ?",event:"container.remove",data:{$container:c}})),i=[],w.getContainerPluginsConfig().forEach(function(a){i.push({name:a.name,title:a.title,data:{type:a.name}})}),f.addControl("add",new h.Button({name:"add",title:"Create a new container after this one",icon:"add-container",disabled:!g.add,event:"container.add",data:{$container:c},choices:i}))),a.createToolbar(f)},a.createLayoutToolbar=function(b){var c=b.$element,d=new h.Toolbar({name:"layout",classes:["vertical","layout-toolbar"],origin:b.origin});c.hasClass("cms-block")&&(d.addControl("default",new h.Slider({name:"size",title:"Size",event:"layout.change",min:1,max:12})),d.addControl("default",new h.Slider({name:"offset",title:"Offset",event:"layout.change",min:0,max:11}))),d.addControl("default",new h.Slider({name:"padding_top",title:"Padding top",event:"layout.change",min:0,max:300})),d.addControl("default",new h.Slider({name:"padding_bottom",title:"Padding bottom",event:"layout.change",min:0,max:300})),d.addControl("footer",new h.Button({name:"submit",title:"Ok",theme:"primary",icon:"ok",event:"layout.submit"})),d.addControl("footer",new h.Button({name:"cancel",title:"Cancel",icon:"cancel",event:"layout.cancel"})),a.createToolbar(d)},a}();z.toolbar=null;var A=function(a){function b(){return null!==a&&a.apply(this,arguments)||this}return __extends(b,a),b}(x);b.SelectionEvent=A;var B=function(){function a(a){var b=this;this.enabled=!1,this.clickEvent=null,this.config=a,this.viewportOrigin={top:50,left:0},this.viewportSize={width:0,height:0},this.selectionOffset={top:0,left:0},this.selectionId=null,this.documentMouseDownHandler=function(a){return b.onDocumentMouseDown(a)},this.documentMouseUpHandler=function(){return b.onDocumentMouseUp()},this.documentSelectHandler=function(a){return b.select(a)},this.powerClickHandler=function(a){return b.onPowerClick(a)},this.viewportLoadHandler=function(a,c){return b.onViewportLoad(a,c)},this.viewportUnloadHandler=function(a){return b.onViewportUnload(a)},this.viewportResizeHandler=function(a){return b.onViewportResize(a)}}return a.prototype.initialize=function(){var a=this;g["default"].on("viewport.resize",this.viewportResizeHandler),g["default"].on("document_manager.select",this.documentSelectHandler),g["default"].on("base_manager.response_parsed",function(b){!b.$element&&a.selectionId&&(b.$element=p.findElementById(a.selectionId)),a.deselect().then(function(){a.select(b)}),a.tweakAnchorsAndForms()}),g["default"].on("block.edit",function(){return z.clearToolbar()})},a.prototype.onPowerClick=function(a){var b=a.get("active");b&&!this.enabled?(this.enabled=!0,this.enableEdition()):this.enabled&&!b?(this.enabled=!1,this.disableEdition()):this.enabled=b},a.prototype.onViewportLoad=function(a,b){var d=c(b);return p.setContentWindow(a),p.setContentDocument(d),this.tweakAnchorsAndForms(),this.enabled&&this.enableEdition(),g["default"].trigger("document_manager.document_data",p.getDocumentData()),this},a.prototype.tweakAnchorsAndForms=function(){var a=this,b=p.getContentDocument();b.off("click","a[href]").on("click","a[href]",function(b){b.preventDefault(),b.stopPropagation();var c=b.currentTarget;return c.hostname!==a.config.hostname?console.log("Attempt to navigate out of the website has been blocked."):a.enabled||g["default"].trigger("document_manager.navigate",c.href),!1}),b.find("form").each(function(b,d){var e=c(d),f=e.attr("action"),g=document.createElement("a");g.href=f,g.hostname!==a.config.hostname?e.off("submit").on("submit",function(a){return console.log("Attempt to navigate out of the website has been blocked."),a.preventDefault(),!1}):e.attr("action",h.Util.addEditorParameterToUrl(f))})},a.prototype.onViewportUnload=function(a){if(!a.defaultPrevented)return w.hasActivePlugin()?(a.preventDefault(),w.getActivePlugin().isUpdated()?(a.returnValue="Vos changements n'ont pas été sauvegardés !",this):(this.deselect().then(function(){g["default"].trigger("document_manager.reload")}),this)):(p.clear(),z.clearToolbar()||a.preventDefault(),this)},a.prototype.onViewportResize=function(a){return this.viewportOrigin=a.origin,this.viewportSize=a.size,z.hasToolbar()&&z.getToolbar().applyOriginOffset(a.origin),this},a.prototype.onDocumentMouseDown=function(a){this.clickEvent=null;var b=c(a.target);if(!(0<b.closest("#editor-document-toolbar").length||w.hasActivePlugin()&&w.getActivePlugin().preventDocumentSelection(b))){this.clickEvent=new A,this.clickEvent.origin={top:a.clientY,left:a.clientX};var d=b.closest(".cms-widget, .cms-block, .cms-row, .cms-container");1==d.length&&this.selectionId!=d.attr("id")&&(this.clickEvent.$element=d,this.clickEvent.$target=b)}},a.prototype.onDocumentMouseUp=function(){var a=this;this.clickEvent&&this.deselect().then(function(){a.clickEvent.$element?a.select(a.clickEvent):a.createToolbar(),a.clickEvent=null})},a.prototype.deselect=function(){var a=this;return w.clearActivePlugin().then(function(){return z.clearToolbar()?void(a.selectionId&&(p.findElementById(a.selectionId).removeClass("selected"),a.selectionId=null)):k.reject("Layout has changes.")})},a.prototype.select=function(a){a.$element&&1==a.$element.length&&(this.selectionId=a.$element.addClass("selected").attr("id"),this.createToolbar(a))},a.prototype.createToolbar=function(a){if(!a.$element){var b=p.findElementById(this.selectionId);if(1!=b.length)return;a.$element=b}if(a.origin?this.selectionOffset={top:a.origin.top-a.$element.offset().top,left:a.origin.left-a.$element.offset().left}:a.origin={top:a.$element.offset().top+this.selectionOffset.top,left:a.$element.offset().left+this.selectionOffset.left},a.$element.hasClass("cms-widget"))z.createWidgetToolbar(a);else if(a.$element.hasClass("cms-block"))z.createBlockToolbar(a);else if(a.$element.hasClass("cms-row"))z.createRowToolbar(a);else{if(!a.$element.hasClass("cms-container"))throw"Unexpected element";z.createContainerToolbar(a)}z.getToolbar().applyOriginOffset(this.viewportOrigin)},a.prototype.enableEdition=function(){var a=p.getContentDocument();if(this.enabled&&null!==a){if(0===a.find("link#cms-editor-stylesheet").length){var b=document.createElement("link");b.id="cms-editor-stylesheet",b.href=document.documentElement.getAttribute("data-asset-base-url")+this.config.css_path,b.media="screen",b.rel="stylesheet",b.type="text/css",a.find("head").append(b)}return p.appendHelpers(),a.on("mousedown",this.documentMouseDownHandler),a.on("mouseup",this.documentMouseUpHandler),this}},a.prototype.disableEdition=function(){var a=p.getContentDocument();if(!this.enabled&&null!==a){this.deselect(),a.off("mousedown",this.documentMouseDownHandler),a.off("mouseup",this.documentMouseUpHandler);var b=a.find("link#cms-editor-stylesheet");return b.length&&b.remove(),a.find("i.cms-helper").remove(),this}},a}();b.DocumentManager=B});