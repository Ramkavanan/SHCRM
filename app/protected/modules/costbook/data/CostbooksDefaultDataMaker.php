<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CostbooksDefaultDataMaker extends DefaultDataMaker {
    public function make() {
        $values = array(
            Zurmo::t('CustomField', 'Labor'),
            Zurmo::t('CustomField', 'Equipment'),
            Zurmo::t('CustomField', 'Material'),    
            Zurmo::t('CustomField', 'Subcontractor'),
            Zurmo::t('CustomField', 'Other'),
            Zurmo::t('CustomField', 'Assembly'),
        );
        static::makeCustomFieldDataByValuesAndDefault('CostOfGoodsTypes', $values);

        $values = array(
            Zurmo::t('CustomField', 'All'),
            Zurmo::t('CustomField', 'Labor'),
            Zurmo::t('CustomField', 'Equipment'),
            Zurmo::t('CustomField', 'Material'),    
            Zurmo::t('CustomField', 'Subcontractor'),
            Zurmo::t('CustomField', 'Other'),
        );
        static::makeCustomFieldDataByValuesAndDefault('CostOfGoodsTypesAssembly', $values,$values[0]);

        $unitofMeasuresDropdownOptions = array();
        foreach(Unitofmeasure::getAll('name') as $uom) {
               $unitofMeasuresDropdownOptions[] = Zurmo::t('CustomField', $uom->name);
        }
        static::makeCustomFieldDataByValuesAndDefault('UnitOfMeasureTypes', $unitofMeasuresDropdownOptions);

        $categoriesDropdownOptions = array();
        foreach(Category::getAll('name') as $categroy) {
               $categoriesDropdownOptions[] = Zurmo::t('CustomField', $categroy->name);
        }
        static::makeCustomFieldDataByValuesAndDefault('CategoryTypes', $categoriesDropdownOptions);

        $assemblyDetailvalues = Costbook::getAllAssemblyDetails();
        $assemblyDetailDropdownOptions = array();
        foreach ($assemblyDetailvalues as $assemblyDetail){
            if($assemblyDetail['assemblydetail']!=NULL){
                $tmpAssemblyoptions = explode(";", $assemblyDetail['assemblydetail']);
                foreach($tmpAssemblyoptions as $assemblyOption){
                    $assemblyDetailDropdownOptions[] = Zurmo::t('CustomField', $assemblyOption);                    
                }
            }
        }
        static::makeCustomFieldDataByValuesAndDefault('AssemblyDetailSearchTypes', $assemblyDetailDropdownOptions);

    }
}

?>
