<?php

include_once 'header.php';
include_once 'database.php';
include_once 'tohtml.php';
include_once 'methods.php';
include_once 'check_access_permissions.php';

mustHaveAllOfTheseRoles( array( 'AWS_ADMIN' ) );

echo "<h3>Annual Work Seminars Summary</h3>";

function awsOnThisBlock( $awsDays, $block, $blockSize )
{
    foreach( $awsDays as $awsDay )
    {
        $awsWeek = intval( $awsDay / $blockSize );
        if( 0 == ($block - $awsWeek ) )
            return true;
    }
    return false;
}

function daysToLine( $awsDays, $totalDays, $blockSize = 7)
{
    $today = strtotime( 'now' );
    $totalBlocks = intval( $totalDays / $blockSize ) + 1;
    $line = '<td><small>';

    // These are fixed to 4 weeks (a month).
    if( count( $awsDays ) > 0 )
    {
        $line .= intval( $awsDays[0] / 30.41 ) . ',' ;
        for( $i = 1; $i < count( $awsDays ); $i++ )
            $line .=  intval(( $awsDays[ $i ] - $awsDays[ $i - 1 ] ) / 30.41 ) . ',';

        $line .= "</small></td><td>";

        for( $i = 0; $i <= $totalBlocks; $i++ )
        {
            if( awsOnThisBlock( $awsDays, $i, $blockSize ) )
                $line .= '|';
            else
                $line .= '.';
        }
    }
    $line .= "</td>";
    return $line;
}


// Get AWS in roughly last 5 years.
$totalDays = 5 * 365;
$from = date( 'Y-m-d', strtotime( 'now' ) - $totalDays * 24 * 3600 );
//$awses = getAWSFromPast( $from, 'ACTIVE' );
$speakers = getAWSUsers( );

$table = '<table border="0" class="show_aws_summary">';

$table .= '<tr>
    <th></th><th>Name <small>email</small></th>
    <th><small>Months between AWSes</small></th>
    <th>Previous AWSes</th>
    </tr>';

$i = 0;
foreach( $speakers as $speaker )
{
    $i +=1 ;

    $fname = $speaker['first_name'];
    $lname = $speaker['last_name'];
    $login = $speaker['login'];

    $table .= "<tr> <td>$i</td> <td> " . $fname . ' ' . $lname 
                . "<br><small> $login </small>" . "</td>";
    $when = array( );
    $awses = getAwsOfSpeaker( $login );
    foreach( $awses as $aws )
    {
        $awsDay = strtotime( $aws['date'] );
        $ndays = intval(( strtotime( 'today' ) - $awsDay) / (24 * 3600 ));
        array_push( $when, $ndays );
    }

    sort( $when );
    $line = daysToLine( $when, $totalDays, $blockSize = 28 );
    $table .= $line;
    $table .= "</tr>";

}

$table .= "</table>";
echo $table;

echo goBackToPageLink( "admin_aws.php", "Go back" );

?>