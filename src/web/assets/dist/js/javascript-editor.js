/*!
 * @project        twigfield
 * @name           javascript-editor.js
 * @author         Andrew Welch
 * @build          Tue Jun 21 2022 02:39:38 GMT+0000 (Coordinated Universal Time)
 * @copyright      Copyright (c) 2022 ©2022 nystudio107.com
 *
 */
"use strict";(self.webpackChunkBuildchain=self.webpackChunkBuildchain||[]).push([[339],{5092:function(e,t,n){n(7941),n(2526),n(7327),n(1539),n(5003),n(4747),n(9337);var o=n(4942),i=(n(4916),n(5306),n(713)),r=n(8152),a=n(1002);n(3123),n(6699),n(2023),n(6755),n(9720),n(8309);function l(e){return e[e.length-1]}function s(e,t,n){monaco.languages.registerCompletionItemProvider("twig",{triggerCharacters:[".","("],provideCompletionItems:function(o,i,r){var s=[],u=e,c=o.getValueInRange({startLineNumber:i.lineNumber,startColumn:0,endLineNumber:i.lineNumber,endColumn:i.column}),d=!0;if(-1===c.lastIndexOf("{")&&(d=!1),-1!==c.substring(c.lastIndexOf("{")).indexOf("}")&&(d=!1),!d&&"TwigExpressionAutocomplete"===t)return null;var m=c.replace("\t","").split(" "),g=m[m.length-1];g.includes("{")&&(g=l(g.split("{"))),g.includes("(")&&(g=l(g.split("("))),g.includes(">")&&(g=l(g.split(">")));var p="."===g.charAt(g.length-1);if(p&&"TwigExpressionAutocomplete"!==t)return null;if(d&&"TwigExpressionAutocomplete"===t&&p){if(!n)return null;var f=g.substring(0,g.length-1).split(".");if(void 0!==e[f[0]]){u=e[f[0]];for(var v=1;v<f.length;v++){if(!u.hasOwnProperty(f[v]))return s;u=u[f[v]]}}}if(void 0!==u)for(var h in u)if(u.hasOwnProperty(h)&&!h.startsWith("__")){var b=u[h].__completions;if(void 0!==b){if(delete b.range,"documentation"in b&&"object"!==(0,a.Z)(b.documentation)){var w=b.documentation;b.documentation={value:w,isTrusted:!0,supportsHtml:!0}}s.push(b)}}return{suggestions:s}}})}function u(e,t,n){monaco.languages.registerHoverProvider("twig",{provideHover:function(t,n){var o={},i=t.getValueInRange({startLineNumber:n.lineNumber,startColumn:0,endLineNumber:n.lineNumber,endColumn:t.getLineMaxColumn(n.lineNumber)}),r=t.getWordAtPosition(n);if(null!==r){for(var l=i.substring(0,r.endColumn-1),s=!1,u=e,c=l.length;c>=0;c--)if(" "===l[c]){l=i.substring(c+1,l.length);break}if(l.includes(".")&&(s=!0),s)for(var d=l.substring(0,l.length).split("."),m=0;m<d.length-1;m++){var g=d[m].replace(/[{(<]/,"");if(!u.hasOwnProperty(g))return o;u=u[g]}if(void 0!==u&&void 0!==u[r.word]){var p=u[r.word].__completions;if(void 0!==p){var f=p.documentation;return"object"===(0,a.Z)(p.documentation)&&(f=p.documentation.value),{range:new monaco.Range(n.lineNumber,r.startColumn,n.lineNumber,r.endColum),contents:[{value:"**"+p.detail+"**"},{value:f}]}}}return o}}})}function c(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,o)}return n}function d(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?c(Object(n),!0).forEach((function(t){(0,o.Z)(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):c(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}""===n.p&&(n.p=window.twigfieldBaseAssetsUrl);var m={language:"twig",theme:"vs",automaticLayout:!0,lineNumbers:"off",glyphMargin:!1,folding:!1,lineDecorationsWidth:0,lineNumbersMinChars:0,renderLineHighlight:!1,wordWrap:!0,scrollBeyondLastLine:!1,scrollbar:{vertical:"hidden",horizontal:"auto"},fontSize:14,fontFamily:'SFMono-Regular, Consolas, "Liberation Mono", Menlo, Courier, monospace',minimap:{enabled:!1}};function g(e,t,n,o,a,l){var c=document.getElementById(e),g=document.createElement("div"),p=JSON.parse(a);g.id=e+"-monaco-editor",g.classList.add("p-2","box-content","monaco-editor-twigfield-icon","w-full","h-full"),""!==n&&g.classList.add(n),g.tabIndex=0,c.parentNode.insertBefore(g,c),c.style.display="none";var f=d(d(d({},m),JSON.parse(o)),{value:c.value}),v=i.editor.create(g,f);if(v.onDidChangeModelContent((function(e){c.value=v.getValue()})),"singleLineEditor"in p&&p.singleLineEditor){var h=v.getModel(),b=h.getValue();h.setValue(b.replace(/\s\s+/g," ")),v.addCommand(i.KeyMod.CtrlCmd|i.KeyCode.KeyF,(function(){})),v.addCommand(i.KeyCode.Enter,(function(){}),"!suggestWidgetVisible"),v.onDidPaste((function(e){for(var t="",n=h.getLineCount(),o=0;o<n;o+=1)t+=h.getLineContent(o+1);t=t.replace(/\s\s+/g," "),h.setValue(t),v.setPosition({column:t.length+1,lineNumber:1})}))}!function(e,t){var n="";null!=e&&(n="?fieldType="+e);var o=new XMLHttpRequest;o.open("GET",t+n,!0),o.onload=function(){if(o.status>=200&&o.status<400){var e=JSON.parse(o.responseText);void 0===window.monacoAutocompleteItems&&(window.monacoAutocompleteItems={});for(var t=0,n=Object.entries(e);t<n.length;t++){var i=(0,r.Z)(n[t],2),a=(i[0],i[1]);a.name in window.monacoAutocompleteItems||(window.monacoAutocompleteItems[a.name]=a.name,s(a.__completions,a.type,a.hasSubProperties),u(a.__completions,a.type))}}else console.log("Autocomplete endpoint failed with status "+o.status)},o.send()}(t,l);var w=function(){var e=v.getLayoutInfo().width,t=Math.min(1e3,v.getContentHeight());g.style.height="".concat(t,"px");try{!0,v.layout({width:e,height:t})}finally{!1}};v.onDidContentSizeChange(w),w()}window.makeMonacoEditor=g},9086:function(){}},function(e){var t=function(t){return e(e.s=t)};e.O(0,[216,532],(function(){return t(5092),t(9086),t(1828)}));e.O()}]);
//# sourceMappingURL=javascript-editor.js.map