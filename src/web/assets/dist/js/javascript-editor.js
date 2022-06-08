/*!
 * @project        twigfield
 * @name           javascript-editor.js
 * @author         Andrew Welch
 * @build          Wed Jun 08 2022 21:51:25 GMT+0000 (Coordinated Universal Time)
 * @copyright      Copyright (c) 2022 ©2022 nystudio107.com
 *
 */
"use strict";(self.webpackChunkBuildchain=self.webpackChunkBuildchain||[]).push([[339],{5463:function(e,n,o){var t=o(713);function i(e,n){var o=document.getElementById(e),i=document.createElement("div");i.id=e+"-monaco-editor",i.classList.add("p-2","box-content","monaco-editor-background-frame","w-full","h-full"),i.tabIndex=0,o.parentNode.insertBefore(i,o),o.style.display="none";var a=t.editor.create(i,{value:o.value,language:"twig",automaticLayout:!0,lineNumbers:"off",glyphMargin:!1,folding:!1,lineDecorationsWidth:0,lineNumbersMinChars:0,renderLineHighlight:!1,wordWrap:!0,scrollBeyondLastLine:!1,scrollbar:{vertical:"hidden",horizontal:"auto"},fontSize:14,fontFamily:'SFMono-Regular, Consolas, "Liberation Mono", Menlo, Courier, monospace',minimap:{enabled:!1}});document.querySelector("#main-form").addEventListener("submit",(function(e){o.value=a.getValue()})),Craft.cp.on("beforeSaveShortcut",(function(){o.value=a.getValue()})),void 0!==window.monacoAutocompleteItemsAdded&&(window.monacoAutocompleteItemsAdded=!0);var r=function(){var e=a.getLayoutInfo().width,n=Math.min(1e3,a.getContentHeight());i.style.height="".concat(n,"px");try{!0,a.layout({width:e,height:n})}finally{!1}};a.onDidContentSizeChange(r),r()}""===o.p&&(o.p=window.twigfieldBaseAssetsUrl),console.log(o.p),window.makeMonacoEditor=i},9086:function(){}},function(e){var n=function(n){return e(e.s=n)};e.O(0,[216,532],(function(){return n(5463),n(9086),n(1828)}));e.O()}]);
//# sourceMappingURL=javascript-editor.js.map