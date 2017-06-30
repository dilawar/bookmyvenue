<?php
include_once( "header.php" );
include_once( "methods.php" );
include_once( 'tohtml.php' );
include_once( "check_access_permissions.php" );
mustHaveAnyOfTheseRoles( Array( 'USER' ) );

echo userHTML( );

$user = $_SESSION[ 'user' ];

if( $_POST )
{
    $data = array( 'speaker' => $user );
    $data = array_merge( $_POST, $data );
    echo( "Sending your acknowledgment to database " );
    $res = updateTable( 'upcoming_aws', 'id,speaker', 'acknowledged', $data );
    if( $res )
    {
        echo printInfo( 
            "You have successfully acknowledged your AWS schedule. 
            Please mark your calendar as well." 
        );

        $email = "<p>" . loginToHTML( $user ) . " has just acknowledged his/her AWS date. </p>";
        $emial .= "<p>" . humanReadableDate( 'now' ) . "</p>";
        $subject = "$user has acknowledged his/her AWS date";

        $to = 'hippo@lists.ncbs.res.in';
        sendPlainTextEmail( $email, $subject, $to );

        goToPage( "user_aws.php", 1 );
        exit;
    }
    else
    {
        echo printWarning( "Failed to update database ..." );
    }
}

echo goBackToPageLink( "user_aws.php", "Go back" );

?>