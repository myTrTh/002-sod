$(document).ready(function(){$(".nav-toggle").on("click",function(){$("nav > ul > li").not(".logo").slideToggle("fast")}),$(window).resize(function(){750<$(window).width()&&$("nav > ul > li").removeAttr("style")})}),$(function(){$(".bbimg").on("click",function(){var t=$(this).attr("id"),e=$("textarea");if("post"==t)var n="post:",l="";else n="["+t+"]",l="[/"+t+"]";var i=e[0].selectionStart,a=e[0].selectionEnd,o=e.val(),s=e.val().substr(i,a-i),r=n+s+l,c=e.val().length,u=o.substr(0,i),g=o.substr(a,c);e.val(u+r+g);e.val().length;e.focus(),0==s.length?e[0].setSelectionRange(i+n.length,i+n.length):e[0].setSelectionRange(i,a+n.length+l.length)})}),$(function(){$(".quote").on("click",function(){var t=$(this).attr("id").substr(5),e="[quote author="+$(this).parent().parent().parent().children().children().html().trim()+" date="+$("#hidden-date-"+t).text().trim()+" post="+t+"]\n"+$("#message"+t).text().trim()+"\n[/quote]\n\n",n=$("textarea"),l=n[0].selectionStart,i=n[0].selectionEnd,a=n.val(),o=a.substr(0,l),s=n.val().length,r=a.substr(i,s);return n.val(o+e+r),n.focus(),n[0].setSelectionRange(l+e.length,l+e.length),!1})});