Foundry.run(function(a){if(a.isMSIE())var b=a(document.createElement("style")).attr({title:"foundry.stylesheet",type:"text/css"}).appendTo("head");var c=function(b,c){return this.title=b,this.cssText=c,this.needle={start:a.uid("/*","*/"),end:a.uid("/*","*/")},this};c.prototype.enable=function(){return a.isMSIE()?b.append(this.needle.start+this.cssText+this.needle.end):this.ownerNode=a(document.createElement("style")).attr({title:this.title,type:"text/css"}).html(this.cssText).appendTo("head"),this},c.prototype.disable=function(){if(a.isMSIE()){var c=b.html();b.html(a.String.remove(c,c.indexOf(this.needle.start),c.indexOf(this.needle.end)))}else this.ownerNode.remove();return this},c.prototype.destroy=function(){this.disable(),delete d[this.name]};var d={};a.Stylesheet=function(){var a=arguments[0];switch(arguments.length){case 0:return d;case 1:return d[a];case 2:default:var b=d[a]=new c(a,arguments[1]);return b.enable()}}});