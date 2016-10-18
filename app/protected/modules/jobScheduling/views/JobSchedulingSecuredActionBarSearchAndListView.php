<?php

    /**
     * Renders an action bar specifically for the Agreement search and listview.
     * 
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class JobSchedulingSecuredActionBarSearchAndListView extends SecuredActionBarForSearchAndListView
    {
        
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'  => 'CreateMenu',
                                  'iconClass' => 'icon-create'),
                            array('type'  => 'ExportMenu',
                                  'listViewGridId' => 'eval:$this->listViewGridId',
                                  'pageVarName' => 'eval:$this->pageVarName',
                                  'iconClass'   => 'icon-export'),
                            array('type'  => 'JobDeleteButton',
                                  'listViewGridId' => 'eval:$this->listViewGridId',
                                  'pageVarName' => 'eval:$this->pageVarName',
                                  'iconClass'   => 'icon-delete'),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        protected function resolveActionElementInformationDuringRender(& $elementInformation)
        {
            parent::resolveActionElementInformationDuringRender($elementInformation);
            if ($elementInformation['type'] == 'ListViewTypesToggleLink')
            {
                $elementInformation['active'] = $this->activeActionElementType;
            }
        }
    }
?>
