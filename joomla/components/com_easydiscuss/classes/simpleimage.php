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

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
defined('_JEXEC') or die('Restricted access');

class SimpleImage
{

   var $image;
   var $image_type;

   function load($filename)
   {
      $image_info = getimagesize($filename);
      
      $this->image_type = $image_info[2];
      
	  if( $this->image_type == IMAGETYPE_JPEG )
	  {
         $this->image = imagecreatefromjpeg($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_GIF )
	  {
         $this->image = imagecreatefromgif($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_PNG )
	  {
         $this->image = imagecreatefrompng($filename);
      }
   }

   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null)
   {
   		$contents	= '';

		if( $image_type == IMAGETYPE_JPEG )
		{
			ob_start();
			imagejpeg( $this->image , null , $compression );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_GIF )
		{
			ob_start();
			imagegif( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_PNG )
		{
			ob_start();
			imagepng( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		
		if( !$contents )
		{
			return false;
		}
		jimport( 'joomla.filesystem.file' );
		$status	= JFile::write( $filename , $contents );

		return $status;
   }
   
   function output($image_type=IMAGETYPE_JPEG)
   {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }
   function getWidth()
   {
      return imagesx($this->image);
   }
   
   function getHeight()
   {
      return imagesy($this->image);
   }

   function resizeToHeight($height)
   {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width)
   {
      $ratio = $width / $this->getWidth();
      $height = $this->getHeight() * $ratio;
      $this->resize($width,$height);
   }
   
   function scale($scale)
   {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }

	public function crop( $width , $height , $x , $y )
	{
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0 , 0 , $x , $y , $width, $height, $width , $height );
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
		    $new_image = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy( $new_image , $this->image );
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, $x, $y, $width , $height , $width , $height );
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{

		    $new_image = imagecreatetruecolor( $width , $height );
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, $x, $y, $width, $height, $width , $height );
		}
		$this->image = $new_image;
	}

   function resize($width, $height)
   {
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
		    $new_image = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy( $new_image , $this->image );
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, 0, 0, $width , $height , $this->getWidth() , $this->getHeight() );
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{
		    $new_image = imagecreatetruecolor( $width , $height );
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
		}
		$this->image = $new_image;
	}

	function resizeToFit($maxWidth, $maxHeight)
	{
		$sourceWidth  = $this->getWidth();
		$sourceHeight = $this->getHeight();
		$targetWidth  = $sourceWidth;
		$targetHeight = $sourceHeight;

		if (!empty($maxWidth) && $targetWidth > $maxWidth)
		{
			$ratio = $maxWidth / $sourceWidth;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if (!empty($maxHeight) && $targetHeight > $maxHeight)
		{
			$ratio = $maxHeight / $sourceHeight;

			$targetWidth  = $sourceWidth  * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		$this->resize($targetWidth, $targetHeight);
	}

	// TODO: Ability to expand original image source if dimension is smaller.
	function resizeToFill($width, $height)
	{
   		$oriHeight  = $this->getHeight();
   		$oriWidth   = $this->getWidth();
   		$newHeight	= $height;
   		$newWidth	= $width;
   		
		$newX = 0;
		$newY = 0;
		$oriX = 0;
		$oriY = 0;
		
		// Getting the correct center points of x/y for later resize.
		if( $oriWidth == $oriHeight )
		{
			$oriX = 0;
			$oriY = 0;
		}
		else if( $oriWidth > $oriHeight )
		{
			$oriX			= intval( ( $oriWidth - $oriHeight ) / 2 );
			$oriY 			= 0;
			$oriWidth		= $oriHeight;
		}
		else
		{
			$oriX		= 0;
			$oriY		= intval( ( $oriHeight - $oriWidth ) / 2 );
			$oriHeight	= $oriWidth;
		}
   
		// When uploaded image is smaller in width and height, just use their width and height instead.
		if( ($oriHeight < $newHeight) && ($oriWidth < $newWidth) )
		{
			$newWidth  = $oriWidth;
			$newHeight = $oriHeight;
		}
		
		//rebuilding new image
		$new_image = imagecreatetruecolor($newWidth, $newHeight);
		
		if( $this->image_type == IMAGETYPE_JPEG ) {
		
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $newWidth, $newHeight, $oriWidth, $oriHeight);
			
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
		
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, $newX, $newY, $oriX, $oriY, $newWidth , $newHeight , $oriWidth , $oriHeight );
			
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $newWidth, $newHeight, $transparent);
			imagecopyresampled($new_image , $this->image, $newX, $newY, $oriX, $oriY, $newWidth, $newHeight, $oriWidth, $oriHeight);
		}

		$this->image = $new_image;
	}

	function getExtension()
	{
		$type	= '';

		switch( $this->image_type )
		{
			case IMAGETYPE_JPEG:
				$type	= '.jpg';
				break;
			case IMAGETYPE_GIF:
				$type	= '.gif';
				break;
			case IMAGETYPE_PNG:
				$type	= '.png';
				break;
		}
		return $type;
	}
}