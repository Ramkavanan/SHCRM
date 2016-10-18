<?php
    /*********************************************************************************
     * It is developed when clicking Next button action in Route module
     ********************************************************************************/

    class NextButtonActionElement extends SubmitButtonActionElement
    {
        public function getActionType()
        {
            return 'Edit';
        }

        public function __construct($controllerId, $moduleId, $modelId, $params = array())
        {
            if (!isset($params['htmlOptions']))
            {
                $params['htmlOptions'] = array();
            }
            $params['htmlOptions'] = array_merge(array('id'     => 'next' . ZurmoHtml::ID_PREFIX . ZurmoHtml::$count++,
                                                       'name'   => 'next', //bad for validation.. not sure its needed..
                                                       'class'  => 'attachLoading',
                                                       'params' => array('next' => 'next')), $params['htmlOptions']);
            parent::__construct($controllerId, $moduleId, $modelId, $params);
        }

        protected function getDefaultLabel()
        {
            return Zurmo::t('Core', 'Next');
        }
        
        protected function getDefaultRoute()
        {
        }
    }
?>
