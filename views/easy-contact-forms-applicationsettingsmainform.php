<?php
/**
 * @file
 *
 * 	EasyContactFormsApplicationSettings main form html template
 *
 * 	@see EasyContactFormsApplicationSettings::getMainForm()
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
echo EasyContactFormsUtils::getTypeFormDescription($obj->getId(), 'ApplicationSettings');
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <?php EasyContactFormsLayout::getTabHeader(array('GeneralSettings', 'TinyMCESettings'), 'top');?>
    <div class='ufo-tab-wrapper ufo-tab-top'>
      <div id='GeneralSettings' class='ufo-tabs ufo-tab ufo-active'>
        <div class='ufo-float-left ufo-width50'>
          <label><?php echo EasyContactFormsT::get('AdminPartApplicationWidth');?></label>
          <input type='text' id='ApplicationWidth2' value='<?php echo $obj->get('ApplicationWidth2');?>' class='ufo-text ufo-formvalue' style='width:230px'>
          <label for='ApplicationWidth'><?php echo EasyContactFormsT::get('ApplicationWidth');?></label>
          <input type='text' id='ApplicationWidth' value='<?php echo $obj->get('ApplicationWidth');?>' class='textinput ufo-text ufo-formvalue' style='width:230px'>
          <input type='hidden' value='var c = {};c.id = "ApplicationWidth";c.events = {};c.events.blur = [];c.integer={};c.integer.msg=AppMan.resources.ThisIsAnIntegerField;c.events.blur.push("integer");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
          <div id='ApplicationWidth-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
          <label><?php echo EasyContactFormsT::get('DefaultStyle');?></label>
          <input type='text' id='DefaultStyle' value='<?php echo $obj->get('DefaultStyle');?>' class='textinput ufo-text ufo-formvalue' style='width:230px'>
          <input type='hidden' value='var c = {};c.id = "DefaultStyle";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("50");c.max="50";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
          <div id='DefaultStyle-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
          <label><?php echo EasyContactFormsT::get('AdminPartDefaultStyle');?></label>
          <input type='text' id='DefaultStyle2' value='<?php echo $obj->get('DefaultStyle2');?>' class='ufo-text ufo-formvalue' style='width:230px'>
          <label><?php echo EasyContactFormsT::get('SecretWord');?></label>
          <input type='text' id='SecretWord' value='<?php echo $obj->get('SecretWord');?>' class='textinput ufo-text ufo-formvalue' style='width:230px'>
          <input type='hidden' value='var c = {};c.id = "SecretWord";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("50");c.max="50";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
          <div id='SecretWord-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
        </div>
        <div class='ufo-float-right ufo-width50'>
          <fieldset>
            <legend>
              <?php echo EasyContactFormsT::get('EmailSettings');?>
            </legend>
            <label><?php echo EasyContactFormsT::get('SendFromAddress');?></label>
            <input type='text' id='SendFrom' value='<?php echo $obj->get('SendFrom');?>' class='textinput ufo-text ufo-formvalue' style='width:230px'>
            <input type='hidden' value='var c = {};c.id = "SendFrom";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("100");c.max="100";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
            <div id='SendFrom-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
          </fieldset>
          <label for='NotLoggenInText' class='ufo-label-top'><?php echo EasyContactFormsT::get('NotLoggedInText');?></label>
          <textarea id='NotLoggenInText' class='textinput ufo-textarea ufo-formvalue' style='width:100%;height:200px'><?php echo $obj->get('NotLoggenInText');?></textarea>
        </div>
        <div style='clear:left'></div>
      </div>
      <div id='TinyMCESettings' class='ufo-tabs ufo-tab'>
        <label for='UseTinyMCE'><?php echo EasyContactFormsT::get('UseTinyMCE');?></label>
        <input type='checkbox' id='UseTinyMCE' value='<?php echo $obj->UseTinyMCE;?>' <?php echo $obj->UseTinyMCEChecked;?> class='ufo-cb checkbox ufo-formvalue' onchange='this.value=(this.checked)?"on":"off"'>
        <label for='TinyMCEConfig' class='ufo-label-top'><?php echo EasyContactFormsT::get('TinyMCEConfig');?></label>
        <textarea id='TinyMCEConfig' class='textinput ufo-textarea ufo-formvalue' style='width:100%;height:330px'><?php echo $obj->get('TinyMCEConfig');?></textarea>
      </div>
    </div>
  </div>
  <div>
    <div class='ufo-float-left'>
      <?php echo EasyContactFormsIHTML::getButton(
        array(
          'id' => "Apply",
          'label' => EasyContactFormsT::get('Apply'),
          'events' => " onclick='ufo.apply($obj->jsconfig)'",
          'iclass' => " class='icon_button_apply ufo-id-link' ",
          'bclass' => "button internalimage",
        )
      );?>
      <input type='hidden' value='var c = {};c.id = "Apply";AppMan.addSubmit(c);' class='ufo-eval'>
    </div>
    <div style='clear:left'></div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
