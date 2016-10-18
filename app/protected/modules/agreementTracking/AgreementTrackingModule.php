<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingModule extends SecurableModule {

    const RIGHT_CREATE_AGREEMENTTRACKING = 'Create AgreementTracking';
    const RIGHT_DELETE_AGREEMENTTRACKING = 'Delete AgreementTracking';
    const RIGHT_ACCESS_AGREEMENTTRACKING = 'Access AgreementTracking Tab';

    public function getDependencies() {
        return array(
            'configuration',
            'zurmo',
        );
    }

    public function getRootModelNames() {
        return array('AgreementTracking');
    }

    public static function getTranslatedRightsLabels() {
        $params = LabelUtil::getTranslationParamsForAllModules();
        $labels = array();
        $labels[self::RIGHT_CREATE_AGREEMENTTRACKING] = Zurmo::t('AgreementTrackingModule', 'Create AgreementTrackingModulePluralLabel', $params);
        $labels[self::RIGHT_DELETE_AGREEMENTTRACKING] = Zurmo::t('AgreementTrackingModule', 'Delete AgreementTrackingModulePluralLabel', $params);
        $labels[self::RIGHT_ACCESS_AGREEMENTTRACKING] = Zurmo::t('AgreementTrackingModule', 'Access AgreementTrackingModulePluralLabel Tab', $params);
        return $labels;
    }

    public static function getDefaultMetadata() {
        $metadata = array();
        $metadata['global'] = array(
            'tabMenuItems' => array(
                array(
                    'label' => "Agreement Tracking",
                    'url' => array('/agreementTracking/default'),
                    'right' => self::RIGHT_ACCESS_AGREEMENTTRACKING,
                ),
            ),
            'designerMenuItems' => array(
                'showFieldsLink' => true,
                'showGeneralLink' => true,
                'showLayoutsLink' => true,
                'showMenusLink' => true,
            ),
            'globalSearchAttributeNames' => array(
                'name'
            )
        );
        return $metadata;
    }

    public static function getPrimaryModelName() {
        return 'AgreementTracking';
    }

    public static function getSingularCamelCasedName() {
        return 'AgreementTracking';
    }

    public static function getAccessRight() {
        return self::RIGHT_ACCESS_AGREEMENTTRACKING;
    }

    public static function getCreateRight() {
        return self::RIGHT_CREATE_AGREEMENTTRACKING;
    }

    public static function getDeleteRight() {
        return self::RIGHT_DELETE_AGREEMENTTRACKING;
    }

    public static function getGlobalSearchFormClassName() {
        return 'AgreementTrackingSearchForm';
    }

    protected static function getSingularModuleLabel($language) {
        return Zurmo::t('AgreementTrackingModule', 'AgreementTracking', array(), null, $language);
    }

    protected static function getPluralModuleLabel($language) {
        return Zurmo::t('AgreementTrackingModule', 'AgreementTracking', array(), null, $language);
    }

}

?>