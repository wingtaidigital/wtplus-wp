/*! modernizr 3.5.0 (Custom Build) | MIT *
 * https://modernizr.com/download/?-appearance-csscolumns-flexbox-inputtypes-touchevents-setclasses !*/
!function(e,t,n){function r(e,t){return typeof e===t}function o(){var e,t,n,o,s,i,a;for(var l in b)if(b.hasOwnProperty(l)){if(e=[],t=b[l],t.name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(n=0;n<t.options.aliases.length;n++)e.push(t.options.aliases[n].toLowerCase());for(o=r(t.fn,"function")?t.fn():t.fn,s=0;s<e.length;s++)i=e[s],a=i.split("."),1===a.length?Modernizr[a[0]]=o:(!Modernizr[a[0]]||Modernizr[a[0]]instanceof Boolean||(Modernizr[a[0]]=new Boolean(Modernizr[a[0]])),Modernizr[a[0]][a[1]]=o),C.push((o?"":"no-")+a.join("-"))}}function s(e){var t=w.className,n=Modernizr._config.classPrefix||"";if(x&&(t=t.baseVal),Modernizr._config.enableJSClass){var r=new RegExp("(^|\\s)"+n+"no-js(\\s|$)");t=t.replace(r,"$1"+n+"js$2")}Modernizr._config.enableClasses&&(t+=" "+n+e.join(" "+n),x?w.className.baseVal=t:w.className=t)}function i(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):x?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function a(){var e=t.body;return e||(e=i(x?"svg":"body"),e.fake=!0),e}function l(e,n,r,o){var s,l,u,f,c="modernizr",d=i("div"),p=a();if(parseInt(r,10))for(;r--;)u=i("div"),u.id=o?o[r]:c+(r+1),d.appendChild(u);return s=i("style"),s.type="text/css",s.id="s"+c,(p.fake?p:d).appendChild(s),p.appendChild(d),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(t.createTextNode(e)),d.id=c,p.fake&&(p.style.background="",p.style.overflow="hidden",f=w.style.overflow,w.style.overflow="hidden",w.appendChild(p)),l=n(d,e),p.fake?(p.parentNode.removeChild(p),w.style.overflow=f,w.offsetHeight):d.parentNode.removeChild(d),!!l}function u(e,t){return!!~(""+e).indexOf(t)}function f(e){return e.replace(/([a-z])-([a-z])/g,function(e,t,n){return t+n.toUpperCase()}).replace(/^-/,"")}function c(e,t){return function(){return e.apply(t,arguments)}}function d(e,t,n){var o;for(var s in e)if(e[s]in t)return n===!1?e[s]:(o=t[e[s]],r(o,"function")?c(o,n||t):o);return!1}function p(e){return e.replace(/([A-Z])/g,function(e,t){return"-"+t.toLowerCase()}).replace(/^ms-/,"-ms-")}function m(t,n,r){var o;if("getComputedStyle"in e){o=getComputedStyle.call(e,t,n);var s=e.console;if(null!==o)r&&(o=o.getPropertyValue(r));else if(s){var i=s.error?"error":"log";s[i].call(s,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else o=!n&&t.currentStyle&&t.currentStyle[r];return o}function h(t,r){var o=t.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(p(t[o]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var s=[];o--;)s.push("("+p(t[o])+":"+r+")");return s=s.join(" or "),l("@supports ("+s+") { #modernizr { position: absolute; } }",function(e){return"absolute"==m(e,null,"position")})}return n}function y(e,t,o,s){function a(){c&&(delete B.style,delete B.modElem)}if(s=r(s,"undefined")?!1:s,!r(o,"undefined")){var l=h(e,o);if(!r(l,"undefined"))return l}for(var c,d,p,m,y,v=["modernizr","tspan","samp"];!B.style&&v.length;)c=!0,B.modElem=i(v.shift()),B.style=B.modElem.style;for(p=e.length,d=0;p>d;d++)if(m=e[d],y=B.style[m],u(m,"-")&&(m=f(m)),B.style[m]!==n){if(s||r(o,"undefined"))return a(),"pfx"==t?m:!0;try{B.style[m]=o}catch(g){}if(B.style[m]!=y)return a(),"pfx"==t?m:!0}return a(),!1}function v(e,t,n,o,s){var i=e.charAt(0).toUpperCase()+e.slice(1),a=(e+" "+E.join(i+" ")+i).split(" ");return r(t,"string")||r(t,"undefined")?y(a,t,o,s):(a=(e+" "+j.join(i+" ")+i).split(" "),d(a,t,n))}function g(e,t,r){return v(e,n,n,t,r)}var C=[],b=[],S={_version:"3.5.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout(function(){t(n[e])},0)},addTest:function(e,t,n){b.push({name:e,fn:t,options:n})},addAsyncTest:function(e){b.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=S,Modernizr=new Modernizr;var w=t.documentElement,x="svg"===w.nodeName.toLowerCase(),_=S._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];S._prefixes=_;var k=i("input"),T="search tel url email datetime date month week time datetime-local number range color".split(" "),z={};Modernizr.inputtypes=function(e){for(var r,o,s,i=e.length,a="1)",l=0;i>l;l++)k.setAttribute("type",r=e[l]),s="text"!==k.type&&"style"in k,s&&(k.value=a,k.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(r)&&k.style.WebkitAppearance!==n?(w.appendChild(k),o=t.defaultView,s=o.getComputedStyle&&"textfield"!==o.getComputedStyle(k,null).WebkitAppearance&&0!==k.offsetHeight,w.removeChild(k)):/^(search|tel)$/.test(r)||(s=/^(url|email)$/.test(r)?k.checkValidity&&k.checkValidity()===!1:k.value!=a)),z[e[l]]=!!s;return z}(T);var P=S.testStyles=l;Modernizr.addTest("touchevents",function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var r=["@media (",_.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");P(r,function(e){n=9===e.offsetTop})}return n});var A="Moz O ms Webkit",E=S._config.usePrefixes?A.split(" "):[];S._cssomPrefixes=E;var j=S._config.usePrefixes?A.toLowerCase().split(" "):[];S._domPrefixes=j;var N={elem:i("modernizr")};Modernizr._q.push(function(){delete N.elem});var B={style:N.elem.style};Modernizr._q.unshift(function(){delete B.style}),S.testAllProps=v,S.testAllProps=g,Modernizr.addTest("appearance",g("appearance")),function(){Modernizr.addTest("csscolumns",function(){var e=!1,t=g("columnCount");try{e=!!t,e&&(e=new Boolean(e))}catch(n){}return e});for(var e,t,n=["Width","Span","Fill","Gap","Rule","RuleColor","RuleStyle","RuleWidth","BreakBefore","BreakAfter","BreakInside"],r=0;r<n.length;r++)e=n[r].toLowerCase(),t=g("column"+n[r]),("breakbefore"===e||"breakafter"===e||"breakinside"==e)&&(t=t||g(n[r])),Modernizr.addTest("csscolumns."+e,t)}(),Modernizr.addTest("flexbox",g("flexBasis","1px",!0)),o(),s(C),delete S.addTest,delete S.addAsyncTest;for(var R=0;R<Modernizr._q.length;R++)Modernizr._q[R]();e.Modernizr=Modernizr}(window,document);
