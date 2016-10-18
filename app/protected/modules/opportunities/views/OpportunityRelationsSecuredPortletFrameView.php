<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AgreementRelationsSecuredPortletFrameView
 *
 * @author ideas2it
 */
class OpportunityRelationsSecuredPortletFrameView extends ModelRelationsSecuredPortletFrameView {

    private $showAsTabs;

    public function __construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible = true, $portletsAreMovable = true, $showAsTabs = false, $layoutType = '100', $portletsAreRemovable = true) {
        parent::__construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible, $portletsAreMovable, $showAsTabs, $layoutType, $portletsAreRemovable);
    }

    //put your code here
    protected function renderPortlets($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        if (!$this->showAsTabs) {
            return $this->renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible, $portletsAreMovable, $portletsAreRemovable);
        }
        assert('is_bool($portletsAreCollapsible) && $portletsAreCollapsible == false');
        assert('is_bool($portletsAreMovable) && $portletsAreMovable == false');
        return $this->renderPortletsTabbed();
    }

    protected function renderPortletsTabbed() {
        assert('count($this->portlets) == 1 || count($this->portlets) == 0');
        if (count($this->portlets) == 1) {
            $tabItems = array();
            foreach ($this->portlets[1] as $noteUsed => $portlet) {
                $tabItems[$portlet->getTitle()] = array(
                    'id' => $portlet->getUniquePortletPageId(),
                    'content' => $portlet->renderContent()
                );
            }
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("JuiTabs");
            $cClipWidget->widget('zii.widgets.jui.CJuiTabs', array(
                'id' => $this->uniqueLayoutId . '-portlet-tabs',
                'tabs' => $tabItems
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['JuiTabs'];
        }
    }

    protected function renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        assert('is_string($uniqueLayoutId)');
        assert('is_bool($portletsAreCollapsible)');
        assert('is_bool($portletsAreMovable)');
        assert('is_bool($portletsAreRemovable)');
        $juiPortletsWidgetItems = array();
        foreach ($this->portlets as $column => $columnPortlets) {
            foreach ($columnPortlets as $position => $portlet) {
                $className = get_class($portlet->getView());
                if (method_exists($className, 'canUserRemove')) {
                    $removable = $className::canUserRemove();
                } else {
                    $removable = $portletsAreRemovable;
                }
                $additionalOptionMenuItems = array();
                if (method_exists($className, 'getAdditionalOptionMenuItems')) {
                    $additionalOptionMenuItems = $className::getAdditionalOptionMenuItems();
                }
                if ($className == 'OpportunityDetailsPortletView') {

                    $juiPortletsWidgetItems[$column][$position] = array(
                        'id' => $portlet->id,
                        'uniqueId' => $portlet->getUniquePortletPageId(),
                        'title' => $portlet->getTitle(),
                        'content' => $this::renderViewForOpportunityWithOpptProduct($portlet), ////$portlet->renderContent(),
                        'headContent' => $portlet->renderHeadContent(),
                        'editable' => $portlet->isEditable(),
                        'collapsed' => $portlet->collapsed,
                        'removable' => $removable,
                        'uniqueClass' => $this->resolveUniqueClass($portlet),
                        'portletParams' => $portlet->getPortletParams(),
                        'additionalOptionMenuItems' => $additionalOptionMenuItems,
                    );
                } else {
                    $juiPortletsWidgetItems[$column][$position] = array(
                        'id' => $portlet->id,
                        'uniqueId' => $portlet->getUniquePortletPageId(),
                        'title' => $portlet->getTitle(),
                        'content' => $portlet->renderContent(),
                        'headContent' => $portlet->renderHeadContent(),
                        'editable' => $portlet->isEditable(),
                        'collapsed' => $portlet->collapsed,
                        'removable' => $removable,
                        'uniqueClass' => $this->resolveUniqueClass($portlet),
                        'portletParams' => $portlet->getPortletParams(),
                        'additionalOptionMenuItems' => $additionalOptionMenuItems,
                    );
                }
            }
        }
        $cClipWidget = new CClipWidget();
        $cClipWidget->beginClip("JuiPortlets");
        $cClipWidget->widget('application.core.widgets.JuiPortlets', array(
            'uniqueLayoutId' => $uniqueLayoutId,
            'moduleId' => $this->moduleId,
            'saveUrl' => Yii::app()->createUrl($this->moduleId . '/defaultPortlet/SaveLayout'),
            'layoutType' => $this->getLayoutType(),
            'items' => $juiPortletsWidgetItems,
            'collapsible' => $portletsAreCollapsible,
            'movable' => $portletsAreMovable,
        ));
        $cClipWidget->endClip();
        return $cClipWidget->getController()->clips['JuiPortlets'];
    }

    protected function renderViewForOpportunityWithOpptProduct(Portlet $portlet) {
        if ($this->params['relationModel']->modelClassNameToBean['Opportunity']->id != null) {
            $id = $this->params['relationModel']->modelClassNameToBean['Opportunity']->id;
            $optProducts = $this::getAllOpportunityProducts($this->params['relationModel']->modelClassNameToBean['Opportunity']->id);
            $opportunity = Opportunity::getById(intval($id));
            $approvalHistories = ApprovalProcess::getAllAppProcess($id);
            $opptPdctMap;

            /* Aproval History Starts */
            $apTab = '';
            $apTab .= '<div id="approvalHistoryListId"></div>';
            /* Aproval History Ends */

            /*
             * Opportunity in Estimate stage - Opportunity Products Portlet view
             */
            if ($this::isSolutionPhase($id)) {
                $content = $portlet->renderContent();
                $tableCreation = $this::checkView($id);
                $tableCreation .= '<div class="view-toolbar-container clearfix">
                                        <div class="panel">
                                            <div class="panelTitle">Opportunity Products</div>';
                $tableCreation .= '<hr/><div class="form-toolbar clearfix">';

                //Showing submit aprroval button                     
                if ($opportunity->owner->id == Yii::app()->user->id || $opportunity->createdByUser->id == Yii::app()->user->id) {
                    if ($opportunity->stage->value == Constant::SOLUTIONPHASE || $opportunity->stage->value == Constant::ESTIMATE) {
                        if (count($approvalHistories) > 0) {
                            if ($approvalHistories[0]->Status->value != ApprovalProcess::PENDING) {
                                $tableCreation .= '<a id="addProduct" name="Add Products" class="attachLoading cancel-button moveaddright" href="/app/index.php/opportunityProducts/default/AddProductsInOpportunity?optId=' . $id . '">
                                         <span class="z-spinner"></span>
                                         <span class="z-icon"></span>
                                         <span class="z-label">Add Product</span>
                                    </a>';
                            }
                        } else {
                            $tableCreation .= '<a id="addProduct" name="Add Products" class="attachLoading cancel-button moveaddright" href="/app/index.php/opportunityProducts/default/AddProductsInOpportunity?optId=' . $id . '">
                                    <span class="z-spinner"></span>
                                    <span class="z-icon"></span>
                                    <span class="z-label">Add Product</span>
                                </a>';
                        }
                    }

                    if ($opportunity->stage->value == Constant::FINALPROPOSAL) {
                        if (!empty($approvalHistories)) {
                            if ($approvalHistories[0]->Status->value != ApprovalProcess::PENDING) {
                                $tableCreation .= '<a id="addProduct" name="Add Products" class="attachLoading cancel-button moveaddright" href="/app/index.php/opportunityProducts/default/changePriceForOpportunity?optId=' . $id . '">
                                        <span class="z-spinner"></span>
                                        <span class="z-icon"></span>
                                        <span class="z-label">Change Price</span>
                                    </a>';
                            }
                        }
                    }
                } else {
                    $tableCreation .= '<a id="addProduct" name="Add Products" class="attachLoading cancel-button moveaddright" href="/app/index.php/opportunityProducts/default/AddProductsInOpportunity?optId=' . $id . '">
                            <span class="z-spinner"></span>
                            <span class="z-icon"></span>
                            <span class="z-label">Add Product</span>
                        </a>';
                }

                if (count($optProducts) > 0) {
                    $tableCreation .= '<a id="estimateSummary" class="cancel-button" name="Estimate Summary" href="/app/index.php/opportunityProducts/default/EstimateSummaryInOpportunity?optId=' . $id . '">
                                                <span class="z-spinner">
                                                </span><span class="z-icon"></span>
                                                <span class="z-label">Estimate Summary</span>
                                            </a>';
                }
                $tableCreation .='</div>';
                if (count($optProducts) > 0) {
                    foreach ($optProducts as $row) {
                        $opptPdctMap[$row->Category][] = $row;
                    }

                    $tableCreation .= '<div class="opp_productlist_table" style="border : solid #dfdfdf 1px;"><table class="items selected_products_table">
                                                <colgroup span="5"></colgroup>';

                    $tableCreation .= '<thead style="font-weight: bold; background-color:#E6E6E6; color: #999;
                                                vertical-align: inherit; padding: 5px;">
                                                    <th style="font-weight: bold;">Product Code</th>
                                                    <th style="font-weight: bold;">Product</th>
                                                    <th style="font-weight: bold;">Quantity</th>';

                    if ($opportunity->recordType->value != OpportunityProductUtils::PROJECTFINAL) {
                        $tableCreation .= '<th style="font-weight: bold;">Frequency</th>';
                    }

                    $tableCreation .= '      <th style="font-weight: bold;">Unit of Measure</th>
                                                    <th style="font-weight: bold;">Total Direct Cost</th>
                                                    <th style="font-weight: bold;">Final Cost</th>
                                            </thead>
                                            <tbody>';
                    $totalDirectCost1 = 0;
                    foreach ($opptPdctMap as $categoryKey1 => $optpdctArray1) {
                        foreach ($optpdctArray1 as $optKey1 => $optpdt1) {
                            $totalDirectCost1 += $optpdt1->Total_Direct_Cost->value;
                        }
                    }
                    foreach ($opptPdctMap as $categoryKey => $optpdctArray) {
                        $tableCreation .= '<th colspan="13" class="align_left" style="background-color:gray; color:white;">' . $categoryKey . '</th>';
                        foreach ($optpdctArray as $optKey => $optpdt) {
                            $tableCreation .= '<tr>
                                                       <td style="width: 10%;  padding-top: 2px; text-align: left;">' . $optpdt->costbook->productcode . '</td>
                                                       <td style="width: 30%;  padding-top: 2px; text-align: left;">' . $optpdt->costbook->productname . '</td>
                                                       <td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optpdt->Quantity . '</td>';

                            if ($opportunity->recordType->value != OpportunityProductUtils::PROJECTFINAL) {
                                $tableCreation .= '<td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optpdt->Frequency . '</td>';
                            }

                            $tableCreation .= '<td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optpdt->costbook->unitofmeasure . '</td>
                                           <td style="width:15%;  padding-top: 2px; text-align: left;">' . OpportunityProductUtils::getCurrencyType() . $optpdt->Total_Direct_Cost->value . '</td>';                                               
                            if($totalDirectCost1 > 0)
                                $tableCreation .= '<td style="width: 15%;  padding-top: 2px; text-align: left;">' . OpportunityProductUtils::getCurrencyType() . round($optpdt->Total_Direct_Cost->value / (1 - ((((($opportunity->finalAmount->value - $totalDirectCost1 ) / $opportunity->finalAmount->value) * 100)) / 100)), 2) . '</td>';
                            else {
                                $tableCreation .= '<td style="width: 15%;  padding-top: 2px; text-align: center;">' . OpportunityProductUtils::getCurrencyType() . '0</td>';
                            }
                            $tableCreation .= '</tr>';
                        }
                    }
                    $tableCreation .= '</tbody></table>';
                }
                $tableCreation .= '</div></div></div>';
                $content .= $tableCreation;
                $content .= '<div id="approvalProcess">' . ApprovalProcessUtils::generateHTMLForApprovalInOppt($id) . '</div>';
                $content .= $this->renderScripts();
                return $content;
            }
            /*
             * Opportunity in Agreement stage - Opportunity Products Portlet view
             */ else if (count($optProducts) > 0) {
                $content = $portlet->renderContent();
                $tableCreation = '';
                $tableCreation .= '<div class="view-toolbar-container clearfix">
                                        <div class="panel">
                                            <div class="panelTitle">
                                                Opportunity Products
                                            </div>';
                $tableCreation .= '<div class="form-toolbar clearfix" style="margin-bottom:2%"> 
                                            <a id="estimateSummary" name="Estimate Summary" href="/app/index.php/opportunityProducts/default/EstimateSummaryInOpportunity?optId=' . $id . '">
                                                    <span class="z-spinner"></span>
                                                    <span class="z-icon"></span>
                                                    <span class="z-label">Estimate Summary</span>
                                            </a>
                                      </div>';
                if (count($optProducts) > 0) {
                    $tableCreation .= '<div style="margin : 2%;"><table class="items">
                                                <colgroup span="5"></colgroup>';

                    $tableCreation .= '<thead style="font-weight: bold; background-color:#E6E6E6; color: #999; 
                                                vertical-align: inherit; padding: 5px;">
                                                    <th style="font-weight: bold; padding-left: 3%;">
                                                        Product Code
                                                    </th>
                                                    <th style="font-weight: bold; padding-left: 3%;">
                                                        Product
                                                    </th>
                                                    <th style="font-weight: bold;padding-left: 3%;">
                                                        Quantity
                                                    </th>';

                    if ($opportunity->recordType->value != OpportunityProductUtils::PROJECTFINAL) {
                        $tableCreation .= ' <th style="font-weight: bold; padding-left: 3%;">
                                                         Frequency
                                                    </th>';
                    }

                    $tableCreation .= '<th style="font-weight: bold; padding-left: 3%;">
                                                Unit of Measure
                                            </th>
                                            <th style="font-weight: bold; padding-left: 3%;">
                                                Total Direct Cost
                                            </th>
                                            <th style="font-weight: bold; padding-left: 3%;">
                                                Final Cost
                                            </th>
                                     </thead>
                                  <tbody>';
                    $totalDirectCost2 = 0;
                    foreach ($optProducts as $optPrdct) {
                        $totalDirectCost2 += $optPrdct->Total_Direct_Cost->value;
                    }
                    foreach ($optProducts as $optPrdct) {
                        $tableCreation .= '<tr>
                                                    <td style="width: 10%; padding-left: 3%; padding-top: 2px; text-align: left;">' . $optPrdct->costbook->productcode . '</td>
                                                    <td style="width: 30%; padding-left: 3%; padding-top: 2px; text-align: left;">' . $optPrdct->costbook->productname . '</td>
                                                    <td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optPrdct->Quantity . '</td>';

                        if ($opportunity->recordType->value != OpportunityProductUtils::PROJECTFINAL) {
                            $tableCreation .= '      <td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optPrdct->Frequency . '</td>';
                        }

                        $tableCreation .= '      <td style="width: 10%;  padding-top: 2px; text-align: center;">' . $optPrdct->costbook->unitofmeasure . '</td>
                                                    <td style="width:15%;  padding-top: 2px; text-align: center;">' . OpportunityProductUtils::getCurrencyType() . $optPrdct->Total_Direct_Cost->value . '</td>';
                        if($totalDirectCost2 > 0)
                            $tableCreation .= '<td style="width: 15%;  padding-top: 2px; text-align: center;">' . OpportunityProductUtils::getCurrencyType() . round($optPrdct->Total_Direct_Cost->value / (1 - ((((($opportunity->finalAmount->value - $totalDirectCost2 ) / $opportunity->finalAmount->value) * 100)) / 100)), 2) . '</td>';
                        else {
                            $tableCreation .= '<td style="width: 15%;  padding-top: 2px; text-align: center;">' . OpportunityProductUtils::getCurrencyType() . '0</td>';
                        }
                                              $tableCreation .= '</tr>';
                    }
                    $tableCreation .= '</tbody></table>';
                }
                $tableCreation .= '</div></div></div>';
                $content .= $tableCreation;
                if ($opportunity->stage->value == 'Final Proposal' || $opportunity->stage->value == 'Final Pricing') {
                    $content .= '<div id="approvalProcess">' . ApprovalProcessUtils::generateHTMLForApprovalInOppt($id) . '</div>';
                }
                $content .= $this->renderScripts();
                return $content;
            } else {
                $content = '';
                $content = $portlet->renderContent();
                $content .= $this->renderScripts();
                if ($opportunity->stage->value == 'Final Proposal' || $opportunity->stage->value == 'Final Pricing') {
                    $content .= '<div id="approvalProcess">' . ApprovalProcessUtils::generateHTMLForApprovalInOppt($id) . '</div>';
                }
                return $content . $apTab;
            }
        } else {
            $content = '';
            $content = $portlet->renderContent();
            $content .= $this->renderScripts();

            return $content;
        }
    }

    protected function getAllOpportunityProducts($id) {
        return OpportunityProduct::getAllByOpptId($id);
    }

    public function isSolutionPhase($optId) {
        $oppt = Opportunity::getById(intval($optId));
        if ($oppt->stage->value == 'Solution Phase' || $oppt->stage->value == 'Estimate' || $oppt->stage->value == 'Final Pricing' || $oppt->stage->value == 'Final Proposal') {
            return true;
        }
        return false;
    }

    protected function checkView($id) {
        $l = new OpportunitiesByTotalManHourChartView('OpportunitiesByTotalMHR', null, null);
        return $l->setOpptId($id);
    }

    //For rendering the approval process js file
    protected function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.approvalProcess.elements.assets')) . '/ApprovalProcessTemplateUtils.js', CClientScript::POS_END);
    }

}
