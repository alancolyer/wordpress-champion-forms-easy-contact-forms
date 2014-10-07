<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomForms_MailingLists CustomFormsMain view row
 * 	html function
 *
 * 	@see
 * 	EasyContactFormsCustomForms_MailingLists::getCustomFormsMainView()
 * 	@see EasyContactFormsLayout::getRows()
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
 * 	Displays a EasyContactFormsCustomForms_MailingLists CustomFormsMain
 * 	view record
 *
 * @param object $view
 * 	the EasyContactFormsCustomForms_MailingLists CustomFormsMain view
 * 	object
 * @param object $obj
 * 	a db object
 * @param int $i
 * 	record index
 * @param array $map
 * 	request data
 */
function getCustomForms_MailingListsCustomFormsMainViewRow($view, $obj, $i, $map) { ?>
  <tr class='ufohighlight <?php EasyContactFormsIHTML::getTrSwapClassName($i);?>'>
    <td class='firstcolumn'>
      <input type='checkbox' id='<?php echo $view->idJoin('cb', $obj->getId());?>' value='off' class='ufo-deletecb' onchange='this.value=(this.checked)?"on":"off";'>
    </td>
    <td>
      <?php echo $obj->get('id');?>
    </td>
    <td>
      <a onclick='ufo.redirect({m:"show", oid:"<?php echo $obj->get('Contacts');?>", t:"Users"})'>
        <?php echo $obj->get('ContactsDescription');?>
      </a>
    </td>
  </tr>
	<?php
}
