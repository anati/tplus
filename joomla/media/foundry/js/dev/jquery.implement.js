/**
 * jquery.implement.
 * Allow JavascriptMVC controller to be implemented
 * within a jquery chain.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 * 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Last updated: Tuesday, 19th July 2011.
 *
 * Usage example:
 * $('#photo').implement('EasySocial.Photo', {params});
 *
 */

(function($)
{

	$.fn.implement = function(controllerName, options, callback)
	{
		var elements = this;

		var handler = 'controller' + Math.random();

		$.require.promise(handler,

			// Attempt to execute once before attaching a promise
			(function()
			{
				var controller = $.String.getObject(controllerName);

				// On first execution, if controller wasn't found,
				// then return this function to be attached as a require promise.
				if (controller===undefined)
					return arguments.callee;

				$.each(elements, function(){
					var instance = new controller(this, options);
					callback && callback.apply(instance);
				});

				// Remove handler
				$.require.promise(handler, false);
			})()
		);

		return this;
	}

})(Foundry);