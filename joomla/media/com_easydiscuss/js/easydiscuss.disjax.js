(function($){window.disjax=disjax={http:!1,format:"text",callback:function(a){},error:!1,btnArray:new Array,getHTTPObject:function(){var a=!1;if(typeof ActiveXObject!="undefined")try{a=new ActiveXObject("Msxml2.XMLHTTP")}catch(b){try{a=new ActiveXObject("Microsoft.XMLHTTP")}catch(c){a=!1}}else if(XMLHttpRequest)try{a=new XMLHttpRequest}catch(b){a=!1}this.http=a},load:function(view,method){var callback={};typeof view=="object"&&(callback=view.callback,view=view.view),url=discuss_site,url+="&tmpl=component",url+="&no_html=1",url+="&format=ajax",url+="&uid="+(new Date).getTime();var parameters="";parameters="&view="+view+"&layout="+method;if(arguments.length>2)for(var i=2;i<arguments.length;i++){var myArgument=arguments[i];if($.isArray(myArgument))for(var j=0;j<myArgument.length;j++){var argument=myArgument[j];if(typeof argument=="string"){var expr=/^\w+\(*\)$/,match=expr.exec(argument),arg=argument;match||(arg=escape(arg)),parameters+="&value"+(i-2)+"[]="+encodeURIComponent(arg)}}else{var argument=myArgument;if(typeof argument=="string"){var expr=/^\w+\(*\)$/,match=expr.exec(argument),arg=argument;match||(arg=escape(arg)),parameters+="&value"+(i-2)+"="+encodeURIComponent(arg)}}}var token=$(".easydiscuss-token").val();parameters+="&"+token+"=1",this.getHTTPObject();if(!this.http||!view||!method)return;var ths=this;this.http.open("POST",url,!0),this.http.setRequestHeader("Content-type","application/x-www-form-urlencoded"),this.http.setRequestHeader("Content-length",parameters.length),this.http.setRequestHeader("Connection","close"),this.http.onreadystatechange=function(){if(!ths)return;var http=ths.http;if(http.readyState==4)if(http.status==200){var result="";http.responseText&&(result=http.responseText),result=result.replace(/[\n\r]/g,""),result=eval(result),ths.process(result,callback)}else ths.error&&ths.error(http.status)},this.http.send(parameters)},getFormVal:function(a){var b=[],c=null;return $(":input",$(a)).each(function(){c=this.value.replace(/"/g,"&quot;"),c=encodeURIComponent(c),$(this).is(":checkbox")||$(this).is(":radio")?$(this).attr("checked")&&b.push(this.name+"="+escape(c)):b.push(this.name+"="+escape(c))}),b},process:function(result,callback){if(typeof callback=="function")return callback.apply(this,result);for(var i=0;i<result.length;i++){var action=result[i][0];switch(action){case"script":var data=result[i][1];eval(data);break;case"after":var id=result[i][1],value=result[i][2];$("#"+id).after(value);break;case"append":var id=result[i][1],value=result[i][2];$("#"+id).append(value);break;case"assign":var id=result[i][1],value=result[i][2];$("#"+id).html(value);break;case"value":var id=result[i][1],value=result[i][2];$("#"+id).val(value);break;case"prepend":var id=result[i][1],value=result[i][2];$("#"+id).prepend(value);break;case"destroy":var id=result[i][1];$("#"+id).remove();break;case"dialog":disjax.dialog(result[i][1]);break;case"alert":disjax.alert(result[i][1],result[i][2],result[i][3]);break;case"create":}}delete result},dialog:function(a){disjax._showPopup(a)},closedlg:function(){var a=$("#discuss-dialog"),b=$("#discuss-overlay");b.hide(),a.unbind(".dialog").hide(),$(document).unbind("keyup",disjax._attachPopupShortcuts)},_showPopup:function(a){var b={width:"500",height:"auto",type:"dialog"},a=$.extend({},b,a),c=$("#discuss-overlay");c.length<1&&(c='<div id="discuss-overlay" class="si_pop_overlay"></div>',c=$(c).appendTo("body"),c.click(function(){disjax.closedlg()})),c.css({width:$(document).width(),height:$(document).height()}).show();var d=$("#discuss-dialog");d.length<1&&(dialogTemplate='<div id="discuss-dialog" class="si_pop"><a href="javascript:void(0);" onclick="disjax.closedlg();" class="si_x">Close</a><div class="si_pop_in"></div></div>',d=$(dialogTemplate).appendTo("body")),d.fadeOut(0);var e=d.children(".si_pop_in");e.html(a.content),d.css({width:a.width=="auto"?"auto":parseInt(a.width),height:a.height=="auto"?"auto":parseInt(a.height),zIndex:99999}).show(0,function(){var a=function(){d.css({top:0,left:0}).position({my:"center",at:"center",of:window})},b;$(window).bind("resize.dialog scroll.dialog",function(){clearTimeout(b),b=setTimeout(a,50)}),a()}),d.hide(0,function(){d.fadeIn("fast")}),$("#edialog-cancel, #edialog-submit").live("mouseup",function(){disjax.closedlg()}),$(document).bind("keyup",disjax._attachPopupShortcuts)},_attachPopupShortcuts:function(a){a.keyCode==27&&disjax.closedlg()}}})(Foundry);