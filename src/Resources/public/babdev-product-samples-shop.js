/*! For license information please see babdev-product-samples-shop.js.LICENSE.txt */
(()=>{"use strict";function t(e){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},t(e)}function e(){e=function(){return r};var r={},n=Object.prototype,o=n.hasOwnProperty,a="function"==typeof Symbol?Symbol:{},i=a.iterator||"@@iterator",u=a.asyncIterator||"@@asyncIterator",l=a.toStringTag||"@@toStringTag";function s(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{s({},"")}catch(t){s=function(t,e,r){return t[e]=r}}function c(t,e,r,n){var o=e&&e.prototype instanceof d?e:d,a=Object.create(o.prototype),i=new _(n||[]);return a._invoke=function(t,e,r){var n="suspendedStart";return function(o,a){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===o)throw a;return O()}for(r.method=o,r.arg=a;;){var i=r.delegate;if(i){var u=S(i,r);if(u){if(u===h)continue;return u}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var l=f(t,e,r);if("normal"===l.type){if(n=r.done?"completed":"suspendedYield",l.arg===h)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(n="completed",r.method="throw",r.arg=l.arg)}}}(t,r,i),a}function f(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}r.wrap=c;var h={};function d(){}function p(){}function y(){}var v={};s(v,i,(function(){return this}));var m=Object.getPrototypeOf,g=m&&m(m(k([])));g&&g!==n&&o.call(g,i)&&(v=g);var b=y.prototype=d.prototype=Object.create(v);function w(t){["next","throw","return"].forEach((function(e){s(t,e,(function(t){return this._invoke(e,t)}))}))}function x(e,r){function n(a,i,u,l){var s=f(e[a],e,i);if("throw"!==s.type){var c=s.arg,h=c.value;return h&&"object"==t(h)&&o.call(h,"__await")?r.resolve(h.__await).then((function(t){n("next",t,u,l)}),(function(t){n("throw",t,u,l)})):r.resolve(h).then((function(t){c.value=t,u(c)}),(function(t){return n("throw",t,u,l)}))}l(s.arg)}var a;this._invoke=function(t,e){function o(){return new r((function(r,o){n(t,e,r,o)}))}return a=a?a.then(o,o):o()}}function S(t,e){var r=t.iterator[e.method];if(void 0===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=void 0,S(t,e),"throw"===e.method))return h;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return h}var n=f(r,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,h;var o=n.arg;return o?o.done?(e[t.resultName]=o.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,h):o:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,h)}function E(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function L(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function _(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(E,this),this.reset(!0)}function k(t){if(t){var e=t[i];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var r=-1,n=function e(){for(;++r<t.length;)if(o.call(t,r))return e.value=t[r],e.done=!1,e;return e.value=void 0,e.done=!0,e};return n.next=n}}return{next:O}}function O(){return{value:void 0,done:!0}}return p.prototype=y,s(b,"constructor",y),s(y,"constructor",p),p.displayName=s(y,l,"GeneratorFunction"),r.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===p||"GeneratorFunction"===(e.displayName||e.name))},r.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,y):(t.__proto__=y,s(t,l,"GeneratorFunction")),t.prototype=Object.create(b),t},r.awrap=function(t){return{__await:t}},w(x.prototype),s(x.prototype,u,(function(){return this})),r.AsyncIterator=x,r.async=function(t,e,n,o,a){void 0===a&&(a=Promise);var i=new x(c(t,e,n,o),a);return r.isGeneratorFunction(e)?i:i.next().then((function(t){return t.done?t.value:i.next()}))},w(b),s(b,l,"Generator"),s(b,i,(function(){return this})),s(b,"toString",(function(){return"[object Generator]"})),r.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},r.values=k,_.prototype={constructor:_,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(L),!t)for(var e in this)"t"===e.charAt(0)&&o.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function r(r,n){return i.type="throw",i.arg=t,e.next=r,n&&(e.method="next",e.arg=void 0),!!n}for(var n=this.tryEntries.length-1;n>=0;--n){var a=this.tryEntries[n],i=a.completion;if("root"===a.tryLoc)return r("end");if(a.tryLoc<=this.prev){var u=o.call(a,"catchLoc"),l=o.call(a,"finallyLoc");if(u&&l){if(this.prev<a.catchLoc)return r(a.catchLoc,!0);if(this.prev<a.finallyLoc)return r(a.finallyLoc)}else if(u){if(this.prev<a.catchLoc)return r(a.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return r(a.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var n=this.tryEntries[r];if(n.tryLoc<=this.prev&&o.call(n,"finallyLoc")&&this.prev<n.finallyLoc){var a=n;break}}a&&("break"===t||"continue"===t)&&a.tryLoc<=e&&e<=a.finallyLoc&&(a=null);var i=a?a.completion:{};return i.type=t,i.arg=e,a?(this.method="next",this.next=a.finallyLoc,h):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),h},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),L(r),h}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;L(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,r){return this.delegate={iterator:k(t),resultName:e,nextLoc:r},"next"===this.method&&(this.arg=void 0),h}},r}function r(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){var r=null==t?null:"undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(null==r)return;var n,o,a=[],i=!0,u=!1;try{for(r=r.call(t);!(i=(n=r.next()).done)&&(a.push(n.value),!e||a.length!==e);i=!0);}catch(t){u=!0,o=t}finally{try{i||null==r.return||r.return()}finally{if(u)throw o}}return a}(t,e)||function(t,e){if(!t)return;if("string"==typeof t)return n(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);"Object"===r&&t.constructor&&(r=t.constructor.name);if("Map"===r||"Set"===r)return Array.from(t);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return n(t,e)}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function n(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}function o(t,e,r,n,o,a,i){try{var u=t[a](i),l=u.value}catch(t){return void r(t)}u.done?e(l):Promise.resolve(l).then(n,o)}function a(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}(function(){function t(e){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.button=e,this.buttonMessageContainer=this.button.querySelector(".button-message")}var n,i,u,l,s;return n=t,i=[{key:"init",value:function(){var t=this;null!==document.getElementById("sylius-variants-pricing")?document.querySelectorAll('[name*="sylius_add_to_cart[cartItem][variant]"]').forEach((function(e){e.addEventListener("change",(function(e){return t.handleProductOptionsChange(e)}))})):null!==document.getElementById("sylius-product-variants")&&document.querySelectorAll('[name="sylius_add_to_cart[cartItem][variant]"]').forEach((function(e){e.addEventListener("change",(function(e){return t.handleProductVariantsChange(e)}))})),this.button.addEventListener("click",(function(e){return t.requestASample(e)}))}},{key:"handleProductOptionsChange",value:function(t){var e,r=document.getElementById("sylius-variants-pricing"),n="";document.querySelectorAll("#sylius-product-adding-to-cart select[data-option]").forEach((function(t){var e=t.options[t.options.selectedIndex];n+="[data-".concat(t.dataset.option,'="').concat(e.value,'"]')}));var o,a=r.querySelector(n);if(null!==a)if("yes"===(null!==(e=a.dataset.freeSample)&&void 0!==e&&e)){var i,u;this.updateButtonDisplayText(null!==(i=null!==(u=this.button.dataset.freeSampleMessage)&&void 0!==u?u:this.button.dataset.sampleMessage)&&void 0!==i?i:"Request a Sample")}else{var l,s,c,f,h=null!==(l=a.dataset.samplePrice)&&void 0!==l?l:null;null!==h?this.updateButtonDisplayText((null!==(s=null!==(c=this.button.dataset.paidSampleMessage)&&void 0!==c?c:this.button.dataset.sampleMessage)&&void 0!==s?s:"Request a Sample").replace("%price%",h)):this.updateButtonDisplayText(null!==(f=this.button.dataset.sampleMessage)&&void 0!==f?f:"Request a Sample")}else this.updateButtonDisplayText(null!==(o=this.button.dataset.sampleMessage)&&void 0!==o?o:"Request a Sample")}},{key:"handleProductVariantsChange",value:function(t){var e,r=t.currentTarget.closest("tr").querySelector(".sylius-product-variant-price");if("yes"===(null!==(e=r.dataset.freeSample)&&void 0!==e&&e)){var n,o;this.updateButtonDisplayText(null!==(n=null!==(o=this.button.dataset.freeSampleMessage)&&void 0!==o?o:this.button.dataset.sampleMessage)&&void 0!==n?n:"Request a Sample")}else{var a,i,u,l,s=null!==(a=r.dataset.samplePrice)&&void 0!==a?a:null;null!==s?this.updateButtonDisplayText((null!==(i=null!==(u=this.button.dataset.paidSampleMessage)&&void 0!==u?u:this.button.dataset.sampleMessage)&&void 0!==i?i:"Request a Sample").replace("%price%",s)):this.updateButtonDisplayText(null!==(l=this.button.dataset.sampleMessage)&&void 0!==l?l:"Request a Sample")}}},{key:"requestASample",value:(l=e().mark((function t(n){var o,a,i,u,l,s,c,f,h;return e().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return n.preventDefault(),a=document.getElementById("sylius-cart-validation-error"),i=document.getElementById("sylius-product-adding-to-cart"),(u=new FormData(i)).append(this.button.name,"1"),i.classList.add("loading"),t.next=8,fetch(i.action,{headers:{"X-Requested-With":"XMLHttpRequest"},method:null!==(o=i.method)&&void 0!==o?o:"POST",body:u});case 8:if(!(l=t.sent).ok){t.next=15;break}a.classList.add("hidden"),(c=null!==(s=i.dataset.redirect)&&void 0!==s?s:null)?window.location.href=c:window.location.reload(),t.next=23;break;case 15:return t.next=17,l.json();case 17:f=t.sent,h="",Object.entries(f.errors.errors).forEach((function(t){var e=r(t,2)[1];h+=e})),a.innerHTML=h,a.classList.remove("hidden"),i.classList.remove("loading");case 23:case"end":return t.stop()}}),t,this)})),s=function(){var t=this,e=arguments;return new Promise((function(r,n){var a=l.apply(t,e);function i(t){o(a,r,n,i,u,"next",t)}function u(t){o(a,r,n,i,u,"throw",t)}i(void 0)}))},function(t){return s.apply(this,arguments)})},{key:"updateButtonDisplayText",value:function(t){null!==this.buttonMessageContainer?this.buttonMessageContainer.innerText=t:this.button.innerText=t}}],u=[{key:"maybeInit",value:function(){var e=document.getElementById("sylius_add_to_cart_requestSample");null!==e&&new t(e).init()}}],i&&a(n.prototype,i),u&&a(n,u),Object.defineProperty(n,"prototype",{writable:!1}),t})().maybeInit()})();