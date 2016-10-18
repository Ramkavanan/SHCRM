<?php

    /**
     * Renders an action bar specifically for the Agreement search and listview.
     * 
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsSecuredActionBarForSearchAndListView extends SecuredActionBarForSearchAndListView
    {
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata['global']['secondToolbar']['elements'][] = array('type'  => 'ListViewTypesToggleLink');
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
