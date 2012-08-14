EasyDiscuss.require(
[
	'jquery.suggest',
	'jquery.caret'
],
function($)
{
	EasyDiscuss.Controllers(
		'Mentions',
		{
			defaults: {
				people: [
					{id: 63, title: 'Adam'},
					{id: 64, title: 'Addy'},
					{id: 65, title: 'Addict'},
					{id: 66, title: 'Adda'},
					{id: 67, title: 'Abby'}
				]
			}
		},
		function(self) { return {
			init: function()
			{
				// Create a dummy textbox
				self.textField = $('<input type="text">');

				self.textField
					.implement(
						'Foundry.Suggest',
						{
							lookup: {
								inside: self.options.people,
								within: ['title']
							},
							contextMenu: {
								display: {
									position: {
										my: 'bottom left',
										at: 'top left',
										of: '#markItUpDc_reply_content'
									}
								},

								onSelectItem: self.mention
							},
						},
						function()
						{
							self.suggestion = this;
							console.log(self.suggestion);
						}
					);
			},

			active: false,

			activate: function()
			{
				self.active = true;
				self.startIndex = self.element.caret().start;

				// console.log('activate at ' + self.startIndex);
			},

			deactivate: function()
			{
				console.log('deactivate');
				self.active = false;
				self.startIndex = -1;
				self.endIndex = -1;

				// TODO: Hide suggestion
			},

			listen: function() {
				var text = self.element.val(),
					caret = self.element.caret();

				self.endIndex = text.indexOf(' ', self.startIndex);

				if (self.endIndex < 0) {
					self.endIndex = caret.start;
				}

				if (caret.start > self.endIndex) {
					self.deactivate();
				} else {
					self.suggest(text.slice(self.startIndex, self.endIndex));
				}
			},

			suggest: function(keyword)
			{
				if (keyword) console.log('Looking up using: ' + keyword);

				self.suggestion.populate(keyword);
			},

			mention: function(person)
			{
				console.log('going to mention ' + person.title);

				var text = self.element.val();

				text = $.String.replaceRange(text, self.startIndex, self.endIndex, person.title + ' ');

				self.element.val(text).focus();

				self.deactivate();
			},

			"keydown": function()
			{

			},

			"keyup": function(textField, event)
			{
				if (self.active)
				{
					self.listen();
				}

				if (event.keyCode==50 && !self.active)
				{
					self.activate();
				}
			}

		}}
	);
});
