!function(t){var r={};function o(n){if(r[n])return r[n].exports;var e=r[n]={i:n,l:!1,exports:{}};return t[n].call(e.exports,e,e.exports,o),e.l=!0,e.exports}o.m=t,o.c=r,o.d=function(n,e,t){o.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:t})},o.r=function(n){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},o.t=function(e,n){if(1&n&&(e=o(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(o.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var r in e)o.d(t,r,function(n){return e[n]}.bind(null,r));return t},o.n=function(n){var e=n&&n.__esModule?function(){return n.default}:function(){return n};return o.d(e,"a",e),e},o.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},o.p="",o(o.s=28)}({28:function(n,e){$(document).ready(function(){$(".js-process-journal-quickview-button").on("click",function(n){var e=$("#journalQuickviewModal"),t=$(this).data("process-id");e.find(".js-journal-entry-modal-show-all-link").attr("href","/process/"+t+"/journal/"),e.modal({}),function(n){$("#journalQuickviewModal .js-journal-entry-modal-content").html(""),$("#journalQuickviewModal .js-journal-entry-modal-spinner").show(),fetch("/process/"+n+"/journal/latest/").then(function(n){return $("#journalQuickviewModal .js-journal-entry-modal-spinner").hide(),n.text()}).then(function(n){$("#journalQuickviewModal .js-journal-entry-modal-content").html(n)})}(t)})})}});