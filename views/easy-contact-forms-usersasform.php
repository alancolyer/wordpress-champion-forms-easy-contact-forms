<?php
/**
 * @file
 *
 * 	EasyContactFormsUsers AS form html template
 *
 * 	@see EasyContactFormsUsers::getASForm()
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
  <div class='ufo-as-form ufo-users'>
    <div class='ufo-as-list-hidden'>
      <?php echo EasyContactFormsUtils::getTypeFormDescription($obj->getId(), 'Users', 'Name,Description'); ?>
    </div>
    <?php if ( !$obj->isEmpty('ContactTypeDescription') ) : ?>
      <div>
        <label><?php echo EasyContactFormsT::get('ContactType');?></label>
        <?php echo $obj->get('ContactTypeDescription');?>
      </div>
    <?php endif; ?>
    <?php if ( !$obj->isEmpty('Notes') ) : ?>
      <div class='ufo-as-list-hidden'>
        <label class='ufo-label-top'><?php echo EasyContactFormsT::get('Notes');?></label>
        <?php EasyContactFormsIHTML::echoStr($obj->get('Notes'), '', 120);?>
      </div>
    <?php endif; ?>
    <?php if ( !$obj->isEmpty('email') ) : ?>
      <div>
        <label><?php echo EasyContactFormsT::get('email');?></label>
        <?php echo $obj->get('email');?>
      </div>
    <?php endif; ?>
    <?php if ( !$obj->isEmpty('Cell') ) : ?>
      <div>
        <label><?php echo EasyContactFormsT::get('Cell');?></label>
        <?php echo $obj->get('Cell');?>
      </div>
    <?php endif; ?>
    <?php if ( !$obj->isEmpty('Phone1') ) : ?>
      <div>
        <label><?php echo EasyContactFormsT::get('Phone1');?></label>
        <?php echo $obj->get('Phone1');?>
      </div>
    <?php endif; ?>
    <?php if ( !$obj->isEmpty('ContactField3') ) : ?>
      <div class='ufo-as-list-hidden'>
        <label class='ufo-label-top'><?php echo EasyContactFormsT::get('ContactField3');?></label>
        <?php EasyContactFormsIHTML::echoStr($obj->get('ContactField3'), '', 120);?>
      </div>
    <?php endif; ?>
  </div>
