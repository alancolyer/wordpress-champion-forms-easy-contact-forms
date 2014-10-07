<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomForms main form html template
 *
 * 	@see EasyContactFormsCustomForms::getMainForm()
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
echo EasyContactFormsUtils::getTypeFormDescription($obj->getId(), 'CustomForms');
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <?php
    EasyContactFormsLayout::getTabHeader(
      array(
        'GeneralSettings',
        'CustomFormFields',
        'Appearance',
        'CustomFormMailingList',
        'CustomFormsEntries',
      ),
    'top', '1')
    ?>
    <input type='hidden' id='switchhandler' value='AppMan.addTabSwitchHandler(function(tab){var id = tab.attr("id"); var names = AppMan.Utils.idSplit(id); var divid=AppMan.Utils.idJoin(names[0],"buttons");$b=jQuery("#"+divid);var tName=names[1];if (tName =="CustomFormFields1"){$b.hide();}else{$b.show();}}, ["GeneralSettings1", "CustomFormFields1", "Appearance1", "CustomFormMailingList1", "CustomFormsEntries1"])' class='ufo-id-link ufo-eval'>
    <div class='ufo-tab-wrapper ufo-tab-top'>
      <div id='GeneralSettings1' class='ufo-tabs ufo-tab1 ufo-active'>
        <div>
          <div class='ufo-float-left ufo-width50'>
            <label>
              <?php echo EasyContactFormsT::get('FormTitle');?>
              <span class='mandatoryast'>*</span>
            </label>
            <input type='text' id='Description' value='<?php echo $obj->get('Description');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
            <input type='hidden' value='var c = {};c.id = "Description";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.required={};c.required.msg=AppMan.resources.ThisFieldIsRequired;c.events.blur.push("required");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
            <div id='Description-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
            <label><?php echo EasyContactFormsT::get('NotificationSubject');?></label>
            <input type='text' id='NotificationSubject' value='<?php echo $obj->get('NotificationSubject');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
            <input type='hidden' value='var c = {};c.id = "NotificationSubject";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
            <div id='NotificationSubject-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
          </div>
          <div class='ufo-float-right ufo-width50'>
            <label><?php echo EasyContactFormsT::get('ShortCode');?></label>
            <input type='text' id='ShortCode' value='<?php echo $obj->get('ShortCode');?>' READONLY class='textinput ufo-text ufo-formvalue' style='width:100%'>
            <input type='hidden' value='var c = {};c.id = "ShortCode";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("300");c.max="300";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
            <div id='ShortCode-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
          </div>
          <div style='clear:left'></div>
        </div>
        <div></div>
        <div>
          <div class='ufo-float-left ufo-width50'>
            <div>
              <fieldset>
                 <legend>
                   <?php echo EasyContactFormsT::get('SubmissionEmailConfirmation');?>
                 </legend>
                 <label for='SendConfirmation'><?php echo EasyContactFormsT::get('SendConfirmation');?></label>
                 <input type='checkbox' id='SendConfirmation' value='<?php echo $obj->SendConfirmation;?>' <?php echo $obj->SendConfirmationChecked;?> class='ufo-cb checkbox ufo-formvalue' onchange='this.value=(this.checked)?"on":"off"'>
                 <label><?php echo EasyContactFormsT::get('SendFrom');?></label>
                 <input type='text' id='SendFrom' value='<?php echo $obj->get('SendFrom');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "SendFrom";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='SendFrom-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                 <label><?php echo EasyContactFormsT::get('SendFromAddress');?></label>
                 <input type='text' id='SendFromAddress' value='<?php echo $obj->get('SendFromAddress');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "SendFromAddress";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='SendFromAddress-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                 <label><?php echo EasyContactFormsT::get('ConfirmationSubject');?></label>
                 <input type='text' id='ConfirmationSubject' value='<?php echo $obj->get('ConfirmationSubject');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "ConfirmationSubject";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='ConfirmationSubject-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                 <label for='ConfirmationText' class='ufo-label-top'><?php echo EasyContactFormsT::get('ConfirmationText');?></label>
                 <textarea id='ConfirmationText' class='textinput ufo-textarea ufo-formvalue' style='width:100%;height:95px'><?php echo $obj->get('ConfirmationText');?></textarea>
              </fieldset>
            </div>
          </div>
          <div class='ufo-float-right ufo-width50'>
            <div>
              <fieldset>
                 <legend>
                   <?php echo EasyContactFormsT::get('SubmissionActions');?>
                 </legend>
                 <label for='Redirect'><?php echo EasyContactFormsT::get('Redirect');?></label>
                 <input type='checkbox' id='Redirect' value='<?php echo $obj->Redirect;?>' <?php echo $obj->RedirectChecked;?> class='ufo-cb checkbox ufo-formvalue' onchange='this.value=(this.checked)?"on":"off"'>
                 <label><?php echo EasyContactFormsT::get('RedirectURL');?></label>
                 <input type='text' id='RedirectURL' value='<?php echo $obj->get('RedirectURL');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <label for='ShowSubmissionSuccess'><?php echo EasyContactFormsT::get('ShowSubmissionSuccess');?></label>
                 <input type='checkbox' id='ShowSubmissionSuccess' value='<?php echo $obj->ShowSubmissionSuccess;?>' <?php echo $obj->ShowSubmissionSuccessChecked;?> class='ufo-cb checkbox ufo-formvalue' onchange='this.value=(this.checked)?"on":"off"'>
                 <label for='SubmissionSuccessText' class='ufo-label-top'><?php echo EasyContactFormsT::get('SubmissionSuccessText');?></label>
                 <textarea id='SubmissionSuccessText' class='textinput ufo-textarea ufo-formvalue' style='width:100%;height:140px'><?php echo $obj->get('SubmissionSuccessText');?></textarea>
              </fieldset>
            </div>
          </div>
          <div style='clear:left'></div>
        </div>
      </div>
      <div id='CustomFormFields1' class='ufo-tabs ufo-tab1 ufo-customform-designer-div'>
        <div style='vertical-align:top;overflow:hidden'>
          <input type='hidden' value='AppMan.initRedirect("CustomFormFields1", {specialfilter:"[{\"property\":\"CustomForms\", \"value\":{\"values\":[<?php echo $obj->get('id');?>]}}]", viewTarget:"CustomFormFieldsDiv", t:"CustomFormFields", m:"viewDetailed"}, [{property:"CustomForms", value:{values:[<?php echo $obj->get('id');?>]}}])' class='ufo-eval'>
          <div id='CustomFormFieldsDiv' class='innerview' style='vertical-align:top;overflow:hidden'></div>
        </div>
      </div>
      <div id='Appearance1' class='ufo-tabs ufo-tab1'>
        <?php EasyContactFormsLayout::getTabHeader(array('Settings', 'StyleSheet'), 'top', '2');?>
        <div class='ufo-tab-wrapper ufo-tab-top'>
          <div id='Settings2' class='ufo-tabs ufo-tab2 ufo-active'>
            <div class='ufo-float-left ufo-width50'>
              <div style='padding:10px'>
                 <div class='ufo-float-left ufo-width50'>
                   <div style='padding:10px'>
                     <label><?php echo EasyContactFormsT::get('FormWidth');?></label>
                     <div style='position:relative;padding-right:50px'>
                       <input type='string' id='Width' value='<?php echo $obj->get('Width');?>' class='textinput textinput ufo-text ufo-formvalue' style='width:100%'>
                       <input type='hidden' value='var c = {};c.id = "Width";c.events = {};c.events.blur = [];c.integer={};c.integer.msg=AppMan.resources.ThisIsAnIntegerField;c.events.blur.push("integer");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                       <div id='Width-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                       <select id='WidthUnit' class='textinput ufo-formvalue ufo-select inputselect' style='right:0;position:absolute;top:0;width:49px'>
                         <?php echo $obj->getListHTML(array( (object) array('id'=>'px', 'Description'=>'px'), (object) array('id'=>'em', 'Description'=>'em'), (object) array('id'=>'%', 'Description'=>'%')), 'WidthUnit', TRUE);?>
                       </select>
                     </div>
                     <div style='clear:left'></div>
                   </div>
                 </div>
                 <div class='ufo-float-right ufo-width50'>
                   <div style='padding:10px'>
                     <label><?php echo EasyContactFormsT::get('LineMarginHeight');?></label>
                     <div style='position:relative;padding-right:50px'>
                       <input type='string' id='LineHeight' value='<?php echo $obj->get('LineHeight');?>' class='textinput textinput ufo-text ufo-formvalue' style='width:100%'>
                       <input type='hidden' value='var c = {};c.id = "LineHeight";c.events = {};c.events.blur = [];c.integer={};c.integer.msg=AppMan.resources.ThisIsAnIntegerField;c.events.blur.push("integer");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                       <div id='LineHeight-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                       <select id='LineHeightUnit' class='textinput ufo-formvalue ufo-select inputselect' style='right:0;position:absolute;top:0;width:49px'>
                         <?php echo $obj->getListHTML(array( (object) array('id'=>'px', 'Description'=>'px'), (object) array('id'=>'em', 'Description'=>'em'), (object) array('id'=>'%', 'Description'=>'%')), 'LineHeightUnit', TRUE);?>
                       </select>
                     </div>
                     <div style='clear:left'></div>
                   </div>
                 </div>
                 <div style='clear:left'></div>
                 <label><?php echo EasyContactFormsT::get('Style');?></label>
                 <select id='Style' class='textinput ufo-formvalue ufo-select inputselect' style='width:100%'>
                   <?php echo $obj->getAvaliableStyles();?>
                 </select>
                 <label><?php echo EasyContactFormsT::get('FormClass');?></label>
                 <input type='text' id='FormClass' value='<?php echo $obj->get('FormClass');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "FormClass";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='FormClass-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                 <label for='FormStyle' class='ufo-label-top'><?php echo EasyContactFormsT::get('FormStyle');?></label>
                 <textarea id='FormStyle' class='textinput ufo-textarea ufo-formvalue' style='width:100%'><?php echo $obj->get('FormStyle');?></textarea>
                 <label><?php echo EasyContactFormsT::get('SuccessMessageClass');?></label>
                 <input type='text' id='SuccessMessageClass' value='<?php echo $obj->get('SuccessMessageClass');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "SuccessMessageClass";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='SuccessMessageClass-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
                 <label><?php echo EasyContactFormsT::get('FailureMessageClass');?></label>
                 <input type='text' id='FailureMessageClass' value='<?php echo $obj->get('FailureMessageClass');?>' class='textinput ufo-text ufo-formvalue' style='width:100%'>
                 <input type='hidden' value='var c = {};c.id = "FailureMessageClass";c.events = {};c.events.blur = [];c.minmax={};c.minmax.msg=AppMan.resources.ValueLengthShouldBeLessThan;c.minmax.args=[];c.minmax.args.push("200");c.max="200";c.events.blur.push("minmax");c.invClass = "ufo-fields-invalid-field";AppMan.addValidation(c);' class='ufo-eval'>
                 <div id='FailureMessageClass-invalid' class='ufo-fields-invalid-value ufo-id-link' style='position:absolute;display:none'></div>
              </div>
            </div>
            <div class='ufo-float-right ufo-width50'>
              <div style='padding:10px'></div>
            </div>
            <div style='clear:left'></div>
          </div>
          <div id='StyleSheet2' class='ufo-tabs ufo-tab2'>
            <textarea id='StyleSheet' class='textnput ufo-style-sheet-text ufo-textarea ufo-formvalue' style='width:100%;height:98%'><?php echo $obj->get('StyleSheet');?></textarea>
          </div>
        </div>
      </div>
      <div id='CustomFormMailingList1' class='ufo-tabs ufo-tab1'>
        <input type='hidden' value='AppMan.initRedirect("CustomFormMailingList1", {viewTarget:"UsersDiv", t:"Users", m:"mtmview", n:"manage", a:"{\"m\":\"mtmview\", \"ca\":[{\"mt\":\"CustomForms_MailingLists\", \"oid\":\"<?php echo $obj->get('id');?>\", \"fld\":\"CustomForms\", \"t\":\"CustomForms\", \"n\":\"Contacts\"}]}"})' class='ufo-eval'>
        <div id='UsersDiv' class='mtmview innerview' style='width:270px;float:right'></div>
        <input type='hidden' value='AppMan.initRedirect("CustomFormMailingList1", {specialfilter:"[{\"property\":\"CustomForms\", \"value\":{\"values\":[<?php echo $obj->get('id');?>]}}]", viewTarget:"CustomForms_MailingListsDiv", t:"CustomForms_MailingLists", m:"mtmview", n:"CustomForms"}, [{property:"CustomForms", value:{values:[<?php echo $obj->get('id');?>]}}])' class='ufo-eval'>
        <div id='CustomForms_MailingListsDiv' class='mtmview innerview' style='margin-right:275px'></div>
      </div>
      <div id='CustomFormsEntries1' class='ufo-tabs ufo-tab1'>
        <input type='hidden' value='AppMan.initRedirect("CustomFormsEntries1", {specialfilter:"[{\"property\":\"CustomForms\", \"value\":{\"values\":[<?php echo $obj->get('id');?>]}}]", viewTarget:"CustomFormsEntriesDiv", t:"CustomFormsEntries", m:"viewDetailed"}, [{property:"CustomForms", value:{values:[<?php echo $obj->get('id');?>]}}])' class='ufo-eval'>
        <div id='CustomFormsEntriesDiv' class='innerview'></div>
      </div>
    </div>
  </div>
  <div>
    <div id='buttons' class='ufo-id-link'>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'id' => "OK",
            'label' => EasyContactFormsT::get('OK'),
            'events' => " onclick='ufo.save($obj->jsconfig)'",
            'iclass' => " class='icon_button_save ufo-id-link' ",
            'bclass' => "button internalimage",
          )
        );?>
        <input type='hidden' value='var c = {};c.id = "OK";AppMan.addSubmit(c);' class='ufo-eval'>
      </div>
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
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'label' => EasyContactFormsT::get('Preview'),
            'events' => " onclick='ufoCf.preview(this, $obj->jsconfig)'",
            'iclass' => " class='icon_preview' ",
            'bclass' => "button internalimage",
          )
        );?>
      </div>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'label' => EasyContactFormsT::get('Copy'),
            'events' => " onclick='ufo.copy($obj->jsconfig)'",
            'iclass' => " class='icon_button_copy' ",
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
    </div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
