/**
 * jquery.lookup.
 * One-level object search plugin.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
/*
	Usage example:

	var dataset = [
	    {id: 63, sex: 'male', description: 'i am tall and handsome', photo: 'no photo'},
		{id: 64, sex: 'male', description: 'i am short but handsome', photo: 'got photo'},
		{id: 65, sex: 'female', description: 'i am short and chubby', photo: 'no photo'},
		{id: 66, sex: 'female', description: 'i am tall and pretty', photo: 'got photo'},
		{id: 67, sex: 'female', description: 'i am short but pretty', photo: 'got photo'}
	];

	$.lookup('female pretty', dataset);
	// this will return:
	// {id: 66, sex: 'female', description: 'i am tall and pretty', photo: 'got photo'},
	// {id: 67, sex: 'female', description: 'i am short but pretty', photo: 'got photo'}

	$.lookup(keyword, dataset);
	$.lookup(options);


	$.lookup({
		keyword: 'female pretty', // Array or String. If String, separate keywords by space.
		inside: dataset, // An array of objects.
		within: [] // Optional. Array or String.
		exclude: [] // Optional. // An array of objects.
	});
*/

Foundry.run(function($)
{
	var defaultOptions = {
		using: [],
		inside: {},
		within: [],
		exclude: [],
		match: function(data){}, // Callback to transform the data if required.
		depth: 1 // How deep should lookup dig if nested objects are found.
	};

	var $match = function(value, keyword)
	{
		if (typeof value=='string' || typeof value=='number')
			return value.toUpperCase().indexOf(keyword.toUpperCase()) >= 0;

		return false;
	};

	$.lookup = function(options)
	{
		// If $.lookup(keyword, dataset) was called instead.
		if (typeof options=="string")
		{
			options = {
				using: arguments[0],
				dataset: arguments[1] || {}
			}
		}

		// Sanitize options.
		options = $.extend({}, defaultOptions, options);

		// Don't search the object if there's nothing to search.
		if ($.isEmptyObject(options.inside))
			return {};

		// If keyword given is a string, split spaces and turn
		// them into an array of keywords.
		if (typeof options.using=="string")
			options.using = $.trim(options.using).split(' ');

		// If there are no keywords, return the entire dataset.
		if (options.using.length < 1)
			return options.inside;

		// If the search context is a string, split spaces and
		// turn them into an array of context.
		if (typeof options.within=="string")
			options.within = $.trim(options.within).split(' ');

		// Clone a copy of the dataset so we can work with it
		// without affecting the original dataset.
		var dataset = $.makeArray($.extend(true, {length: options.inside.length}, options.inside));

		// Go through each keyword
		var result = [];
		$.each(options.using, function(i, keyword)
		{
			// TODO: Nested object lookup
			result = result.concat(
				$.grep(dataset, function(data)
				{
					var match = false;

					// If data has been added to the result, then skip it.
					if (data['.added']) return;

					// Exclude object if it matches the pattern given in the exclude list.
					$.each(options.exclude, function(i, pattern)
					{
						for (key in pattern)
						{
							match = $match(data[key], pattern[key]);
							return !match;
						}
					});

					// If the object is excluded, skip & conitnue to the next object in the dataset.
					if (match) return false;

					// If a search context was given, search WITHIN that context.
					if (options.within.length > 0)
					{
						// TODO: Transform matched data with provided match callback
						$.each(options.within, function(i, key)
						{
							match = $match(data[key], keyword);
							return !match;
						});

					// Else, go through every property in the object.
					} else {

						// TODO: Transform matched data with provided match callback
						for (key in data)
						{
							match = $match(data[key], keyword);
							if (match) break;
						}
					}

					// Mark the object as added, so we won't add it again the second time around.
					if (match) data['.added'] = true;

					return match;
				})
			);
		});

		return result;
	};
});
