<?php

include_once './check_access_permissions.php';
mustHaveAllOfTheseRoles( array( 'USER' ) );

include_once 'database.php';
include_once 'tohtml.php';

echo userHTML( );

echo "<h3>You are updating your upcoming AWS </h3>";
echo alertUser( "If you can't find supervisors/TCM members in drop down menu,
    you'd have to go back and add them. The facility to do so is in your home 
    page (<tt>My Home</tt> link in top-right corner)." );

echo printInfo( "<strong>DO NOT COPY/PASTE from Office/Word/Webpage/etc.</strong>
    <p>
    Copy/paste from many applications insert special characters which causes my 
    system to break. If you paste from other application, select all the text and 
    execute <strong>Format -> Clear formatting</strong> once.
    </p>
    " );

echo "<br><br>";

if( $_POST[ 'response' ] == 'update' )
{
    $awsId = $_POST[ 'id' ];
    $aws = getUpcomingAWSById( $awsId );

    echo '<form method="post" action="user_aws_update_upcoming_aws_submit.php">';
    echo editableAWSTable( -1, $aws );
    echo '<input type="hidden", name="id" value="' . $awsId . '">';
    echo '</form>';
}

echo goBackToPageLink( 'user_aws.php', 'Go back' );

?>
