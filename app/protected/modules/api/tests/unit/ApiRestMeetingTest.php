<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    /**
    * Test Meeting related API functions.
    */
    class ApiRestMeetingTest extends ApiRestTest
    {
        public function testGetMeeting()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $meeting = MeetingTestHelper::createMeetingByNameForOwner('First Meeting', $super);
            $compareData  = $this->getModelToApiDataUtilData($meeting);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meeting->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals($compareData, $response['data']);
            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($super->id, $response['data']['owner']['id']);
            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
        }

        /**
         * @depends testGetMeeting
         */
        public function testDeleteMeeting()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meetings = Meeting::getByName('First Meeting');
            $this->assertEquals(1, count($meetings));

            $response = $this->createApiCallWithRelativeUrl('delete/' . $meetings[0]->id, 'DELETE', $headers);

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('The ID specified was invalid.', $response['message']);
        }

        /**
         * @depends testGetMeeting
         */
        public function testCreateMeeting()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $categories = array(
                'Meeting',
                'Call',
            );
            $categoryFieldData = CustomFieldData::getByName('MeetingCategories');
            $categoryFieldData->serializedData = serialize($categories);
            $this->assertTrue($categoryFieldData->save());

            $startStamp             = DateTimeUtil::convertTimestampToDbFormatDateTime(time()  + 10000);
            $endStamp               = DateTimeUtil::convertTimestampToDbFormatDateTime(time() + 11000);

            $data['name']           = "Michael Meeting with no permissions";
            $data['startDateTime']  = $startStamp;
            $data['endDateTime']    = $endStamp;
            $data['location']       = "Office";
            $data['description']    = "Description";

            $data['category']['value'] = $categories[1];

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('id', $response['data']);
            $meetingId     = $response['data']['id'];

            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($super->id, $response['data']['owner']['id']);
            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals(1, $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);

            $data['owner'] = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['createdByUser']    = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['modifiedByUser'] = array(
                'id' => $super->id,
                'username' => 'super'
            );

            // unset explicit permissions, we won't use these in comparison.
            unset($response['data']['explicitReadWriteModelPermissions']);
            // We need to unset some empty values from response.
            unset($response['data']['createdDateTime']);
            unset($response['data']['modifiedDateTime']);
            unset($response['data']['category']['id']);
            unset($response['data']['id']);
            unset($response['data']['logged']);
            unset($response['data']['processedForLatestActivity']);
            unset($response['data']['attendees']);
            $data['latestDateTime'] = $startStamp;

            ksort($data);
            ksort($response['data']);
            $this->assertEquals($data, $response['data']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetingId, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('data', $response);
            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($super->id, $response['data']['owner']['id']);

            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals(1, $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);
        }

        /**
         * @depends testCreateMeeting
         */
        public function testCreateMeetingWithSpecificOwner()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $billy  = User::getByUsername('billy');
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $categories = array(
                'Meeting',
                'Call',
            );
            $categoryFieldData = CustomFieldData::getByName('MeetingCategories');
            $categoryFieldData->serializedData = serialize($categories);
            $this->assertTrue($categoryFieldData->save());

            $startStamp             = DateTimeUtil::convertTimestampToDbFormatDateTime(time()  + 10000);
            $endStamp               = DateTimeUtil::convertTimestampToDbFormatDateTime(time() + 11000);

            $data['name']           = "Michael Meeting with just owner";
            $data['startDateTime']  = $startStamp;
            $data['endDateTime']    = $endStamp;
            $data['location']       = "Office";
            $data['description']    = "Description";
            $data['category']['value'] = $categories[1];
            $data['owner']['id']        = $billy->id;

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('id', $response['data']);
            $meetingId     = $response['data']['id'];

            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($billy->id, $response['data']['owner']['id']);

            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals(1, $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);

            $data['owner'] = array(
                'id' => $billy->id,
                'username' => 'billy'
            );
            $data['createdByUser']    = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['modifiedByUser'] = array(
                'id' => $super->id,
                'username' => 'super'
            );

            // unset explicit permissions, we won't use these in comparison.
            unset($response['data']['explicitReadWriteModelPermissions']);
            // We need to unset some empty values from response.
            unset($response['data']['createdDateTime']);
            unset($response['data']['modifiedDateTime']);
            unset($response['data']['category']['id']);
            unset($response['data']['id']);
            unset($response['data']['logged']);
            unset($response['data']['processedForLatestActivity']);
            unset($response['data']['attendees']);
            $data['latestDateTime'] = $startStamp;

            ksort($data);
            ksort($response['data']);
            $this->assertEquals($data, $response['data']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetingId, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('data', $response);
            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($billy->id, $response['data']['owner']['id']);

            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals(1, $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);
        }

        /**
         * @depends testCreateMeeting
         */
        public function testCreateMeetingWithSpecificExplicitPermissions()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $categories = array(
                'Meeting',
                'Call',
            );
            $categoryFieldData = CustomFieldData::getByName('MeetingCategories');
            $categoryFieldData->serializedData = serialize($categories);
            $this->assertTrue($categoryFieldData->save());

            $startStamp             = DateTimeUtil::convertTimestampToDbFormatDateTime(time()  + 10000);
            $endStamp               = DateTimeUtil::convertTimestampToDbFormatDateTime(time() + 11000);

            $data['name']           = "Michael Meeting with owner only permissions";
            $data['startDateTime']  = $startStamp;
            $data['endDateTime']    = $endStamp;
            $data['location']       = "Office";
            $data['description']    = "Description";
            $data['category']['value'] = $categories[1];
            // TODO: @Shoaibi/@Ivica: null does not work, empty works. null doesn't send it.
            $data['explicitReadWriteModelPermissions'] = array('nonEveryoneGroup' => '', 'type' => '');

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('id', $response['data']);
            $meetingId     = $response['data']['id'];

            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($super->id, $response['data']['owner']['id']);
            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['type']);
            // following also works. wonder why.
            //$this->assertTrue(null === $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);

            $data['owner'] = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['createdByUser']    = array(
                'id' => $super->id,
                'username' => 'super'
            );
            $data['modifiedByUser'] = array(
                'id' => $super->id,
                'username' => 'super'
            );
            // We need to unset some empty values from response.
            unset($response['data']['createdDateTime']);
            unset($response['data']['modifiedDateTime']);
            unset($response['data']['category']['id']);
            unset($response['data']['id']);
            unset($response['data']['logged']);
            unset($response['data']['processedForLatestActivity']);
            unset($response['data']['attendees']);
            $data['latestDateTime'] = $startStamp;

            ksort($data);
            ksort($response['data']);
            $this->assertEquals($data, $response['data']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetingId, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertArrayHasKey('data', $response);
            $this->assertArrayHasKey('owner', $response['data']);
            $this->assertCount(2, $response['data']['owner']);
            $this->assertArrayHasKey('id', $response['data']['owner']);
            $this->assertEquals($super->id, $response['data']['owner']['id']);

            $this->assertArrayHasKey('explicitReadWriteModelPermissions', $response['data']);
            $this->assertCount(2, $response['data']['explicitReadWriteModelPermissions']);
            $this->assertArrayHasKey('type', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['type']);
            $this->assertArrayHasKey('nonEveryoneGroup', $response['data']['explicitReadWriteModelPermissions']);
            $this->assertEquals('', $response['data']['explicitReadWriteModelPermissions']['nonEveryoneGroup']);
        }

        /**
         * @depends testCreateMeeting
         */
        public function testUpdateMeeting()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meetings = Meeting::getByName( 'Michael Meeting with just owner');
            $this->assertEquals(1, count($meetings));
            $compareData  = $this->getModelToApiDataUtilData($meetings[0]);
            $meetings[0]->forget();
            $group  = static::$randomNonEveryoneNonAdministratorsGroup;
            $explicitReadWriteModelPermissions = array('type' => 2, 'nonEveryoneGroup' => $group->id);
            $data['description']    = "Some new description";
            $data['explicitReadWriteModelPermissions']    = $explicitReadWriteModelPermissions;
            $compareData['description'] = "Some new description";
            $compareData['explicitReadWriteModelPermissions']   = $explicitReadWriteModelPermissions;
            $response = $this->createApiCallWithRelativeUrl('update/' . $compareData['id'], 'PUT', $headers,
                                                                                                array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            // We need to unset some empty values from response and dates.
            unset($response['data']['modifiedDateTime']);
            unset($compareData['modifiedDateTime']);
            ksort($compareData);
            ksort($response['data']);
            $this->assertEquals($compareData, $response['data']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            unset($response['data']['modifiedDateTime']);
            ksort($response['data']);
            $this->assertEquals($compareData, $response['data']);
        }

        /**
         * @depends testUpdateMeeting
         */
        public function testListMeetings()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meetings = Meeting::getByName( 'Michael Meeting with owner only permissions');
            $this->assertEquals(1, count($meetings));
            $compareData  = $this->getModelToApiDataUtilData($meetings[0]);

            $response = $this->createApiCallWithRelativeUrl('list/' , 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['items']));
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals($compareData, $response['data']['items'][2]);
        }

        public function testListMeetingAttributes()
        {
            RedBeanModel::forgetAll();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $allAttributes      = ApiRestTestHelper::getModelAttributes(new Meeting());

            $response = $this->createApiCallWithRelativeUrl('listAttributes/' , 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals($allAttributes, $response['data']['items']);
        }

        /**
         * @depends testListMeetings
         */
        public function testUnprivilegedUserViewUpdateDeleteMeetings()
        {
            Yii::app()->user->userModel        = User::getByUsername('super');
            $notAllowedUser = UserTestHelper::createBasicUser('Steven');
            $notAllowedUser->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $saved = $notAllowedUser->save();

            $everyoneGroup = Group::getByName(Group::EVERYONE_GROUP_NAME);
            $this->assertTrue($everyoneGroup->save());

            $meetings = Meeting::getByName( 'Michael Meeting with owner only permissions');
            $this->assertEquals(1, count($meetings));
            $data['description']    = "Some new description 2";

            // Test with unprivileged user to view, edit and delete account.
            $authenticationData = $this->login('steven', 'steven');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights to perform this action.', $response['message']);

            $response = $this->createApiCallWithRelativeUrl('update/' . $meetings[0]->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights to perform this action.', $response['message']);

            $response = $this->createApiCallWithRelativeUrl('delete/' . $meetings[0]->id, 'DELETE', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have rights to perform this action.', $response['message']);

            //now check if user have rights, but no permissions.
            $notAllowedUser->setRight('MeetingsModule', MeetingsModule::getAccessRight());
            $notAllowedUser->setRight('MeetingsModule', MeetingsModule::getCreateRight());
            $notAllowedUser->setRight('MeetingsModule', MeetingsModule::getDeleteRight());
            $saved = $notAllowedUser->save();
            $this->assertTrue($saved);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have permissions for this action.', $response['message']);

            $response = $this->createApiCallWithRelativeUrl('update/' . $meetings[0]->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have permissions for this action.', $response['message']);

            $response = $this->createApiCallWithRelativeUrl('delete/' . $meetings[0]->id, 'DELETE', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals('You do not have permissions for this action.', $response['message']);

            // Allow everyone group to read/write meeting
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            unset($data);
            $data['explicitReadWriteModelPermissions'] = array(
                'type' => ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP
            );
            $response = $this->createApiCallWithRelativeUrl('update/' . $meetings[0]->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $authenticationData = $this->login('steven', 'steven');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            unset($data);
            $data['description']    = "Some new description 3";
            $response = $this->createApiCallWithRelativeUrl('update/' . $meetings[0]->id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals("Some new description 3", $response['data']['description']);

            // Test with privileged user
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            //Test Delete
            $response = $this->createApiCallWithRelativeUrl('delete/' . $meetings[0]->id, 'DELETE', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);

            $response = $this->createApiCallWithRelativeUrl('read/' . $meetings[0]->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
        }

        /**
        * @depends testUnprivilegedUserViewUpdateDeleteMeetings
        */
        public function testBasicSearchMeetings()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            Meeting::deleteAll();
            $anotherUser = User::getByUsername('steven');

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $firstAccount = AccountTestHelper::createAccountByNameTypeAndIndustryForOwner('First Account', 'Customer', 'Automotive', $super);
            $secondAccount = AccountTestHelper::createAccountByNameTypeAndIndustryForOwner('Second Account', 'Customer', 'Automotive', $super);

            MeetingTestHelper::createMeetingWithOwnerAndRelatedAccount('First Meeting', $super, $firstAccount);
            $secondMeeting = MeetingTestHelper::createMeetingWithOwnerAndRelatedAccount('Second Meeting', $super, $firstAccount);
            MeetingTestHelper::createMeetingWithOwnerAndRelatedAccount('Third Meeting', $super, $secondAccount);
            MeetingTestHelper::createMeetingWithOwnerAndRelatedAccount('Forth Meeting', $anotherUser, $secondAccount);
            MeetingTestHelper::createMeetingWithOwnerAndRelatedAccount('Fifth Meeting', $super, $firstAccount);

            $billy = User::getByUsername('billy');
            $secondMeeting->userAttendees->add($billy);
            $this->assertTrue($secondMeeting->save());

            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'name' => '',
                ),
                'sort' => 'name',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['items']));
            $this->assertEquals(5, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Fifth Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('First Meeting', $response['data']['items'][1]['name']);
            $this->assertEquals('Forth Meeting', $response['data']['items'][2]['name']);

            // Second page
            $searchParams['pagination']['page'] = 2;
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['items']));
            $this->assertEquals(5, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['currentPage']);
            $this->assertEquals('Second Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('Third Meeting', $response['data']['items'][1]['name']);

            // Search by name
            $searchParams['pagination']['page'] = 1;
            $searchParams['search']['name'] = 'First Meeting';
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['items']));
            $this->assertEquals(1, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('First Meeting', $response['data']['items'][0]['name']);

            // No results
            $searchParams['pagination']['page'] = 1;
            $searchParams['search']['name'] = 'First Meeting 2';
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(0, $response['data']['totalCount']);
            $this->assertFalse(isset($response['data']['items']));

            // Search by name desc.
            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'name' => '',
                ),
                'sort' => 'name.desc',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, count($response['data']['items']));
            $this->assertEquals(5, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Third Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('Second Meeting', $response['data']['items'][1]['name']);
            $this->assertEquals('Forth Meeting', $response['data']['items'][2]['name']);

            // Second page
            $searchParams['pagination']['page'] = 2;
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['items']));
            $this->assertEquals(5, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['currentPage']);
            $this->assertEquals('First Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('Fifth Meeting', $response['data']['items'][1]['name']);

            // Search by custom fields, order by name desc
            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'owner'   => array( 'id' => $super->id),
                ),
                'sort' => 'name.desc',
            );

            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(4, $response['data']['totalCount']);
            $this->assertEquals(3, count($response['data']['items']));
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Third Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('Second Meeting', $response['data']['items'][1]['name']);
            $this->assertEquals('First Meeting', $response['data']['items'][2]['name']);

            // Search by account, order by name desc
            $searchParams = array(
                            'pagination' => array(
                                'page'     => 1,
                                'pageSize' => 3,
            ),
                            'search' => array(
                                'activityItems'   => array('id' => $firstAccount->getClassId('Item')),
            ),
                            'sort' => 'name.desc',
            );

            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);

            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(3, count($response['data']['items']));
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Second Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('First Meeting', $response['data']['items'][1]['name']);
            $this->assertEquals('Fifth Meeting', $response['data']['items'][2]['name']);
        }

        /**
        * @depends testBasicSearchMeetings
        */
        public function testDynamicSearchMeetings()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel        = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $data = array(
                'dynamicSearch' => array(
                    'dynamicClauses' => array(
                        array(
                            'attributeIndexOrDerivedType' => 'owner',
                            'structurePosition' => 1,
                            'owner' => array(
                                'id' => Yii::app()->user->userModel->id,
                            ),
                        ),
                        array(
                            'attributeIndexOrDerivedType' => 'name',
                            'structurePosition' => 2,
                            'name' => 'Fi',
                        ),
                        array(
                            'attributeIndexOrDerivedType' => 'name',
                            'structurePosition' => 3,
                            'name' => 'Se',
                        ),
                    ),
                    'dynamicStructure' => '1 AND (2 OR 3)',
                ),
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 2,
                ),
                'sort' => 'name.asc',
           );

            $response = $this->createApiCallWithRelativeUrl('list/filter/', 'POST', $headers, array('data' => $data));

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['items']));
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Fifth Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('First Meeting', $response['data']['items'][1]['name']);

            // Get second page
            $data['pagination']['page'] = 2;
            $response = $this->createApiCallWithRelativeUrl('list/filter/', 'POST', $headers, array('data' => $data));

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['items']));
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['currentPage']);
            $this->assertEquals('Second Meeting', $response['data']['items'][0]['name']);
        }

        public function testNewSearchMeetings()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel        = $super;

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $data = array(
                'search' => array(
                    'modelClassName' => 'Meeting',
                    'searchAttributeData' => array(
                        'clauses' => array(
                            1 => array(
                                'attributeName'        => 'owner',
                                'relatedAttributeName' => 'id',
                                'operatorType'         => 'equals',
                                'value'                => Yii::app()->user->userModel->id,
                            ),
                            2 => array(
                                'attributeName'        => 'name',
                                'operatorType'         => 'startsWith',
                                'value'                => 'Fi'
                            ),
                            3 => array(
                                'attributeName'        => 'name',
                                'operatorType'         => 'startsWith',
                                'value'                => 'Se'
                            ),
                        ),
                        'structure' => '1 AND (2 OR 3)',
                    ),
                ),
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 2,
                ),
                'sort' => 'name asc',
            );

            $response = $this->createApiCallWithRelativeUrl('search/filter/', 'POST', $headers, array('data' => $data));

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['items']));
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Fifth Meeting', $response['data']['items'][0]['name']);
            $this->assertEquals('First Meeting', $response['data']['items'][1]['name']);

            // Get second page
            $data['pagination']['page'] = 2;
            $response = $this->createApiCallWithRelativeUrl('search/filter/', 'POST', $headers, array('data' => $data));

            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['items']));
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['currentPage']);
            $this->assertEquals('Second Meeting', $response['data']['items'][0]['name']);
        }

        public function testNewSearchForMeetingsWithUserAttendees()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel        = $super;
            $billy = User::getByUsername('billy');

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $data = array(
                'search' => array(
                    'modelClassName' => 'Meeting',
                    'searchAttributeData' => array(
                        'clauses' => array(
                            1 => array(
                                'attributeName'        => 'userAttendees',
                                'relatedAttributeName' => 'id',
                                'operatorType'         => 'equals',
                                'value'                => $billy->id,
                            ),
                        ),
                        'structure' => '1',
                    ),
                ),
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 2,
                ),
                'sort' => 'name asc',
            );

            $response = $this->createApiCallWithRelativeUrl('search/filter/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['items']));
            $this->assertEquals(1, $response['data']['totalCount']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertEquals('Second Meeting', $response['data']['items'][0]['name']);
        }

        public function testCreateWithRelations()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            $everyoneGroup = Group::getByName(Group::EVERYONE_GROUP_NAME);
            $this->assertTrue($everyoneGroup->save());

            $john = UserTestHelper::createBasicUser('John');
            $john->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $john->setRight('MeetingsModule', MeetingsModule::getAccessRight());
            $john->setRight('MeetingsModule', MeetingsModule::getCreateRight());
            $saved = $john->save();
            $this->assertTrue($saved);

            $contact  = ContactTestHelper::createContactByNameForOwner('Simon', $super);
            $contact2  = ContactTestHelper::createContactByNameForOwner('Simona', $john);
            $contactItemId = $contact->getClassId('Item');
            $contact2ItemId = $contact2->getClassId('Item');

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $categories = array(
                'Meeting',
                'Call',
            );
            $categoryFieldData = CustomFieldData::getByName('MeetingCategories');
            $categoryFieldData->serializedData = serialize($categories);
            $this->assertTrue($categoryFieldData->save());

            $startStamp             = DateTimeUtil::convertTimestampToDbFormatDateTime(time()  + 10000);
            $endStamp               = DateTimeUtil::convertTimestampToDbFormatDateTime(time() + 11000);

            $data['name']           = "Meeting with relations for super user";
            $data['startDateTime']  = $startStamp;
            $data['endDateTime']    = $endStamp;
            $data['category']['value'] = $categories[1];

            $data['modelRelations'] = array(
                'activityItems' => array(
                    array(
                        'action' => 'add',
                        'modelId' => $contact->id,
                        'modelClassName' => 'Contact'
                    ),
                ),
                'userAttendees' => array(
                    array(
                        'action' => 'add',
                        'modelId' => $john->id,
                        'modelClassName' => 'User'
                    ),
                )
            );

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals($data['name'], $response['data']['name']);
            $this->assertEquals($data['startDateTime'], $response['data']['startDateTime']);
            $this->assertEquals($data['endDateTime'], $response['data']['endDateTime']);

            RedBeanModel::forgetAll();
            $meeting = Meeting::getById($response['data']['id']);
            $this->assertEquals(1, count($meeting->activityItems));
            $this->assertEquals($contactItemId, $meeting->activityItems[0]->id);
            $this->assertEquals(1, count($meeting->userAttendees));
            $this->assertEquals($john->id, $meeting->userAttendees[0]->id);

            // Now test with regular user
            $authenticationData = $this->login('john', 'john');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $data = array();
            $data['name']           = "Meeting with relations for regular user";
            $data['startDateTime']  = $startStamp;
            $data['endDateTime']    = $endStamp;
            $data['category']['value'] = $categories[1];
            $data['modelRelations'] = array(
                'activityItems' => array(
                    array(
                        'action' => 'add',
                        'modelId' => $contact2->id,
                        'modelClassName' => 'Contact'
                    ),
                ),
                'userAttendees' => array(
                    array(
                        'action' => 'add',
                        'modelId' => $super->id,
                        'modelClassName' => 'User'
                    ),
                )
            );
            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals($data['name'], $response['data']['name']);
            $this->assertEquals($data['startDateTime'], $response['data']['startDateTime']);
            $this->assertEquals($data['endDateTime'], $response['data']['endDateTime']);

            RedBeanModel::forgetAll();
            $meeting = Meeting::getById($response['data']['id']);
            $this->assertEquals(1, count($meeting->activityItems));
            $this->assertEquals($contact2ItemId, $meeting->activityItems[0]->id);
            $this->assertEquals(1, count($meeting->userAttendees));
            $this->assertEquals($super->id, $meeting->userAttendees[0]->id);
        }

        public function testEditMeetingWithIncompleteData()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meeting = MeetingTestHelper::createMeetingByNameForOwner('New Meeting', $super);

            // Provide data without required fields.
            $data['location']         = "Test 123";

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(2, count($response['errors']));

            $id = $meeting->id;
            $data = array();
            $data['name']                = '';
            $response = $this->createApiCallWithRelativeUrl('update/' . $id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(1, count($response['errors']));
        }

        public function testEditMeetingWIthIncorrectDataType()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meeting = MeetingTestHelper::createMeetingByNameForOwner('Newest Meeting', $super);

            // Provide data with wrong type.
            $data['startDateTime']         = "A";

            $response = $this->createApiCallWithRelativeUrl('create/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(2, count($response['errors']));

            $id = $meeting->id;
            $data = array();
            $data['startDateTime']         = "A";
            $response = $this->createApiCallWithRelativeUrl('update/' . $id, 'PUT', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_FAILURE, $response['status']);
            $this->assertEquals(1, count($response['errors']));
        }

        /**
         * Test if all newly created items was pulled from read permission tables via API.
         * Please note that here we do not test if data are inserted in read permission tables correctly, that is
         * part of read permission subscription tests
         * @throws NotFoundException
         * @throws NotImplementedException
         * @throws NotSupportedException
         */
        public function testGetCreatedMeetings()
        {
            $timestamp = time();
            sleep(1);
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $lisa = UserTestHelper::createBasicUser('Lisa');
            $lisa->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $lisa->setRight('MeetingsModule', MeetingsModule::getAccessRight());
            $this->assertTrue($lisa->save());
            $this->deleteAllModelsAndRecordsFromReadPermissionTable('Meeting');
            $job = new ReadPermissionSubscriptionUpdateJob();
            ReadPermissionsOptimizationUtil::rebuild();

            $meeting1 = MeetingTestHelper::createMeetingByNameForOwner('Meeting1', $super);
            sleep(1);
            $meeting2 = MeetingTestHelper::createMeetingByNameForOwner('Meeting2', $super);
            sleep(1);
            $meeting3 = MeetingTestHelper::createMeetingByNameForOwner('Meeting3', $super);
            sleep(1);
            $this->assertTrue($job->run());

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getCreatedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);

            $this->assertEquals($meeting1->id, $response['data']['items'][0]['id']);
            $this->assertEquals($super->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting1->name, $response['data']['items'][0]['name']);

            $this->assertEquals($meeting2->id, $response['data']['items'][1]['id']);
            $this->assertEquals($super->id, $response['data']['items'][1]['owner']['id']);
            $this->assertEquals($meeting2->name, $response['data']['items'][1]['name']);

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime(0),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 2
                )
            );
            $response = $this->createApiCallWithRelativeUrl('getCreatedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(2, $response['data']['currentPage']);

            $this->assertEquals($meeting3->id, $response['data']['items'][0]['id']);
            $this->assertEquals($super->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting3->name, $response['data']['items'][0]['name']);

            // Change owner of $contact1, it should appear in Lisa's created contacts
            $meeting1->owner = $lisa;
            $this->assertTrue($meeting1->save());
            sleep(1);
            $this->assertTrue($job->run());

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getCreatedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(2, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);

            $this->assertEquals($meeting2->id, $response['data']['items'][0]['id']);
            $this->assertEquals($super->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting2->name, $response['data']['items'][0]['name']);

            $this->assertEquals($meeting3->id, $response['data']['items'][1]['id']);
            $this->assertEquals($super->id, $response['data']['items'][1]['owner']['id']);
            $this->assertEquals($meeting3->name, $response['data']['items'][1]['name']);

            $authenticationData = $this->login('lisa', 'lisa');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getCreatedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(1, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);

            $this->assertEquals($meeting1->id, $response['data']['items'][0]['id']);
            $this->assertEquals($lisa->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting1->name, $response['data']['items'][0]['name']);
        }

        /**
         * Test if all modified items was pulled via API correctly.
         * Please note that here we do not test if data are inserted in read permission tables correctly, that is
         * part of read permission subscription tests
         * @throws NotFoundException
         */
        public function testGetModifiedMeetings()
        {
            $timestamp = time();
            sleep(1);
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $this->deleteAllModelsAndRecordsFromReadPermissionTable('Meeting');
            $job = new ReadPermissionSubscriptionUpdateJob();
            $meeting1 = MeetingTestHelper::createMeetingByNameForOwner('Meeting1', $super);
            $meeting2 = MeetingTestHelper::createMeetingByNameForOwner('Meeting2', $super);
            $meeting3 = MeetingTestHelper::createMeetingByNameForOwner('Meeting3', $super);
            $meeting4 = MeetingTestHelper::createMeetingByNameForOwner('Meeting4', $super);
            sleep(1);
            $this->assertTrue($job->run());
            sleep(1);

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getModifiedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(0, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);

            $timestamp = time();
            sleep(2);
            $meeting1->name = "Meeting1 Modified";
            $this->assertTrue($meeting1->save());
            sleep(1);
            $meeting3->name = "Meeting2 Modified";
            $this->assertTrue($meeting3->save());
            sleep(1);
            $meeting4->name = "Meeting3 Modified";
            $this->assertTrue($meeting4->save());
            sleep(2);

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getModifiedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);

            $this->assertEquals($meeting1->id, $response['data']['items'][0]['id']);
            $this->assertEquals($super->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting1->name, $response['data']['items'][0]['name']);

            $this->assertEquals($meeting3->id, $response['data']['items'][1]['id']);
            $this->assertEquals($super->id, $response['data']['items'][1]['owner']['id']);
            $this->assertEquals($meeting3->name, $response['data']['items'][1]['name']);

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 2
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getModifiedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(2, $response['data']['currentPage']);

            $this->assertEquals($meeting4->id, $response['data']['items'][0]['id']);
            $this->assertEquals($super->id, $response['data']['items'][0]['owner']['id']);
            $this->assertEquals($meeting4->name, $response['data']['items'][0]['name']);
        }

        /**
         * Test if all deleted items was pulled from read permission tables via API.
         * Please note that here we do not test if data are inserted in read permission tables correctly, that is
         * part of read permission subscription tests
         * @throws NotFoundException
         */
        public function testGetDeletedMeetings()
        {
            $timestamp = time();
            sleep(1);
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $this->deleteAllModelsAndRecordsFromReadPermissionTable('Meeting');
            $job = new ReadPermissionSubscriptionUpdateJob();
            $meeting1 = MeetingTestHelper::createMeetingByNameForOwner('Meeting1', $super);
            $meeting2 = MeetingTestHelper::createMeetingByNameForOwner('Meeting2', $super);
            $meeting3 = MeetingTestHelper::createMeetingByNameForOwner('Meeting3', $super);
            $this->assertTrue($job->run());
            sleep(1);
            $meetingId1 = $meeting1->id;
            $meetingId2 = $meeting2->id;
            $meetingId3 = $meeting3->id;
            $meeting1->delete();
            $meeting2->delete();
            $meeting3->delete();

            $this->assertTrue($job->run());

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );
            $data = array(
                'userId' => $super->id,
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime($timestamp),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 1
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getDeletedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(1, $response['data']['currentPage']);
            $this->assertContains($meetingId1, $response['data']['items']);
            $this->assertContains($meetingId2, $response['data']['items']);

            $data = array(
                'sinceDateTime' => DateTimeUtil::convertTimestampToDbFormatDateTime(0),
                'pagination' => array(
                    'pageSize' => 2,
                    'page'     => 2
                )
            );

            $response = $this->createApiCallWithRelativeUrl('getDeletedItems/', 'POST', $headers, array('data' => $data));
            $response = json_decode($response, true);
            $this->assertEquals(3, $response['data']['totalCount']);
            $this->assertEquals(2, $response['data']['pageSize']);
            $this->assertEquals(2, $response['data']['currentPage']);
            $this->assertContains($meetingId3, $response['data']['items']);
        }

        public function testGetMeetingWithAttendees()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $evelina  = UserTestHelper::createBasicUser('Evelina');
            $amelia  = UserTestHelper::createBasicUser('Amelia');
            $amelia->primaryEmail->emailAddress = 'amelia@example.com';
            $this->assertTrue($amelia->save());
            $contact1 = ContactTestHelper::createContactByNameForOwner('TestContact3', $super);
            $contact2 = ContactTestHelper::createContactByNameForOwner('TestContact4', $super);
            $contact2->primaryEmail->emailAddress = 'aaa@example.com';
            $this->assertTrue($contact2->save());

            $authenticationData = $this->login();
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $meeting = MeetingTestHelper::createMeetingByNameForOwner('Meeting 2 With User Attendees', $super);

            $response = $this->createApiCallWithRelativeUrl('read/?id=' . $meeting->id, 'GET', $headers);
            $response = json_decode($response, true);

            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertTrue(isset($response['data']['attendees']));
            $this->assertTrue(isset($response['data']['attendees']['Organizer']));
            $this->assertEquals($super->id, $response['data']['attendees']['Organizer']['id']);
            $this->assertEquals($super->firstName, $response['data']['attendees']['Organizer']['firstName']);
            $this->assertEquals($super->lastName, $response['data']['attendees']['Organizer']['lastName']);
            $this->assertEquals($super->username, $response['data']['attendees']['Organizer']['username']);
            $this->assertFalse(isset($response['data']['attendees']['Organizer']['email']));

            $meeting->activityItems->add($contact1);
            $meeting->activityItems->add($contact2);
            $this->assertTrue($meeting->save());
            $response = $this->createApiCallWithRelativeUrl('read/?id=' . $meeting->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['attendees']['Contact']));
            $this->assertEquals($contact1->id, $response['data']['attendees']['Contact'][0]['id']);
            $this->assertEquals($contact1->firstName, $response['data']['attendees']['Contact'][0]['firstName']);
            $this->assertEquals($contact1->lastName, $response['data']['attendees']['Contact'][0]['lastName']);
            $this->assertEquals($contact2->id, $response['data']['attendees']['Contact'][1]['id']);
            $this->assertEquals($contact2->firstName, $response['data']['attendees']['Contact'][1]['firstName']);
            $this->assertEquals($contact2->lastName, $response['data']['attendees']['Contact'][1]['lastName']);
            $this->assertEquals($contact2->primaryEmail->emailAddress, $response['data']['attendees']['Contact'][1]['email']);

            $meeting->userAttendees->add($evelina);
            $meeting->userAttendees->add($amelia);
            $this->assertTrue($meeting->save());
            $response = $this->createApiCallWithRelativeUrl('read/?id=' . $meeting->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(2, count($response['data']['attendees']['Contact']));
            $this->assertEquals($contact1->id, $response['data']['attendees']['Contact'][0]['id']);
            $this->assertEquals($contact1->firstName, $response['data']['attendees']['Contact'][0]['firstName']);
            $this->assertEquals($contact1->lastName, $response['data']['attendees']['Contact'][0]['lastName']);
            $this->assertEquals($contact2->id, $response['data']['attendees']['Contact'][1]['id']);
            $this->assertEquals($contact2->firstName, $response['data']['attendees']['Contact'][1]['firstName']);
            $this->assertEquals($contact2->lastName, $response['data']['attendees']['Contact'][1]['lastName']);
            $this->assertEquals(2, count($response['data']['attendees']['User']));
            $this->assertEquals($evelina->id, $response['data']['attendees']['User'][0]['id']);
            $this->assertEquals($evelina->firstName, $response['data']['attendees']['User'][0]['firstName']);
            $this->assertEquals($evelina->lastName, $response['data']['attendees']['User'][0]['lastName']);
            $this->assertEquals($evelina->username, $response['data']['attendees']['User'][0]['username']);
            $this->assertEquals($amelia->id, $response['data']['attendees']['User'][1]['id']);
            $this->assertEquals($amelia->primaryEmail->emailAddress, $response['data']['attendees']['User'][1]['email']);
            $this->assertEquals($amelia->firstName, $response['data']['attendees']['User'][1]['firstName']);
            $this->assertEquals($amelia->lastName, $response['data']['attendees']['User'][1]['lastName']);
            $this->assertEquals($amelia->username, $response['data']['attendees']['User'][1]['username']);
            $this->assertTrue(isset($response['data']['attendees']['Organizer']));
            $this->assertEquals($super->id, $response['data']['attendees']['Organizer']['id']);
            $this->assertEquals($super->firstName, $response['data']['attendees']['Organizer']['firstName']);
            $this->assertEquals($super->lastName, $response['data']['attendees']['Organizer']['lastName']);
            $this->assertEquals($super->username, $response['data']['attendees']['Organizer']['username']);

            // Test with opportunity and account activity items
            $account = AccountTestHelper::createAccountByNameForOwner('Account 2', $super);
            $opportunity = OpportunityTestHelper::createOpportunityByNameForOwner('TestOpportunity 2', $super);
            $meeting2 = MeetingTestHelper::createMeetingByNameForOwner('Meeting 3 With Account and Opportunity', $super);
            $meeting2->activityItems->add($account);
            $meeting2->activityItems->add($opportunity);
            $this->assertTrue($meeting2->save());
            $searchParams = array(
                'pagination' => array(
                    'page'     => 1,
                    'pageSize' => 3,
                ),
                'search' => array(
                    'name' => 'Meeting 3 With Account and Opportunity',
                ),
                'sort' => 'name',
            );
            $searchParamsQuery = http_build_query($searchParams);
            $response = $this->createApiCallWithRelativeUrl('list/filter/' . $searchParamsQuery, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(1, count($response['data']['items']));
            $this->assertEquals($account->id, $response['data']['items'][0]['attendees']['Account'][0]['id']);
            $this->assertEquals($account->name, $response['data']['items'][0]['attendees']['Account'][0]['name']);
            $this->assertEquals($opportunity->id, $response['data']['items'][0]['attendees']['Opportunity'][0]['id']);
            $this->assertEquals($opportunity->name, $response['data']['items'][0]['attendees']['Opportunity'][0]['name']);

            // Test with regular user if he can get user attendees
            $michael = UserTestHelper::createBasicUser('Michael');
            $michael->primaryEmail->emailAddress = 'michael@example.com';
            $this->assertTrue($michael->save());
            $michael->setRight('UsersModule', UsersModule::RIGHT_LOGIN_VIA_WEB_API);
            $michael->setRight('MeetingsModule', MeetingsModule::getAccessRight());
            $michael->setRight('MeetingsModule', MeetingsModule::getCreateRight());
            $saved = $michael->save();
            $this->assertTrue($saved);

            $michaelMeeting = MeetingTestHelper::createMeetingByNameForOwner('Meeting 4 With User', $michael);
            $michaelMeeting->userAttendees->add($evelina);
            $this->assertTrue($michaelMeeting->save());

            $authenticationData = $this->login('michael', 'michael');
            $headers = array(
                'Accept: application/json',
                'ZURMO_SESSION_ID: ' . $authenticationData['sessionId'],
                'ZURMO_TOKEN: ' . $authenticationData['token'],
                'ZURMO_API_REQUEST_TYPE: REST',
            );

            $response = $this->createApiCallWithRelativeUrl('read/?id=' . $michaelMeeting->id, 'GET', $headers);
            $response = json_decode($response, true);
            $this->assertEquals(ApiResponse::STATUS_SUCCESS, $response['status']);
            $this->assertEquals(1, count($response['data']['attendees']['User']));
            $this->assertEquals($evelina->id, $response['data']['attendees']['User'][0]['id']);
            $this->assertEquals($evelina->firstName, $response['data']['attendees']['User'][0]['firstName']);
            $this->assertEquals($evelina->lastName, $response['data']['attendees']['User'][0]['lastName']);
            $this->assertEquals($evelina->username, $response['data']['attendees']['User'][0]['username']);
            $this->assertTrue(isset($response['data']['attendees']['Organizer']));
            $this->assertEquals($michael->id, $response['data']['attendees']['Organizer']['id']);
            $this->assertEquals($michael->firstName, $response['data']['attendees']['Organizer']['firstName']);
            $this->assertEquals($michael->lastName, $response['data']['attendees']['Organizer']['lastName']);
            $this->assertEquals($michael->username, $response['data']['attendees']['Organizer']['username']);
            $this->assertEquals($michael->primaryEmail->emailAddress, $response['data']['attendees']['Organizer']['email']);
        }

        protected function getApiControllerClassName()
        {
            Yii::import('application.modules.meetings.controllers.MeetingApiController', true);
            return 'MeetingsMeetingApiController';
        }

        protected function getModuleBaseApiUrl()
        {
            return 'meetings/meeting/api/';
        }
    }
?>