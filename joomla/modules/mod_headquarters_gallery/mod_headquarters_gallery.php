<?php
/**
* Headquarters Image Gallery
* Author: Agape Red
*/

// Prevent direct access
defined('_JEXEC') or die('Restricted access');

// Get a list of the images to display
$image_dir = $params->get('image_dir');
$urls = array(
    'normal' => JURI::BASE()."/{$image_dir}/normal",
    'thumbs' => JURI::BASE()."/{$image_dir}/thumbs",
    );
$dh = opendir(JPATH_BASE.$image_dir."/normal");
if( ! $dh )
{
    return;
}
$thumb_attribs = array('width' => "70", 'height' => "70");
$normal_attribs = array('width' => "300", 'height' => "300");
$columns = 3;
$images = array();
while( false !== ( $image = readdir($dh) ) )
{
    if( $image !== "." and $image !== ".." )
    {
        $images[] = $image;
    }
}
sort($images);
?>
<style type="text/css">
#headquarters-image-gallery,
#headquarters-image-gallery > div
{ 
}
#headquarters-image-gallery .thumbs, #headquarters-image-gallery .viewspace
{
    float: left;
}
#headquarters-image-gallery .thumbs .thumb-wrapper
{
    padding: 0px 6px 6px 0px;
    line-height: 0px !important;
}
#headquarters-image-gallery .view-image-wrapper
{
    background-color:#333;
    line-height: 0px !important;
}
#headquarters-image-gallery img
{
    padding: 0px !important;
}
#headquarters-image-gallery img, #headquarters-image-gallery td, #headquarters-image-gallery tr, #headquarters-image-gallery a
{
    line-height: 0px !important;
}
</style>
<script type="text/javascript">
    /*
     * Display a headquarter image based on the clicked thumbnail image.
     */
    function viewHeadquartersGalleryImage( img )
    {
        var viewSpace = jQuery('#headquarters-image-gallery').find('.viewspace');
        var imgUrl = '<?php echo $urls['normal']; ?>/'+img;
        if( imgUrl == jQuery(viewSpace).find('img').attr('src') )
        {
            return;
        }
        var newImageWrapper = jQuery('<div class="view-image-wrapper"></div>');
        var newImage = jQuery('<img src="'+imgUrl+'" width="<?php echo $normal_attribs['width']; ?>" height="<?php echo $normal_attribs['height']; ?>" style="display: none;">');
        jQuery(newImageWrapper).append(newImage);
        jQuery(viewSpace).html('').append(newImageWrapper);
        jQuery(newImage).fadeIn(450);
    }
    /*
     * Pre-load the images so they're ready to go when clicked.
     */
    function preloadHeadquarterImages(list)
    {
        var hqGallery = jQuery('#headquarters-image-gallery');
        for( var i = 0; i < list.length; ++ i )
        {
            hqGallery.append(jQuery('<img src="<?php echo $urls['normal']; ?>/'+list[i]+'" style="display: none;">'));
        }
    }
    /*
     * Prepare the gallery layout and default to the first image.
     */
    jQuery(document).ready(function() {
    var images = [ <?php echo "'".join("','", $images)."'"; ?> ];
    var hqGallery = jQuery('#headquarters-image-gallery');
    // Load if the hqGallery div exists, and if it has not been displayed yet.
    if( hqGallery && jQuery(hqGallery).find('.thumbs').length <= 0 )
    {
        jQuery(hqGallery).css('height', '<?php echo $normal_attribs['height']; ?>px').hide();
        var thumbs = jQuery('<div class="thumbs"></div>');
        var thumbsLayout = "<table>";
        for( var i = 0; i < images.length; ++ i )
        {
            var nextLine = (i % <?php echo $columns; ?> == 0);
            if( nextLine )
            {
                if( i > 0 )
                {
                    thumbsLayout += '</tr>';
                }
                thumbsLayout += '<tr>';
            }
            thumbsLayout += '<td>'+ '<div class="thumb-wrapper"><a href="javascript:void(0);" onclick="viewHeadquartersGalleryImage(\''+images[i]+'\');"><img src="<?php echo $urls['thumbs']; ?>/' + images[i] + '" width="<?php echo $thumb_attribs['width']; ?>" height="<?php echo $thumb_attribs['height']; ?>" /></a></div></td>';
        }
        thumbsLayout += "</tr></table>";
        jQuery(thumbs).append(jQuery(thumbsLayout));
        var viewspace = jQuery('<div class="viewspace"></div>');
        hqGallery.append(thumbs);
        hqGallery.append(viewspace);
        hqGallery.append('<div style="clear: left;"></div>');
        <?php if( isset($images[0]) ) { ?>
            viewHeadquartersGalleryImage('<?php echo $images[0]; ?>');
            jQuery(hqGallery).fadeIn(600);
        <?php } ?>
        preloadHeadquarterImages(images);
    }
    });
</script>

<?php
