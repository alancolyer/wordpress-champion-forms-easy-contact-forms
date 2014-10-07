<?php
/**
 * @file
 *
 * 	EasyContactFormsDashBoardView DashBoardView form html template
 *
 * 	@see EasyContactFormsDashBoardView::getDashBoardViewForm()
 */

/*  Copyright championforms.com, 2012-2013 | http://championforms.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 */


EasyContactFormsLayout::getFormHeader('ufo-formpage ufo-dashboardviewform ufo-' . strtolower($obj->type));
echo EasyContactFormsUtils::getViewDescriptionLabel(EasyContactFormsT::get('DashBoardView'));
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <div>
      <div style='width:300px;float:left'>
        <div class='ufo-dashboard-header'>
          <?php echo EasyContactFormsT::get('UserStatistics');?>
        </div>
        <div>
          <?php $obj->getUserStatistics();?>
        </div>
      </div>
      <div style='margin-left:305px'>
        <div class='ufo-float-left'>
          <div class='cf-logo' style='width:180px;height:70px;padding:85px 10px 0'>
            <div>
              <a href='http://championforms.com/champion-forms-getting-started/easy' class='icon_video_tutorial' style='line-height:2.6em;background-position:left center;padding-left:20px;background-repeat:no-repeat'>
                 Getting Started Tutorial
              </a>
            </div>
          </div>
        </div>
        <div class='ufo-float-left'>
          <div style='width:350px'>
            <div>
              <div id='easycontactforms-dashboard-api'>
                 <input type='hidden' value='ufo.api("dashboard", {elid:"easycontactforms-dashboard-api"})' class='ufo-eval'/>
              </div>
            </div>
          </div>
        </div>
        <div style='clear:left'></div>
      </div>
      <div style='clear:both;height:1px'></div>
    </div>
    <div>
      <div class='ufo-dashboard-header'>
        <?php echo EasyContactFormsT::get('EntryStatistics');?>
      </div>
      <div>
        <?php $obj->getEntryStatistics();?>
      </div>
      <div class='ufo-dashboard-header'>
        <?php echo EasyContactFormsT::get('PageStatistics');?>
      </div>
      <div>
        <?php $obj->getFormPageStatistics();?>
      </div>
      <div class='ufo-dashboard-header'>
        <?php echo EasyContactFormsT::get('FormStatistics');?>
      </div>
      <div>
        <?php $obj->getFormStatistics();?>
      </div>
    </div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
