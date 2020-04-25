<?php


//OPTIONAL: Un-comment this section to protect your page by requiring a key in the URI
/*
if ($_GET['key'] != "fa554bf790dadf9fd5316ed4b8bcdedc")
    die();
*/
?>

<!DOCTYPE html>
<html>
<head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
</head>
<body>

<?php

//TODO: Insert your WordPress API key here OR use an environment variable to be safer
$api_key = 'APIKEY';

//TODO: Identify the site whose stats you wish to display
$site_id = '123456';

//Set time offset based on local time versus server clock (Optional -- otherwise uncomment the next line)
$timestamp = time() + (60*60*2);
//$timestamp = time();

$today=date('Y-m-d',($timestamp));

//Display a bar graph of the site's stats over the last two weeks
function displayChart($site_id,$api_key,$timestamp,$today)
{
    
    $return = file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=14&limit=-1&end=' . $today);
    $return = str_replace("\",","\",\"",$return);
    $return = str_replace("\n","\",",$return);
    $return = str_replace("\"\"","\"",$return);
    $return = str_getcsv($return);    

    $looper = 3;

    //TODO: Set the scale for your stacked chart so it fits properly
    $scale = 2;

    while ($looper < 30)
    {
        $height = $return[$looper] / $scale;
        
        echo "<img src=black.png width=\"4\" height=\"" . $height . "\">";
        echo "<img src=white.png width=\"1\" height=\"2\">";
        
        $looper = $looper + 2;
    }
}

//Display a summary of the site's performance and the current day's performance
function currentStatsBox($site_id,$api_key,$timestamp,$today)
{
    $return = str_getcsv(file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=1&limit=-1&end=' . $today));

    $todayposts = $return[2];

    $yesterday=date('Y-m-d',($timestamp - 86400));
    $return = s tr_getcsv(file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=1&limit=-1&end=' . $yesterday));
    $yesterdayposts = $return[2];

    $oneweekago=date('Y-m-d',($timestamp - 604800));
    $return = str_getcsv(file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=1&limit=-1&end=' . $oneweekago));
    $oneweekposts = $return[2];

    $return = str_getcsv(file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=7&limit=-1&end=' . $today . '&summarize'));
    $return = substr($return[0],6);
    $weekavg = round(($return)/7);

    $return = str_getcsv(file_get_contents('https://stats.wordpress.com/csv.php?api_key=' . $api_key . '&blog_id=' . $site_id . '&table=views&days=30&limit=-1&end=' . $today . '&summarize'));
    $return = substr($return[0],6);
    $monthavg = round(($return)/30);

    //Prognosticate based on current time, how much left until midnight, and current pageviews
    $miltiplier = round((24 / date("G",$timestamp)),2);
    $prog = $todayposts * $miltiplier;

    if ($todayposts >= $weekavg)
        $color = "DEFDE0";
    elseif ($prog >= $weekavg)
        $color = "FCF7DE";
    else
        $color = "FDDFDF";

    echo "<!-- PROG " . $prog . " MULT " . $miltiplier . " -->";

    echo "<table border=\"1\">";

    echo "<tr><td>";
    echo "Today";
    echo "</td><td width=\"50\" bgcolor=\"#" . $color . "\">";
    echo $todayposts;
    echo "</td><td>";
    echo date("G:i m/d/y",$timestamp);
    echo "</td></tr>";

    echo "<tr><td>";
    echo "vs yesterday";
    echo "</td><td>";
    echo $yesterdayposts;
    echo "</td><td>";
    echo ($todayposts - $yesterdayposts) . " (" . ((round((($todayposts / $yesterdayposts)*100)))-100) . "%)";
    echo "</td></tr>";

    echo "<tr><td>";
    echo "vs last week";
    echo "</td><td>";
    echo $oneweekposts;
    echo "</td><td>";
    echo ($todayposts - $oneweekposts) . " (" . ((round((($todayposts / $oneweekposts)*100)))-100) . "%)";
    echo "</td></tr>";

    echo "<tr><td>";
    echo "7d avg";
    echo "</td><td>";
    echo $weekavg;
    echo "</td><td>";
    echo ($todayposts - $weekavg) . " (" . ((round((($todayposts / $weekavg)*100)))-100) . "%)";
    echo "</td></tr>";

    echo "<tr><td>";
    echo "30d avg";
    echo "</td><td>";
    echo $monthavg;
    echo "</td><td>";
    echo ($todayposts - $monthavg) . " (" . ((round((($todayposts / $monthavg)*100)))-100) . "%)";
    echo "</td></tr>";

    echo "</table>";
}

//Display charts
if ($_GET['type'] == '1')
{
    echo "<table border=\"0\">";
    echo "<tr><td>";
    currentStatsBox($site_id,$api_key,$timestamp,$today);
    echo "</td><td>";
    displayChart($site_id,$api_key,$timestamp,$today);
    echo "</td></tr></table>";
}
elseif ($_GET['type'] == '2')
{
    displayChart($site_id,$api_key,$timestamp,$today);

}
else
{
    currentStatsBox($site_id,$api_key,$timestamp,$today);
    
}
?>

</body>
</html>