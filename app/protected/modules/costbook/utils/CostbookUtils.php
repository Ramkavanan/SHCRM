<?php

class CostbookUtils {

    public function updateAssemlblyProductStep2(Costbook $costbook, $detailIds, $model_id) {
        try {
            $costbook->assemblydetail = $detailIds;
            if (!$costbook->save()) {
                throw new Exception('Exception While saving model');
            } else {
                return 1;
            }
        } catch (Exception $ex) {
            echo $ex;
            die;
        }
    }

    public function updateAssemlblyProductNew(Costbook $costbook, $detailIds, $model_id) {
        try {
            $tableName = $costbook::getTableName();
            $sql = "UPDATE $tableName SET assemblydetail = '" . $detailIds . "'
                        WHERE id = '" . $model_id . "'";
            return ZurmoRedBean::exec($sql);
        } catch (Exception $ex) {
            echo $ex;
            die;
        }
    }

    public function totalCostCalculation(Costbook $costbook, $ids) {
        try {
            if ($costbook != null) {
                if ($ids != null) {
                    $assemblyDetails = explode(";", $ids);
                    $totalDirectCost = 0.0;
                    $totalManHour = 0.0;
                    $totalRatio = 0.0;

                    foreach ($assemblyDetails as $assemblyDetail) {
                        $productCode = explode("|", $assemblyDetail);
                        $cb = Costbook::getByProductCode($productCode[1]);
                        $ratio = $productCode[2];
                        if (isset($cb[0])) {
                            if ($cb[0]->costofgoodssold->value == 'Labor') {
                                //$totalDirectCost += (intval($cb[0]->departmentreference->laborCost) * $ratio) + (intval($cb[0]->departmentreference->burdonCost) * $ratio);
                                // Modified by Sundar P - 12-Sep-2016
                                $totalDirectCost += (floatval($cb[0]->departmentreference->laborCost) * $ratio) + (floatval($cb[0]->departmentreference->burdonCost) * $ratio);
                            } else if ($cb[0]->costofgoodssold->value == 'Material') {
                                $totalDirectCost += $cb[0]->unitdirectcost * $ratio;
                            } else if ($cb[0]->costofgoodssold->value == 'Equipment') {
                                $totalDirectCost += $cb[0]->unitdirectcost * $ratio;
                            } else if ($cb[0]->costofgoodssold->value == 'Subcontractor') {
                                $totalDirectCost += $cb[0]->unitdirectcost * $ratio;
                            } else if ($cb[0]->costofgoodssold->value == 'Other') {
                                $totalDirectCost += $cb[0]->unitdirectcost * $ratio;
                            }
                            $totalManHour += $ratio * $cb[0]->costperunit;
                            $totalRatio += $ratio;
                        } else {
                            continue;
                        }
                    }
                    $costbook->unitdirectcost = $totalDirectCost;
                    $costbook->costperunit = $totalDirectCost;

                    if (!$costbook->save()) {
                        throw new Exception('Exception While saving model');
                    } else {
                        return 1;
                    }
                } else {
                    throw new Exception('Please select a product');
                }
            } else {
                throw new Exception('Cost book should not be null');
            }
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

    public function getAssemblyUnitDirectCost($costbook) {
        try {
            if (!empty($costbook['assemblydetail'])) {
                $costbookList = explode(";", $costbook['assemblydetail']);
                $totalDirectCost = 0.0;

                foreach ($costbookList as $costbookProduct) {
                    $productCode = explode("|", $costbookProduct);
                    $costbookProduct = Costbook::getByProductCode($productCode[1]);
                    $ratio = $productCode[2];
                    if (isset($costbookProduct[0])) {
                        if ($costbookProduct[0]->costofgoodssold->value == Constant::LABOUR) {
                            $totalDirectCost += (intval($costbookProduct[0]->departmentreference->laborCost) * $ratio) + (intval($costbookProduct[0]->departmentreference->burdonCost) * $ratio);
                        } else if ($costbookProduct[0]->costofgoodssold->value == Constant::MATERIAL) {
                            $totalDirectCost += $costbookProduct[0]->unitdirectcost * $ratio;
                        } else if ($costbookProduct[0]->costofgoodssold->value == Constant::EQUIPMENT) {
                            $totalDirectCost += $costbookProduct[0]->unitdirectcost * $ratio;
                        } else if ($costbookProduct[0]->costofgoodssold->value == Constant::SUBCONTRACT) {
                            $totalDirectCost += $costbookProduct[0]->unitdirectcost * $ratio;
                        } else if ($costbookProduct[0]->costofgoodssold->value == Constant::OTHER) {
                            $totalDirectCost += $costbookProduct[0]->unitdirectcost * $ratio;
                        }
                    } else {
                        continue;
                    }
                }
                $costbook['unitdirectcost'] = $totalDirectCost;
                $costbook['costperunit'] = $totalDirectCost;

                return $costbook;
            } else {
                return $costbook;
            }
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
    
    public function GetIsAccountManagerGroup(){
        $userIdArr = array();
        $AccountManagerGroup = Group::getByName(User::ACCOUNTMANAGER);  //Access Account manager group
        if(!empty($AccountManagerGroup)){
            foreach($AccountManagerGroup->users as $AccountManagerUserId)
            {
                $userIdArr[] = $AccountManagerUserId->id;
            }
        }
        if(in_array(Yii::app()->user->userModel->id, $userIdArr)){
            return TRUE;
        }else {
            return FALSE;
        }
    }
    
    public function GetIsCatalogManagerGroup(){
        $CostCatalogUserIdArr = array();
        $CostCatalogGroup = Group::getByName(Constant::CATALOGMANAGER);    //Access Catalog manager group
        if(!empty($CostCatalogGroup)){
            foreach($CostCatalogGroup->users as $CostCatalogGroupUserId)
            {
                $CostCatalogUserIdArr[] = $CostCatalogGroupUserId->id;
            }
        }
        if(in_array(Yii::app()->user->userModel->id, $CostCatalogUserIdArr)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

}

?>
