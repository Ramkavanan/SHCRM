<?php
    /**
     * Display the name and hidden id of the account model.
     * Displays a select button and auto-complete input
     */
    class CostbookNameIdElement extends NameIdElement
    {
        protected static $moduleId = 'costbook';

        protected $idAttributeId = 'costbookId';

        protected $nameAttributeName = 'costbookName';
    }
?>
