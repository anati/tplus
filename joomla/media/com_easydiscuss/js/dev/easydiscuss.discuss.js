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

 var discuss;

(function($)
{
discuss = {
	reply: {
		clear: function(){

			// Empty contents
			Foundry( '#dc_reply_content' ).val( '' );

			// Clear off attachments
			discuss.attachments.clear();

			// Clear off references
			discuss.references.clear();
		},
		submit: function(){
			var token	= $( '.easydiscuss-token' ).val();
			action_url  = discuss_site + '&view=post&layout=ajaxSubmitReply&format=ajax&tmpl=component&' + token + '=1';


			form    = document.getElementById( 'dc_submit' );

			var iframe = document.createElement("iframe");
				iframe.setAttribute("id","upload_iframe");
				iframe.setAttribute("name","upload_iframe");
				iframe.setAttribute("width","0");
				iframe.setAttribute("height","0");
				iframe.setAttribute("border","0");
				iframe.setAttribute("style","width: 0; height: 0; border: none;");

			form.parentNode.appendChild(iframe);
			window.frames['upload_iframe'].name="upload_iframe";
			iframeId = document.getElementById("upload_iframe");

			// Add event...
			var eventHandler = function()  {

				if (iframeId.detachEvent)
				{
					iframeId.detachEvent("onload", eventHandler);
				}
				else
				{
					iframeId.removeEventListener("load", eventHandler, false);
				}

				// Message from server...
				if( iframeId.contentDocument )
				{
					content = iframeId.contentDocument;
				}
				else if( iframeId.contentWindow )
				{
					content = iframeId.contentWindow.document;
				}
				else if( iframeId.document )
				{
					content = iframeId.document;
				}

				content = Foundry(content).find('script#ajaxResponse').html();

				var result = Foundry.parseJSON(content);

				switch( result.type )
				{
					case 'success.captcha':
						Recaptcha.reload();
					case 'success':
						discuss.spinner.hide( "reply_loading" );
						Foundry( "#submit-reply" ).removeAttr("disabled");
						Foundry( '#dc_notification .msg_in' ).html( result.message );
						Foundry( "#dc_notification .msg_in" ).addClass( "dc_success" );
						Foundry( 'div#dc_reply ul.discuss-responds' ).append( result.html );

						// If there is a no reply item, clear it from the view.
						$( '#dc_reply .no-replies' ).hide();

						// Reload the lightbox for new contents
						discuss.attachments.initGallery({
							type: 'image',
							helpers : {
								overlay : null
							}
						});

						// Reload the syntax highlighter.
						if( result.script != 'undefined' )
						{
							eval( result.script );
						}

						// Clear the form.
						discuss.reply.clear();
					break;
					case 'error':
						discuss.spinner.hide( "reply_loading" );
						Foundry( "#submit-reply" ).removeAttr("disabled");
						Foundry( '#dc_notification .msg_in' ).html( result.message );
						Foundry( "#dc_notification .msg_in" ).addClass( "dc_error" );
					break;
					case 'error.captcha':
						Recaptcha.reload();
						discuss.spinner.hide( "reply_loading" );
						Foundry( "#submit-reply" ).removeAttr("disabled");
						Foundry( '#dc_notification .msg_in' ).html( result.message );
						Foundry( "#dc_notification .msg_in" ).addClass( "dc_error" );
					break;
				}

				// Del the iframe...
				setTimeout(function()
				{
					Foundry(iframeId).remove();
				}, 250);
			}

			Foundry(iframeId).load(eventHandler);

			// Set properties of form...
			form.setAttribute("target","upload_iframe");
			form.setAttribute("action", action_url);
			form.setAttribute("method","post");
			form.setAttribute("enctype","multipart/form-data");
			form.setAttribute("encoding","multipart/form-data");

			// Submit the form...
			form.submit();

		},
		verify: function(){

			$( '#dc_notification .msg_in' ).html( '' );
			$( '#dc_notification .msg_in' ).removeClass( 'dc_error dc_success dc_alert' );
			discuss.spinner.show( 'reply_loading' );
			disjax.load('post', 'checklogin');
		},
		post: function(){
			$( '#submit-reply' ).attr( 'disabled' , 'disabled' );
			$( '#dc_notification .msg_in' ).html( '' );
			$( '#dc_notification .msg_in' ).removeClass( 'dc_error dc_success dc_alert' );
			discuss.spinner.show( 'reply_loading' );
			if ( discuss.post.validate(true, 'reply') ) {
				disjax.load( 'post' , 'ajaxSubmitReply' , disjax.getFormVal( '#dc_submit' ) );
			} else {
				discuss.spinner.hide( "reply_loading" );
				$( '#submit-reply' ).removeAttr("disabled");
			}
		},
		minimize: function (id) {
			$('#dc_reply_' + id).hide();
			$('#reply_minimize_msg_'+id).show();
		},
		maximize: function (id) {
			$('#dc_reply_' + id).show();
			$('#reply_minimize_msg_'+id).hide();
		},
		addURL: function( element ){

			var data	= $(element).siblings( 'ul.attach-list' ).children( ':first' ).clone();
			var remove  = $( '#remove-url' ).clone();
			remove.css( 'display' , 'block' );

			// Clear up the value of the url.
			Foundry( data ).find( 'input' ).val( '' );

			// Show the remove link for new items.
			Foundry( data ).find( 'a' ).show();

			$( element ).siblings( 'ul.attach-list' ).append( data );
		},
		removeURL: function( element ){
			$(element).parent().remove();
		},
		accept: function( id ){
				disjax.load( 'post' , 'acceptReply' , id );
		},
		reject: function( id ){
				disjax.load( 'post' , 'rejectReply' , id );
		}
	},
	/*
	 * Filter items
	 */
	filter: function( type , categoryId ){

		EasyDiscuss.ajax( 'site.views.index.filter' , { args: [type , categoryId ] } ,
		{
			beforeSend: function(){
				// Show loading
				discuss.spinner.show( 'index-loading' );

				// Hide the main list item
				$( '#dc_list' ).hide();

				// Hide all paginations during filter
				$( '#dc_pagination' ).hide();

				// Remove all active classes from the child filters.
				$( '#filter-links' ).children().removeClass( 'active' );
			},
			success: function( showFeaturedList , content , sorting , type , nextLimit , paginationContent ){

				// Show only if necessary
				if( showFeaturedList )
				{
					$( '#dc_featured_list' ).show();
				}

				// Assign the new content
				$( '#dc_list' ).html( content );

				// Update the sorting content
				$( '#sort-wrapper' ).html( sorting );

				// Update the pagination type.
				$( '#pagination-filter' ).val( type );

				// Update the pagination limit
				$( '#pagination-start' ).val( nextLimit );

				// Update pagination content
				$( '#dc_pagination' ).html( paginationContent );
			},
			complete: function(){

				// Hide loading once the process is complete
				discuss.spinner.hide( "index-loading" );

				// Since we hid it earlier, show the list content
				$( '#dc_list' ).show();

				// Show the pagination since we hid it earlier.
				$( '#dc_pagination' ).show();

				// Add active class for the child filter
				$( '#filter-links' ).find( '.' + type ).addClass( 'active' );
			}
		});
	},
	sort: function( type , filter , categoryId ){
		discuss.spinner.show( 'index-loading' );

		if( discuss_featured_style == '2' && filter != 'allposts' )
		{
            $( '#dc_featured_list' ).hide();
		}

		$('#dc_list' ).hide();

		// Hide all paginations during filter
		$( '#dc_pagination' ).hide();

		// Remove all active classes from the child sorts.
		$( '#sort-links' ).children().removeClass( 'active' );

		// Add active class for the current sort type
		$( '#sort-links' ).find( '.' + type ).addClass( 'active' );

		disjax.load( 'index' , 'sort' , type , filter , categoryId );
	},
	references:{
		clear: function(){
			Foundry( '#dc_user_reply' ).find( '.attach-list' ).children().each(function(){
				var remove  = Foundry( this ).children( 'a' );

				if( Foundry( remove ).css( 'display' ) !== 'none' )
				{
					Foundry( remove ).click();
				}
			});

			// Clear off the website's value
			Foundry( '#dc_user_reply' ).find( '.attach-list :first' ).find( 'input' ).val('');
		}
	},
	attachments:{
		initGallery: function( options ){
			$("a.attachments-image").fancybox( options );
		},
		removeItem: function( element , id ){
			if( id != null )
			{
				disjax.load( 'attachments' , 'delete' , id );
				Foundry( element ).parent().remove();
			}
			else
			{
				Foundry( element ).parent().remove();
			}
		},
		getExtension: function( name ){
			var extension = name.substr( (name.lastIndexOf('.') +1) );

			switch(extension)
			{
				case 'jpg':
				case 'png':
				case 'gif':
					extension   = 'image';
				break;
				case 'zip':
				case 'rar':
					extension   = 'archive';
				break;
				case 'pdf':
					extension   = 'pdf';
				break;
				default:
					extension   = 'default';
			}

			return extension;
		},
		addQueue: function( element ){

			var element = $(element);

			element.after( '<input type="file" name="filedata[]" id="filedata" size="50" onchange="discuss.attachments.addQueue(this);" />' );

			var queue	= Foundry( element ).parents( '.discuss-attachments-upload' ).find( '.upload-queue' );

			var item = Foundry( '<li>' )
				.addClass( 'attachments-' + discuss.attachments.getExtension( element.val() ) )
				.append( element.val() + ' - <a href="javascript:void(0);" onclick="discuss.attachments.removeItem(this);">Remove</a>' );

			element.appendTo(item).hide();

			queue.append( item );

		},
		clear: function(){
			Foundry( '.upload-queue' ).empty();
		}
	},
	map:{
		render: function( title , latitude , longitude , elementId ){

			var latLng = new google.maps.LatLng( latitude , longitude );

			var map		= new google.maps.Map( document.getElementById( elementId ) ,
				{
					zoom: 12,
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			);

			var marker	= new google.maps.Marker(
				{
					position: latLng,
					center	: latLng,
					title	: title,
					map		: map
				}
			);
		}
	},
	/**
	 * Widgets that can be toggled
	 */
	widget: {
		init: function(){
			Foundry('.widget-toggle').click(function(){
				Foundry(this).parents('.discuss-widget').find('.widget-body').toggle();
				Foundry(this).parents('.discuss-widget').toggleClass('is-hidden');
			});
		}
	},
	/**
	 * Submit posts
	 */
	post:{
		// submit new post
		submit: function() {

			if( $('#category_id' ).val() == '0' )
			{
				disjax.load( 'index', 'getTemplate', 'ajax.selectcategory.php' );
				return false;
			}

			$('#createpost').attr('disabled', 'disabled');
			document.dc_submit.submit();
		},
		qqSubmit: function() {

			if( $('#category_id' ).val() == '0' )
			{
				disjax.load( 'index', 'getTemplate', 'ajax.selectcategory.php' );
				return false;
			}

			$('#createpost').attr('disabled', 'disabled');
			document.mod_edqq.submit();
		},
		// reply to post
		reply: function() {

			if ( discuss.post.validate(true, 'reply') ) {
				finalData	= disjax.getFormVal('#dc_submit');
				disjax.load('Post', 'ajaxSubmitReply', finalData);
			}
			return false;
		},
		// validate all required fields
		validate: function( notitle, submitType ) {

			if ( !notitle ) {
				// if the title is empty
				if ( $('#ez-title').val() == '' || $('#ez-title').val() == langPostTitle)
				{
					// do something here
					if(submitType == 'reply')
					{
						$('#dc_notification .msg_in').html(langEmptyTitle);
						$('#dc_notification .msg_in').addClass('dc_error');
					}
					else
					{
						$('#dc_post_notification .msg_in').html(langEmptyTitle);
						$('#dc_post_notification .msg_in').addClass('dc_error');
					}
					return false;
				}
			}
			return true;
		},
		editPost: function(id){
			// remove the recaptcha from reply form.
			$('#reply_new_antispam').empty();

			//hide all edit link in reply.
			$('.post_edit_link').hide();

			//hide reply submit button
			$('#submit-reply').hide();

			$( '#dc_main_post' ).hide();
			$( '#dc_post' ).append('<div id="dc_main_post_edit" style="display: none;"></div>');

			disjax.load('Post', 'ajaxGetEditForm', id);

		},
		cancelEditPost: function(){
			$('#post_new_antispam').empty();
			$( '#dc_main_post_edit' ).hide();
			$( '#dc_main_post' ).show();

			if($('#dc_main_post').hasClass('locked') == false)
			{
				$('#dc_main_reply_form').slideDown('fast');
			}

			// add recaptcha form into reply form
			if ( $('#reply_new_antispam').length )
			{
				disjax.load('Post', 'ajaxReloadRecaptcha', 'reply_new_antispam');
			}

			//display edit link in replies
			$('.post_edit_link').show();

			//display reply submit button
			$('#submit-reply').show();

		},
		submitEditPost: function(id)
		{
			//disable the button to prevent spamming
			$('#createpost').attr('disabled', 'disabled');
			if ( discuss.post.validate(false, 'newpost') ) {
				document.dc_submit.submit();
			} else {
				$('#createpost').attr('disabled', '');
				return false;
			}
		},
		editReply: function(id)
		{
			// If form is already shown, hide it since it is being clicked again.
			if( $('#post_content_edit_' + id ).css('display') == 'none' )
			{
				//hide all edit link in reply.
				$('.post_edit_link').hide();

				$( '#post_content_edit_' + id + ' .reply-edit-field-tabs' ).children( ':first' ).addClass( 'active' );
				$( '#post_content_edit_' + id + ' .reply-edit-field-forms' ).children( ':first' ).css( 'display' , 'block' );

				// Hide all other fields
				$( '#post_content_edit_' + id + ' .reply-edit-field-forms' ).children( ':first' ).siblings().css( 'display' , 'none' );

				discuss.spinner.hide( 'reply_edit_loading' );
				$( '#reply-notification-'+id+' .msg_in').html('');
				$( '#reply-notification-'+id+' .msg_in').removeClass('dc_alert dc_error dc_success');

				// Hide attachments
				$( '#dc_reply_' + id + ' .dc_attachments' ).hide();

				// Hide references
				$( '#dc_reply_' + id + ' .dc_references' ).hide();

				// Hide avatar
				$( '#avatar-' + id ).hide();

				//hide the reply form
				$('#reply_new_antispam').empty();
				$('#post_content_layout_'+id).show();
				$('#dc_main_reply_form').hide()

				$('#post_content_' + id).hide();
				$('#post_content_edit_' + id).show();

				if ( $('#markItUpReply_content_' + id).length <= 0 )
				{
					$("#reply_content_" + id).markItUp(mySettings);
				}

				if ( $('#reply_edit_antispam_' + id).length )
				{
					disjax.load('Post', 'ajaxReloadRecaptcha', 'reply_edit_antispam_' + id, 'edit-reply-recaptcha' + id);
				}
			}
			else
			{
				 discuss.post.cancel( id );
			}
		},
		save: function(id )
		{
			action_url  = discuss_site  + '&view=post&layout=ajaxSaveContent&format=ajax&tmpl=component';
			action_url	+= '&' + $('.easydiscuss-token').val() + '=1';
			form    = document.getElementById( 'form_edit_' + id );

			var iframe = document.createElement("iframe");
				iframe.setAttribute("id","upload_iframe");
				iframe.setAttribute("name","upload_iframe");
				iframe.setAttribute("width","0");
				iframe.setAttribute("height","0");
				iframe.setAttribute("border","0");
				iframe.setAttribute("style","width: 0; height: 0; border: none;");


			form.parentNode.appendChild(iframe);
			window.frames['upload_iframe'].name="upload_iframe";
			iframeId = document.getElementById("upload_iframe");

			// Add event...
			var eventHandler = function()  {

				if (iframeId.detachEvent)
				{
					iframeId.detachEvent("onload", eventHandler);
				}
				else
				{
					iframeId.removeEventListener("load", eventHandler, false);
				}

				// Message from server...
				if( iframeId.contentDocument )
				{
					content = iframeId.contentDocument;
				}
				else if( iframeId.contentWindow )
				{
					content = iframeId.contentWindow.document;
				}
				else if( iframeId.document )
				{
					content = iframeId.document;
				}

				content = Foundry(content).find('script#ajaxResponse').html();

				var result = Foundry.parseJSON(content);

				switch( result.type )
				{
					case 'success.captcha':
						Recaptcha.reload();
					case 'locked':
						Foundry( '#dc_main_reply_form' ).show();
					case 'success':
						$( '#dc_reply_container_' + result.id ).replaceWith( result.content );

						if( result.script )
						{
							Foundry.parseJSON( result.script );
						}

						// Display edit links again
						Foundry('#dc_reply .post_edit_link').show();

						// Clear the form.
						discuss.reply.clear();
					break;
					case 'error':
						Foundry( '#reply-notification-' + result.id + ' .msg_in' ).html( result.message );
						Foundry( '#reply-notification-' + result.id + ' .msg_in' ).addClass( 'dc_error' );
						discuss.spinner.hide( 'reply_edit_loading' );
					break;
					case 'error.captcha':
						Recaptcha.reload();
						Foundry( '#reply-notification-' + result.id + ' .msg_in' ).html( result.message );
						Foundry( '#reply-notification-' + result.id + ' .msg_in' ).addClass( 'dc_error' );
						discuss.spinner.hide( 'reply_loading' );
					break;
				}

				// Del the iframe...
				setTimeout(function()
				{
					$(iframeId).remove();
				}, 250);
			}

			if (iframeId.addEventListener)
				iframeId.addEventListener("load", eventHandler, true);

			if (iframeId.attachEvent)
				iframeId.attachEvent("onload", eventHandler);

			// Set properties of form...
			form.setAttribute("target","upload_iframe");
			form.setAttribute("action", action_url);
			form.setAttribute("method","post");
			form.setAttribute("enctype","multipart/form-data");
			form.setAttribute("encoding","multipart/form-data");

			// Submit the form...
			form.submit();


		},
		cancel: function(id)
		{
			//show all edit link in reply.
			$('.post_edit_link').show();

			discuss.spinner.hide( 'reply_edit_loading' );
			$( '#reply-notification-'+id+' .msg_in').html('');
			$( '#reply-notification-'+id+' .msg_in').removeClass('dc_alert dc_error dc_success');

			// remove the antispam feature if there is any
			$('#reply_edit_antispam_' + id).empty();

			// Show attachments
			$( '#dc_reply_' + id + ' .dc_attachments' ).show();

			// show avatar
			$( '#avatar-' + id ).show();

			$('#post_content_edit_' + id).hide();
			$('#post_content_' + id).show();

			if($('#dc_main_post div').hasClass('locked') == false)
			{
				$('#dc_main_reply_form').show();
			}

			//get original content
			disjax.load('Post', 'ajaxGetRawContent', id);

			if ( $('#reply_new_antispam').length )
			{
				disjax.load('Post', 'ajaxReloadRecaptcha', 'reply_new_antispam', 'new-reply-recaptcha');
			}

		},
		del: function(id, oType , url ) {
			disjax.load( 'post' , 'deletePostForm' , id , oType , url );
		},
		tag: {
			defaultOptions: {
				tags: [],
				newTags: [],
				tagInput: '.tag-input',
				tagList : '.tag-list',
				tagTemplate: '.tag-list-item'
			},
			init: function(container, options)
			{
				var options = $.extend({}, this.defaultOptions, options);

				var tagInput    = $(container).find(options.tagInput);
				var tagList     = $(container).find(options.tagList);
				var tagTemplate = $(container).find(options.tagTemplate);

				// Generate existing tags
				if (options.tags.length > 0)
				{
					tagTemplate
						.tmpl(options.tags)
						.appendTo(tagList);
				}

				// Show no tag caption if there are no tags
				var noTagAvailable = (function(){

					var picker = $('#tag-list-picker');

					if (picker.length > 0)
					{
						var tagItems = $('#tag-list-picker .stackSuggestItem');

						var noTag = tagItems.length < 1;

						picker.toggle(!noTag);
					}

					var tagItems = tagList.find('.tag_item');

					var noTag = tagItems.length < 1;

					tagList.find('.no-tag').toggle(noTag);

					return arguments.callee;
				})();

				// Enable suggestions on tagInput
				tagInput
					.stretchToFit()
					.stackSuggest({
						dataset: options.newTags,
						filterkey: ['title'],
						position: {
							my: 'left bottom',
							at: 'left top'
						},
						custom: function(keyword)
						{
							return {
								id: $.uid(),
								title: keyword
							};
						},
						add: function(data)
						{
							var existingTag =
								tagList
									.find('input[name="tags[]"]')
									.filter(function()
									{
										return $(this).val()==data.title;
									});

							if (existingTag.length > 0)
							{
								existingTag.parent().hide().prependTo(tagList).fadeIn();
							} else {
								tagTemplate
									.tmpl(data)
									.hide()
									.prependTo(tagList)
									.fadeIn();

								tagInput.stackSuggest('exclude', data['$dataId']);
							}

							noTagAvailable();
						}
					});

				tagList.find('.remove-tag').live('click', function()
				{
					var tag = $(this).parent();

					var picker = $('#tag-list-picker');
					if (picker.length > 0)
					{
						 $('.stackSuggestPickerItem')
							.tmpl(tag.tmplItem().data)
							.appendTo('#tag-list-picker .stackSuggestItemGroup')
							.click(addNewTag);
					} else {
						tagInput.stackSuggest('include', tag.attr('dataid'));
					}

					tag.remove();

					noTagAvailable();
				});


				function addNewTag()
				{
					var data = $(this).tmplItem().data;

					var existingTag =
						tagList
							.find('input[name="tags[]"]')
							.filter(function()
							{
								return $(this).val()==data.title;
							});

					if (existingTag.length > 0)
					{
						existingTag.parent().hide().prependTo(tagList).fadeIn();
					} else {
						tagTemplate
							.tmpl(data)
							.hide()
							.prependTo(tagList)
							.fadeIn();

						$(this).remove();
					}

					noTagAvailable();
				}

				if ($('.stackSuggestPickerItem').length > 0)
				{
					$.each(options.newTags, function(i, tag)
					{
						var skip = false;
						$.each(options.tags, function(i, _tag)
						{
							if (tag.title==_tag.title)
							{
								skip = true;
								return false;
							}
						});

						if (!skip)
						{
							$('.stackSuggestPickerItem')
								.tmpl(tag)
								.appendTo('#tag-list-picker .stackSuggestItemGroup')
								.click(addNewTag);
						}
					});

					noTagAvailable();
				}


			},
			clear: function( noTagMessage ){
				$( '#tag-list-container li' ).remove()
				$( '#tag-list-container ul' ).append( '<li class="no-tag">' + noTagMessage + '</li>' );
			}
		},

		tags: {
			add: function() {
				var tags = $('#new_tags').val();

				if(tags == langTagSepartor || tags == '')
				{
					$('#dc_tag_notification .msg_in').html(langEmptyTag);
					$('#dc_tag_notification .msg_in').addClass('dc_error');
					return false;
				}

				$('#dc_tag_notification .msg_in').html('');
				$('#dc_tag_notification .msg_in').removeClass('dc_error');

				var tagArr = tags.split(',');

				if( tagArr.length > 0 )
				{
					$( tagArr ).each( function( key , value ) {

						value	= $.trim( value );
						idValue	= value.replace( / /g, '-' );

						if( $('#tag_' + idValue ).html() == null )
						{
							if(idValue != '')
							{
								var strItem  = '<li class="tag_item" id="tag_' + idValue + '">';
									strItem += '	<a class="remove_tag" href="javascript:void(0);" onclick="discuss.post.tags.remove(\'' + idValue + '\');"><span>X</span></a>';
									strItem += '	<span class="tag_caption">' + value + '</span>';
									strItem += '	<input type="hidden" name="tags[]" value="' + value + '" />';
									strItem += '</li>';

								$( '#tag_items' ).append( strItem );
							}
						}
					});
				}
				$('#new_tags').val('');

				var addedTags = $(':hidden[name="tags[]"]');
				if(addedTags.length >= 1)
				{
					$('#tag_required_msg').hide();
					$('div.tag_selected').removeClass('alert');
				}


			},

			addexisting: function(value) {
				if( value.length > 0 )
				{
					value	= $.trim(value);
					idValue	= value.replace( / /g, '-' );

					if( $('#tag_' + idValue ).html() == null )
					{
						var strItem  = '<li class="tag_item" id="tag_' + idValue + '">';
							strItem += '	<a class="remove_tag" href="javascript:void(0);" onclick="discuss.post.tags.remove(\'' + idValue + '\');"><span>X</span></a>';
							strItem += '	<span class="tag_caption">' + value + '</span>';
							strItem += '	<input type="hidden" name="tags[]" value="' + value + '" />';
							strItem += '</li>';

						$( '#tag_items' ).append( strItem );
					}
				}
				var addedTags = $(':hidden[name="tags[]"]');
				if(addedTags.length >= 1)
				{
					$('#tag_required_msg').hide();
					$('div.tag_selected').removeClass('alert');
				}
			},

			remove: function(key) {
				$('#tag_' + key ).remove();

				var addedTags = $(':hidden[name="tags[]"]');
				if(addedTags.length <= 0)
				{
					$('#tag_required_msg').show();
				}

			}

		},

		vote: {

			add: function(post_id, value, vtype) {
				disjax.load('Post', 'ajaxAddVote', post_id, value);
			},

			check: function(post_id) {
				// this function is for debug purposes.
				disjax.load('Post', 'ajaxSumVote', post_id);
			}

		},

		lock: function(post_id) {
			disjax.load('Post', 'ajaxLockPost', post_id);
		},

		unlock: function(post_id) {
			disjax.load('Post', 'ajaxUnlockPost', post_id);
		},

		resolve: function(post_id) {
			disjax.load('Post', 'resolve', post_id);
		},

		unresolve: function(post_id) {
			disjax.load('Post', 'unresolve', post_id);
		},

		likes: function(contentId, status, likeId) {
			disjax.load('post', 'ajaxLikes', contentId, status, likeId);
		},

		featured: function(contentId, status ) {
			disjax.load('Post', 'ajaxFeatured', contentId, status );
		},

		toggleTools: function(show, id, showDelete)
		{
			if(show)
			{
				$('.post_delete_link').show();
				$('.likes').show();
				$('.comments').show();
				$('.vote_up').show();
				$('.vote_down').show();
				$('#dc_main_reply_form').show();
			}
			else
			{
				//revert comment form if currently visible
				discuss.comment.cancel();

				if(showDelete == '1')
				{
					$('.post_delete_link').show();
				}
				else
				{
					$('.post_delete_link').hide();
				}
				$('.likes').hide();
				$('.comments').hide();
				$('.vote_up').hide();
				$('.vote_down').hide();
				$('#dc_main_reply_form').hide();
			}
		},

		attachment: {
			remove: function(attachment_id) {
				$('#button-delete-att-'+attachment_id).attr('disabled', 'disabled');
				disjax.load('post', 'deleteAttachment', attachment_id);
			}
		}

	},
	login:{
		verify : function() {
			// if the content is empty
			if ( discuss.post.validate(false, 'reply') ) {
				$('#submit').attr('disabled', 'disabled');
				disjax.load('post', 'checklogin');
			} else {
				return false;
			}
		},
		token: '',
		showpane : function(usertype) {

			$('#usertype_pane_right').children().hide();
			$('#usertype_pane_left').children();

			$('#usertype_member').removeClass('active');
			$('#usertype_guest').removeClass('active');
			$('#usertype_twitter').removeClass('active');

			$('#usertype_status .msg_in').html('');
			$('#usertype_status .msg_in').removeClass('dc_error');


			switch(usertype)
			{
				case 'guest':
					$('#usertype_guest').addClass('active');
					$('#usertype_guest_pane_wrapper').show();
					break;
				case 'twitter':
					$('#usertype_twitter').addClass('active');
					$('#usertype_twitter_pane_wrapper').show();
					break;
				case 'member':
				default:
					$('#usertype_member').addClass('active');
					$('#usertype_member_pane_wrapper').show();
			}
		},
		submit : {
			reply : function(usertype) {
				switch(usertype)
				{
					case 'guest':
						$('#edialog-guest-reply').attr('disabled', 'disabled');
						var email	= $('#discuss_usertype_guest_email').val();
						var name	= $('#discuss_usertype_guest_name').val();
						disjax.load('post', 'ajaxGuestReply', email, name);
						break;
					case 'member':
						$('#edialog-member-reply').attr('disabled', 'disabled');
						var username	= $('#discuss_usertype_member_username').val();
						var password	= $('#discuss_usertype_member_password').val();
						var token		= discuss.login.token;
						disjax.load('post', 'ajaxMemberReply', username, password, token);
						break;
					case 'twitter':
						$('#edialog-twitter-reply').attr('disabled', 'disabled');
						disjax.load('post', 'ajaxTwitterReply');
						break;
					default:
						break;
				}
			}
		},

		getGuestDefaultName : function() {
			var email = $('#discuss_usertype_guest_email').val();
			$('#discuss_usertype_guest_name').val(email.split('@',1));
		},

		twitter : {
			signin : function(status, msg) {
				if(status) {
					disjax.load('post', 'ajaxRefreshTwitter');
				} else {
					alert('failed');
				}
			},

			signout : function(){
				disjax.load('post', 'ajaxSignOutTwitter');
			}
		}
	},
	files:{
		add: function(){
			jQuery( '#file_contents div' ).before( '<input type="file" name="filedata[]" id="filedata" size="50" />' );
		}
	},
	pagination:{
		more: function( type ){

			if( type == 'questions')
				disjax.load( 'index' , 'ajaxReadmore' , $( '#pagination-start' ).val() , $( '#pagination-sorting' ).val() , type , $( '#discuss_parent' ).val(), $( '#pagination-filter' ).val(), $( '#pagination-category' ).val(), $( '#pagination-query' ).val() );
			else
				disjax.load( 'index' , 'ajaxReadmore' , $( '#pagination-start' ).val() , $( '#pagination-sorting' ).val() , type , $( '#discuss_parent' ).val(), $( '#pagination-filter' ).val(), $( '#pagination-category' ).val() );
		},
		addButton: function( type, label ){
			html = '<a href="javascript:void(0);" onclick="discuss.pagination.more( \'' + type + '\' );"><span>' + label + '</span></a>';

			if( Foundry('#dc_pagination a').length < 1)
				Foundry('#dc_pagination').prepend( html );
		}
	},
	comment: {
		save: function () {
			discuss.spinner.show("discussSubmitWait");

			finalData	= disjax.getFormVal('#frmComment');
			disjax.load('Post', 'ajaxSubmitComment', finalData);
		},

		add : function (id) {
			$('#post_content_layout_'+id).show();

			//clear err-msg
			$('#err-msg .msg_in').html('');

			//prepare the comment input form
			var commentForm = $('#discuss-wrapper #comment-wrapper').clone();
			$('#discuss-wrapper #comment-wrapper').remove();

			$('#comment-action-container-' + id).addClass('comment-form-inline').append(commentForm);
			$('#post_id').val(id);

			$('#comment-action-container-' + id).show();
			$('#comment-action-container-' + id + ' #comment-form').show();

			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('dc_alert dc_error dc_success');
		},


		cancel : function () {
			var id  = $('#post_id').val();

			$('#comment-err-msg .msg_in').html('');
			$('#comment-err-msg .msg_in').removeClass('dc_alert dc_error dc_success');
			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('dc_alert dc_error dc_success');

			//revert the comment input form
			$('#discuss-wrapper #comment-action-container-' + id + ' #comment-form').hide();
			var commentForm = $('#discuss-wrapper #comment-action-container-' + id + ' #comment-wrapper').clone();
			$('#discuss-wrapper #comment-action-container-' + id + ' #comment-wrapper').remove();

			$('#discuss-wrapper #comment-separator').after(commentForm);

			$('#post_id').val('');
			$('#comment.inputbox').val('');
			$('#comment-action-container-' + id).hide();

			//toggle toolbar button
			$('#comments-button-' + id).show();
		},

		remove : function (id) {
			var message = langConfirmDeleteComment;
			// var title 	= langConfirmDeleteCommentTitle;

			if (window.confirm(message))
			{
				disjax.load('Post', 'ajaxCommentDelete', id);
			} else {
				return false;
			}
		},

		removeEntry : function (id) {
			effect.highlight('#comment-' + id);

			setTimeout( function() {
				$('#comment-' + id).slideDown('slow', function() {
					$('#comment-' + id).remove();
				});
			}, 1000);
		}
	},

	reports: {
		add : function (id) {
			disjax.load( 'post' , 'reportForm' , id );
		},
		cancel : function () {
			disjax.closedlg();
		},
		submit : function () {
			disjax.load( 'post' , 'ajaxSubmitReport' , disjax.getFormVal( '#frmReport' ) );
		},
		revertForm : function (id) {
			effect.highlight('#post_content_layout_' + id);

			setTimeout( function() {
				discuss.reports.cancel();
			}, 4000);
		}
	},

	/**
	 * Elements
	 */
	element: {

		focus: function(element) {
			ele	= '#' + element;
			$(ele).focus();
		}
	},


	/**
	 * Spinner
	 */
	spinner:{
		// toggle btw the spinner and save button
		show: function( id ){
			var loading		= new Image;
			loading.src		= spinnerPath;
			loading.name	= 'discuss-loading';
			loading.id		= 'discuss-loading';

			$( '#' + id ).html( loading );
			$( '#' + id ).show();
		},
		// toggle btw the spinner and save button
		hide: function(id) {
			$('#' + id).hide();
		}
	},
	system: {
		redirect: function (url) {
			window.location= url;
		},

		refresh: function() {
			window.location.reload();
		}
	},
	subscribe: {
		post: function(post_id) {
			var type		= 'post';
			var email		= $('#subscribe_email').val();
			var name		= $('#subscribe_name').val();
			var interval	= 'instant';
			discuss.spinner.show( 'dialog_loading' );
			disjax.load('post', 'ajaxAddSubscription', type, email, name, interval, post_id+'');
		},
		site: function() {
			var type		= 'site';
			var email		= $('#subscribe_email').val();
			var name		= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			var post_id		= '0';

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, post_id+'');
		},

		tag: function(tag_id) {
			var type		= 'tag';
			var email		= $('#subscribe_email').val();
			var name		= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, tag_id+'');
		},

		category: function(cat_id) {
			var type		= 'category';
			var email		= $('#subscribe_email').val();
			var name		= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show( 'dialog_loading' );

			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, cat_id+'');
		},

		user: function(user_id) {
			var type		= 'user';
			var email		= $('#subscribe_email').val();
			var name		= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, user_id+'');
		}
	},

	unsubscribe: {
		post: function() {
			var type	= 'post';
			var id		= $('#subscribe_id').val();

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxRemoveSubscription', type, id);
		},
		site: function() {
			var type	= 'site';
			var id		= $('#subscribe_id').val();

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxRemoveSubscription', type, id);
		},
		tag: function() {
			var type	= 'tag';
			var id		= $('#subscribe_id').val();

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxRemoveSubscription', type, id);
		},
		category: function() {
			var type	= 'category';
			var id		= $('#subscribe_id').val();

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxRemoveSubscription', type, id);
		},
		user: function() {
			var type	= 'user';
			var id		= $('#subscribe_id').val();

			discuss.spinner.show( 'dialog_loading' );
			disjax.load('index', 'ajaxRemoveSubscription', type, id);
		}
	},
	user:{
		tabs:{
			show: function( element , tabClass , ajax )
			{
			    discuss.spinner.show( 'profile-loading' );

				// Reset all tabs to non active.
				$( '.user-tabs ul li' ).removeClass( 'active' );

				// Set the current item as active
				$( element ).parent().addClass( 'active' );

				// Hide all tab contents first.
				$( '#dc_profile .tab-item' ).hide();

				// Hide all paginations during filter
				$( '#dc_pagination' ).hide();

				var pid	= $( '#profile-id' ).val();

				if( ajax )
				{
					disjax.load( 'profile' , 'filter' , tabClass , pid );
				}
				else
				{
					$( '#dc_profile .' + tabClass ).show();
				}
			}
		},
		checkAlias: function() {
			var	alias		= $('#profile-alias').val()

			if ( alias != '' )
			{
				disjax.load( 'profile', 'ajaxCheckAlias', alias )
			}
		},
		cropPhoto: function(){
			$( '#crop-photo' ).submit();
		}
	},
	tooltips:{
		init: function(){},
		execute: function(id, type){}
	},
	notifications: {
		interval: 3000,

		monitor: null,

		count: null,

		// Initializes the notification checks
		startMonitor: function()
		{
			var self = discuss.notifications;

			self.monitor = setTimeout(self.update, self.interval);
		},

		stopMonitor: function()
		{
			clearTimeout(discuss.notifications.monitor);
		},

		update: function()
		{
			var self = discuss.notifications;

			self.stopMonitor();

			var params	= {};

			params[ $( '.easydiscuss-token' ).val() ]	= 1;

			EasyDiscuss.ajax('site.views.notifications.count', params ,
			{
				type: 'jsonp',

				success: function(count)
				{
					if ( count == 0 || !count ) return;

					if ( self.count != count )
					{
						// Remove the empty class when there are new items.
						$( '#notification-count' ).removeClass( 'empty-notification' );

						$( '#notification-count' ).html( count );

						if( $( '.to_notification' ).css( 'display' ) == 'none' )
						{
							$( '.to_notification' ).css( 'display' , 'inline-block' );
						}
					}

					// Update the count
					self.count = count;
				},

				complete: function()
				{
					self.startMonitor();
				}
			});
		},

		load: function(){
			// If it's not hidden, hide it.
			if( $( '.to_notification .toolbar-note' ).css( 'display' ) != 'none' )
			{
				$('.to_notification .toolbar-note' ).hide();

			} else {
				var params	= {};

				params[ $( '.easydiscuss-token' ).val() ]	= 1;
				EasyDiscuss.ajax('site.views.notifications.load', params,
				{
					success: function(html)
					{
						$('#notification-items').html(html);
						$(".to_notification .toolbar-note").css( "display" , "block");
					},

					fail: function(message)
					{
					}
				});
			}
			return false;
		}
	},
	polls: {
		show: function(){
			$( '#discuss-polls' ).toggle();
		},
		insert: function( element ){
			var data	= $(element).siblings( 'ul.polls-list' ).children( ':first' ).clone();
			var remove  = $( '#remove-poll' ).clone();
			var total	= parseInt( $( '.discuss-polls #poll-item-count' ).val() ) + 1;
			var next	= total + 1;

			remove.css( 'display' , 'block' );

			// Clear up the value of the url.
			Foundry( data ).find( 'input' ).val( '' );

			// Show the remove link for new items.
			Foundry( data ).find( 'a' ).show();
			Foundry( data ).find( 'a' ).attr( 'onclick' , 'discuss.polls.remove(this);' );

			$( element ).siblings( 'ul.polls-list' ).append( data );
		},
		remove: function( element , id ){

			if( typeof( id ) == 'string' )
			{
				var current 	= $( '#pollsremove' ).val();

				if( current != '' )
				{
					current 	+= ',';
				}

				$( '#pollsremove' ).val( current + id );
			}

			$(element).parent().remove();
		},
		vote: function( element ){
			var id	= $( element ).val();

			disjax.load( 'polls' , 'vote' , id );
		},
		unvote: function( postId ){
			disjax.load( 'polls' , 'unvote' , postId );
		},
		showVoters: function( pollId ){
			disjax.load( 'polls' , 'getvoters' , pollId );
		}
	},
	tabs: {
		show: function( element , className )
		{
			// Hide all tabs
			$( '.form-tab-item' ).hide();

			// Remove active class
			$( element ).parent().siblings().removeClass( 'active' );

			$(element).parent().addClass( 'active' );

			// Show the responsible tab
			$( '.tab-' + className ).show();


		}
	},
	toolbar: {
		login: function(){
			$( '#dc_toolbar .to_login div.toolbar-note' ).toggle();
		}
	}
}

window.effect = effect = {
	highlight: function(element) {
		setTimeout( function() {
			$(element).animate({ backgroundColor: '#ffff66' }, 300).animate({ backgroundColor: 'transparent' }, 1500);
		}, 500);
	}
}
})(Foundry);
