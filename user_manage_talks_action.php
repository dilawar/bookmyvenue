<?php

include_once 'header.php';
include_once 'database.php';
include_once 'mail.php';
include_once 'tohtml.php';
include_once './check_access_permissions.php';
mustHaveAnyOfTheseRoles( array( 'USER' ) );


if( ! $_POST[ 'response' ] )
{
    // Go back to previous page.
    goToPage( $_SERVER[ 'HTTP_REFERER' ], 0 );
    exit;
}
else if( $_POST[ 'response' ] == 'delete' )
{
    $res = deleteFromTable( 'talks', 'id', $_POST );
    if( $res )
    {
        echo printInfo( 'Successfully delete entry' );
        echo goToPage( $_SERVER[ 'HTTP_REFERER' ], 0 );
        exit;
    }
    else
        echo printWarning( "Failed to delete the talk " );
}
else if( $_POST[ 'response' ] == 'edit' )
{
    echo printInfo( "Here you can only change the host, title and description
        of the talk." );

    $id = $_POST[ 'id' ];
    $talk = getTableEntry( 'talks', 'id', $_POST );

    echo '<form method="post" action="user_manage_talks_action_update.php">';
    echo dbTableToHTMLTable('talks', $talk, 'host,title,description', 'update');
    echo '</form>';
}
else if( $_POST[ 'response' ] == 'schedule' )
{
    echo printInfo( 'Scheduling' );
    $venues = array_map( 
        function( $x ) { return $x['id' ]; }
        ,  getTableEntries( 'venues', 'strength', "has_projector='YES'"  )
        );

    $venueSelect = arrayToSelectList( 'venue', $venues );

    $default = array( "venue" => $venueSelect );
    echo $venueSelect;


}

echo goBackToPageLink( "user_manage_talk.php", "Go back" );
exit;

?>
