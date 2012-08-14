<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_SITE.'/plugins/user/eloquacontactpushusers.php');

$menu = & JSite::getMenu();
if ($menu->getActive() == $menu->getDefault()) {
        $isFrontPage = true;
}

if( eloquaMockSubmit::newUserSubmitted() ) {
    eloquaMockSubmit::finish();
}
require_once(JPATH_SITE.'/modules/mod_tpjoomla_sidemenu/mod_tpjoomla_sidemenu_check.php');

/*
 * Get the current language based on the HTTP_ACCEPT_LANGUAGE header.
 */
function get_language( $default = 'en' )
{
    if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
    {
        $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $language_priority = preg_split('/(;|,)/', $language);
        foreach( $language_priority as $l )
        {
            // Remove the country code
            $l = strtolower(trim(preg_replace('/-.*$/', '', $l)));
            if( strlen($l) == 2 && ! preg_match('/^q=/', $l) )
            {
                return $l;
            }
        }
    }
    return $default;
}
$language = get_language();

// Only show left modules if it's not front page and if there is something to display.
$show_left_modules = false;
if($this->countModules('left') && ! $isFrontPage)
{
    if( active_item_has_relatives() || ($this->countModules('left') > 1) )
    {
        $show_left_modules = true;
    }
}
$template_dir = "{$this->baseurl}/templates/{$this->template}/";
$content_style = ($show_left_modules)? 'style="width: 78%; padding-left: 35px;"' : '';
?>
<!DOCTYPE html>
<html>
	<head>
		<jdoc:include type="head" />
        <script type="text/javascript">
            var _elqQ = _elqQ || [];
            _elqQ.push(['elqSetSiteId', '1839']);
            _elqQ.push(['elqTrackPageView']);
            (function () {
             function async_load() {
             var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
             s.src = '<?php echo $template_dir; ?>scripts/eloqua/elqNow/elqCfg.min.js';
             var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
             }
             if (window.addEventListener) window.addEventListener('DOMContentLoaded', async_load, false);
             else if (window.attachEvent) window.attachEvent('onload', async_load); 
             })();
        </script>
		<script src="/includes/js/common_scripts.js" type="text/javascript"></script>
		<script src="/includes/js/swfobject.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $template_dir; ?>/scripts/jquery.js"></script>
        <!-- FancyBox -->
        <script type="text/javascript" src="<?php echo $template_dir; ?>scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" href="<?php echo $template_dir; ?>scripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $template_dir; ?>css/left_sidemenu.css" type="text/css" />

        <!--[if lte IE 7]>
        <link rel="stylesheet" href="<?php echo $template_dir; ?>css/ie_specific.css" type="text/css" />
        <![endif]-->
        <!-- -->


        <!-- Nivo Slider CSS -->
        <link rel="stylesheet" href="<?php echo $template_dir; ?>scripts/nivo-slider/themes/default/default.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $template_dir; ?>scripts/nivo-slider/nivo-slider.css" type="text/css" media="screen" />
        <!-- -->


		<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>css/corpweb.css">
        <link rel="stylesheet" href="<?php echo $template_dir; ?>css/menu_module.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $template_dir; ?>css/site.css" type="text/css" />

		<link rel="shortcut icon" href="/userfiles/image/favicon.png" type="image/png" />
		<link rel="icon" href="/userfiles/image/favicon.png" type="image/png" />
	</head>

	<body>
		<div id="envelope">

            
			<div id="container">

				<div id="header">
						<div id="searchposition">
                        <img src="<?php echo $template_dir; ?>/images/magnifying_glass.png" class="magnifying_glass" alt="Search" />
                        <?php if( $this->countModules('searchposition') ): ?>
							<jdoc:include type="modules" name="searchposition" style="rounded" />
				        <?php endif; ?>
						</div>
                        <div id="slogan">
                            <a href="/"><img src="/images/talent_plus_slogan.gif" alt="The Science of Talent" /></a>
                        </div>
					    <a href="/"><img src="<?php echo $template_dir; ?>/images/talent_plus/talent_plus_logo.png" alt="Talent Plus" id="logo" /></a>
				</div>

				<div style="clear: both;"></div>

				<div id="topnav">
					<jdoc:include type="modules" name="top" />
					<jdoc:include type="modules" name="user3" />
				</div>

            <?php if($isFrontPage) { ?>
            <div style="clear: both;"></div>
            <div class="slider-wrapper theme-default" style="padding-top:17px; background-color: #222; text-align: center;">
                <div id="bannerslider" class="nivoSlider" style="">
                <?php
                $slider = array( 
                        array('image' => 'business_answers.png', 'url' => '/index.php?option=com_content&view=article&id=47&Itemid=54'),
                        array('image' => 'cultural_dev.png', 'url' => '/index.php?option=com_content&view=article&id=81&Itemid=83'),
                        array('image' => 'front_line.png', 'url' => '/index.php?option=com_content&view=article&id=82&Itemid=84'),
                        array('image' => 'professional.png', 'url' => '/index.php?option=com_content&view=article&id=83&Itemid=85'),
                        array('image' => 'senior_leader.png', 'url' => '/index.php?option=com_content&view=article&id=84&Itemid=86'),
                        array('image' => 'awards.png', 'url' => '/index.php?option=com_content&view=article&id=67'),
                        );
               foreach( $slider as $image => $slide )
               {
                   $image = $slide['image'];
                   $url = $slide['url'];
                   ?>
                   <a href="<?php echo $url; ?>">
                    <img src="<?php echo "{$template_dir}images/header/{$image}"; ?>" alt="" />
                   </a>
                   <?php
                   echo "\n";
               }
               ?>
                </div>
            </div>
            <?php if( $this->countModules('newsflash') ): ?>
                <jdoc:include type="modules" name="newsflash" style="xhtml" />
            <?php endif; ?>
            <?php } ?>


				<div style="clear: both;"></div>

                <?php if($show_left_modules) : ?>
					<div id="leftnav">
						<div id="nav">
							<jdoc:include type="modules" name="left" style="rounded" />
						</div>
					</div>
				<?php endif; ?>
				<div id="content"<?php echo $content_style;?>>

				<jdoc:include type="message" />

					<div id="maincolumn">
                        <?php if ($isFrontPage) { // Front-Page Modules ?>
                        <div id="custombox1" class="custombox">
                        <?php if( $this->countModules('custombox1') ): ?>
                            <jdoc:include type="modules" name="custombox1" style="xhtml" />
                        <?php endif; ?>
                        </div>
                        <div id="custombox2" class="custombox">
                        <?php if( $this->countModules('custombox2') ): ?>
                            <jdoc:include type="modules" name="custombox2" style="xhtml" />
                        <?php endif; ?>
                        </div>
                        <?php if( $this->countModules('home-right') ): ?>
                        <div style="clear: both;"></div>
                        <div id="home-right">
                            <jdoc:include type="modules" name="home-right" style="rounded" />
                        </div>
                        <?php endif; ?>
                        <?php } // Front-Page Modules ?>
                        <div style="clear: both;"></div>

                        <?php if( $this->countModules('precontent') ): ?>
                            <jdoc:include type="modules" name="precontent" style="xhtml" />
                        <?php endif; ?>

						<jdoc:include type="component" />
                        <?php
                            // Include a hidden contact form
                            include('embed/contact_us_iframe.php');
                        ?>
					</div>
				<div style="clear: both;"></div>
				</div><!-- content -->
                <div id="pre-footer" style="clear: both;">
                    <?php if ($isFrontPage) { // Front-Page Modules ?>
                    <div id="customfooter1" class="customfooter" style="">
                    <?php if( $this->countModules('customfooter1') ): ?>
                        <jdoc:include type="modules" name="customfooter1" style="xhtml" />
                    <?php endif; ?>
                    </div>
                    <div id="customfooter3" class="customfooter" style="">
                    <?php if( $this->countModules('customfooter3') ): ?>
                        <jdoc:include type="modules" name="customfooter3" style="xhtml" />
                    <?php endif; ?>
                    </div>
                    <?php } // Front-Page Modules ?>
                    <div id="social-networking-links" style="text-align: center;">
                    <?php
                    $links = array(
                            'rss' => array
                            (
                                'url' => '/index.php?option=com_content&view=category&id=51&format=feed&type=rss',
                                'alt' => 'Talent Plus RSS Feed',
                            ),
                            'linkedin' => array
                            (
                                'url' => 'http://www.linkedin.com/company/32532',
                                'alt' => 'Talent Plus LinkedIn',
                            ),
                            'twitter' => array
                            (
                                'url' => 'http://twitter.com/TalentPlusInc',
                                'alt' => 'Talent Plus Twitter',
                            ),
                            'facebook' => array
                            (
                                'url' => 'http://www.facebook.com/TalentPlusInc',
                                'alt' => 'Talent Plus Facebook',
                            ),
                            'pinterest' => array(
                                'url' => 'http://pinterest.com/TalentPlusInc/',
                                'alt' => 'Talent Plus Pinterest',
                            ),
                            'youtube' => array
                            (
                                'url' => 'http://www.youtube.com/user/talentplusinc?feature=mhee',
                                'alt' => 'Talent Plus YouTube Channel',
                            ),
                            'podcast' => array
                            (
                                'url' => 'index.php?option=com_content&view=category&id=59&Itemid=116',
                                'alt' => 'Talent Plus YouTube Channel',
                            ),
                    );

                    foreach( $links as $image => $params )
                    {
                        $src = "{$template_dir}images/social/{$image}.png";
                        $alt = (isset($params['alt']))? $params['alt'] : '';
                        $url = (isset($params['url']))? $params['url'] : '';
                        ?><a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" /></a>
                        <?php
                    }
                    ?>

                    <div id="google-plusone-icon">
                        <!-- Google +1 button -->
                        <div class="g-plusone" data-width="24" data-annotation="none"></div>
                    </div>
                    </div><!-- social-networking-links -->
                    </div><!-- pre-footer -->
				<div style="clear: both;"></div>
			</div>
		</div>

		<div id="home_envelope_shadow"></div>
		<div id="footer">
			<div id="footercontent">
                <div style="float: left; width: 100%;">
                    <jdoc:include type="modules" name="footer" style="xhtml"/>
                </div>
                <div style="clear: both;width: 100%; clear: both; padding-top: 5px; margin-top: 0px;">
                <div style="width: 90%; margin: 0px auto;"><!-- margin-left div -->
                <div id="footer_form_title">
                    <div class="title">
                        Subscribe to learn more!
                    </div>
                    <div class="summary"></div>
                </div>
				<form class="footer_subscribe_form" name="SubscribeToTalentPlus" id="SubscribeToTalentPlus" onsubmit='return submitForm(this);' action="http://now.eloqua.com/e/f2" method="post">
                    <input type="hidden" name="elqFormName" value="SubscribeToTalentPlus">
                    <input type="hidden" name="elqSiteID" value="1839">

                <table>
                    <tr>
                        <td>
                            <label class="elqLabel" for="C_FirstName">FIRST NAME</label>
                        </td>
                        <td>
                            <label class="elqLabel" for="C_LastName">LAST NAME</label>
                        </td>
                        <td>
                            <label class="elqLabel" for="C_EmailAddress">EMAIL ADDRESS</label>
                        </td>
                        <td>
                            <label class="elqLabel" for=C_Industry1>INDUSTRY</label>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="elqInputWrapper">
							    <INPUT style="WIDTH: 100px" id=C_FirstName class=elqField  name=C_FirstName size=40>
                            </div>
                        </td>
                        <td>
                            <div class="elqInputWrapper">
                                <INPUT style="width: 100px" id=C_LastName class=elqField  name=C_LastName size=40 >
                            </div>
                        </td>
                        <td>
                            <div class="elqInputWrapper">
							    <input style="WIDTH: 150px" id=C_EmailAddress class=elqField  name=C_EmailAddress size=40 >
                            </div>
                        </td>
                        <td>
                            <div class="elqInputWrapper">
                                <SELECT style="VERTICAL-ALIGN: top; color: #333; padding: 1px;" id=C_Industry1 class=elqField name=C_Industry1 > <OPTION selected value="">-- Please Select --</OPTION><OPTION value=Agriculture>Agriculture</OPTION><OPTION value=Automotive>Automotive</OPTION><OPTION value=Biotechnology>Biotechnology</OPTION><OPTION value=Chemicals>Chemicals</OPTION><OPTION value=Communications>Communications</OPTION><OPTION value=Construction>Construction</OPTION><OPTION value=Consulting>Consulting</OPTION><OPTION value=Education>Education</OPTION><OPTION value=Energy>Energy</OPTION><OPTION value=Engineering>Engineering</OPTION><OPTION value=Entertainment>Entertainment</OPTION><OPTION value=Environmental>Environmental</OPTION><OPTION value=Finance>Finance</OPTION><OPTION value="Food & Beverage">Food & Beverage</OPTION><OPTION value=Government>Government</OPTION><OPTION value="Health Care">Health Care</OPTION><OPTION value=Hospitality>Hospitality</OPTION><OPTION value="Human Resources">Human Resources</OPTION><OPTION value=Insurance>Insurance</OPTION><OPTION value="Legal Services">Legal Services</OPTION><OPTION value=Machinery>Machinery</OPTION><OPTION value=Manufacturing>Manufacturing</OPTION><OPTION value=Marketing/Advertising>Marketing/Advertising</OPTION><OPTION value=Media>Media</OPTION><OPTION value=Ministry>Ministry</OPTION><OPTION value="Not For Profit">Not For Profit</OPTION><OPTION value=Other>Other</OPTION><OPTION value="Real Estate">Real Estate</OPTION><OPTION value="Recreation & Fitness">Recreation & Fitness</OPTION><OPTION value=Retail>Retail</OPTION><OPTION value=Shipping>Shipping</OPTION><OPTION value=Technology>Technology</OPTION><OPTION value=Telecommunications>Telecommunications</OPTION><OPTION value=Transportation>Transportation</OPTION><OPTION value=Utilities>Utilities</OPTION></SELECT>
                            </div>
                        </td>
                        <td>
                            <INPUT id=submit class=elqSubmit name=submit value="Subscribe by Email" type=submit style="color: #333;" >
                        </td>
                    </tr>
            </table>
            </form>
            </div><!-- margin-left div -->
            </div>
            <div style="clear: both;"></div>
				<br />
				<i>&copy;<?php echo date('Y'); ?> Talent Plus, Inc.  All rights reserved.</i>
			</div>
		</div>
		<jdoc:include type="modules" name="debug" />

	<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-1347558-8']);
        _gaq.push(['_setDomainName', 'talentplus.com']);
        _gaq.push(['_trackPageview']);
      
        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
	</script>
        <!-- Nivo Slider -->
        <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/scripts/nivo-slider/jquery.nivo.slider.js"></script>
        <!--             -->


        <script type="text/javascript">
        jQuery.noConflict();
        jQuery(window).load(function() {
                if( jQuery('#bannerslider') )
                {
                    jQuery('#bannerslider').nivoSlider({
                        pauseTime: 4800, 
                        animSpeed: 600, 
                        captionOpacity: 0, 
                        directionNav: true,
                        controlNav: true,
                        controlNavThumbs: true,
                        keyboardNav: true,
                        startSlide: 0
                        });
                    jQuery("#video-popout").fancybox();
                    }
                }
                );
        jQuery(document).ready(function(){
             var createPlusOneIcon = function()
             {
                 var po = document.createElement('script');
                 po.type = 'text/javascript';
                 po.async = true;
                 po.src = 'https://apis.google.com/js/plusone.js';
                 var s = document.getElementsByTagName('script')[0];
                 s.parentNode.insertBefore(po, s);
             };
             createPlusOneIcon();
             jQuery('#searchposition img.magnifying_glass').bind('click', function(){
                jQuery('#searchposition').find('form .search input[name=searchword]').focus();
             });
            });
          (function() {
                  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                   po.src = 'https://apis.google.com/js/plusone.js';
                   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
          /* 
           * Show or hide certain elements if they are 
           * attributed to a specific language.
           */
          function toggleByLanguage(language_code, show)
          {
            var list = jQuery('.language-'+language_code);
            for( var i = 0; i < list.length; ++ i )
            {
                if( show )
                {
                    jQuery(list[i]).show();
                }
                else
                {
                    jQuery(list[i]).hide();
                }
            }
          }
          toggleByLanguage('es', <?php echo get_language() === 'es'? 'true':'false'; ?>);
          toggleByLanguage('en', <?php echo get_language() === 'en'? 'true':'false'; ?>);
        </script>
        <script type="text/javascript" src="<?php echo $template_dir; ?>/scripts/expand_list.js"></script>
		<style type="text/css"> .elqFieldValidation { background-color:FC8888; } </style>
		<script TYPE="text/javascript">
		var errorSet = null;

		FieldObj = function() {
		   var Field;
		   this.get_Field = function() { return Field; }
		   this.set_Field = function(val) { Field = val; }

		   var ErrorMessage;
		   this.get_ErrorMessage = function() { return ErrorMessage; }
		   this.set_ErrorMessage = function(val) { ErrorMessage = val; }
		}


		function ResetHighlight() {
		   var field;

		   if (errorSet != null) {
		      for (var i = 0; i < errorSet.length; i++) {
		         errorSet[i].Field.className = 'elqField'
		      }
		    }
		   errorSet = new Array();
		}


		function DisplayErrorSet(ErrorSet) {
		   var element;
		   var ErrorMessage = '';

		   for (var i = 0; i < ErrorSet.length; i++) {
		      ErrorMessage = ErrorMessage + ErrorSet[i].ErrorMessage + '\n';
		      ErrorSet[i].Field.className = 'elqFieldValidation';
		   }

		   if (ErrorMessage != '')
		      alert(ErrorMessage);
		}


		function ValidateRequiredField(Element, args) {
		   var elementVal=Element.value;
		   var testPass=true;
		   if (Element) {
		      if (args.Type == 'text') {
		         if (Element.value == null || Element.value == "") {
		            return false;
		         }
		      }
		      else if (args.Type == 'singlesel') {
		         if (Element.value == null || Element.value == "") {
		            return false;
		         }
		   }
		      else if (args.Type == 'multisel') {
		         var selCount=0;
		         for (var i=0; i<Element.length; i++) {
		              if (Element[i].selected && Element[i].value !='') {
		                 selCount += 1;
		              }
		         }
		      if (selCount == 0)
		         return false;
		   }
		   }
		   else
		      testPass = false;
		return testPass;
		}


		function ValidateEmailAddress(Element) {
		   var varRegExp='^[A-Z0-9!#\\$%&\'\\*\\+\\-/=\\?\\^_`\\{\\|\\}~][A-Z0-9!#\\$%&\'\\*\\+\\-/=\\?\\^_`\\{\\|\\}~\\.]{0,62}@([A-Z0-9](?:[A-Z0-9\\-]{0,61}[A-Z0-9])?(\\.[A-Z0-9](?:[A-Z0-9\\-]{0,61}[A-Z0-9])?)+)$';
		   if ((Element) && (Element.value != '')) {
		      var reg = new RegExp(varRegExp,"i");
		      var match = reg.exec(Element.value);
		         if ((match) && (match.length=3) && (match[1].length<=255) && ((match[2].length>=3) & (match[2].length<=7)))
		            return true;
		   }
		   return false;
		}


		function ValidateDataTypeLength(Element, args, ErrorMessage) {
		   var elementVal = Element.value;
		   var testPass = true;

		   if (Element) {
		      if (args.Type == 'text') {
		         if ((elementVal == '')) {
		            testPass = false;
		         }
		         if ((args.Minimum != '') && (elementVal.length < args.Minimum))
		            testPass = false;
		         if ((args.Maximum != '') && (elementVal.length > args.Maximum))
		            testPass = false;
		      }
		      else if (args.Type == 'numeric') {
		         if ((elementVal == '')) {
		            testPass = false;
		         }
		         if ((elementVal != '') && (elementVal != parseFloat(elementVal)))
		            testPass = false;
		         if (args.Minimum != '') {
		            if ((elementVal == '') || (parseFloat(elementVal) < args.Minimum))
		            testPass = false;
		         }
		         if (args.Maximum != '') {
		            if ((elementVal != '') && (parseFloat(elementVal) > args.Maximum))
		               testPass = false;
		         }
		      }
		   }
		   else
		      testPass = false;
		   return testPass;
		}


		function CheckElqForm(elqForm) {
		var args = null;
		var allValid = true;

		if (elqForm == null) {
		   alert('Unable to execute form validation!\Unable to locate correct form');
		   return false;
		}
		ResetHighlight();


		formField = new FieldObj();
		formField.Field = elqForm.elements['C_EmailAddress'];
		formField.ErrorMessage ='Form field Email Address contains an invalid email address'
		if (formField.Field != null) {
		   if (!ValidateEmailAddress(formField.Field)) {
		      errorSet.push(formField);
		      allValid = false;
		   }
		}


		formField = new FieldObj();
		formField.Field = elqForm.elements['C_FirstName'];
		formField.ErrorMessage ='Form field First Name is required'
		args = {'Type': 'text' };
		if (formField.Field != null) {
		   if (!ValidateRequiredField(formField.Field, args)) {
		      errorSet.push(formField);
		      allValid = false;
		   }
		}


		formField = new FieldObj();
		formField.Field = elqForm.elements['C_LastName'];
		formField.ErrorMessage ='Form field Last Name is required'
		args = {'Type': 'text' };
		if (formField.Field != null) {
		   if (!ValidateRequiredField(formField.Field, args)) {
		      errorSet.push(formField);
		      allValid = false;
		   }
		}

        if( typeof elqForm.elements['country'] !== 'undefined' )
        {
            formField = new FieldObj();
            formField.Field = elqForm.elements['country'];
            formField.ErrorMessage ='Form field Country is required'
                args = {'Type': 'text' };
            if (formField.Field != null) {
                if (!ValidateRequiredField(formField.Field, args)) {
                    errorSet.push(formField);
                    allValid = false;
                }
            }
        }

		if (!allValid) {
		   DisplayErrorSet(errorSet);
		   return false;
		}

		return true;
		}

		function submitForm(elqForm) {
		   if (CheckElqForm(elqForm)) {
		       prepareSelectsForEloqua(elqForm);
		       fnPrepareCheckboxMatricesForEloqua(elqForm);
		       return true;
		   }
		   else { return false; }
		}

		function prepareSelectsForEloqua(elqForm) {
		   var selects = elqForm.getElementsByTagName("SELECT");
		   for (var i = 0; i < selects.length; i++) {
		       if (selects[i].multiple) {
		           createEloquaSelectField(elqForm, selects[i]);
		       }
		   }
		   return true;
		}

		function createEloquaSelectField(elqForm, sel) {
		   var inputName = sel.name;
		   var newInput = document.createElement('INPUT');
		   newInput.style.display = "none";
		   newInput.name = inputName;
		   newInput.value = "";

		   for (var i = 0; i < sel.options.length; i++) {
		       if (sel.options[i].selected) {
		           newInput.value += sel.options[i].value + "::";
		       }
		   }
		   if (newInput.value.length > 0) {
		       newInput.value = newInput.value.substr(0, newInput.value.length - 2);
		   }
		   sel.disabled = true;
		   newInput.id = inputName;
		   elqForm.insertBefore(newInput, elqForm.firstChild);
		}

		function fnPrepareCheckboxMatricesForEloqua(elqForm) {
		   var matrices = elqForm.getElementsByTagName('table');
		   for (var i = 0; i < matrices.length; i++) {
		       var tableClassName = matrices[i].className;
		       if (tableClassName.match(/elqMatrix/)) {
		           if (fnDetermineMatrixType(matrices[i]).toLowerCase() == 'checkbox') {
		               if (matrices[i].rows[0].cells[0].childNodes.length == 1) {
		                   if (matrices[i].rows[0].cells[0].childNodes[0].nodeName != '#text') {
		                       fnCreateHorizontalMatrixCheckboxField(elqForm, matrices[i]);
		                   }
		                   else {
		                       fnCreateVerticalMatrixCheckboxField(elqForm, matrices[i]);
		                   }
		               }
		           }
		       }
		   }
		   return true;
		}

		function fnCreateVerticalMatrixCheckboxField(elqForm, matrix) {
		   var inputName = matrix.id + 'r' + 1;
		   var newInput = document.createElement('INPUT');
		   newInput.style.display = 'none';
		   newInput.name = inputName;
		   newInput.value = '';

		   var inputs = document.getElementsByName(inputName);
		   for (var i=0; i < inputs.length; i++) {
		       if (inputs[i].nodeName.toLowerCase() == 'input') {
		           if (inputs[i].checked == true) {
		               if (inputs[i].type.toLowerCase() == 'checkbox') {
		                   newInput.value += inputs[i].value + '::';
		                   inputs[i].disabled = true;
		               }
		           }
		       }
		   }
		   if (newInput.value.length > 0) {
		       newInput.value = newInput.value.substr(0, newInput.value.length - 2);
		   }

		   newInput.id = inputName;
		   elqForm.insertBefore(newInput, elqForm.firstChild);
		   matrix.disabled = true;
		}

		function fnCreateHorizontalMatrixCheckboxField(elqForm, matrix) {
		   for (var i=1; i < matrix.rows.length; i++) {
		       var inputs = document.getElementsByName(matrix.id + 'r' + i);
		       var oMatrixRow = matrix.rows[i];
		       var inputName = oMatrixRow.id;
		       var newInput = document.createElement('INPUT');
		       newInput.style.display = 'none';
		       newInput.name = inputName;
		       newInput.value = '';

		       for (var j=0; j < inputs.length; j++) {
		           if (inputs[j].nodeName.toLowerCase() == 'input') {
		               if (inputs[j].checked == true) {
		                   if (inputs[i].type.toLowerCase() == 'checkbox') {
		                       newInput.value += inputs[j].value + '::';
		                       inputs[j].disabled = true;
		                   }
		               }
		           }
		       }

		       if (newInput.value.length > 0) {
		           newInput.value = newInput.value.substr(0, newInput.value.length - 2);
		       }

		       newInput.id = inputName;
		       elqForm.insertBefore(newInput, elqForm.firstChild);
		   }
		   matrix.disabled = true;
		}

		function fnDetermineMatrixType(oTable) {
		   var oFirstMatrixInput = oTable.rows[1].cells[1].childNodes[0];
		   return oFirstMatrixInput.type;
		}

		</script>
        <!-- Page-Specific Javascripts -->
        <?php
            $request_uri = (isset($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : "";
            if( preg_match('/^\/?results\/([0-9]+-)?talent-advantage/', $request_uri) )
            {
                $talent_advantage_identifier = isset($_GET['talent_advantage'])? $_GET['talent_advantage'] : "";
                // Load the script for displaying the correct Talent Advantage.
                ?>
                    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/scripts/talent_advantages.js"></script>
                    <script type="text/javascript">
                    jQuery(document).ready(function(){
                        TalentAdvantages.initialize('<?php echo $talent_advantage_identifier; ?>');
                    });
                    </script>
                <?php
            }
        ?>
    <?php 
    if( eloquaMockSubmit::isNewUser() ) {
        // Submit the "Subscribe" form to start tracking the new user.
        echo eloquaMockSubmit::formSubmit("form#SubscribeToTalentPlus");
    }
    ?>
        <!--                           -->
	</body>
</html>
