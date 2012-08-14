	ns4 = (document.layers)? true:false
	ie4 = (document.all)? true:false
	
	function show(id) {
		if (id == "dd_tapl"){clearTimeout(timeout_tapl)}
		else if (id == "dd_solutions"){clearTimeout(timeout_solutions)}
		else if (id == "dd_tadv"){clearTimeout(timeout_tadv)}
		else if (id == "dd_news"){clearTimeout(timeout_news)}
		else if (id == "dd_contact"){clearTimeout(timeout_contact)}
		if (ns4) document.layers[id].visibility = "show"
		else if (ie4) document.all[id].style.visibility = "visible"
		else if(!document.all && document.getElementById) document.getElementById(id).style.visibility = "visible"
	}
		
	var timeout_tapl = 0
	var timeout_solutions = 0
	var timeout_tadv = 0
	var timeout_news = 0
	var timeout_contact = 0
		
	function hide(id) {
		if (id == "dd_tapl"){timeout_tapl = setTimeout("hide2('dd_tapl')",800)}
		else if (id == "dd_solutions"){timeout_solutions = setTimeout("hide2('dd_solutions')",800)}
		else if (id == "dd_tadv"){timeout_tadv = setTimeout("hide2('dd_tadv')",800)}
		else if (id == "dd_news"){timeout_news = setTimeout("hide2('dd_news')",800)}
		else if (id == "dd_contact"){timeout_contact = setTimeout("hide2('dd_contact')",800)}
	}
		
	function hide2(id) {
		if (ns4) document.layers[id].visibility = "hide"
		else if (ie4) document.all[id].style.visibility = "hidden"
		else if(!document.all && document.getElementById) document.getElementById(id).style.visibility = "hidden"
		if (id == "dd_tapl")
			{rollover('tapl','NavOff_tapl')}
		if (id == "dd_solutions")
			{rollover('solutions','NavOff_solutions')}
		if (id == "tadv")
			{rollover('tadv','NavOff_tadv')}
		if (id == "news")
			{rollover('news','NavOff_news')}
		if (id == "contact")
			{rollover('contact','NavOff_contact')}
	}
