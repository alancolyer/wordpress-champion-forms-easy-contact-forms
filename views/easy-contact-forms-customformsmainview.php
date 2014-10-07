<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomForms main view html template
 *
 * 	@see EasyContactFormsCustomForms ::getMainView()
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */


EasyContactFormsLayout::getFormHeader('ufo-formpage ufo-mainview ufo-' . strtolower($obj->type));
echo EasyContactFormsUtils::getViewDescriptionLabel(EasyContactFormsT::get('CustomForms'));
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <div class='buttons'>
      <div class='ufo-float-left'>
        <?php EasyContactFormsIHTML::getScroller($obj);?>
      </div>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'title' => EasyContactFormsT::get('Delete'),
            'events' => " onclick='ufo.mdelete($obj->jsconfig)'",
            'iclass' => " class='icon_button_delete' ",
            'bclass' => "ufo-imagebutton",
          )
        );?>
      </div>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'title' => EasyContactFormsT::get('Add'),
            'events' => " onclick='ufo.newObject($obj->jsconfig, this)'",
            'iclass' => " class='icon_button_add' ",
            'bclass' => "ufo-imagebutton",
          )
        );?>
      </div>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'title' => EasyContactFormsT::get('Search'),
            'events' => " onclick='ufo.doFilter($obj->jsconfig, this)'",
            'iclass' => " class='icon_filter' ",
            'bclass' => "ufo-imagebutton",
          )
        );?>
      </div>
      <div style='clear:left'></div>
    </div>
  </div>
  <div>
    <div id='divCustomFormsFilter' class='ufo-filter'>
      <div class='ufofilterbutton'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'label' => EasyContactFormsT::get('Filter'),
            'events' => " onclick='ufo.filter($obj->jsconfig);'",
            'iclass' => " class='icon_filter_pane' ",
            'bclass' => "button internalimage",
          )
        );?>
      </div>
      <div class='ufo-clear-both'></div>
      <div>
        <div>
          <div>
            <label for='<?php echo $obj->sId('id');?>'><?php echo EasyContactFormsT::get('id');?></label>
            <select id='<?php echo $obj->sId('id');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('general');?>
            </select>
            <input type='text' id='id' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('Description');?>'><?php echo EasyContactFormsT::get('Description');?></label>
            <select id='<?php echo $obj->sId('Description');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='Description' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('NotificationSubject');?>'><?php echo EasyContactFormsT::get('NotificationSubjectF');?></label>
            <select id='<?php echo $obj->sId('NotificationSubject');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='NotificationSubject' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('SendFrom');?>'><?php echo EasyContactFormsT::get('SendFrom');?></label>
            <select id='<?php echo $obj->sId('SendFrom');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='SendFrom' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('SendConfirmation');?>'><?php echo EasyContactFormsT::get('SendConfirmationF');?></label>
            <select id='<?php echo $obj->sId('SendConfirmation');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('bool');?>
            </select>
            <input type='checkbox' id='SendConfirmation' value='off' class='ufo-cb checkbox ufo-filtervalue' onchange='this.value=(this.checked)?"on":"off"'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('ConfirmationSubject');?>'><?php echo EasyContactFormsT::get('ConfirmationSubject');?></label>
            <select id='<?php echo $obj->sId('ConfirmationSubject');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='ConfirmationSubject' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
        </div>
        <div>
          <div>
            <label for='<?php echo $obj->sId('ConfirmationText');?>'><?php echo EasyContactFormsT::get('ConfirmationText');?></label>
            <select id='<?php echo $obj->sId('ConfirmationText');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='ConfirmationText' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('Redirect');?>'><?php echo EasyContactFormsT::get('Redirect');?></label>
            <select id='<?php echo $obj->sId('Redirect');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('bool');?>
            </select>
            <input type='checkbox' id='Redirect' value='off' class='ufo-cb checkbox ufo-filtervalue' onchange='this.value=(this.checked)?"on":"off"'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('RedirectURL');?>'><?php echo EasyContactFormsT::get('RedirectURL');?></label>
            <select id='<?php echo $obj->sId('RedirectURL');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='RedirectURL' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('ShortCode');?>'><?php echo EasyContactFormsT::get('ShortCode');?></label>
            <select id='<?php echo $obj->sId('ShortCode');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='ShortCode' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('Template');?>'><?php echo EasyContactFormsT::get('Template');?></label>
            <select id='<?php echo $obj->sId('Template');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('bool');?>
            </select>
            <input type='checkbox' id='Template' value='off' class='ufo-cb checkbox ufo-filtervalue' onchange='this.value=(this.checked)?"on":"off"'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('SubmissionSuccessText');?>'><?php echo EasyContactFormsT::get('SubmissionSuccessTextF');?></label>
            <select id='<?php echo $obj->sId('SubmissionSuccessText');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='SubmissionSuccessText' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
    <div class='viewtable'>
      <table class='vtable'>
        <tr>
          <th style='width:8px'>
            <input type='checkbox' class='ufo-id-link' style='margin:0' onchange='ufo.checkAll(this)'>
          </th>
          <th style='width:30px'>
            <?php EasyContactFormsIHTML::getColumnHeader(array('view' => $obj, 'field' => "id"));?>
          </th>
          <th>
            <?php EasyContactFormsIHTML::getColumnHeader(array('view' => $obj, 'field' => "Description"));?>
          </th>
          <th>
            <?php EasyContactFormsIHTML::getColumnHeader(array('view' => $obj, 'field' => "ShortCode"));?>
          </th>
        </tr>
        <?php EasyContactFormsLayout::getRows(
          $resultset,
          'EasyContactFormsCustomForms',
          $obj,
          'easy-contact-forms-customformsmainviewrow.php',
          'getCustomFormsMainViewRow',
          $viewmap
        );?>
      </table>
    </div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
