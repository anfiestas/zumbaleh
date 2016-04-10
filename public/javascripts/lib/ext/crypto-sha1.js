/*
 * Crypto-JS v2.5.2
 * http://code.google.com/p/crypto-js/
 * (c) 2009-2011 by Jeff Mott. All rights reserved.
 * http://code.google.com/p/crypto-js/wiki/License
 */
(typeof Crypto=="undefined"||!Crypto.util)&&function(){var d=window.Crypto={},m=d.util={rotl:function(a,c){return a<<c|a>>>32-c},rotr:function(a,c){return a<<32-c|a>>>c},endian:function(a){if(a.constructor==Number)return m.rotl(a,8)&16711935|m.rotl(a,24)&4278255360;for(var c=0;c<a.length;c++)a[c]=m.endian(a[c]);return a},randomBytes:function(a){for(var c=[];a>0;a--)c.push(Math.floor(Math.random()*256));return c},bytesToWords:function(a){for(var c=[],b=0,i=0;b<a.length;b++,i+=8)c[i>>>5]|=a[b]<<24-
i%32;return c},wordsToBytes:function(a){for(var c=[],b=0;b<a.length*32;b+=8)c.push(a[b>>>5]>>>24-b%32&255);return c},bytesToHex:function(a){for(var c=[],b=0;b<a.length;b++)c.push((a[b]>>>4).toString(16)),c.push((a[b]&15).toString(16));return c.join("")},hexToBytes:function(a){for(var c=[],b=0;b<a.length;b+=2)c.push(parseInt(a.substr(b,2),16));return c},bytesToBase64:function(a){if(typeof btoa=="function")return btoa(f.bytesToString(a));for(var c=[],b=0;b<a.length;b+=3)for(var i=a[b]<<16|a[b+1]<<8|
a[b+2],l=0;l<4;l++)b*8+l*6<=a.length*8?c.push("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(i>>>6*(3-l)&63)):c.push("=");return c.join("")},base64ToBytes:function(a){if(typeof atob=="function")return f.stringToBytes(atob(a));for(var a=a.replace(/[^A-Z0-9+\/]/ig,""),c=[],b=0,i=0;b<a.length;i=++b%4)i!=0&&c.push(("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(a.charAt(b-1))&Math.pow(2,-2*i+8)-1)<<i*2|"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(a.charAt(b))>>>
6-i*2);return c}},d=d.charenc={};d.UTF8={stringToBytes:function(a){return f.stringToBytes(unescape(encodeURIComponent(a)))},bytesToString:function(a){return decodeURIComponent(escape(f.bytesToString(a)))}};var f=d.Binary={stringToBytes:function(a){for(var c=[],b=0;b<a.length;b++)c.push(a.charCodeAt(b)&255);return c},bytesToString:function(a){for(var c=[],b=0;b<a.length;b++)c.push(String.fromCharCode(a[b]));return c.join("")}}}();
(function(){var d=Crypto,m=d.util,f=d.charenc,a=f.UTF8,c=f.Binary,b=d.SHA1=function(a,l){var g=m.wordsToBytes(b._sha1(a));return l&&l.asBytes?g:l&&l.asString?c.bytesToString(g):m.bytesToHex(g)};b._sha1=function(b){b.constructor==String&&(b=a.stringToBytes(b));var c=m.bytesToWords(b),g=b.length*8,b=[],d=1732584193,h=-271733879,j=-1732584194,k=271733878,f=-1009589776;c[g>>5]|=128<<24-g%32;c[(g+64>>>9<<4)+15]=g;for(g=0;g<c.length;g+=16){for(var o=d,p=h,q=j,r=k,s=f,e=0;e<80;e++){if(e<16)b[e]=c[g+e];else{var n=
b[e-3]^b[e-8]^b[e-14]^b[e-16];b[e]=n<<1|n>>>31}n=(d<<5|d>>>27)+f+(b[e]>>>0)+(e<20?(h&j|~h&k)+1518500249:e<40?(h^j^k)+1859775393:e<60?(h&j|h&k|j&k)-1894007588:(h^j^k)-899497514);f=k;k=j;j=h<<30|h>>>2;h=d;d=n}d+=o;h+=p;j+=q;k+=r;f+=s}return[d,h,j,k,f]};b._blocksize=16;b._digestsize=20})();
