<?php

    /**
     * Render areements to view for user
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsListView extends StarredListView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Contract_Number', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'account', 'type' => 'Account', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
				array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Status', 'type' => 'DropDown'),
                                            ),
                                        ),
                                    )
                                ),
                               array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'RecordType', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
				array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'owner', 'type' => 'User'),
                                            ),
                                        ),
                                    )
                                ),
                                 array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'createdDateTime', 'type' => 'dateTime'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),
                    ),
                ),

            );
            return $metadata;
        }

        protected function resolveExtraParamsForKanbanBoard()
        {
            return array('cardColumns' => $this->getCardColumns());
        }

        protected function getCardColumns()
        {
            return array( 'name'         => array('value'  => $this->getLinkString('$data->name', 'name'), 'class' => 'opportunity-name'),
                         'account'      => array('value'  => $this->getRelatedLinkString('$data->account', 'account', 'accounts'),
                                                 'class'  => 'account-name'));
        }
    }
?>
