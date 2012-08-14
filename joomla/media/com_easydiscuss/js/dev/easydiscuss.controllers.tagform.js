EasyDiscuss.require(
[
	'jquery.suggest',
	'@easydiscuss.controllers.tagform.tagitem',
	'@easydiscuss.controllers.tagform.suggest.contextmenu'
],
function($)
{
	EasyDiscuss.Controllers(
		'TagForm',
		{
			defaults: {
				mode: 'entry',

				tags: [],

				dataset: [],

				'{textField}': '.tagform-textfield',

				'{pickerField}': '.tagform-pickerfield',

				'{tagList}': '.tagform-taglist',

				'{tagListMessage}': '.tagform-taglist-message',

				'{tagItemGroup}': '.tagform-tagitemgroup',

				'{tagItem}': '.tagform-tagitem',

				'{removeTagButton}': '.tagform-tagitem-remove',

				'@tagItem': 'easydiscuss.controllers.tagform.tagitem'
			}
		},
		function(self) { return {
			init: function()
			{
				if (self.options.mode!='picker')
				{
					self.textField().show();
				}

				self.textField()
					.implement(
						'Foundry.Suggest',
						{
							lookup: {
								inside: self.options.dataset,
								within: ['title']
							},
							keyword: {
								separator: ',',
								includeAsSuggestion: true
							},
							contextMenu: {
								display: {
									position: {
										my: 'left bottom',
										at: 'left top'
									}
								},

								onSelectItem: self.addTag,

								"@menu": 'easydiscuss.controllers.tagform.suggest.contextmenu'
							}
						},
						function()
						{
							self.suggest = this;

							// Create a datamap
							// This speed up access to tag data.
							self.datamap = {};

							$.each(self.suggest.dataset(), function(i, data)
							{
								self.datamap[data.id] = data;
							});

							// Add existing tags into the the tag list
							self.tags = {};

							$.map(self.options.tags, function(tag)
							{
								self.addTag(self.datamap[tag.id]);
							});

							// Add a unique class to the context menu
							// so our styling will take effect
							self.suggest.contextMenu.element
								.addClass('tagform-contextmenu');

							self.refreshTagList();

							if (self.options.mode=='picker')
							{
								var tags = self.suggest.search();

								self.renderTag(tags);
							}

							self.suggest.contextMenu.element
								.find('.suggest-contextmenu-closebutton')
								.click(function()
								{
									self.suggest.contextMenu.hide(true);
								});
						}
					);
			},

			getTag: function(data)
			{
				return $(
					// By suggestId
					self.tagItem('.' + data['.suggestId'])[0] ||

					// By matching title
					self.tagItem(function(){ return $(this).find('input[name="tags[]"]').val()==data.title; })[0]
				);
			},

			addTag: function(data)
			{
				var suggestId = data['.suggestId'];

				// Get existing tag item,
				var existingTagItem = self.getTag(data)[0];

				// or create a new tag item.
				var tagItem = $(
					existingTagItem ||
					$.View(self.template('tagItem'), data)
				);

				// Insert tag item into tag list.
				tagItem
					.prependTo(self.tagItemGroup())
					.hide()
					.fadeIn();

				// If tag item is new,
				if (!existingTagItem)
				{
					tagItem.data('suggestId', suggestId);

					// create an entry in our tag map,
					self.tags[suggestId] = {
						data: data,
						item: tagItem
					};

					// also, exclude this data from being displayed in suggest's context menu.
					self.suggest.exclude(suggestId);
				}

				self.refreshTagList();
			},

			removeTag: function(suggestId)
			{
				// Get tag from our tag map
				var tag = self.tags[suggestId];

				// If picker, restore selection
				if (self.options.mode=='picker')
				{
					self.pickerField().find('.pickId-' + tag.data.id).show();
				}

				// Remove the tag item from the tag list
				tag.item.remove();

				// Remove the tag data from the suggest's exclusion list
				self.suggest.include(suggestId);

				// Remove the tag from our tag map
				delete self.tags[suggestId];

				self.refreshTagList();
			},

			removeAllTags: function()
			{
				// Remove all tag items
				self.tagItemGroup().empty();

				// Reset exclusion list
				self.suggest.options.lookup.exclude = [];

				// Reset our tag map
				self.tags = {};

				self.refreshTagList();
			},

			renderTag: function(tags)
			{
				$.each(tags, function(i, tag)
				{
					var tagItem = $('<li>')
						.html(tag.title)
						.addClass('pickId-' + tag.id)
						.data('tag', tag)
						.click(self.pickTag)
						.appendTo(self.pickerField());
				});

				self.refreshTagList();
			},

			pickTag: function()
			{
				var tagItem = $(this);

				tagItem.hide();

				self.addTag(tagItem.data('tag'));
			},

			refreshTagList: function()
			{
				if (self.options.mode=='picker')
				{
					var show = self.pickerField().find('li:visible').length > 0;

					self.element.find('.select-tag')
						.toggle(show);
				}

				self.tagListMessage()
					.html((self.tagItem().length < 1) ? self.options.lang['COM_EASYDISCUSS_POST_NO_TAGS_ASSIGNED_YET'] : '');
			},

			"{removeTagButton} click": function(button, event)
			{
				var tagItem = button.parents(self.options['{tagItem}']),
					suggestId = tagItem.data('suggestId');

				self.removeTag(suggestId);
			}
		}}
	);
});
