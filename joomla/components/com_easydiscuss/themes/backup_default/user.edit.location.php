<?php
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

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
EasyDiscuss.require(
[
	'jquery.suggest'
],
function($)
{
	var geocoder = new google.maps.Geocoder();

	var locationField = $('.user-location input#location');

	var suggest,
		retrieveLocationTask;

	var autoDetectLocation = function()
	{
		if( navigator.geolocation )
		{
			navigator.geolocation.getCurrentPosition(
				function(position)
				{
					locationField.addClass('loading');

					var coords = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

					geocoder.geocode({'location': coords}, function(results)
					{
						setLocation(results[0]);

						locationField.removeClass('loading');
					});
				});
		}
	};

	var setLocation = function(location)
	{
		locationField.val( location.formatted_address );
		$('.lat').html( location.geometry.location.lat() );
		$('.long').html( location.geometry.location.lng() );
		$('#latitude').val( location.geometry.location.lat() );
		$('#longitude').val( location.geometry.location.lng() );
		$('.latlng').show();
	};

	$( '#detect-address' ).click(autoDetectLocation);

	locationField
		.implement(
			'Foundry.Suggest',
			{
				keyword: {
					clearAfterSelection: false
				},
				contextMenu: {
					onSelectItem: setLocation
				}
			},
			function()
			{
				suggest = this;

				/* Hack */
				locationField
					// Unbind the original keyup event attached by suggest
					.unbind('keyup')

					// Rebind our own
					.keyup(function(event)
					{
						switch (event.keyCode)
						{
							case $.ui.keyCode.DOWN:
							case $.ui.keyCode.UP:
							case $.ui.keyCode.LEFT:
							case $.ui.keyCode.RIGHT:
							case $.ui.keyCode.ESCAPE:
							case $.ui.keyCode.ENTER:
								break;

							default:
								clearTimeout(retrieveLocationTask);

								suggest.contextMenu.hide();

								retrieveLocationTask = setTimeout(function()
								{
									var keyword = locationField.val();

									geocoder.geocode({'address': keyword}, function(results, status)
									{
										if (results.length < 0) return;

										$.map(results, function(result)
										{
											result.title = result.formatted_address;
										});

										suggest.dataset(results);
										suggest.populate('');
									});
								}, 250);
						}
					});
			}
		);

});
</script>
<div class="tab-item user-location" style="display:none;">
	<div class="form-row">
		<div class="input-label pb-10"><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_LOCATION' ); ?></div>
		<div class="location input-wrap">
			<input type="text" name="location" autocomplete="off" class="input width-350" value="<?php echo $profile->location; ?>" id="location" />
			<input type="button" class="button-submit" id="detect-address" value="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_DETECT_BUTTON');?>" />
		</div>
		<div class="latlng small" style="<?php echo !empty( $profile->location ) ? '' : 'display:none'; ?>">
			<?php echo JText::_( 'Latitude:' );?> <span class="lat"><?php echo $profile->latitude;?></span> ,
			<?php echo JText::_( 'Longitude:' );?> <span class="long"><?php echo $profile->longitude;?></span>
		</div>
		<input type="hidden" id="longitude" name="longitude" value="<?php echo $profile->longitude;?>" />
		<input type="hidden" id="latitude" name="latitude" value="<?php echo $profile->latitude;?>" />
	</div>
</div>
