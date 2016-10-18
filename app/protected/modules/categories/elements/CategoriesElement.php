<?php
    class CategoryElement extends ModelElement
    {
        protected static $moduleId = 'categories';

        /**
         * Render a hidden input, a text input with an auto-complete
         * event, and a select button. These three items together
         * form the Account Editable Element
         * @return The element's content as a string.
         */
        protected function renderControlEditable()
        {
            assert('$this->model->{$this->attribute} instanceof Category');
            return parent::renderControlEditable();
        }
    }
?>
