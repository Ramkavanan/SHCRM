<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobSchedulingDefaultController extends ZurmoModuleController {
    /**public function filters()  {
            $modelClassName   = $this->getModule()->getPrimaryModelName();
            $viewClassName    = $modelClassName . 'EditAndDetailsView';
            return array_merge(parent::filters(),
                array(
                    array(
                        ZurmoBaseController::REQUIRED_ATTRIBUTES_FILTER_PATH . ' + create, createFromRelation, edit',
                        'moduleClassName' => get_class($this->getModule()),
                        'viewClassName'   => $viewClassName,
                   ),
                    array(
                        ZurmoModuleController::ZERO_MODELS_CHECK_FILTER_PATH . ' + list, index',
                        'controller' => $this,
                   ),
               )
            );
        }*/
        
    public function actionList()  {
        $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
             'listPageSize', get_class($this->getModule()));
        $jobScheduling                         = new JobScheduling(false);
        $searchForm                     = new JobSchedulingSearchForm($jobScheduling);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              'OpportunitiesMetadataAdapter',   //Adapter to filter the Archived list for Job Scheduling List view
              'JobSchedulingSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')     {
            $mixedView = $this->makeListView(
                $searchForm,
                $dataProvider
            );
            $view = new JobSchedulingPageView($mixedView);
        }
        else
        {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new JobSchedulingPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
    }
        
    protected function makeActionBarSearchAndListView($searchModel, $dataProvider,
                                                      $actionBarViewClassName = 'JobSchedulingSecuredActionBarSearchAndListView',
                                                      $viewPrefixName = null, $activeActionElementType = null,
                                                      IntroView $introView = null)
    {
        assert('is_string($actionBarViewClassName)');
        assert('is_string($viewPrefixName) || $viewPrefixName == null');
        assert('is_string($activeActionElementType) || $activeActionElementType == null');
        if ($viewPrefixName == null)
        {
            $viewPrefixName = $this->getModule()->getPluralCamelCasedName();
        }
        $listModel = $searchModel->getModel();
        return new ActionBarSearchAndListView(
            $this->getId(),
            $this->getModule()->getId(),
            $searchModel,
            $listModel,
            $viewPrefixName,
            $dataProvider,
            GetUtil::resolveSelectedIdsFromGet(),
            $actionBarViewClassName,
            $activeActionElementType,
            $introView
        );
    }

    public function actionEdit($id){
        //get boject by id
        $jobScheduling = JobScheduling::getById(intval($id));
        if($jobScheduling->status != Constant::DEACTIVATED && $jobScheduling->status != Constant::COMPLETEDAGREEMENT){
            //Security check
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($jobScheduling);
            //create view and render
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                    $this->attemptToSaveModelFromPost($jobScheduling, 'from_edit'), 'Edit');
            $view = new JobSchedulingPageView(ZurmoDefaultViewUtil::
                                            makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        } else{
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Deactivated and Completed Jobs cannot be edited.')
                );
            $this->redirect(array($this->getId() . '/details?id=' . $id));
        }
    } 

    public function actionCreate() {
        $editAndDetailsView = $this->makeEditAndDetailsView(
                                        $this->attemptToSaveModelFromPost(new JobScheduling(), 'from_create'), 'Edit');
        $view = new JobSchedulingPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionPrintView($id) {
        $printData = '';
        $printView = new JobSchedulingPrintView($printData, $id);
        echo $printView->render();
    }
        
    public function actionCreateStep1($agreementId){
        $agmnt = Agreement::getById($agreementId);
            if(!empty($agmnt->Total_MHR)){
                $createStep1View = new JobSchedulingCreateView($agreementId);
                $view = new JobSchedulingCustomView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $createStep1View));
                echo $view->render(); 
            } else {
                Yii::app()->user->setFlash('notification',
                        Zurmo::t('ZurmoModule', 'No Labour Products Available.'));
                    $this->redirect(Yii::app()->createUrl('/agreements/default/details?id='.$agreementId));
                    Yii::app()->end(false);
            }    
    }
    
    protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false)
    {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
        $savedSuccessfully   = false;
        $modelToStringValue = null;
        $postVariableName   = get_class($model);
        if (isset($_POST[$postVariableName]))
        {            
            if (!empty($_POST[$postVariableName]['agreement']['id'])){
                $agmnt = Agreement::getById($_POST[$postVariableName]['agreement']['id']);
                if($agmnt->Agreement_Type->value == Constant::CLONEAGREEMENT && $agmnt->Status == Constant::DRAFTAGREEMENT)
                {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'This is a clone agreement in draft stage that needs GM approval. Once approved, you will be able to schedule a job for it.'));
                    $this->redirect(Yii::app()->request->url);
                    Yii::app()->end(false);
                }
            }
            
            // To add the current user id
            $_POST[$postVariableName]['user_id'] = Yii::app()->user->id;
            
            $postData = $_POST[$postVariableName];
            $controllerUtil   = static::getZurmoControllerUtil();
            $model            = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully,
                                                                   $modelToStringValue, $returnOnValidate);
            if(!empty($postData['agreement']['id'])){
               $this->addStatus($model, $postData['agreement']['id'], $redirectUrlParams);
            }  
        }
        if ($savedSuccessfully && $redirect){
            //$this->actionAfterSuccessfulModelSave($model, $modelToStringValue);
            if(Yii::app()->controller->action->id == 'edit'){
                $linkUrl = Yii::app()->getBaseUrl(true).'/index.php/jobScheduling/default/CreateStep2?id='.$model->id.'&agmt_id='.$postData['agreement']['id'].'&type=edit';
            }elseif (Yii::app()->controller->action->id == 'create') {
                $linkUrl = Yii::app()->getBaseUrl(true).'/index.php/jobScheduling/default/CreateStep2?id='.$model->id.'&agmt_id='.$postData['agreement']['id'];
            }
            $this->redirect($linkUrl);
        }
        return $model;
    }
    
    private function addStatus($model, $agreementId, $request_from=''){
        $agreement = Agreement::getById($agreementId);
        $model->agreement = $agreement;
        $model->status = Constant::ACTIVEAGREEMENT;
        if($request_from == 'from_create')
            $model->scheduleDate = DateTimeUtil::getTodaysDate();
        if($model->save()){
            $agreement->jobScheduled = Constant::SCHEDULED;
            $agreement->save();
        }
    }


    public function actionGetJobSchedule($jobInformation) {
        $jobSchedule = json_decode($jobInformation, TRUE);
        $jobInfo = array();
        $jobInfo['agreementId'] = $jobSchedule['agreementId'];
        if(isset($jobSchedule)){
            $job = new JobScheduling();
            $job->name = $jobSchedule['jobName'];
            $job->crewName = $jobSchedule['crewName'];
            $this->addStatus($job, $jobSchedule['agreementId'], 'from_create');
        }
        $jobInfo['jobId'] = $job->id;
        echo json_encode($jobInfo);
    }
        
    public function actionDetails($id) {
        $jobSchedule = JobScheduling::getById($id);
        if($jobSchedule->archive != Constant::ARCHIVE){
            $jobScheduling = static::getModelAndCatchNotFoundAndDisplayError('JobScheduling', intval($id));
            $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'JobSchedulingSearchView', $jobScheduling);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($jobScheduling);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($jobScheduling), 'JobSchedulingModule'), $jobScheduling);
//            $titleBarAndEditView = $this->makeEditAndDetailsView($jobScheduling, 'Details');
            $detailsAndRelationsView = $this->makeDetailsAndRelationsView($jobSchedule, 'JobSchedulingModule',
                                                                          'JobSchedulingDetailsAndRelationsView',
                                                                          Yii::app()->request->getRequestUri(), $breadCrumbView);
            $view = new JobSchedulingPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $detailsAndRelationsView));
            echo $view->render();
        }else{
        Yii::app()->user->setFlash('notification',
                Zurmo::t('ZurmoModule', 'Record does not exist.'));
            $this->redirect(Yii::app()->createUrl('/jobScheduling/default'));
            Yii::app()->end(false);                
        }    
    }
        
    public function actionMassArchive()  {
        $selectedIds = explode(',', Yii::app()->request->getQuery('selectedIds'));
        $successfulCount = count($selectedIds);
        foreach ($selectedIds as $selectedId) {
            $job = JobScheduling::getById($selectedId);
            $job->archive = Constant::ARCHIVE;
            $job->save();
        }

        $notificationContent =  $successfulCount . ' ' .
                                                LabelUtil::getUncapitalizedRecordLabelByCount($successfulCount) .
                                                ' ' . Zurmo::t('Core', 'successfully Archived') . '.';
        Yii::app()->user->setFlash('notification', $notificationContent);
        $this->redirect(Yii::app()->createUrl('jobScheduling/default'));
        Yii::app()->end(false);            
    }        
        
    protected static function getSearchFormClassName()
    {
        return 'JobSchedulingSearchForm';
    }

    public function actionExport(){
        $this->export('JobSchedulingSearchView');
    }

    public function actionModalList()
    {
        $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                                        $_GET['modalTransferInformation']['sourceIdFieldId'],
                                        $_GET['modalTransferInformation']['sourceNameFieldId'],
                                        $_GET['modalTransferInformation']['modalId']
        );
        echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
    }
        
    public function actionCreateStep2($id, $agmt_id, $type='create') {       
        $view = new JobStep2View($id, $agmt_id, $type);
        $jobView = new JobPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $jobView->render();
    }    
    
    public function actionAddJobScheduling(){
        parse_str($_POST['scheduleData'], $processedJobArr);
        $processedJobArr = array_filter($processedJobArr);
        unset($processedJobArr['YII_CSRF_TOKEN']);
        
        if(isset($_POST['jobId']) && $_POST['jobId'] > 0 && $_POST['isEdit'] == 'edit' ){
            $scheduleExist = AgreementJobs::getAgmtJobsByJobId($_POST['jobId']);
            if(!empty ($scheduleExist) && $_POST['isEdit'] == 'edit'){
                foreach ($scheduleExist as $agmtjob) {
                    $agmtjob->id;
                    $agmtjob->delete();
                }
            }
        }
        
        // For the Job Scheduling Week date
        $jobScheduleData    = JobScheduling::getById($_POST['jobId']);
        $weekDayArr         = JobSchedulingUtils::getDateByWeek($jobScheduleData->scheduleDate);
        
        foreach ($processedJobArr as $key=>$val)
        {
            $cat_week_arr = explode('_week_', $key);
            if(is_numeric($cat_week_arr['0']))
            {
                $newSchedule                = new AgreementJobs();
                $newSchedule->jobscheduling = $jobScheduleData;
                $newSchedule->category      = Category::getById($cat_week_arr['0']);
                $newSchedule->week_no       = $cat_week_arr['1'];
                $newSchedule->value         = $val;
                $newSchedule->week_day      = $weekDayArr[$cat_week_arr['1']];
                $newSchedule->save();
            }
        }
        echo json_encode($_POST['jobId']);
    }

    public function actionArchive($id)
    {
        $job = JobScheduling::getById($id);
        $job->archive = Constant::ARCHIVE;
        if(!$job->save()){
            throw new FailedToSaveModelException();    
        }else{
            $agmntId = $job->agreement->id;
            if($agmntId > 0){
                $jobByAgmntId = JobScheduling::getJobsByAgmntId($agmntId);
                $agmnt = Agreement::getById($agmntId);

                if(!empty($jobByAgmntId) && $job->archive !== Constant::ARCHIVE){
                    $agmnt->jobScheduled = Constant::SCHEDULED;
                }else{
                    $agmnt->jobScheduled = Constant::UNSCHEDULED;
                }

                if (!$agmnt->save()) {
                    throw new FailedToSaveModelException();
                }
            }
            Yii::app()->user->setFlash('notification',
            Zurmo::t('ZurmoModule', 'JobSchedule archived successfully.'));
            $this->redirect(Yii::app()->createUrl('jobScheduling/default'));
            Yii::app()->end(false);   
        }    
    }
        
    public function actionJobScheduleReports() {
        // To get the current Year
        $todayDate  = date("Y-01-01");        
        $weekArr    = JobSchedulingUtils::getDateByWeek($todayDate);
        $dateWeek   = array_flip($weekArr);
        $view       = new JobReportView($dateWeek);
        $jobView    = new JobPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $jobView->render();
    }

    /**
     * Update week_day field
     * @param Null
     * @return Null
     * @author Sundar P - 05-Oct-2016
     * Describtion: To update the status week day for the records which is not updated at the time of job scheduling.
     */
    public function actionUpdateWeekDay(){
        
        $result = JobSchedulingUtils::getJobScheduleNotUpdated();
        if(count($result) == 0){
            Yii::app()->user->setFlash('notification',Zurmo::t('ZurmoModule', 'No records found.'));
            $this->redirect(Yii::app()->createUrl('jobScheduling/default'));    
        }
        foreach ($result as $k => $value) {
            parse_str($value['scheduleData'], $processedJobArr);
            $processedJobArr = array_filter($processedJobArr);
            if(isset($value['jobId']) && $value['jobId'] > 0 && $value['isEdit'] == 'edit' ){
                $scheduleExist = AgreementJobs::getAgmtJobsByJobId($value['jobId']);
                if(!empty ($scheduleExist) && $value['isEdit'] == 'edit'){
                    foreach ($scheduleExist as $agmtjob) {
                        $agmtjob->id;
                        $agmtjob->delete();
                    }
                }
            }
            
            // For the Job Scheduling Week date
            $jobScheduleData    = JobScheduling::getById($value['jobId']);
            $weekDayArr         = JobSchedulingUtils::getDateByWeek($jobScheduleData->scheduleDate);

            foreach ($processedJobArr as $key=>$val)
            {
                $cat_week_arr = explode('_week_', $key);
                if(is_numeric($cat_week_arr['0']))
                {
                    $newSchedule                = new AgreementJobs();
                    $newSchedule->jobscheduling = $jobScheduleData;
                    $newSchedule->category      = Category::getById($cat_week_arr['0']);
                    $newSchedule->week_no       = $cat_week_arr['1'];
                    $newSchedule->value         = $val;
                    $newSchedule->week_day      = $weekDayArr[$cat_week_arr['1']];
                    $newSchedule->save();
                }
            }
            Yii::app()->user->setFlash('notification',Zurmo::t('ZurmoModule', 'JobSchedule updated successfully. Job Id:'.$value['jobId']));
            $this->redirect(Yii::app()->createUrl('jobScheduling/default'));
        }       
            
    }
}
?>
