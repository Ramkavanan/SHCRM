<?php

    /**
     * Display the account selection. This is a
     * combination of a type-ahead input text field
     * and a selection button which renders a modal list view
     * to search on account.  Also includes a hidden input for the user
     * id.
     */
    class RouteElement extends ModelElement
    {
        protected static $moduleId = 'routes';

        /**
         * Render a hidden input, a text input with an auto-complete
         * event, and a select button. These three items together
         * form the Account Editable Element
         * @return The element's content as a string.
         */
        protected function renderControlEditable()
        {
            assert('$this->model->{$this->attribute} instanceof Route');
            return parent::renderControlEditable();
        }
    }
?>
