<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    /**
     * Display the user selection. This is a
     * combination of a type-ahead input text field
     * and a selection button which renders a modal list view
     * to search on user.  Also includes a hidden input for the user
     * id.
     */
    class UserElement extends ModelElement
    {
        protected static $moduleId = 'users';

        /**
         * Override because users is a special module.  While a user
         * might not have access to the users tab, they would always be
         * able to pick a user for the 'owner' field.
         */
        protected static $editableActionType = 'UsersModalList';

        /**
         * Render a hidden input, a text input with an auto-complete
         * event, and a select button. These three items together
         * form the User Editable Element
         * @return The element's content as a string.
         */
        protected function renderControlEditable()
        {
            assert('$this->model->{$this->attribute} instanceof User');
            return parent::renderControlEditable();
        }
        
        //Added to get the Estimators only
        protected function renderTextField($idInputName)
        {
            if($idInputName == 'Opportunity_estimator_id' || $idInputName == 'ApprovalProcess_actualapprover_id')
                $source = Yii::app()->createUrl($this->resolveModuleId() . '/' . $this->getAutoCompleteControllerId()
                                                   . '/AutocompleteEstimator' , $this->getAutoCompleteUrlParams());
            else if($idInputName == 'Opportunity_gm_id' || $idInputName == 'ApprovalProcess_actualgmapprover_id')
                $source = Yii::app()->createUrl($this->resolveModuleId() . '/' . $this->getAutoCompleteControllerId()
                                                   . '/AutocompleteGm' , $this->getAutoCompleteUrlParams());
            else
                $source = Yii::app()->createUrl($this->resolveModuleId() . '/' . $this->getAutoCompleteControllerId()
                                                   . '/' . static::$autoCompleteActionId, $this->getAutoCompleteUrlParams());
           
          
            $this->registerScriptForAutoCompleteTextField();
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("ModelElement");
            $cClipWidget->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name'    => $this->getNameForTextField(),
                'id'      => $this->getIdForTextField(),
                'value'   => $this->getName(),
                'source'  => $source,
                'options' => array(
                    'select'   => $this->getOnSelectOptionForAutoComplete($idInputName), // Not Coding Standard
                    'appendTo' => 'js:$("#' . $this->getIdForTextField() . '").parent().parent()',
                    'search'   => 'js: function(event, ui)
                                  {
                                       var context = $("#' . $this->getIdForTextField() . '").parent();
                                       $(".model-select-icon", context).fadeOut(100);
                                       $(this).makeOrRemoveTogglableSpinner(true, context);
                                  }',
                    'open'     => 'js: function(event, ui)
                                  {
                                       var context = $("#' . $this->getIdForTextField() . '").parent();
                                       $(".model-select-icon", context).fadeIn(250);
                                       $(this).makeOrRemoveTogglableSpinner(false, context);
                                  }',
                    'close'    => 'js: function(event, ui)
                                  {
                                       var context = $("#' . $this->getIdForTextField() . '").parent();
                                       $(".model-select-icon", context).fadeIn(250);
                                       $(this).makeOrRemoveTogglableSpinner(false, context);
                                  }',
                    'response' => 'js: function(event, ui)
                                  {
                                       if (ui.content.length < 1)
                                       {
                                           var context = $("#' . $this->getIdForTextField() . '").parent();
                                           $(".model-select-icon", context).fadeIn(250);
                                           $(this).makeOrRemoveTogglableSpinner(false, context);
                                       }
                                  }'
                ),
                'htmlOptions' => array(
                    'disabled' => $this->getDisabledValue(),
                    'onblur' => 'clearIdFromAutoCompleteField($(this).val(), \'' . $idInputName . '\');'
                )
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['ModelElement'];
        }
    }
?>
