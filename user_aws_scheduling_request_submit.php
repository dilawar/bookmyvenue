<?php 

include_once 'header.php';
include_once 'database.php';
include_once 'mail.php';
include_once 'tohtml.php';
include_once 'check_access_permissions.php';

mustHaveAnyOfTheseRoles( array( 'USER' ) );
// Logic to add your preference.

$sendEmail = true;

if( $_POST[ 'response' ] == 'submit' )
{
    // Check if preferences are available.
    $firstPref = __get__( $_POST, 'first_preference', '' );
    $secondPref = __get__( $_POST, 'second_preference', '' );
    $keys = 'id,speaker,reason,created_on';
    $updateKeys = 'created_on,reason';

    // check if dates are monday. If not assign next monday.
    $firstPref = nextMonday( $firstPref );
    $secondPref = nextMonday( $secondPref );

    if( $firstPref )
    {
        $prefDate = dbDate( $firstPref );
        if( strtotime( 'next monday' ) >= strtotime( $prefDate) )
            echo printInfo( "I can not change the past without Time Machine. 
                Ignoring " . humanReadableDate( $prefDate ) );
        else
        {
            $upcomingAWSs = getTableEntries( 
                'upcoming_aws', 'date', "date='$prefDate'" 
                );
            if( count( $upcomingAWSs ) == 3 )
                echo printInfo( "Date $prefDate is not available. Ignoring ..." );
            else
            {
                $keys .= ',first_preference';
                $updateKeys .= ',first_preference';
            }
        }
    }

    if( $secondPref )
    {
        $prefDate = dbDate( $secondPref );
        if( strtotime( 'next monday' ) >= strtotime( $prefDate) )
            echo printInfo( "I can not change the past without Time Machine. 
                Ignoring " . humanReadableDate( $prefDate ) );
        else
        {
            $upcomingAWSs = getTableEntries( 
                'upcoming_aws', 'date', "date='$prefDate'" 
                );

            if( count( $upcomingAWSs ) == 3 )
                echo printInfo( "Date $prefDate is not available. Ignoring ..." );
            else
            {
                $keys .= ",second_preference";
                $updateKeys .= ",second_preference";
            }
        }
    }

    $res = insertOrUpdateTable(
                'aws_scheduling_request', $keys, $updateKeys, $_POST
            );

    if( $res )
    {
        // Store id, it is needed to send email.
        $_POST[ 'id' ] = $res[ 'id' ];
        echo printInfo( "I have recorded your preferences." );
    }
    else
        $sendEmail = false;

    // Create subject for email
    $subject = "Your preferences for AWS schedule  has been recieved";
}
else if( $_POST[ 'response' ] == 'delete' )
{
    $table = getTableEntry( 'aws_scheduling_request', 'id', $_POST );
    if( $table )
        $_POST = array_merge( $_POST, $table );
    $_POST[ 'status' ] = 'CANCELLED';

    $res = updateTable( 'aws_scheduling_request', 'id'
                , 'status', $_POST );
    if( $res )
    {
        echo printInfo( "Sucessfully cancelled your request" );
        $subject = "You have cancelled your AWS preference";
    }
    else
        $sendEmail = false;
}


if( $sendEmail )
{
    $to = getLoginEmail( $_POST[ 'speaker' ] );
    $table = getTableEntry( 
        'aws_scheduling_request', 'id' , array( 'id' => $_POST[ 'id' ] ) 
        );


    if( ! $table )
    {
        echo minionEmbarrassed( "Could not fetch your preference.." );
    }
    else
    {
        $options = array( 
            'USER' => loginToText( $_POST[ 'speaker' ] )
            , 'EMAIL_BODY' => arrayToVerticalTableHTML( $table, 'info' )
            );
        $templ = emailFromTemplate( 'user_create_request', $options );

        sendPlainTextEmail(
            $templ['email_body'], $subject, $to, $templ['cc'] 
            ); 
    }
}

echo goBackToPageLink( "user_aws.php", "Go back" );

?>