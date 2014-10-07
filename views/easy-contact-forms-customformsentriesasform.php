<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomFormsEntries AS form html template
 *
 * 	@see EasyContactFormsCustomFormsEntries::getASForm()
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */

?>
  <div class='ufo-as-form ufo-customformsentries'>
    <div>
      <?php EasyContactFormsIHTML::echoDate($obj->get('Date'), EasyContactFormsT::get('DateTimeFormat'), 0);?>
    </div>
    <div class='ufo-as-list-hidden'>
      <?php EasyContactFormsIHTML::echoStr($obj->get('Content'), '', 3000);?>
    </div>
  </div>
