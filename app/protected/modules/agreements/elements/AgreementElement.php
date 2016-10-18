<?php
    /**
     * Display the agreement selection. This is a
     * combination of a type-ahead input text field
     * and a selection button which renders a modal list view
     * to search on agreement.  Also includes a hidden input for the user
     * id.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementElement extends ModelElement
    {
        protected static $moduleId = 'agreements';

        /**
         * Render a hidden input, a text input with an auto-complete
         * event, and a select button. These three items together
         * form the Agreement Editable Element
         * @return The element's content as a string.
         */
        protected function renderControlEditable()
        {
            assert('$this->model->{$this->attribute} instanceof Agreement');
            return parent::renderControlEditable();
        }
        
        //Added to get the Agreements for Job Scheduling
        protected function renderTextField($idInputName)
        {
            if($idInputName == 'JobScheduling_agreement_id')
                $source = Yii::app()->createUrl($this->resolveModuleId() . '/' . $this->getAutoCompleteControllerId()
                                                   . '/AutocompleteAgreement' , $this->getAutoCompleteUrlParams());
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
