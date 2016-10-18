<?php
    /*********************************************************************************
     * 
     ********************************************************************************/

    /**
     * Helper class for Customext customizations.
     */
    class CustomextInstallUtil
    {
        public static function resolveCustomMetadataAndLoad()
        { 
            $shouldSaveZurmoModuleMetadata = false;
            $metadata                      = ZurmoModule::getMetadata();
            //Add Material to Menu if it doesn't exist
            if(!in_array('costbook', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'costbook';                
                $shouldSaveZurmoModuleMetadata = true;
            }            
            if(!in_array('departmentReferences', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'departmentReferences';                
                $shouldSaveZurmoModuleMetadata = true;
            } 
            if(!in_array('agreements', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'agreements';                
                $shouldSaveZurmoModuleMetadata = true;
            } 
            if(!in_array('agreementProducts', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'agreementProducts';                
                $shouldSaveZurmoModuleMetadata = true;
            }
			if(!in_array('opportunityProducts', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'opportunityProducts';                
                $shouldSaveZurmoModuleMetadata = true;
            }
            if(!in_array('approvalProcess', $metadata['global']['tabMenuItemsModuleOrdering']))
			{
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'approvalProcess';                
                $shouldSaveZurmoModuleMetadata = true;
            }
            if(!in_array('jobScheduling', $metadata['global']['tabMenuItemsModuleOrdering']))
			{
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'jobScheduling';                
                $shouldSaveZurmoModuleMetadata = true;
            }
            if(!in_array('categories', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'categories';                
                $shouldSaveZurmoModuleMetadata = true;
            }

	    if(!in_array('routeTracking', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'routeTracking';                
                $shouldSaveZurmoModuleMetadata = true;
            }
	    if(!in_array('routes', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'routes';                
                $shouldSaveZurmoModuleMetadata = true;
            }		
	    if(!in_array('approvalProcess', $metadata['global']['tabMenuItemsModuleOrdering']))
            {
                $metadata['global']['tabMenuItemsModuleOrdering'][] = 'approvalProcess';                
                $shouldSaveZurmoModuleMetadata = true;
            }		

           
            if($shouldSaveZurmoModuleMetadata)
            {
                ZurmoModule::setMetadata($metadata);
                GeneralCache::forgetAll();
            }
            
            Yii::import('application.extensions.zurmoinc.framework.data.*');

            
            $defaultDataMaker = new AgreementsDefaultDataMaker();
            $defaultDataMaker->make();

            $defaultDataMaker = new CostbooksDefaultDataMaker();
            $defaultDataMaker->make();

            $defaultDataMaker = new OpportunitiesDefaultDataMaker();
            $defaultDataMaker->make();

	    $defaultDataMaker = new ApprovalProcessDefaultDataMaker();
            $defaultDataMaker->make();

            $defaultDataMaker = new ConversationsDefaultDataMaker();
            $defaultDataMaker->make();
        }
    }
?>
