<?php
if (!session_id()) {
  session_start();
}

require 'vendor/autoload.php';

$fb = new Facebook\Facebook([
  'app_id'=>'346578099397815',
  'app_secret' => '6990705a0b8a89143b9a850505e923d3',
  'default_graph_version' => 'v3.2'
]);

//Fetch url login
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Optional permissions
$login_url = $helper->getLoginUrl('https://ziki.hng.tech/Authentication/auth/fbook.php', $permissions);
//$login_url = $helper->getLoginUrl('https://localhost/ziki/Authentication/auth/fbook.php', $permissions);


try{
  $access_token = $helper->getAccessToken();
  if(isset($access_token)){
    $_SESSION['accesstoken'] = (string)$access_token;
    header("Location: https://ziki.hng.tech/home.php");
    //header("Location: https://localhost/ziki/home.php");

  }
  if(isset($_SESSION['accesstoken'])){
    
    try{
        $fb->setDefaultAccessToken($_SESSION['accesstoken']);
        $result = $fb->get('/me?local=en_US&fields=name,email');
        $user = $result->getGraphUser();




        $name = $user->getField('name');
       // $id = $user->getField('id');
        $email = $user->getField('email');
        $response = array();
        $user = array();
        $user[] = array('Name' => $name, 'Email' => $email, 'Access Token' => $_SESSION['accesstoken']);
        $response['user'] = $user;
        $fp = fopen('settings.json', 'w');
        fwrite($fp, json_encode($response));
        fclose($fp);
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        //echo  $user->getField('name');
       // echo   $user->getField('email');
        //echo  $_SESSION['access_token'];

    } catch(Exception $e){
      //echo $e->getTraceAsString();
      return $e->getTraceAsString();
    }
  }
}
  catch(Exception $e){
    echo $e->getTraceAsString();
  }


?>