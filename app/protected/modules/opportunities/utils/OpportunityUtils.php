<?php

    /**
     * Class utilized by opportunity product selection
     * 
     * @author Ramachandran.K 
     */
    class OpportunityUtils   {

        const RECURRING = 'Recurring Final';
        const PROJECT = 'Project Final';
        const RECURRINGAGREEMENT = 'Recurring Agreement';
        const PROJECTAGREEMENT = 'Project Agreement';
        const AGREEMENT = 'Agreement';
        const WON = 'Won';
        
	public function convertOpportunityToAgreement($optId) {
		try{
			$opt = Opportunity::getById(intval($optId));
			$optProducts = OpportunityProduct::getAllByOpptId(intval($optId));
			$agmnt = new Agreement();
			//$agmnt->Anticipated_Start_Date = $opt->exptStrtDateCstm; Error Using That
			$agmnt->account = $opt->account;
			$agmnt->CustomerSignedDate = DateTimeUtil::convertDateToDateTimeByTimeZoneOffset(DateTimeUtil::getTodaysDate());
			$agmnt->owner = $opt->owner;
			$agmnt->opportunity = $opt;
			$agmnt->Initial_Sales_Rep = $opt->owner;
			$agmnt->name = $opt->name;
			$agmnt->Estimator = $opt->estimator;
			$agmnt->Estimator_Approval = $opt->estimatorApproval;
			$agmnt->budget = $opt->budget;
			$agmnt->suggestedPrice = $opt->suggestedPrice;
                        
			$agmnt->Status->value = Constant::DRAFTAGREEMENT;
			$agmnt->Agreement_Type->value = Constant::OPPORTUNITYAGREEMENT;
                        $agmnt->Total_MHR = $opt->totalMHR;
			$currencies                = Currency::getAll();
                        $Rev_MH                    = new CurrencyValue();
                        $Rev_MH->value             = round($opt->revenueMHR->value, 2);;
                        $Rev_MH->currency          = $currencies[0];
                        $agmnt->Rev_MH = $Rev_MH;
                        $totalDirectCosts                    = new CurrencyValue();
                        $totalDirectCosts->value             = round($opt->totalDirectCosts->value, 2);;
                        $totalDirectCosts->currency          = $currencies[0];
                        $agmnt->Total_Direct_Costs = $totalDirectCosts;
			if($opt->recordType->value == OpportunityUtils::PROJECT) {
				$agmnt->RecordType = OpportunityUtils::PROJECTAGREEMENT;// Not implememted in opportunity
				$projectAmount                    = new CurrencyValue();
				$projectAmount->value             = $opt->finalAmount->value;
				$projectAmount->currency          = $currencies[0];
				$agmnt->Project_Agreement_Amount = $projectAmount;
			} else if($opt->recordType->value == OpportunityUtils::RECURRING) {
				$agmnt->RecordType = OpportunityUtils::RECURRINGAGREEMENT;
				$CAAmount                    = new CurrencyValue();
				$CAAmount->value             = $opt->finalAmount->value;
				$CAAmount->currency          = $currencies[0];
				$FYAmount                    = new CurrencyValue();
				$FYAmount->value             = $opt->finalAmount->value;
				$FYAmount->currency          = $currencies[0];
				$agmnt->First_Year_Amount = $FYAmount;
				$agmnt->Current_Annual_Amount = $CAAmount;
                                $projectAmount                    = new CurrencyValue();
				$projectAmount->value             = 0;
				$projectAmount->currency          = $currencies[0];
				$agmnt->Project_Agreement_Amount = $projectAmount;                                
			} else {
				$agmnt->RecordType = OpportunityUtils::PROJECTAGREEMENT;// Not implememted in opportunity
				$projectAmount                    = new CurrencyValue();
				$projectAmount->value             = $opt->finalAmount->value;
				$projectAmount->currency          = $currencies[0];
				$agmnt->Project_Agreement_Amount = $projectAmount;
			}
			$agmnt->StartDate = DateTimeUtil::getTodaysDate();
			$agmnt->ContractTerm = 0;
			$agmnt->XREF = 1;
			if($opt->aggregateGPM > 0) {	
				$agmnt->Current_GPM = $opt->aggregateGPM;
			} else {
				$agmnt->Current_GPM = 0;
			}                        
                        $agmnt->jobScheduled = Constant::UNSCHEDULED; // For the job scheduling
			$saved = $agmnt->save();
	        if (!$saved) {
	            throw new FailedToSaveModelException();
	        }
		// Here to convert the opportunity product to Agreement Product
                $opt->agreement = $agmnt;
                		
	        if (!$opt->save()) {
	            throw new FailedToSaveModelException();
	        }
            $total_MHR = 0;
            $total_material = 0;
            $total_equipment = 0;
            $total_labor = 0;
                        
			foreach($optProducts as $optProduct) {
                            $agmntPrdct = new AgreementProduct();
                            
                                $agmntPrdct->name = $optProduct->name;
                                $agmntPrdct->costbook = $optProduct->costbook;
                                //$agmntPrdct->Product = $optProduct->Product;
                                $agmntPrdct->Quantity = $optProduct->Quantity;
                                if(!empty($optProduct->Frequency)) {
                                        $agmntPrdct->Frequency = $optProduct->Frequency;
                                } else {
                                    $agmntPrdct->Frequency = NULL;
                                }
                                if(!empty($optProduct->Total_MHR)) {
                                        $agmntPrdct->Total_MHR = $optProduct->Total_MHR;
                                } else {
                                        $agmntPrdct->Total_MHR = 0.0;
                                }
                                $agmntPrdct->Category = $optProduct->Category;
                                $agmntPrdct->Product_Code = $optProduct->Product_Code;
                                $agmntPrdct->Category_GPM = $optProduct->Category_GPM;
                                $agmntPrdct->Burden_Cost = $optProduct->Burden_Cost;
                                $agmntPrdct->Equipment_Cost = $optProduct->Equipment_Cost;
                                $agmntPrdct->Suggested_Cost = $optProduct->Final_Cost;
                                $agmntPrdct->Labor_Cost = $optProduct->Labor_Cost;
                                $agmntPrdct->Materials_Cost = $optProduct->Materials_Cost;
                                $agmntPrdct->Other_Cost = $optProduct->Other_Cost;
                                $agmntPrdct->Sub_Cost = $optProduct->Sub_Cost;
                                $agmntPrdct->Total_Direct_Cost = $optProduct->Total_Direct_Cost;
                                $agmntPrdct->agreement = $agmnt;
                                $agmntPrdct->Product_Code = $optProduct->Product_Code;
                                if(!$agmntPrdct->save()) {
                                         throw new Exception();
                                }

                                // For the Agreement calculation
                                if($optProduct->costbook->costofgoodssold->value == 'Material') {
                                    if($agmnt->RecordType == OpportunityUtils::PROJECTAGREEMENT){
                                        $total_material += $optProduct->Quantity;
                                    }else{
                                        $total_material += $optProduct->Quantity*$optProduct->Frequency;
                                    }                              
                                }
                                else if($optProduct->costbook->costofgoodssold->value == 'Equipment') {
                                    if($agmnt->RecordType == OpportunityUtils::PROJECTAGREEMENT){
                                        $total_equipment += $optProduct->Quantity;
                                    }else{
                                        $total_equipment += $optProduct->Quantity*$optProduct->Frequency;
                                    }
                                }
                                else if($optProduct->costbook->costofgoodssold->value == 'Assembly') {
                                    $assembly_total_arr = OpportunityUtils::AssemblyCalculation($optProduct->costbook, $optProduct->Quantity, $agmntPrdct->Frequency);
                                    $total_material     += $assembly_total_arr['total_material'];
                                    $total_equipment    += $assembly_total_arr['total_equipment'];
                                    OpportunityUtils::AssemblyProductSave($optProduct, $agmnt);
                                }
                            // Ends here                    	
			}
                       // To save the agreement new fields
                        //$agmnt->Total_MHR           = $agmnt->Total_MHR;
                        $agmnt->Total_Available_MHR = $agmnt->Total_MHR;
                        $agmnt->Total_Equipment     = round($total_equipment,2);
                        $agmnt->Available_Equipment = round($total_equipment,2);
                        $agmnt->Total_Material      = round($total_material,2);
                        $agmnt->Available_Material  = round($total_material,2);

                        //Update the contract_number in agreement- Sundar P - 10-Sep-2016
                        $agmnt->Contract_Number='Agmnt-'.$agmnt->id;
                        if (!$agmnt->save()) {
		                    throw new FailedToSaveModelException();
		                }
		} catch(Exception $ex) {
			echo $ex; die;
		}
	}

        public function AssemblyProductSave($product, $agmnt){            
            $assemblyCostbook = $product->costbook;
            $vAssemblyDetails = '';
            $assemblyDetail = trim($assemblyCostbook->assemblydetail);
            if (empty($assemblyDetail)){
                return FALSE;
            } 
            $vAssemblyDetails = explode(';', $assemblyCostbook->assemblydetail);
            
            foreach ($vAssemblyDetails as $vAssemblyDetail) {
                $agmntProduct = new AgreementProduct();
                $productDetails = explode('|', $vAssemblyDetail);
                $costBookData = Costbook::getByProductCode($productDetails[1]);
                $agmntProduct->Assembly_Product_Code = $assemblyCostbook->productcode;
                if(count($costBookData) > 0)
                {
                    if(strpos($costBookData[0]->productcode, 'A') == FALSE){
                       $agmntProduct->name = $costBookData[0]->productname;
                       $agmntProduct->costbook = $costbook = Costbook::GetById(intval($costBookData[0]->id));

                        if(empty($product->Frequency)) {
                            $productQuantity = $product->Quantity * $productDetails[2];
                            $productFrequency = NULL;
                        } else {
                            $productQuantity = $product->Quantity * $productDetails[2];
                            $productFrequency = $product->Frequency;
                        }

                        $agmntProduct->Product_Code = $costBookData[0]->productcode;
                        $agmntProduct->agreement = Agreement::GetById(intval($agmnt->id));
                        AgreementProductUtils::saveAssemblyProductsCalculation($agmntProduct, (float) $productQuantity, $productFrequency, $costBookData, $product->Category);

                    }
                }                
            }
        }


        public function GetAllOpportunityProduct($optId) {
		try{
			$mysql = 'SELECT * FROM opportunityproduct WHERE opportunity_id =\''.intval($optId).'\''; 
            		$rows            = ZurmoRedBean::getAll($mysql);
                        return $rows;
		} catch(Exception $ex) {
			return null;
		}	
	}
        
        public static function makePrintView($data, $Id) {
            $content    = '';   
            $oppProduct = '';
            $opp       = Opportunity::getById($Id);
            
            $opptProducts = array();//OpportunityProduct::getAllByOpptId(intval($Id));
            $count = count($opptProducts);
            $totalDirectCost = 0;
            $totalMH = 0;
            $suggestedPrice = 0;
            $opptPdctMap;
            if(count($opptProducts) > 0) {
                foreach($opptProducts as $row) {
                    $opptPdctMap[$row->Category][] = $row;
                }
                $tableCreation = '';
                
                $tableCreation .= '
                    <table border="1" width="100%" class="">
                                 <colgroup span="5"></colgroup>';

                $tableCreation .= '<thead style="background-color:#E6E6E6; color: black;padding: 2px;">
                                       <tr style="border: 1px solid gray;">
                                           <th colspan="13" style="font-weight: bold;padding: 2px;text-align:center; font-size:14px;">Opportunity Products</th>
                                       </tr>                                         
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Product Code</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Product Name</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Unit of Measure</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Quantity</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Frequency</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">MH</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">L</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">OH</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">M</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">E</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">S</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">O</th>
                                            <th style="font-weight: bold;padding-top: 2px;text-align:center; font-size:10px;">Total Direct Cost</th>
                                       </thead><tbody>';
                 foreach ($opptPdctMap as $key => $optpdctArray)  {
                    $tableCreation .= '<th  style="padding: 3px;font-weight: bold;background-color:gray;color:white; font-size:13px;" colspan="13">'.$key.'</th>';
                     foreach ($optpdctArray as $optKey => $optpdt){
                        $totalDirectCost += $optpdt->Total_Direct_Cost->value;
                        $suggestedPrice += $optpdt->Final_Cost->value;
                        $totalMH += $optpdt->Total_MHR;
                        $tableCreation .= '<tr>
                                <td style="width: 8%; text-align: left; padding: 3px; font-size:10px;">'.$optpdt->costbook->productcode.'</td>
                                <td style="width: 15%; text-align: left; padding: 3px; font-size:10px;">'.$optpdt->name.'</td>
                                <td style="width: 4%; text-align: center; padding: 3px; font-size:10px;">'.$optpdt->costbook->unitofmeasure.'</td>
                                <td style="width: 5%; text-align: center; padding: 3px; font-size:10px;">'.$optpdt->Quantity.'</td>
                                <td style="width: 5%; text-align: center; padding: 3px; font-size:10px;">'.$optpdt->Frequency.'</td>
                                <td style="width: 6%; text-align: center; padding: 3px; font-size:10px;">'.$optpdt->Total_MHR.'</td>
                                <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Labor_Cost.'</td>
                                <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Burden_Cost.'</td>
                                <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Materials_Cost.'</td>
                                <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Equipment_Cost.'</td>
                                <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Sub_Cost.'</td>
                                <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Other_Cost.'</td>
                                <td style="width: 16%;  padding-top: 2px; text-align: right; padding: 3px; font-size:10px;">'.OpportunityProductUtils::getCurrencyType() .$optpdt->Total_Direct_Cost->value.'</td>
                            </tr>';
                      }

                 }
                    $tableCreation .= '</tbody></table>';
                    $tableCreation .= '<table style="margin-left: 20%; margin-top:2%;" border="0"
                                    cellpadding="2" width="60%" text-align="right">
                            <tr>
                                <td rowspan="2" style="text-align:center; font-weight: bold;color:black; font-size:13px;">Direct Cost</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Total</td>
                                <td style="text-align:right;"></td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Suggested</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Final</td>
                            </tr>
                            <tr>
                                <td style="text-align:right; color:black; font-size:13px;">'.OpportunityProductUtils::getCurrencyType() .round($totalDirectCost,2).'</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Price</td>
                                <td style="text-align:right; color:green; font-size:13px;">'.OpportunityProductUtils::getCurrencyType() .round($suggestedPrice,2).'</td>
                                <td style="text-align:right; color:green; font-size:13px;">'.OpportunityProductUtils::getCurrencyType() .round($opp->amount->value,2).'</td>
                            </tr>
                            <tr>
                                <td style="text-align:center; font-weight: bold;color:black; font-size:13px;">MH</td>
                                <td style="text-align:right; color:black; font-size:13px;">'.$totalMH.'</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Rev/MH</td>
                                <td style="text-align:right; color:black; font-size:13px;">'.OpportunityProductUtils::getCurrencyType() .round(($suggestedPrice/$totalMH),2).'</td>
                                <td style="text-align:right; font-size:13px;">'.OpportunityProductUtils::getCurrencyType() .round(($opp->amount->value/$totalMH),2).'</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:right;"></td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:13px;">Aggregate GPM%</td>
                                <td style="text-align:right; color:black; font-size:13px;">'.round(((($suggestedPrice - $totalDirectCost)/$suggestedPrice)*100),2).' </td>
                                <td style="text-align:right; color:black; font-size:13px;">'.round(((($opp->amount->value -$totalDirectCost )/$opp->amount->value)*100),2).'</td>
                            </tr>
                        </table>';
                    $oppProduct .= $tableCreation;
                }
            $appHistoryContent = '';   
            $approvalHistories = array();//ApprovalProcess::getAllAppProcess($Id);            
            
            if(count($approvalHistories)>0) {
                
                $appHistoryContent .= '<table class="" border="1" align="center" width="100%"><colgroup span="6"></colgroup>';
                                           
                $appHistoryContent .= '<thead style="font-weight: bold; background-color:#E6E6E6; color: #999;vertical-align: inherit; padding: 2px;">
                                  <th style="width: 20%; font-weight: bold; font-size:10px; text-align:left">Date</th>
                                  <th style="width: 10%; font-weight: bold; font-size:10px; text-align:left">Status</th>
                                  <th style="width: 15%; font-weight: bold; font-size:10px; text-align:left">Assigned To</th>
                                  <th style="width: 15%; font-weight: bold; font-size:10px; text-align:left">Actual Approver</th>
                                  <th style="width: 20%; font-weight: bold; font-size:10px; text-align:left">Comments</th>
                                  <th style="width: 15%;font-weight: bold; font-size:10px; text-align:left">Overall Status</th>
                               </thead><tbody>';
                $appHistTable = '';
                
                foreach($approvalHistories as $approvalHistory) {
                    if($approvalHistory->action != ''){
                        $appHistTable .= '<th colspan="5" style="background-color:gray; color:white; font-weight:bold; font-size:13px;">'.$approvalHistory->action.'</th><th style="background-color:gray; color:#fff; font-weight:bold;"><span style="background-color:'.($approvalHistory->overallstatus->value == ApprovalProcess::APPROVED ? "green" : ($approvalHistory->overallstatus->value == ApprovalProcess::PENDING ? "#262770": "#a82d31")).'; padding:2px 5px; width:150px;">'.$approvalHistory->overallstatus->value.'</span></th>';		
                        $appHistTable .= '<tr style="padding-top: 2px; text-align: left;">';
                                            
                        $appHistTable .='   <td style="font-size:10px;">'.DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($approvalHistory->date).'</td>
                                            <td style="font-size:10px;">'.$approvalHistory->Status->value.'</td>
                                            <td style="font-size:10px;">'.$approvalHistory->assignedto.'</td>
                                            <td style="font-size:10px;">'.$approvalHistory->actualapprover.'</td>
                                            <td colspan="2" style="font-size:10px;">'.$approvalHistory->comments.'</td>
                                         </tr>';
                    } else {
                        $appHistTable .= '<tr style="padding-top: 2px; text-align: left;">
                                                <td style="font-size:10px;">'.DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($approvalHistory->date).'</td>                                                
                                                <td style="font-size:10px;">'.$approvalHistory->Status->value.'</td>
                                                <td style="font-size:10px;">'.$approvalHistory->assignedto.'</td>
                                                <td style="font-size:10px;">'.$approvalHistory->actualapprover.'</td>
                                                <td colspan="2" style="font-size:10px;">'.$approvalHistory->comments.'</td>
                                         </tr>';
                    }
                }
                $appHistoryContent .= $appHistTable;
                $appHistoryContent .= ApprovalProcessUtils::generateApprovalHistoryTableEnd();
            } 
            $themeName  = Yii::app()->theme->name;
            $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
            $content = '
                <html class="zurmo" lang="en">
                <head>
                <style>
                    @font-face{font-family: "zurmo_gamification_symbly_rRg";src: url("/app/assets/7a93c9f1/zurmogamificationsymblyregular-regular-webfont.eot");src: url("/app/assets/7a93c9f1/zurmogamificationsymblyregular-regular-webfont.eot?#iefix") format("embedded-opentype"), url("/app/assets/7a93c9f1/zurmogamificationsymblyregular-regular-webfont.woff") format("woff"), url("/app/assets/7a93c9f1/zurmogamificationsymblyregular-regular-webfont.ttf") format("truetype"), url("/app/assets/7a93c9f1/zurmogamificationsymblyregular-regular-webfont.svg#zurmo_gamification_symbly_rRg") format("svg");font-weight: normal;font-style: normal;unicode-range: U+00-FFFF;}
                    .clsSmallFont{
                        color: #545454;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 12px;
                    }
                    .details-table td, .details-table th{
                            font-weight: normal;
                    }
                    .zurmo{
                         font-size: 12px;
                    }
                </style>
                
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/zurmo-blue.css" />
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/imports-blue.css" />
                <script>
                    function divShow()
                    {      
                        document.getElementById("oppurtunityGPM").style.display="block";
                        document.getElementById("systemInformation").style.display="block";
                        document.getElementById("estimatorSummary").style.display="block";
                        document.getElementById("managerApproval").style.display="block";
                        document.getElementById("oppProducts").style.display="block";
                        document.getElementById("appHistory").style.display="block";        
                    }
                    function divHide()
                    {        
                        document.getElementById("oppurtunityGPM").style.display="none";
                        document.getElementById("systemInformation").style.display="none";
                        document.getElementById("estimatorSummary").style.display="none";
                        document.getElementById("managerApproval").style.display="none";
                        document.getElementById("oppProducts").style.display="none";
                        document.getElementById("appHistory").style.display="none";
                    }
                </script>
                </head>
                <body class="blue">
                <div>
                    <div style="padding:20px;">                    
                        <img src='.$logoUrl.'>
                    
                        <ul style="padding:2px; margin-left:600px;">
                            <li><a href="javascript:window.close();">Close Window</a></li>
                            <li><a href="javascript:window.print();">Print This Page</a></li>  
                            <li><a href="javascript:divShow();">Expand</a> || <a href="javascript:divHide();">Collapse</a></li>
                        </ul>
                    </div>
                </div>
                <div class="details-table clsSmallFont" style="font-size:10px;">
                    <div class="panel">
                        <table class="form-fields double-column">
                            <colgroup>
                                <col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td colspan="4" style="padding:20px;">'.$opp->name.'<hr></td>
                                </tr>';
            if($opp->recordType->value == Constant::PROJECT){
                $content .=    '<tr style="font-size:12px;">
                                    <th colspan="1">Opportunity</th><td colspan="1">'.$opp->name.'</td>  
                                    <th colspan="1">Record Type</th><td colspan="1">'.$opp->recordType->value.'</td>                            
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Account</th><td colspan="1">'.$opp->account.'</td>
                                    <th>Stage</th><td colspan="1">'.($opp->stage->value != '(None)' ? $opp->stage->value : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th colspan="1">Agreement</th><td colspan="1">'.($opp->agreement != '(Unnamed)' ? $opp->agreement : '').'</td>       
                                    <th colspan="1">Cloned From Opportunity</th><td colspan="1">'.($opp->Opportunity != '(Unnamed)' ? $opp->Opportunity : '').'</td>                            
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Close Date</th><td colspan="1">'.($opp->closeDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->closeDate) : '').'</td>
                                    <th>Reason Lost</th><td colspan="1">'.($opp->reasonLost != '(None)' ? $opp->reasonLost : '' ).'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Oppurtunity Type</th><td colspan="1">'.($opp->opportunityTypes != '(None)' ? $opp->opportunityTypes : '').'</td>
                                    <th>Probability (%)</th><td colspan="1">'.($opp->probability != '(None)' ? $opp->probability .'%' : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Goals</th><td colspan="1">'.$opp->goals.'</td>
                                    <th>Expected Start Date</th><td colspan="1">'.($opp->expectedStartDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->expectedStartDate) : '').'</td>
                                </tr>';
            }else if($opp->recordType->value == Constant::RECURRING ){
                $content .=    '<tr style="font-size:12px;">
                                    <th colspan="1">Record Type</th><td colspan="1">'.$opp->recordType->value.'</td> 
                                    <th colspan="1">Opportunity</th><td colspan="1">'.$opp->name.'</td>  
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Account</th><td colspan="1">'.$opp->account.'</td>
                                    <th>Stage</th><td colspan="1">'.($opp->stage->value != '(None)' ? $opp->stage->value : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th colspan="1">Cloned From Opportunity</th><td colspan="3">'.($opp->Opportunity != '(Unnamed)' ? $opp->Opportunity : '').'</td>                            
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Agreement</th><td colspan="1">'.($opp->agreement != '(Unnamed)' ? $opp->agreement : '').'</td>       
                                    <th>Reason Lost</th><td colspan="1">'.($opp->reasonLost != '(None)' ? $opp->reasonLost : '' ).'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Oppurtunity Type</th><td colspan="1">'.($opp->opportunityTypes != '(None)' ? $opp->opportunityTypes : '').'</td>
                                    <th>Probability (%)</th><td colspan="1">'.($opp->probability != '(None)' ? $opp->probability .'%' : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Initial Term Length</th><td colspan="3">'.($opp->intialTermLengths != '(None)' ? $opp->intialTermLengths : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Goals</th><td colspan="1">'.$opp->goals.'</td>
                                    <th>Close Date</th><td colspan="1">'.($opp->closeDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->closeDate) : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Expected Start Date</th><td colspan="3">'.($opp->expectedStartDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->expectedStartDate) : '').'</td>
                                </tr>';
            }
            
                $content .=  ' 
                            </tbody>
                      </table>                   
               </div>  
               
               <div class="panelTitle">Description Information</div>
                <div id="systemInformation" style="diplay:block;">
                    <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                        <tbody>
                            <tr style="font-size:12px;">
                                <th>Description</th><td colspan="3">'.$opp->description.'</td>
                            </tr> 
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <div class="panelTitle">Opportunity Totals and Gross Profit Margins</div>
                    <div id="oppurtunityGPM" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>';
            if($opp->recordType->value == Constant::PROJECT){
            
                $content .=    '<tr style="font-size:12px;"> 
                                    <th>Budget</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->budget->value, $opp->budget->currency->code).'</td>
                                    <th>Final Amount</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->finalAmount->value, $opp->finalAmount->currency->code).'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Estimator</th><td colspan="1" style="font-size:12px;">'.($opp->estimator != '(Unnamed)' ? $opp->estimator : '').'</td> 
                                    <th>Suggested Price</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->suggestedPrice->value, $opp->suggestedPrice->currency->code).'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>GM</th><td colspan="1" style="font-size:12px;">'.($opp->gm != '' ? $opp->gm : '').'</td> 
                                    <th>Aggregate GPM</th><td colspan="1" style="font-size:12px;">'.$opp->aggregateGPM.'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Revenue / MHR</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->revenueMHR->value, $opp->revenueMHR->currency->code).'</td> 
                                    <th>Total Direct Costs</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->totalDirectCosts->value, $opp->totalDirectCosts->currency->code).'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Total MHR</th><td colspan="3" style="font-size:12px;">'.$opp->totalMHR.'</td> 
                                 </tr>';
            }else if($opp->recordType->value == Constant::RECURRING){
                $content .=    '<tr style="font-size:12px;"> 
                                    <th>Estimator</th><td colspan="1" style="font-size:12px;">'.($opp->estimator != '(Unnamed)' ? $opp->estimator : '').'</td> 
                                    <th>Budget</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->budget->value, $opp->budget->currency->code).'</td>
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>GM</th><td colspan="1" style="font-size:12px;">'.($opp->gm != '' ? $opp->gm : '').'</td> 
                                    <th>Aggregate GPM</th><td colspan="1" style="font-size:12px;">'.$opp->aggregateGPM.'</td>
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Expected Revenue</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->expectedRevenue->value, $opp->expectedRevenue->currency->code).'</td> 
                                    <th>Final Amount</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($opp->finalAmount->value, $opp->finalAmount->currency->code).'</td>    
                                </tr>';
            }
                $content .=   ' </tbody>
                        </table>
                    </div>
                    
                
                    <div class="panelTitle">Estimator Summary</div>
                    <div id="estimatorSummary" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Estimator Approval</th><td colspan="1" style="font-size:12px;">'.($opp->estimatorApproval > 0 ? '<input id="" type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                    <th>Approval Date</th><td colspan="1" style="font-size:12px;">'.($opp->estimatorApprovalDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->estimatorApprovalDate) : '').'</td>
                                </tr> 
                            </tbody>
                        </table>
                    </div>                
                
                    <div class="panelTitle">Manager Approval</div>
                    <div id="managerApproval" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Pricing Approval</th><td colspan="1" style="font-size:12px;">'.($opp->managementPricingApproval > 0 ? '<input type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                    <th>Approval Date</th><td colspan="1" style="font-size:12px;">'.($opp->managementPricingApprovalDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($opp->managementPricingApprovalDate) : '').'</td>
                                </tr>
                                 <tr style="font-size:12px;"> 
                                    <th>Create Agreement</th><td colspan="1" style="font-size:12px;">'.($opp->createAgreement > 0 ? '<input type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>                                    
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                </div>
                </body>
                </html>';
            return $content;
	}
        
        public function AssemblyCalculation($costbook, $quantity, $frequency){
            $vAssemblyDetails = '';
            $assemblyDetail = trim($costbook->assemblydetail);
            if (empty($assemblyDetail)){
                return FALSE;
            } 
            $vAssemblyDetails = explode(';', $costbook->assemblydetail);

            $equipmentTotal = 0;
            $materialTotal = 0;
            $laborTotal = 0;
            $productArray = array();
            
            foreach ($vAssemblyDetails as $vAssemblyDetail) {
                $productDetails = explode('|', $vAssemblyDetail);
                $dataProductCode = Costbook::getByProductCode($productDetails[1]);
                if(isset($dataProductCode[0])) {
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::EQUIPMENT) {
                        if($frequency != NULL){
                            $equipmentTotal += $productDetails[2]*$quantity*$frequency;
                        }  else {
                            $equipmentTotal += $productDetails[2]*$quantity;
                        }
                        
                    }
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::MATERIAL) {
                        if($frequency != NULL){
                            $materialTotal += $productDetails[2]*$quantity*$frequency;
                        }  else {
                            $materialTotal += $productDetails[2]*$quantity;
                        }
                    } 
                } else {
                    continue;
                }
            }
            $productArray['total_material']     = $materialTotal;
            $productArray['total_equipment']    = $equipmentTotal;
            $productArray['total_labor']        = $laborTotal;
            return $productArray;            
        }
        
        public function getChartData($opportunities){
            $resultdataArray = array();
            $chartdataArray = array();
            foreach ($opportunities as $opportunity) {
                $chartdataArray['category'] = $opportunity['sales_person'];
                $chartdataArray['column-1'] = $opportunity['project_amt'];
                $chartdataArray['column-2'] = $opportunity['recurring_amt'];
                $resultdataArray[] = $chartdataArray;
            }
            return $resultdataArray;
        }
    }
?>