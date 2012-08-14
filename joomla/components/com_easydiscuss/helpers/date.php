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

jimport('joomla.utilities.date');

class DiscussDateHelper
{
	/*
	 * return the jdate with the correct specified timezone offset
	 * param : date string
	 * return : JDate object
	 */
	public static function dateWithOffSet($str='')
	{
		$userTZ = DiscussDateHelper::getOffSet();

		$date	= new JDate($str);
		$date->setOffset($userTZ);

		return $date;
	}

	public static function getDate($str='')
	{
		return self::dateWithOffSet($str);
	}

	public static function getOffSet()
	{
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userTZ     = '';

		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone');
		}

		//if user did not set timezone, we use joomla one.
		if(empty($userTZ))
		{
			$userTZ	= $mainframe->getCfg('offset');
		}

		return $userTZ;
	}

	public static function getLapsedTime( $time )
	{
		$now	= JFactory::getDate();
		$end	= JFactory::getDate( self::dateWithOffset( $time )->toMySQL() );
		$time	= $now->toUnix() - $end->toUnix();

		$tokens = array (
							31536000 	=> 'COM_EASYDISCUSS_X_YEAR',
							2592000 	=> 'COM_EASYDISCUSS_X_MONTH',
							604800 		=> 'COM_EASYDISCUSS_X_WEEK',
							86400 		=> 'COM_EASYDISCUSS_X_DAY',
							3600 		=> 'COM_EASYDISCUSS_X_HOUR',
							60 			=> 'COM_EASYDISCUSS_X_MINUTE',
							1 			=> 'COM_EASYDISCUSS_X_SECOND'
						);

		foreach( $tokens as $unit => $key )
		{
			if ($time < $unit)
			{
				continue;
			}

			$units	= floor( $time / $unit );

			$string = $units > 1 ?  $key . 'S' : $key;
			$string = $string . '_AGO';

			$text   = JText::sprintf(strtoupper($string), $units);
			return $text;
		}

	}

	public static function enableDateTimePicker()
	{
		$document	= JFactory::getDocument();

		// load language for datetime picker
		$html = '
		<script type="text/javascript">
		/* Date Time Picker */
		var sJan			= "'.JText::_('COM_EASYDISCUSS_JAN').'";
		var sFeb			= "'.JText::_('COM_EASYDISCUSS_FEB').'";
		var sMar			= "'.JText::_('COM_EASYDISCUSS_MAR').'";
		var sApr			= "'.JText::_('COM_EASYDISCUSS_APR').'";
		var sMay			= "'.JText::_('COM_EASYDISCUSS_MAY').'";
		var sJun			= "'.JText::_('COM_EASYDISCUSS_JUN').'";
		var sJul			= "'.JText::_('COM_EASYDISCUSS_JUL').'";
		var sAug			= "'.JText::_('COM_EASYDISCUSS_AUG').'";
		var sSep			= "'.JText::_('COM_EASYDISCUSS_SEP').'";
		var sOct			= "'.JText::_('COM_EASYDISCUSS_OCT').'";
		var sNov			= "'.JText::_('COM_EASYDISCUSS_NOV').'";
		var sDec			= "'.JText::_('COM_EASYDISCUSS_DEC').'";
		var sAm				= "'.JText::_('COM_EASYDISCUSS_AM').'";
		var sPm				= "'.JText::_('COM_EASYDISCUSS_PM').'";
		var btnOK			= "'.JText::_('COM_EASYDISCUSS_OK').'";
		var btnReset		= "'.JText::_('COM_EASYDISCUSS_RESET').'";
		var btnCancel		= "'.JText::_('COM_EASYDISCUSS_CANCEL').'";
		var sNever			= "'.JText::_('COM_EASYDISCUSS_NEVER').'";
		</script>';

		$document->addCustomTag( $html );
	}

	public static function toFormat($jdate, $format='%Y-%m-%d %H:%M:%S')
	{
		if(is_null($jdate))
		{
			$jdate  = new JDate();
		}

		if (!$jdate instanceof JDate)
		{
			$jdate	= JFactory::getDate($jdate);
		}

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			// There is no way to have cross version working, except for detecting % in the format
			if( JString::stristr( $format , '%') === false )
			{
				return $jdate->format( $format , true );
			}

			// Check for Windows to find and replace the %e modifier correctly
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			{
				$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
			}
			return $jdate->toFormat( $format, true );
		}
		else
		{
			// Check for Windows to find and replace the %e modifier correctly
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			{
				$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
			}
		}
		
		return $jdate->toFormat( $format );
	}
}
