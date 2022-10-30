/*!
 * @project        code-editor
 * @name           code-editor.js
 * @author         Andrew Welch
 * @build          Sun Oct 30 2022 22:46:54 GMT+0000 (Coordinated Universal Time)
 * @copyright      Copyright (c) 2022 ©2022 nystudio107.com
 *
 */
"use strict";(self.webpackChunkBuildchain=self.webpackChunkBuildchain||[]).push([[85],{5919:function(){},3422:function(e,t,n){var o=n(713);function i(e){return e[e.length-1]}function l(e,t,n){o.languages.registerCompletionItemProvider("twig",{triggerCharacters:[".","("],provideCompletionItems:function(o,l,s){let r=[],a=e;const d=o.getValueInRange({startLineNumber:l.lineNumber,startColumn:0,endLineNumber:l.lineNumber,endColumn:l.column});let c=!0;-1===d.lastIndexOf("{")&&(c=!1);if(-1!==d.substring(d.lastIndexOf("{")).indexOf("}")&&(c=!1),!c&&"TwigExpressionAutocomplete"===t)return null;const u=d.replace("\t","").split(" ");let m=u[u.length-1];m.includes("{")&&(m=i(m.split("{"))),m.includes("(")&&(m=i(m.split("("))),m.includes(">")&&(m=i(m.split(">")));const g="."===m.charAt(m.length-1);if(g&&"TwigExpressionAutocomplete"!==t)return null;if(c&&"TwigExpressionAutocomplete"===t&&g){if(!n)return null;const t=m.substring(0,m.length-1).split(".");if(void 0!==e[t[0]]){a=e[t[0]];for(let e=1;e<t.length;e++){if(!a.hasOwnProperty(t[e])){return{suggestions:r}}a=a[t[e]]}}}if(void 0!==a)for(let e in a)if(a.hasOwnProperty(e)&&!e.startsWith("__")){const t=a[e].__completions;if(void 0!==t){if(delete t.range,"documentation"in t&&"object"!=typeof t.documentation){let e=t.documentation;t.documentation={value:e,isTrusted:!0,supportsHtml:!0}}r.push(t)}}return{suggestions:r}}})}function s(e,t){o.languages.registerHoverProvider("twig",{provideHover:function(t,n){const i=t.getValueInRange({startLineNumber:n.lineNumber,startColumn:0,endLineNumber:n.lineNumber,endColumn:t.getLineMaxColumn(n.lineNumber)}),l=t.getWordAtPosition(n);if(null===l)return;let s=i.substring(0,l.endColumn-1),r=!1,a=e;for(let e=s.length;e>=0;e--)if(" "===s[e]){s=i.substring(e+1,s.length);break}if(s.includes(".")&&(r=!0),r){const e=s.substring(0,s.length).split(".");for(let t=0;t<e.length-1;t++){const n=e[t].replace(/[{(<]/,"");if(!a.hasOwnProperty(n))return;a=a[n]}}if(void 0!==a&&void 0!==a[l.word]){const e=a[l.word].__completions;if(void 0!==e){let t=e.documentation;"object"==typeof e.documentation&&(t=e.documentation.value);return{range:new o.Range(n.lineNumber,l.startColumn,n.lineNumber,l.endColumn),contents:[{value:"**"+e.detail+"**"},{value:t}]}}}}})}const r={twig:'<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 320 320" style="enable-background:new 0 0 320 320;" xml:space="preserve">\n<style type="text/css">.st0{fill:currentcolor;}</style>\n<g>\n\t<path class="st0" d="M128,35.6c-17.7,0-32,15.9-32,35.6v35.6c0,29.5-21.5,53.3-48,53.3c26.5,0,48,23.9,48,53.3v35.6\n\t\tc0,19.6,14.3,35.6,32,35.6V320H96c-35.3,0-64-31.9-64-71.1v-35.6c0-19.6-14.3-35.6-32-35.6v-35.6c17.7,0,32-15.9,32-35.6V71.1\n\t\tC32,31.9,60.7,0,96,0h32V35.6L128,35.6z"/>\n\t<path class="st0" d="M320,177.8c-17.7,0-32,15.9-32,35.6v35.6c0,39.2-28.7,71.1-64,71.1h-32v-35.6c17.7,0,32-15.9,32-35.6v-35.6\n\t\tc0-29.5,21.5-53.3,48-53.3c-26.5,0-48-23.9-48-53.3V71.1c0-19.6-14.3-35.6-32-35.6V0h32c35.3,0,64,31.9,64,71.1v35.6\n\t\tc0,19.6,14.3,35.6,32,35.6V177.8L320,177.8z"/>\n</g>\n</svg>',javascript:'\n <svg width="256px" height="289px" viewBox="0 0 256 289" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid">\n <style type="text/css">.st0{fill:currentcolor;}</style>\n   <g>\n        <path class="st0" d="M127.999999,288.463771 C124.024844,288.463771 120.314699,287.403728 116.869564,285.548656 L81.6231884,264.612838 C76.32298,261.697724 78.9730854,260.637682 80.5631458,260.107661 C87.7184259,257.72257 89.0434775,257.192547 96.4637688,252.952381 C97.2587979,252.422361 98.3188405,252.687372 99.1138718,253.217392 L126.144927,269.383024 C127.20497,269.913045 128.530021,269.913045 129.325053,269.383024 L235.064182,208.165634 C236.124225,207.635611 236.654245,206.575571 236.654245,205.250519 L236.654245,83.0807467 C236.654245,81.7556929 236.124225,80.6956526 235.064182,80.1656324 L129.325053,19.2132506 C128.26501,18.6832305 126.939959,18.6832305 126.144927,19.2132506 L20.4057954,80.1656324 C19.3457551,80.6956526 18.8157349,82.0207041 18.8157349,83.0807467 L18.8157349,205.250519 C18.8157349,206.31056 19.3457551,207.635611 20.4057954,208.165634 L49.2919247,224.861286 C64.9275364,232.811595 74.7329196,223.536234 74.7329196,214.260871 L74.7329196,93.681159 C74.7329196,92.0910985 76.0579711,90.5010358 77.9130428,90.5010358 L91.4285716,90.5010358 C93.0186343,90.5010358 94.6086948,91.8260873 94.6086948,93.681159 L94.6086948,214.260871 C94.6086948,235.196689 83.2132512,247.387164 63.3374737,247.387164 C57.2422362,247.387164 52.4720502,247.387164 38.9565214,240.761906 L11.1304347,224.861286 C4.24016581,220.886129 5.68434189e-14,213.46584 5.68434189e-14,205.515528 L5.68434189e-14,83.3457557 C5.68434189e-14,75.3954465 4.24016581,67.9751552 11.1304347,64.0000006 L116.869564,2.78260752 C123.494824,-0.927535841 132.505176,-0.927535841 139.130436,2.78260752 L244.869565,64.0000006 C251.759834,67.9751552 256,75.3954465 256,83.3457557 L256,205.515528 C256,213.46584 251.759834,220.886129 244.869565,224.861286 L139.130436,286.078676 C135.685299,287.668739 131.710145,288.463771 127.999999,288.463771 L127.999999,288.463771 Z M160.596274,204.455488 C114.219461,204.455488 104.679089,183.254659 104.679089,165.233955 C104.679089,163.643893 106.004141,162.053832 107.859212,162.053832 L121.639752,162.053832 C123.229813,162.053832 124.554864,163.113872 124.554864,164.703935 C126.674947,178.749484 132.770187,185.639753 160.861283,185.639753 C183.122154,185.639753 192.662526,180.604556 192.662526,168.67909 C192.662526,161.788821 190.012423,156.753624 155.296065,153.308489 C126.409938,150.393375 108.389235,144.033126 108.389235,120.977226 C108.389235,99.5113875 126.409938,86.7908901 156.621119,86.7908901 C190.542443,86.7908901 207.238095,98.4513472 209.358178,123.89234 C209.358178,124.687371 209.093167,125.482403 208.563147,126.277434 C208.033127,126.807454 207.238095,127.337474 206.443064,127.337474 L192.662526,127.337474 C191.337475,127.337474 190.012423,126.277434 189.747412,124.952382 C186.567289,110.376813 178.351966,105.606625 156.621119,105.606625 C132.240165,105.606625 129.325053,114.086957 129.325053,120.447205 C129.325053,128.132506 132.770187,130.5176 165.631471,134.757766 C198.227744,138.997931 213.598344,145.093169 213.598344,167.884058 C213.333333,191.20497 194.252589,204.455488 160.596274,204.455488 L160.596274,204.455488 Z"></path>\n    </g>\n</svg>'},a={language:"twig",theme:"vs",automaticLayout:!0,tabIndex:0,lineNumbers:"off",glyphMargin:!1,folding:!1,lineDecorationsWidth:0,lineNumbersMinChars:0,renderLineHighlight:"none",wordWrap:"on",scrollBeyondLastLine:!1,scrollbar:{vertical:"hidden",horizontal:"auto",alwaysConsumeMouseWheel:!1,handleMouseWheel:!1},fontSize:14,fontFamily:'SFMono-Regular, Consolas, "Liberation Mono", Menlo, Courier, monospace',minimap:{enabled:!1}};function d(e,t,n,i,d,c,u=""){const m=document.getElementById(e),g=document.createElement("div"),f=JSON.parse(d),p=e+"-monaco-editor-placeholder";if(null===m||null===m.parentNode)return;const C=JSON.parse(i),h={...a,...C,value:m.value};g.id=e+"-monaco-editor",g.classList.add("relative","box-content","monaco-editor-codefield","h-full");const w=void 0===h.language?null:r[h.language];if(w){const e=document.createElement("div");e.classList.add("monaco-editor-codefield--icon"),e.setAttribute("title",Craft.t("twigfield","Twig code is supported.")),e.setAttribute("aria-hidden","true"),e.innerHTML=w,g.appendChild(e)}if(""!==n){const e=g.classList,t=n.trim().split(/\s+/);e.add(...t)}if(""!==u){const t=document.createElement("div");t.id=e+"-monaco-editor-placeholder",t.innerHTML=u,t.classList.add("monaco-placeholder","p-2"),g.appendChild(t)}m.parentNode.insertBefore(g,m),m.style.display="none";const v=o.editor.create(g,h);if(v.onDidChangeModelContent((()=>{m.value=v.getValue()})),"singleLineEditor"in f&&f.singleLineEditor){const e=v.getModel();if(null!==e){const t=e.getValue();e.setValue(t.replace(/\s\s+/g," ")),v.addCommand(o.KeyMod.CtrlCmd|o.KeyCode.KeyF,(()=>{})),v.addCommand(o.KeyCode.Enter,(()=>{}),"!suggestWidgetVisible"),v.addCommand(o.KeyCode.Tab,(()=>{!function(){const e=b();if(document.activeElement instanceof HTMLFormElement){const t=e.indexOf(document.activeElement);if(t>-1){(e[t+1]||e[0]).focus()}}}()})),v.addCommand(o.KeyMod.Shift|o.KeyCode.Tab,(()=>{!function(){const e=b();if(document.activeElement instanceof HTMLFormElement){const t=e.indexOf(document.activeElement);if(t>-1){(e[t-1]||e[e.length]).focus()}}}()})),v.onDidPaste((()=>{let t="";const n=e.getLineCount();for(let o=0;o<n;o+=1)t+=e.getLineContent(o+1);t=t.replace(/\s\s+/g," "),e.setValue(t),v.setPosition({column:t.length+1,lineNumber:1})}))}}!function(e="Twigfield",t="",n){const o=new URLSearchParams;void 0!==e&&o.set("fieldType",e),void 0!==t&&o.set("twigfieldOptions",t);const i=n.includes("?")?"&":"?";if(void 0===window.twigfieldFieldTypes&&(window.twigfieldFieldTypes={}),e in window.twigfieldFieldTypes)return;window.twigfieldFieldTypes[e]=e;let r=new XMLHttpRequest;r.open("GET",n+i+o.toString(),!0),r.onload=function(){if(r.status>=200&&r.status<400){const e=JSON.parse(r.responseText);void 0===window.monacoAutocompleteItems&&(window.monacoAutocompleteItems={});for(const[t,n]of Object.entries(e))n.name in window.monacoAutocompleteItems||(window.monacoAutocompleteItems[n.name]=n.name,l(n.__completions,n.type,n.hasSubProperties),s(n.__completions,n.type))}else console.log("Autocomplete endpoint failed with status "+r.status)},r.send()}(t,d,c);let y=!1;const L=()=>{const e=v.getLayoutInfo().width,t=Math.min(1e3,v.getContentHeight());g.style.height=`${t}px`;try{y=!0,v.layout({width:e,height:t})}finally{y=!1}};function b(){let e=[];if(document.activeElement instanceof HTMLFormElement){const t=document.activeElement;t&&t.form&&(e=Array.prototype.filter.call(t.form.querySelectorAll('a:not([disabled]), button:not([disabled]), select:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])'),(function(e){return e instanceof HTMLElement&&(e.offsetWidth>0||e.offsetHeight>0||e===document.activeElement)})))}return e}function x(e,t){if(""===t){const t=document.querySelector(e);null!==t&&(t.style.display="initial")}}return v.onDidContentSizeChange(L),L(),""!==u&&(x("#"+p,v.getValue()),v.onDidBlurEditorWidget((()=>{x("#"+p,v.getValue())})),v.onDidFocusEditorWidget((()=>{!function(e){const t=document.querySelector(e);null!==t&&(t.style.display="none")}("#"+p)}))),v}""===n.p&&(n.p=window.codeEditorBaseAssetsUrl),window.makeMonacoEditor=d}},function(e){var t=function(t){return e(e.s=t)};e.O(0,[216,532],(function(){return t(3422),t(5919),t(1828)}));e.O()}]);
//# sourceMappingURL=code-editor.js.map