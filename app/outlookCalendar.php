<?php

class outlookCalendar {
    
    private static $clientId = "0af5f3f4-427a-4834-aaab-554e0697153d";
    private static $clientSecret = "2Pb4FU3KD44rqOfm91w7oAi";
    private static $authority = "https://login.microsoftonline.com";
    private static $authorizeUrl = '/common/oauth2/v2.0/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code&scope=%3$s';
    private static $tokenUrl = "/common/oauth2/v2.0/token";
    // The app only needs openid (for user's ID info), and Mail.Read
    private static $scopes = array("openid", "offline_access",
        "https://outlook.office.com/mail.read", "https://outlook.office.com/calendars.readwrite");
    private static $outlookApiUrl = "https://outlook.office.com/api/v2.0";
    public static function getRedirectUrl()
    {
        return str_replace('http', 'https', Yii::app()->getBaseUrl(true)).'/index.php/users/default/addOutlook';
    }

    public static function getLoginUrl($redirectUri) {
        // Build scope string. Multiple scopes are separated
        // by a space
        $scopestr = implode(" ", self::$scopes);

        $loginUrl = self::$authority . sprintf(self::$authorizeUrl, self::$clientId, urlencode($redirectUri), urlencode($scopestr));

        yii::log("Generated login URL: " . $loginUrl);
        return $loginUrl;
    }

    public static function getTokenFromAuthCode($grantType, $authCode, $redirectUri) {
        // Build the form data to post to the OAuth2 token endpoint
        $parameter_name = $grantType;
        if (strcmp($parameter_name, 'authorization_code') == 0) {
          $parameter_name = 'code';
        }
        $token_request_data = array(
            "grant_type" => $grantType,
            $parameter_name => $authCode,
            "redirect_uri" => $redirectUri,
            "scope" => implode(" ", self::$scopes),
            "client_id" => self::$clientId,
            "client_secret" => self::$clientSecret
        );       

        // Calling http_build_query is important to get the data
        // formatted as expected.
        $token_request_body = http_build_query($token_request_data);
        yii::log("Request body: " . $token_request_body);

        $curl = curl_init(self::$authority . self::$tokenUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);

        $response = curl_exec($curl);
        yii::log("curl_exec done.");
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        yii::log("Request returned status " . $httpCode);
        if ($httpCode >= 400) {
            return array('errorNumber' => $httpCode,
                'error' => 'Token request returned HTTP error ' . $httpCode);
        }

        // Check error
        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);
        if ($curl_errno) {
            $msg = $curl_errno . ": " . $curl_err;
            yii::log("CURL returned an error: " . $msg);
            return array('errorNumber' => $curl_errno,
                'error' => $msg);
        }

        curl_close($curl);

        // The response is a JSON payload, so decode it into
        // an array.
        $json_vals = json_decode($response, true);
        yii::log("TOKEN RESPONSE:");
        foreach ($json_vals as $key => $value) {
            yii::log("  " . $key . ": " . $value);
        }
        return $json_vals;
    }

    public static function getUserEmailFromIdToken($idToken) {
        yii::log("ID TOKEN: " . $idToken);

        // JWT is made of three parts, separated by a '.' 
        // First part is the header 
        // Second part is the token 
        // Third part is the signature 
        $token_parts = explode(".", $idToken);

        // We care about the token
        // URL decode first
        $token = strtr($token_parts[1], "-_", "+/");
        // Then base64 decode
        $jwt = base64_decode($token);
        // Finally parse it as JSON
        $json_token = json_decode($jwt, true);
        return $json_token['preferred_username'];
    }
    
    public static function getUser($access_token) {
        $getUserParameters = array (
          // Only return the user's display name and email address
          "\$select" => "DisplayName,EmailAddress"
        );

        $getUserUrl = self::$outlookApiUrl."/Me?".http_build_query($getUserParameters);

        return self::makeApiCall($access_token, "", "GET", $getUserUrl);
    }
    
    public static function makeApiCall($access_token, $user_email, $method, $url, $payload = NULL) {
        // Generate the list of headers to always send.
        $headers = array(
            "User-Agent: outlook/1.0", // Sending a User-Agent header is a best practice.
            "Authorization: Bearer " . $access_token, // Always need our auth token!
            "Accept: application/json", // Always accept JSON response.
            "client-request-id: " . self::makeGuid(), // Stamp each new request with a new GUID.
            "return-client-request-id: true", // Tell the server to include our request-id GUID in the response.
            "X-AnchorMailbox: " . $user_email         // Provider user's email to optimize routing of API call
        );
        
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $url);

        switch (strtoupper($method)) {
            case "GET":
                // Nothing to do, GET is the default and needs no
                // extra headers.
                yii::log("Doing GET");
                break;
            case "POST":
                yii::log("Doing POST");
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "PATCH":
                yii::log("Doing PATCH");
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "DELETE":
                yii::log("Doing DELETE");
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                yii::log("INVALID METHOD: " . $method);
                exit;
        }
        
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
        $response = curl_exec($curl);
        yii::log("curl_exec done.");

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        yii::log("Request returned status " . $httpCode);
        
        if(curl_errno($curl)){   
            echo 'Curl error: ' . curl_error($curl);
        }
        else {
            yii::log('Curl error: NO');
        }
        
        if ($httpCode >= 400) {
            return array('errorNumber' => $httpCode,
                'error' => 'Request returned HTTP error ' . $httpCode);
        }
        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);
        if ($curl_errno) {
            $msg = $curl_errno . ": " . $curl_err;
            yii::log("CURL returned an error: " . $msg);
            curl_close($curl);
//            mail('jagajeevan@ideas2it.com','From: Outlook Error - '.date('d-m-Y'), $msg);
            return array('errorNumber' => $curl_errno,
                'error' => $msg);
        } else {
            yii::log("Response: " . $response);
            curl_close($curl);
            $return_arr = json_decode($response, true);
//            mail('jagajeevan@ideas2it.com','From: Outlook success - '.date('d-m-Y'), print_r($return_arr, true));
            return $return_arr;
        }
    }

    // This function generates a random GUID.
    public static function makeGuid() {
        if (function_exists('com_create_guid')) {
            yii::log("Using 'com_create_guid'.");
            return strtolower(trim(com_create_guid(), '{}'));
        } else {
            yii::log("Using custom GUID code.");
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12);

            return $uuid;
        }
    }
    
    public static function getUserName($id_token) {
        $token_parts = explode(".", $id_token);

        // First part is header, which we ignore
        // Second part is JWT, which we want to parse
        yii::log("getUserName found id token: ".$token_parts[1]);

        // First, in case it is url-encoded, fix the characters to be 
        // valid base64
        $encoded_token = str_replace('-', '+', $token_parts[1]);
        $encoded_token = str_replace('_', '/', $encoded_token);
        yii::log("After char replace: ".$encoded_token);

        // Next, add padding if it is needed.
        switch (strlen($encoded_token) % 4){
          case 0:
            // No pad characters needed.
            yii::log("No padding needed.");
            break;
          case 2:
            $encoded_token = $encoded_token."==";
            yii::log("Added 2: ".$encoded_token);
            break;
          case 3:
            $encoded_token = $encoded_token."=";
            yii::log("Added 1: ".$encoded_token);
            break;
          default:
            // Invalid base64 string!
            yii::log("Invalid base64 string");
            return null;
        }

        $json_string = base64_decode($encoded_token);
        yii::log("Decoded token: ".$json_string);
        $jwt = json_decode($json_string, true);
        yii::log("Found user name: ".$jwt['name']);
        return $jwt['name'];
    }

    public static function getMessages($access_token, $user_email) {
        $getMessagesParameters = array(
            // Only return Subject, ReceivedDateTime, and From fields
            "\$select" => "Subject,ReceivedDateTime,From",
            // Sort by ReceivedDateTime, newest first
            "\$orderby" => "ReceivedDateTime DESC",
            // Return at most 10 results
            "\$top" => "10"
        );

        $getMessagesUrl = self::$outlookApiUrl . "/Me/Messages?" . http_build_query($getMessagesParameters);

        return self::makeApiCall($access_token, $user_email, "GET", $getMessagesUrl);
    }

    public static function getEvents($access_token, $user_email) {
        $getEventsParameters = array(
            // Only return Subject, Start, and End fields
            "\$select" => "Subject,Start,End",
            // Sort by Start, oldest first
            "\$orderby" => "Start/DateTime",
            // Return at most 10 results
            "\$top" => "10"
        );

        $getEventsUrl = self::$outlookApiUrl . "/Me/Events?" . http_build_query($getEventsParameters);

        return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }
    
    public static function addEventToCalendar($access_token, $user_email, $subject, $location, $startTime, $endTime, $description, $attendeeArr, $requestFrom, $syncId) {
        // Generate the JSON payload
        $event = array(
          "Subject" => $subject,
          "Location" => array("DisplayName" => $location),
          "Start" => array('DateTime' => $startTime,  'TimeZone' => 'India Standard Time'),
          "End" => array('DateTime' => $endTime,  'TimeZone' => 'India Standard Time'),
          "Body" => array("ContentType" => "HTML", "Content" => $description)
        );

        if (count($attendeeArr)) {
            yii::log("Attendees included: ".print_r($attendeeArr));
            $attendees = array();
            foreach($attendeeArr as $address) {
                $attendee = array(
                  "EmailAddress" => array ("Address" => $address),
                  "Type" => "Required"
                );
                $attendees[] = $attendee;
            }
            $event["Attendees"] = $attendees;
        }
        else
        {
            /**
             * Modified by : Murugan M
             * Modified date : Sep 15 2016
             * Description : Meeting creation to not added any attendies then the following else condition will add the attendees list to meeting creator email
             */
            $event["Attendees"][0] = array(
                                    "EmailAddress" => array ("Address" => $user_email),
                                    "Type" => "Required"
                                  );
        }
        
        if($requestFrom == 'from_edit')
        {
            return self::updateEventCalendar($access_token, $user_email, $event, $syncId);
        }
        
        $eventPayload = json_encode($event);
        $createEventUrl = self::$outlookApiUrl."/Me/Events";
        $response = self::makeApiCall($access_token, $user_email, "POST", $createEventUrl, $eventPayload);
        // If the call succeeded, the response should be a JSON representation of the
        // new event. Try getting the Id property and return it.        
        
        if(isset($response['Id']))
        {
            if ($response['Id']) {
              return $response['Id'];
            }
            else {
              yii::log("ERROR: ".print_r($response,true));
              return $response;
            }
        }
        else {
            yii::log("ERROR: ".print_r($response,true));
            return;
        }
    }
    
    public static function deleteEventCalendar($access_token, $user_email, $eventId) {
        yii::log(' Inside deleteEventCalendar');
        $eventPayload = '';
        $deleteEventUrl = self::$outlookApiUrl."/Me/Events/$eventId";

        $response = self::makeApiCall($access_token, $user_email, "DELETE", $deleteEventUrl, $eventPayload);
        // If the call succeeded, the response should be a JSON representation of the
        // new event. Try getting the Id property and return it.
        if(isset($response['Id']))
        {
            if ($response['Id']) {
              return $response['Id'];
            }
            else {
              yii::log("ERROR: ".$response);
              return $response;
            }
        }
    }
    
    public static function updateEventCalendar($access_token, $user_email, $meetingArr, $syncId) {
        $eventPayload = json_encode($meetingArr);
        $updateEventUrl = self::$outlookApiUrl."/Me/Events/$syncId";
        $response = self::makeApiCall($access_token, $user_email, "PATCH", $updateEventUrl, $eventPayload);
        // If the call succeeded, the response should be a JSON representation of the
        // new event. Try getting the Id property and return it.
        if(isset($response['Id']))
        {
            if ($response['Id']) {
              return $response['Id'];
            }
            else {
              yii::log("ERROR: ".$response);
              return $response;
            }
        }
    }

    public static function encodeDateTime($dateTime) {
        $utcDateTime = $dateTime->setTimeZone(new DateTimeZone("UTC"));

        $dateFormat = "Y-m-d\TH:i:s\Z";
        return date_format($utcDateTime, $dateFormat);
    }
    
    public static function getTokenFromRefreshToken($refreshToken, $redirectUri) {
        return self::getTokenFromAuthCode("refresh_token", $refreshToken, $redirectUri);
    }
    
    public static function getAccessToken($refresh_token, $redirectUri='') {
        $redirectUri = self::getRedirectUrl();
        if(isset($_SESSION['access_token']))
        {
            $current_token = $_SESSION['access_token'];
        }
        else
        {
            $new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);          
            // Update the stored tokens and expiration
            if(isset($new_tokens['access_token']))
            {
                $_SESSION['access_token'] = $new_tokens['access_token'];
                $_SESSION['refresh_token'] = $new_tokens['refresh_token'];

                // expires_in is in seconds
                // Get current timestamp (seconds since Unix Epoch) and
                // add expires_in to get expiration time
                // Subtract 5 minutes to allow for clock differences
                $expiration = time() + $new_tokens['expires_in'] - 300;
                $_SESSION['token_expires'] = $expiration;

                // Return new token
                return $new_tokens['access_token'];
            }
            else {
                return;
            }
        }
        if (!is_null($current_token)) {
          // Check expiration
          $expiration = $_SESSION['token_expires'];
          if ($expiration < time()) {
            yii::log('Token expired! Refreshing...');
            // Token expired, refresh
            $refresh_token = $_SESSION['refresh_token'];
            $new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);
            
            if(isset($new_tokens['access_token']))
            {
                // Update the stored tokens and expiration
                $_SESSION['access_token'] = $new_tokens['access_token'];
                $_SESSION['refresh_token'] = $new_tokens['refresh_token'];

                // expires_in is in seconds
                // Get current timestamp (seconds since Unix Epoch) and
                // add expires_in to get expiration time
                // Subtract 5 minutes to allow for clock differences
                $expiration = time() + $new_tokens['expires_in'] - 300;
                $_SESSION['token_expires'] = $expiration;

                // Return new token
                return $new_tokens['access_token'];
            }
            else {
                return;
            }
          }
          else {
            // Token is still valid, return it
            return $current_token;
          }
        } 
        else {
          return null;
        }
    }

    
    public static function getToken($grantType, $code, $redirectUri) {
        $parameter_name = $grantType;
        if (strcmp($parameter_name, 'authorization_code') == 0) {
          $parameter_name = 'code';
        }

        // Build the form data to post to the OAuth2 token endpoint
        $token_request_data = array(
          "grant_type" => $grantType,
          $parameter_name => $code,
          "redirect_uri" => $redirectUri,
          "scope" => implode(" ", self::$scopes),
          "client_id" => self::$clientId,
          "client_secret" => self::$clientSecret
        );

        // Calling http_build_query is important to get the data
        // formatted as expected.
        $token_request_body = http_build_query($token_request_data);
        yii::log("Request body: ".$token_request_body);

        $curl = curl_init(self::$authority.self::$tokenUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);

        $response = curl_exec($curl);
        yii::log("curl_exec done.");

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        yii::log("Request returned status ".$httpCode);
        if ($httpCode >= 400) {
          return array('errorNumber' => $httpCode,
                        'error' => 'Token request returned HTTP error '.$httpCode);
        }

        // Check error
        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);
        if ($curl_errno) {
          $msg = $curl_errno.": ".$curl_err;
          yii::log("CURL returned an error: ".$msg);
          return array('errorNumber' => $curl_errno,
                        'error' => $msg);
        }

        curl_close($curl);

        // The response is a JSON payload, so decode it into
        // an array.
        $json_vals = json_decode($response, true);
        yii::log("TOKEN RESPONSE:");
        foreach ($json_vals as $key=>$value) {
          yii::log("  ".$key.": ".$value);
        }

        return $json_vals;
    }
}