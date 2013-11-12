<?php

//Getting user ids from github

date_default_timezone_set('Europe/Amsterdam');

//the way pagination works is, you get 100 results per query, and to get the next page, you need to start from the last id you got
$next=-1;

function getUsers($next)
{
$token = "APPTOKEN";
$url = "https://api.github.com/users?since=";

$request = curl_init();

curl_setopt($request, CURLOPT_URL, $url.$next);
curl_setopt($request, CURLOPT_HTTPHEADER, array('Authorization: token '.$token, "Content-Type: application/json"));
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

$content = curl_exec($request);
$results=json_decode($content, true);
return $results;
}


//You can make 5.000 calls per hour
for($i = 0; $i < 4200; $i++){
unset($ids);
$ids=array();
$call = getUsers($next);
$con = mysql_connect('127.0.0.1','root','');
 
	if (!$con){
		die('Could not connect: ' . mysql_error());
	}
foreach($call as $item)
{
  //I put all the ids in an array and then get the last element to paginate
  $ids[]=$item["id"];
  $next=end($ids);
  $login=mysql_real_escape_string($item["login"]);
  $id=$item["id"];
  $avatar_url=mysql_real_escape_string($item["avatar_url"]);
  $url=mysql_real_escape_string($item["url"]);
  $html_url=mysql_real_escape_string($item["html_url"]);
  $type=mysql_real_escape_string($item["type"]);
  $site_admin=$item["site_admin"];
  
  //I am just inserting everything into a mysql table
  mysql_select_db("github", $con);
  $query = "INSERT into users VALUES ('$login','$id','$avatar_url','$url','$html_url','$type','$site_admin')";
  $result = mysql_query($query);

  if ($result === false) {
    // An error has occured...
    echo mysql_error();
}

}
mysql_close($con);	

}
	

?>
