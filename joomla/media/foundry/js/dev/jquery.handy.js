/**
 * jquery.handy.
 * Mini jquery plugins that are
 * handy but too costly for its own file.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Last updated: Tuesday, 12th July 2011.
 *
 */

Foundry.run(function($)
{

/**
 * [jquery.distinct]
 * by Jason Ramos
 *
 * Enhanced version of jQuery's unique function
 * that removes duplicates from anything else other
 * than just DOM elements.
 *
 * Last updated: Tuesday, 12th July 2011.
 */
$.distinct = function()
{
	var uniqueElements = $.unique;

	return function(items)
	{
		if (items.length < 1)
			return;

		// If item is an array of DOM elements
		if (items[0].nodeType)
			return uniqueElements.apply(this, arguments);

		// If item is an array of objects
		if (typeof items[0]=='object')
		{
			var unique = Math.random();
			var uniqueObjects = [];

			$.each(items, function(i)
			{
				if (!items[i][unique])
				{
					uniqueObjects.push(items[i]);
					items[i][unique] = true;
				}
			});

			$.each(uniqueObjects, function(i)
			{
				delete uniqueObjects[i][unique];
			});

			return uniqueObjects;
		}

		// Anything else (can be combination of string, integers and boolean)
		return $.grep(items, function(item, i) {
			return $.inArray(item, items) === i;
		});
	}

};


/**
 * [jquery.cleanDelimiter]
 * by Jason Ramos
 *
 * Holy grail of trimming whitespace & delimiter altogether
 * Turns this : ",df        ,,,  ,,,abc, sdasd sdfsdf    ,   asdsad, ,, , "
 * into this  : "df,abc,sdasd sdfsdf,asdsad"
 *
 */
$.cleanDelimiter = function(keyword, separator, removeDuplicates)
{
	var s = separator;
	keyword = keyword
		.replace(new RegExp('^['+s+'\\s]+|['+s+',\\s]+$','g'), '') // /^[,\s]+|[,\s]+$/g
		.replace(new RegExp(s+'['+s+'\\s]*'+s,'g'), s) // /,[,\s]*,/g
		.replace(new RegExp('[\\s]+'+s,'g'), s) // /[\s]+,/g
		.replace(new RegExp(s+'[\\s]+','g'), s); // /,[\s]+/g

	if (removeDuplicates)
		keyword = $.distinct(keyword.split(s)).join(s);

	return keyword;
};


/**
 * [jquery.initialPosition]
 * by Jason Ramos
 *
 * Sets an initial position to element, whether or not
 * the element has been rendered on screen, or is currently
 * hidden.
 *
 * Last updated: Friday, 1st July 2011.
 */
$.fn.initialPosition = function(options, forceHidden)
{
    return this.each(function()
    {
    	var element = $(this),
    		hidden = element.css('display')=='none',
    	    visibility = element.css('visiblity');

    	// TODO: Also check position reference element's visiblity?
    	if (hidden || forceHidden)
    	{
    		element
    			.css('visibility', 'hidden')
    			.show()
    			.position(options)
    			.hide()
    			.css('visibility', visibility);
    	} else {
    		element.position(options);
    	}
    });
};

/*
 * jquery.uid
 * Generates a unique id with optional prefix/suffix.
 *
 * Copyright (c) 2011 Jason Ramos
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version : 0.1
 * Released: Thursday, 14th April 2011.
 *
 */

$.uid = function(p,s)
{
	return ((p) ? p : '') + Math.random().toString().replace('.','') + ((s) ? s : '');
};

/**
 * [jquery.isDeferred]
 */
$.isDeferred = function(obj)
{
	return obj && $.isFunction(obj.always);
};

/**
 * [jquery.isUrl]
 */
$.isUrl = function(s)
{
	var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	return regexp.test(s);
};

/**
 * [jquery.isOpera]
 */
$.isOpera = typeof opera !== "undefined" && opera.toString() === "[object Opera]";

/**
 * [jquery.serializeJSON]
 */
$.fn.serializeJSON = function()
{
	var json = {};

	$.each($(this).serializeArray(), function(i, param)
	{
		if (json.hasOwnProperty(param.name))
		{
			// Convert it into an array
			if (!$.isArray(json[param.name]))
			{
				json[param.name] = [json[param.name]];
			}

			json[param.name].push(param.value);

		} else {

			json[param.name] = param.value;

		}
	});

	return json;
};

/*
 * [jquery.stretchToFit]
 *
 */
$.fn.stretchToFit = function() {
	return $.each(this, function()
	{
		var $this = $(this);

		$this
			.css('width', '100%')
			.css('width', $this.width() * 2 - $this.outerWidth(true) - parseInt($this.css('borderLeftWidth')) - parseInt($this.css('borderRightWidth')));
	});
}

// TODO: Should be done in foundry.php using conditional comments.
$.isMSIE = function() {
  return '\v' == 'v';
}

});
