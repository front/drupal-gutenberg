!function(e){function t(t){for(var a,s,i=t[0],d=t[1],l=t[2],u=0,c=[];u<i.length;u++)s=i[u],r[s]&&c.push(r[s][0]),r[s]=0;for(a in d)Object.prototype.hasOwnProperty.call(d,a)&&(e[a]=d[a]);for(p&&p(t);c.length;)c.shift()();return n.push.apply(n,l||[]),o()}function o(){for(var e,t=0;t<n.length;t++){for(var o=n[t],a=!0,i=1;i<o.length;i++){var d=o[i];0!==r[d]&&(a=!1)}a&&(n.splice(t--,1),e=s(s.s=o[0]))}return e}var a={},r={1:0},n=[];function s(t){if(a[t])return a[t].exports;var o=a[t]={i:t,l:!1,exports:{}};return e[t].call(o.exports,o,o.exports,s),o.l=!0,o.exports}s.m=e,s.c=a,s.d=function(e,t,o){s.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,t){if(1&t&&(e=s(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(s.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)s.d(o,a,function(t){return e[t]}.bind(null,a));return o},s.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return s.d(t,"a",t),t},s.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},s.p="";var i=window.webpackJsonp=window.webpackJsonp||[],d=i.push.bind(i);i.push=t,i=i.slice();for(var l=0;l<i.length;l++)t(i[l]);var p=d;n.push([1,0]),o()}({1:function(e,t,o){"use strict";o.r(t);const a={page:{labels:{Document:Drupal.t("Node"),document:Drupal.t("Node"),posts:Drupal.t("Nodes"),extras:Drupal.t("Fields")},name:"Page",rest_base:"pages",slug:"page",supports:{author:!1,comments:!1,"custom-fields":!0,document:!0,editor:!0,"media-library":!1,"page-attributes":!1,posts:!1,revisions:!1,"template-settings":!1,thumbnail:!1,title:!1,extras:!0},viewable:!1,saveable:!1,publishable:!1,autosaveable:!1}},r={"save-post":{method:"PUT",regex:/\/wp\/v2\/(\w*)\/(\d*)/g,process:(e,t)=>new Promise(o=>{o({pathType:"save-post",id:e[2],type:e[1],title:{raw:document.title},content:{raw:t}})})},"load-node":{method:"GET",regex:/\/wp\/v2\/pages\/(\d*)/g,process:()=>new Promise(e=>{e(wp.node)})},"load-media":{method:"GET",regex:/\/wp\/v2\/media\/(\d*)/g,process:e=>new Promise((t,o)=>{jQuery.ajax({method:"GET",url:drupalSettings.path.baseUrl+"editor/image/load/"+e[1],accepts:{json:"application/json, text/javascript, */*; q=0.01"}}).done(e=>{t(e)}).fail(()=>{o({message:"Error"})})})},"save-media":{method:"POST",regex:/\/wp\/v2\/media/g,process:(e,t)=>(console.log("save media",drupalSettings),new Promise((e,o)=>{let a;for(let e of t.entries())console.log(e),"file"===e[0]&&(a=e[1]);const r=new FormData;r.append("files[fid]",a),r.append("fid[fids]",""),r.append("attributes[alt]","Test"),r.append("_drupal_ajax","1"),r.append("form_id",jQuery('[name="form_id"]').val()),r.append("form_build_id",jQuery('[name="form_build_id"]').val()),r.append("form_token",jQuery('[name="form_token"]').val()),jQuery.ajax({method:"POST",url:drupalSettings.path.baseUrl+"editor/image/upload/gutenberg",data:r,processData:!1,contentType:!1,accepts:{json:"application/json, text/javascript, */*; q=0.01"}}).done(t=>{e(t)}).fail(()=>{o("Error")})}))},categories:{method:"GET",regex:/\/wp\/v2\/categories\?(.*)/g,process:()=>new Promise(e=>{e("ok")})},users:{method:"GET",regex:/\/wp\/v2\/users\/\?(.*)/g,process:()=>new Promise(e=>{e("ok")})},taxonomies:{method:"GET",regex:/\/wp\/v2\/taxonomies\?(.*)/g,process:()=>new Promise(e=>{e("ok")})},embed:{method:"GET",regex:/\/oembed\/1\.0\/proxy\?(.*)/g,process:e=>new Promise((t,o)=>{jQuery.ajax({method:"GET",url:`http://open.iframe.ly/api/oembed?${e[1]}&origin=drupal`,processData:!1,contentType:!1,accepts:{json:"application/json, text/javascript, */*; q=0.01"}}).done(e=>{t(e)}).fail(()=>{o("Error")})})},root:{method:"GET",regex:/^\/$/g,process:()=>new Promise(e=>e({theme_supports:{formats:["standard","aside","image","video","quote","link","gallery","audio"],"post-thumbnails":!0}}))},"load-type-page":{method:"GET",regex:/\/wp\/v2\/types\/page/g,process:()=>new Promise(e=>e(a.page))},"load-types":{method:"GET",regex:/\/wp\/v2\/types/g,process:()=>new Promise(e=>e(a))}};window._wpDateSettings={l10n:{locale:"pt_PT"}},window.wp={apiRequest:function(e){return console.log(e),function(e){for(const t in r)if(r.hasOwnProperty(t)){const o=r[t];o.regex.lastIndex=0;let a=o.regex.exec(e.path+"");if(a&&a.length>0&&(e.method||"GET"===o.method))return o.process(a,e.data)}return new Promise((t,o)=>o({code:"api_handler_not_found",message:"API handler not found.",data:{path:e.path,status:404}}))}(e)},url:{addQueryArgs:function(e,t){return console.log("addQueryArgs",e,t),""}}};var n=o(0);const{registerBlockType:s}=n.blocks;console.log("register block"),s("drupal/block",{title:"Drupal Block",icon:"welcome-widgets-menus",category:"common",edit:({className:e})=>Object(n.createElement)("p",{className:e,value:"Drupal Block - from editor."}),save:({className:e})=>Object(n.createElement)("p",{className:e,value:"Drupal Block - from frontend."})});o(6);let i,d,l;((e,t,o)=>{t.editors.gutenberg={attach(a){function r(){return i(l,{name:"additional-fields",title:"Additional fields",icon:"forms",isPinnable:!0},i(d,{},""))}(function(a){const r=e(a),s="editor-"+r.data("drupal-selector");e('<div id="'+s+'" class="gutenberg__editor"></div>').insertAfter(r),r.hide(),o.node={content:{raw:e(a).val()},templates:"",title:{raw:document.title},type:"page",status:"auto-draft",id:12345};const i={alignWide:!0,availableTemplates:[],allowedBlockTypes:!0,disableCustomColors:!1,disablePostFormats:!1,titlePlaceholder:t.t("Add title"),bodyPlaceholder:t.t("Add content"),isRTL:!1,autosaveInterval:100,canAutosave:!1,canPublish:!1,canSave:!1};return window.customGutenberg={events:{OPEN_GENERAL_SIDEBAR:t=>{console.log("OPEN_GENERAL_SIDEBAR",t);let o=t.name.replace(/edit-post\//g,"");o=o.replace(/drupal\//g,"");let a=e(".edit-post-sidebar .components-panel .tab");e(".gutenberg-sidebar").append(a),setTimeout(()=>{let t=e(".gutenberg-sidebar .tab."+o);e(".edit-post-sidebar .components-panel").append(t)},0),e(document.body).addClass("gutenberg-sidedar-open")},CLOSE_GENERAL_SIDEBAR:()=>{e(document.body).removeClass("gutenberg-sidedar-open"),e(".gutenberg-sidebar").append(e(".edit-post-sidebar .components-panel .tab"))},REMOVE_BLOCKS:(e,t)=>{console.log("REMOVE_BLOCKS",e,t)}}},new Promise(e=>{setTimeout(()=>{n.editPost.initializeEditor(s,"page",12345,i,{}),e()},0)})})(a).then(()=>{setTimeout(()=>{e(".region-highlighted").hide()},5e3),n.data.dispatch("core/edit-post").openGeneralSidebar("edit-post/document"),i=o.element.createElement,d=n.components.PanelBody,l=n.editPost.PluginSidebar,n.plugins.registerPlugin("drupal",{icon:"forms",render:r}),setTimeout(()=>{e(".edit-post-header__settings").append(e(".gutenberg-header-settings"))},0),e(".gutenberg-full-editor").addClass("ready"),e("#gutenberg-loading").addClass("hide"),e(document.forms[0]).submit(t=>{const o=n.data.select("core/editor");n.data.dispatch("core/editor").savePost(),e(a).val(o.getEditedPostContent());const r=e(t.originalEvent.explicitOriginalTarget);return"edit-submit"===r.attr("id")||"edit-preview"===r.attr("id")||"edit-delete"===r.attr("id")||(t.preventDefault(),t.stopPropagation(),!1)})})},detach(t,o,a){const r=e(t),s="editor-"+r.data("drupal-selector"),i=n.data.select("core/editor").getEditedPostContent();r.val(i),"serialize"!==a&&e("#"+s).remove()},onChange(){}}})(jQuery,Drupal,wp,drupalSettings,_)},6:function(e,t){}});