<?php

/**
 * @file
 *
 * 	EasyContactFormsMenu class definition
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */

/**
 * 	Provides an html menu for each defined role
 *
 */
class EasyContactFormsMenu {

	/**
	 * 	getMenu
	 *
	 * 	Main menu function
	 *
	 * @param array $menumap
	 * 	Request data
	 */
	function getMenu($menumap) {

		if (isset($menumap['r'])) {
			return '';
		}
		$role = $menumap['easycontactusr']->role->Description;
		switch ($role) {
			case 'Guest': return EasyContactFormsMenu::getGuestMenu($menumap);
			case 'SuperAdmin': return EasyContactFormsMenu::getSuperAdminMenu($menumap);

			default: return '';
		}

	}

	/**
	 * 	Guest role menu
	 *
	 * @param array $map
	 * 	Request data
	 */
	function getGuestMenu($map) {
	}

	/**
	 * 	SuperAdmin role menu
	 *
	 * @param array $map
	 * 	Request data
	 */
	function getSuperAdminMenu($map) {
			?>

		
    <div class='ufomenuwrapper'>
      <div class='menupanel'>
        <ul class='ufoMenu'>
          <li>
            <a href='javascript:ufo.mcall("t=CustomForms&m=view")'>
              <?php echo EasyContactFormsT::get('CustomForms');?>
            </a>
          </li>
          <li>
            <a href='javascript:ufo.mcall("t=CustomFormsEntries&m=view")'>
              <?php echo EasyContactFormsT::get('CustomFormsEntries');?>
            </a>
          </li>
          <li>
            <a href='javascript:ufo.mcall("t=Users&m=view")'>
              <span>
                 <?php echo EasyContactFormsT::get('Users');?>
              </span>
            </a>
            <ul class='ufoMenui'>
              <li>
                 <a href='javascript:ufo.mcall("t=ContactTypes&m=view")'>
                   <?php echo EasyContactFormsT::get('ContactTypes');?>
                 </a>
              </li>
            </ul>
          </li>
          <li>
            <a href='javascript:ufo.mcall("t=ApplicationSettings&m=show")'>
              <?php echo EasyContactFormsT::get('ApplicationSettings');?>
            </a>
          </li>
        </ul>
      </div>
    </div>

			<?php
	}

}
