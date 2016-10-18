<?php

    /**
     * Agreement Search form to perform search using model object
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsSearchForm extends OwnedSearchForm
    {
        public $sortDescending = true;
        protected static function getRedBeanModelClassName()
        {
            return 'Agreement';
        }

        public function __construct(Agreement $model)
        {
            $this->setKanbanBoard(new KanbanBoard($model, 'Status'));
            parent::__construct($model);
        }
    }
?>
