!function(e,n){var t=e.documentElement,d="orientationchange"in window?"orientationchange":"resize",i=function(){var n=e.documentElement.getBoundingClientRect().width;n&&(n>768?(n=768,e.body.style.width="768px",e.body.style.margin="0 auto"):e.body.style.width=n+"px",t.style.fontSize=n/10+"px")};e.addEventListener&&(n.addEventListener(d,i,!1),e.addEventListener("DOMContentLoaded",i,!1))}(document,window);