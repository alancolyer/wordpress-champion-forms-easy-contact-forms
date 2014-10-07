<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomFormsEntries main form html template
 *
 * 	@see EasyContactFormsCustomFormsEntries::getMainForm()
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */


EasyContactFormsLayout::getFormHeader('ufo-formpage ufo-mainform ufo-' . strtolower($obj->type));
echo EasyContactFormsUtils::getTypeFormDescription($obj->getId(), 'CustomFormsEntries', 'CustomForms', 'Formid:%d');
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <div></div>
    <div>
      <div class='ufo-float-left ufo-width50'>
        <div>
          <label><?php echo EasyContactFormsT::get('Date');?></label>
          <?php EasyContactFormsIHTML::echoDate($obj->get('Date'), EasyContactFormsT::get('DateTimeFormat'), 0);?>
        </div>
      </div>
      <div class='ufo-float-right ufo-width50'>
        <div style='width:100%'>
          <label><?php echo EasyContactFormsT::get('CustomForm');?></label>
          <span>
            <?php echo $obj->get('CustomFormsDescription');?>
          </span>
        </div>
      </div>
      <div style='clear:left'></div>
    </div>
    <div>
      <div>
        <label class='ufo-label-top'><?php echo EasyContactFormsT::get('Content');?></label>
        <div class='ufo-y-overflow'>
          <div style='width:100%'><?php echo $obj->get('Content');?></div>
        </div>
      </div>
    </div>
  </div>
  <div>
    <div class='ufo-float-left'>
      <?php echo EasyContactFormsIHTML::getButton(
        array(
          'label' => EasyContactFormsT::get('OK'),
          'events' => " onclick='ufo.save($obj->jsconfig)'",
          'iclass' => " class='icon_button_save' ",
          'bclass' => "button internalimage",
        )
      );?>
    </div>
    <div class='ufo-float-left'>
      <?php echo EasyContactFormsIHTML::getButton(
        array(
          'label' => EasyContactFormsT::get('Back'),
          'events' => " onclick='ufo.back()'",
          'iclass' => " class='icon_button_back' ",
          'bclass' => "button internalimage",
        )
      );?>
    </div>
    <div style='clear:left'></div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
