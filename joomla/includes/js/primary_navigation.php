
NavOn_tapl = new Image();
NavOn_tapl.src = "http://www.talentplus.com/images/topnav_tapl_on.gif"
NavOff_tapl = new Image();
NavOff_tapl.src = "http://www.talentplus.com/images/topnav_tapl_off.gif"

NavOn_solutions = new Image();
NavOn_solutions.src = "http://www.talentplus.com/images/topnav_solutions_on.gif"
NavOff_solutions = new Image();
NavOff_solutions.src = "http://www.talentplus.com/images/topnav_solutions_off.gif"

NavOn_tadv = new Image();
NavOn_tadv.src = "http://www.talentplus.com/images/topnav_tadv_on.gif"
NavOff_tadv = new Image();
NavOff_tadv.src = "http://www.talentplus.com/images/topnav_tadv_off.gif"

NavOn_news = new Image();
NavOn_news.src = "http://www.talentplus.com/images/topnav_news_on.gif"
NavOff_news = new Image();
NavOff_news.src = "http://www.talentplus.com/images/topnav_news_off.gif"

NavOn_contact = new Image();
NavOn_contact.src = "http://www.talentplus.com/images/topnav_contact_on.gif"
NavOff_contact = new Image();
NavOff_contact.src = "http://www.talentplus.com/images/topnav_contact_off.gif"

function rollover(whichimg,overimg) {
	eval('document.images["' + whichimg + '"].src = ' + overimg + '.src');
}

