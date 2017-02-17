<?php

include_once 'database.php';
include_once './check_access_permissions.php';

mustHaveAllOfTheseRoles( array( "AWS_ADMIN" ) ); 

var_dump( $_POST );

$res = updateTable( 'upcoming_aws', 'id', 'abstract', $_POST );
if( $res )
{
    echo printInfo( "Successfully updated abstract of upcoming AWS entry" );
    echo goToPage( "admin_aws_manages_upcoming_aws.php",  1 );
    exit;
}
else
    echo minionEmbarrassed( "I could not update abstract of upcoming AWS" );

echo goBackToPageLink( "admin_aws_manages_upcoming_aws.php", "Go back" );


?>
