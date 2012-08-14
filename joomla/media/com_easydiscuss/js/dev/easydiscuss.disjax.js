/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */


(function($)
{
window.disjax = disjax = {
	http:		false, //HTTP Object
	format: 	'text',
	callback:	function(data){},
	error:		false,
	btnArray: 	new Array(),
	getHTTPObject : function() {
		var http = false;

		//Use IE's ActiveX items to load the file.
		if ( typeof ActiveXObject != 'undefined' ) {
			try {
				http = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					http = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (E) {
					http = false;
				}
			}
		//If ActiveX is not available, use the XMLHttpRequest of Firefox/Mozilla etc. to load the document.
		}
		else if ( XMLHttpRequest ) {
			try {http = new XMLHttpRequest();}
			catch (e) {http = false;}
		}
		this.http	= http;
	},
	/**
	 * Ajax function
	 */
	load : function ( view, method )
	{
		var callback = {};

		if (typeof view == "object")
		{
			callback = view.callback;
			view = view.view;
		}

		// This will be the site we are trying to connect to.
		url	 = discuss_site;
		url	+= '&tmpl=component';
		url += '&no_html=1';
		url += '&format=ajax';

		//Kill the Cache problem in IE.
		url	+= "&uid=" + new Date().getTime();

		var parameters	= '';
		parameters	= '&view=' + view + '&layout=' + method;

		// If there is more than 1 arguments, we want to accept it as parameters.
		if ( arguments.length > 2 )
		{

			// Make header requests
			for ( var i = 2; i < arguments.length; i++ )
			{
				var myArgument	= arguments[ i ];

				if($.isArray(myArgument))
				{
					for(var j = 0; j < myArgument.length; j++)
					{
					    var argument    = myArgument[j];

						if ( typeof( argument ) == 'string' )
						{
							// Regular expression to check if the argument have () or not
							var expr = /^\w+\(*\)$/;
							// check the argument
							var match = expr.exec( argument );

							var arg = argument;

							if ( !match ) {
								arg = escape( arg );
							}

							// Encode value to proper html entities.
							parameters	+= '&value' + ( i - 2 ) + '[]=' + encodeURIComponent( arg );
						}
					}
				}
				else
				{
				    var argument    = myArgument;
					if ( typeof( argument ) == 'string' )
					{
						// Regular expression to check if the argument have () or not
						var expr = /^\w+\(*\)$/;
						// check the argument
						var match = expr.exec( argument );

						var arg = argument;

						if ( !match ) {
							arg = escape( arg );
						}

						// Encode value to proper html entities.
						parameters	+= '&value' + ( i - 2 ) + '=' + encodeURIComponent( arg );
					}
				}
			}
		}

		// Add in tokens
		var token	= $( '.easydiscuss-token' ).val();
		parameters	+= '&' + token + '=1';

		this.getHTTPObject(); //The XMLHttpRequest object is recreated at every call - to defeat Cache problem in IE

		if ( !this.http || !view || !method ) return;


		var ths = this;//Closure

		this.http.open( 'POST' , url , true );

		// Required because we are doing a post
		this.http.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		this.http.setRequestHeader( "Content-length", parameters.length );
		this.http.setRequestHeader( "Connection", "close" );

		this.http.onreadystatechange = function(){
			//Call a function when the state changes.
			if(!ths)
				return;

			var http = ths.http;

			if (http.readyState == 4)
			{
				//Ready State will be 4 when the document is loaded.
				if(http.status == 200)
				{
					var result = "";

					if(http.responseText)
					{
						result = http.responseText;
					}

					// Evaluate the result before processing the JSON text. New lines in JSON string,
					// when evaluated will create errors in IE.
					result	= result.replace(/[\n\r]/g,"");

					//alert(result);

					result	= eval( result );

					//Give the data to the callback function.
					ths.process( result , callback );
				}
				else
				{
					//An error occured
					if(ths.error) ths.error(http.status);
				}
			}
		}

		this.http.send( parameters );
	},

	/**
	 * Get form values
	 *
	 * @param	string	Form ID
	 */
	getFormVal : function( element ) {

	    var inputs  = [];
	    var val		= null;

		$( ':input', $( element ) ).each( function() {
			val = this.value.replace(/"/g, "&quot;");
			val = encodeURIComponent(val);

			if($(this).is(':checkbox') || $(this).is(':radio'))
		    {
				if($(this).attr('checked'))
				    inputs.push( this.name + '=' + escape( val ) );
		    }
		    else
		    {
				inputs.push( this.name + '=' + escape( val ) );
			}
		});
		//var finalData = inputs.join('&&');
		//return finalData;


		//console.log(inputs);
		//return false;
		return inputs;
	},

	process : function ( result , callback ){

		// If the callback is being applied we just push the data to the callback
		if( typeof( callback ) == 'function' )
		{
			return callback.apply( this , result );
		}

		// Process response according to the key
		for(var i=0; i < result.length;i++)
		{
			var action	= result[ i ][ 0 ];

			switch( action )
			{
				case 'script':
					var data	= result[ i ][ 1 ];
					eval( data );
					break;

				case 'after':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];
					$( '#' + id ).after( value );
					break;

				case 'append':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];
					$( '#' + id ).append( value );
					break;

				case 'assign':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).html( value );
					break;

				case 'value':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).val( value );
					break;

				case 'prepend':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];
					$( '#' + id ).prepend( value );
					break;

				case 'destroy':
					var id		= result[ i ][ 1 ];
					$( '#' + id ).remove();
					break;

				case 'dialog':
					disjax.dialog( result[ i ][ 1 ] );
					break;

				case 'alert':
					disjax.alert( result[ i ][ 1 ], result[ i ][ 2 ], result[ i ][ 3 ] );
					break;

				case 'create':
					break;
			}
		}
		delete result;
	},

	/**
	 * Dialog
	 */
	dialog: function( options ) {
		disjax._showPopup( options );
	},
	// Close dialog box
	closedlg: function() {
		var dialog = $('#discuss-dialog');
		var dialogOverlay = $('#discuss-overlay');
		dialogOverlay.hide();
		dialog
			.unbind('.dialog')
			.hide();

		$(document).unbind('keyup', disjax._attachPopupShortcuts);
	},

	/**
	 * Private function
	 *
	 * Generate dialog and popup dialog
	 */
	// _showPopup: function( type, content, callback, title, width, height ) {

	_showPopup: function(options) {

		var defaultOptions = {
			width: '500',
			height: 'auto',
			type: 'dialog'
		}

		var options = $.extend({}, defaultOptions, options);

		var dialogOverlay = $('#discuss-overlay');

		if (dialogOverlay.length < 1)
		{
			dialogOverlay = '<div id="discuss-overlay" class="si_pop_overlay"></div>';

			dialogOverlay = $(dialogOverlay).appendTo('body');

			dialogOverlay.click(function()
			{
				disjax.closedlg();
			});
		}

		dialogOverlay
			.css({
				width: $(document).width(),
				height: $(document).height()
			})
			.show();

		var dialog = $('#discuss-dialog');

		if (dialog.length < 1)
		{
			dialogTemplate = '<div id="discuss-dialog" class="si_pop"><a href="javascript:void(0);" onclick="disjax.closedlg();" class="si_x">Close</a><div class="si_pop_in"></div></div>';

			dialog = $(dialogTemplate).appendTo('body');
		}

		dialog.fadeOut(0);

		var dialogContent = dialog.children('.si_pop_in');

		dialogContent
			.html(options.content);

		dialog
			.css({
				width : (options.width=='auto') ? 'auto' : parseInt(options.width),
				height: (options.height=='auto') ? 'auto' : parseInt(options.height),
				zIndex: 99999
			})
			.show(0, function()
			{
				var positionDialog = function()
				{
					dialog
						.css({ top: 0, left: 0 })
						.position({ my: 'center', at: 'center', of: window });
				}

				var positionDelay;

				$(window).bind('resize.dialog scroll.dialog', function()
				{
					clearTimeout(positionDelay);
					positionDelay = setTimeout(positionDialog, 50);
				});

				positionDialog();
			});

		dialog
			.hide(0, function()
			{
				dialog.fadeIn('fast');
			});

		$('#edialog-cancel, #edialog-submit').live('mouseup', function() {
		 	disjax.closedlg();
		});

		$(document).bind('keyup', disjax._attachPopupShortcuts);

		// <a href="javascript:void(0);" onclick="disjax.closedlg();" class="closeme">Close</a>

		// add hover effect to dialog button
		// $('.dialog :input').hover( function() {
		// 	$(this).parent().parent().addClass('button-hover');
		// }, function() {
		// 	$(this).parent().parent().removeClass('button-hover');
		// });

	},

	_attachPopupShortcuts: function(e)
	{
		if (e.keyCode == 27) { disjax.closedlg(); }
	}
}
})(Foundry);
