!function(o){var r={};function n(e){if(r[e])return r[e].exports;var t=r[e]={i:e,l:!1,exports:{}};return o[e].call(t.exports,t,t.exports,n),t.l=!0,t.exports}n.m=o,n.c=r,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)n.d(o,r,function(e){return t[e]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=31)}({0:function(e,t){var o;o=function(){return this}();try{o=o||new Function("return this")()}catch(e){"object"==typeof window&&(o=window)}e.exports=o},31:function(e,t,o){"use strict";var r=o(32);$(document).ready(function(){var e=$("#kontrolgruppenGlobalSearch"),t=r(window.location,!0);$("#kontrolgruppenGlobalSearch > input").val(t.query.search),e.on("submit",function(e){e.preventDefault(),e.stopPropagation();var t=$(this).find("input").val();return window.location.href="/search/?search="+t,!1})})},32:function(e,t,r){"use strict";(function(s){var h=r(33),d=r(34),a=/^[A-Za-z][A-Za-z0-9+-.]*:\/\//,o=/^([a-z][a-z0-9.+-]*:)?(\/\/)?([\S\s]*)/i,t=new RegExp("^[\\x09\\x0A\\x0B\\x0C\\x0D\\x20\\xA0\\u1680\\u180E\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200A\\u202F\\u205F\\u3000\\u2028\\u2029\\uFEFF]+");function y(e){return(e||"").toString().replace(t,"")}var m=[["#","hash"],["?","query"],function(e){return e.replace("\\","/")},["/","pathname"],["@","auth",1],[NaN,"host",void 0,1,1],[/:(\d+)$/,"port",void 0,1],[NaN,"hostname",void 0,1,1]],i={hash:1,query:1};function v(e){var t,o=("undefined"!=typeof window?window:void 0!==s?s:"undefined"!=typeof self?self:{}).location||{},r={},n=typeof(e=e||o);if("blob:"===e.protocol)r=new w(unescape(e.pathname),{});else if("string"==n)for(t in r=new w(e,{}),i)delete r[t];else if("object"==n){for(t in e)t in i||(r[t]=e[t]);void 0===r.slashes&&(r.slashes=a.test(e.href))}return r}function g(e){e=y(e);var t=o.exec(e);return{protocol:t[1]?t[1].toLowerCase():"",slashes:!!t[2],rest:t[3]}}function w(e,t,o){if(e=y(e),!(this instanceof w))return new w(e,t,o);var r,n,s,a,i,u,c=m.slice(),l=typeof t,p=this,f=0;for("object"!=l&&"string"!=l&&(o=t,t=null),o&&"function"!=typeof o&&(o=d.parse),t=v(t),r=!(n=g(e||"")).protocol&&!n.slashes,p.slashes=n.slashes||r&&t.slashes,p.protocol=n.protocol||t.protocol||"",e=n.rest,n.slashes||(c[3]=[/(.*)/,"pathname"]);f<c.length;f++)"function"!=typeof(a=c[f])?(s=a[0],u=a[1],s!=s?p[u]=e:"string"==typeof s?~(i=e.indexOf(s))&&(e="number"==typeof a[2]?(p[u]=e.slice(0,i),e.slice(i+a[2])):(p[u]=e.slice(i),e.slice(0,i))):(i=s.exec(e))&&(p[u]=i[1],e=e.slice(0,i.index)),p[u]=p[u]||r&&a[3]&&t[u]||"",a[4]&&(p[u]=p[u].toLowerCase())):e=a(e);o&&(p.query=o(p.query)),r&&t.slashes&&"/"!==p.pathname.charAt(0)&&(""!==p.pathname||""!==t.pathname)&&(p.pathname=function(e,t){if(""===e)return t;for(var o=(t||"/").split("/").slice(0,-1).concat(e.split("/")),r=o.length,n=o[r-1],s=!1,a=0;r--;)"."===o[r]?o.splice(r,1):".."===o[r]?(o.splice(r,1),a++):a&&(0===r&&(s=!0),o.splice(r,1),a--);return s&&o.unshift(""),"."!==n&&".."!==n||o.push(""),o.join("/")}(p.pathname,t.pathname)),h(p.port,p.protocol)||(p.host=p.hostname,p.port=""),p.username=p.password="",p.auth&&(a=p.auth.split(":"),p.username=a[0]||"",p.password=a[1]||""),p.origin=p.protocol&&p.host&&"file:"!==p.protocol?p.protocol+"//"+p.host:"null",p.href=p.toString()}w.prototype={set:function(e,t,o){var r=this;switch(e){case"query":"string"==typeof t&&t.length&&(t=(o||d.parse)(t)),r[e]=t;break;case"port":r[e]=t,h(t,r.protocol)?t&&(r.host=r.hostname+":"+t):(r.host=r.hostname,r[e]="");break;case"hostname":r[e]=t,r.port&&(t+=":"+r.port),r.host=t;break;case"host":r[e]=t,/:\d+$/.test(t)?(t=t.split(":"),r.port=t.pop(),r.hostname=t.join(":")):(r.hostname=t,r.port="");break;case"protocol":r.protocol=t.toLowerCase(),r.slashes=!o;break;case"pathname":case"hash":if(t){var n="pathname"===e?"/":"#";r[e]=t.charAt(0)!==n?n+t:t}else r[e]=t;break;default:r[e]=t}for(var s=0;s<m.length;s++){var a=m[s];a[4]&&(r[a[1]]=r[a[1]].toLowerCase())}return r.origin=r.protocol&&r.host&&"file:"!==r.protocol?r.protocol+"//"+r.host:"null",r.href=r.toString(),r},toString:function(e){e&&"function"==typeof e||(e=d.stringify);var t,o=this,r=o.protocol;r&&":"!==r.charAt(r.length-1)&&(r+=":");var n=r+(o.slashes?"//":"");return o.username&&(n+=o.username,o.password&&(n+=":"+o.password),n+="@"),n+=o.host+o.pathname,(t="object"==typeof o.query?e(o.query):o.query)&&(n+="?"!==t.charAt(0)?"?"+t:t),o.hash&&(n+=o.hash),n}},w.extractProtocol=g,w.location=v,w.trimLeft=y,w.qs=d,e.exports=w}).call(this,r(0))},33:function(e,t,o){"use strict";e.exports=function(e,t){if(t=t.split(":")[0],!(e=+e))return!1;switch(t){case"http":case"ws":return 80!==e;case"https":case"wss":return 443!==e;case"ftp":return 21!==e;case"gopher":return 70!==e;case"file":return!1}return 0!==e}},34:function(e,t,o){"use strict";var s=Object.prototype.hasOwnProperty;function a(e){try{return decodeURIComponent(e.replace(/\+/g," "))}catch(e){return null}}t.stringify=function(e,t){t=t||"";var o,r,n=[];for(r in"string"!=typeof t&&(t="?"),e)if(s.call(e,r)){if((o=e[r])||null!=o&&!isNaN(o)||(o=""),r=encodeURIComponent(r),o=encodeURIComponent(o),null===r||null===o)continue;n.push(r+"="+o)}return n.length?t+n.join("&"):""},t.parse=function(e){for(var t,o=/([^=?&]+)=?([^&]*)/g,r={};t=o.exec(e);){var n=a(t[1]),s=a(t[2]);null===n||null===s||n in r||(r[n]=s)}return r}}});