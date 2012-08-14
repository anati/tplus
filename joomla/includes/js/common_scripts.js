
function printArticle() {
	if (window.print) {
		setTimeout('window.print();', 333);
	}
	else if (agt.indexOf("mac") != -1) {
		alert("Press 'Cmd+p' on your keyboard to print article.");
	}
	else {
		alert("Press 'Ctrl+p' on your keyboard to print article.")
	}
}

function show_block(block)
{
	//alert(block);
	document.getElementById(block).style.display = "block";
}

function open_me(gohere)
{
	window.open(gohere, "popup", config="width=475,height=450,scrollbars=yes,toolbar=no,menubar=yes,resizable=no,top=0,left=0");
}

function printMe(gohere)
{
	window.open(gohere, "print", config="width=600,height=450,scrollbars=yes,toolbar=no,menubar=yes,resizable=no,top=0,left=0");
}
function emailMe(gohere)
{
	window.open(gohere, "print", config="width=600,height=450,scrollbars=yes,toolbar=no,menubar=yes,resizable=no,top=0,left=0");
}

function reSize()
{
	try{	
	var oBody	=	ifrm.document.body;
	var oFrame	=	document.all("ifrm");
		
	oFrame.style.height = oBody.scrollHeight + (oBody.offsetHeight - oBody.clientHeight);
	oFrame.style.width = oBody.scrollWidth + (oBody.offsetWidth - oBody.clientWidth);
	}
	//An error is raised if the IFrame domain != its container's domain
	catch(e)
	{
	window.status =	'Error: ' + e.number + '; ' + e.description;
	}
}

