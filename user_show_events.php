<?php

include_once( "header.php" );
include_once( "methods.php" );
include_once( "database.php" );
include_once( "tohtml.php" );

echo userHTML( );

?>

<?php


$groups = getEventsOfUser( $_SESSION['user'] );
if( count( $groups ) < 1 )
    echo alertUser( "No upcoming events." );
else 
{
    echo "<h2>You have following upcoming events </h2>";
    echo '<div style="font-size:small">';

    $hide = 'last_modified_on,created_by,external_id,is_public_event' 
                    .  ',calendar_id,calendar_event_id,url';

    foreach( $groups as $group )
    {
        $gid = $group['gid'];
        echo '<form method="post" action="user_show_events_edit.php">';
        echo "<table style=\"width:600px\">";
        echo "<tr><td> <strong>Group id $gid </strong>";
        echo "<button name=\"response\" title=\"Cancel this event\" 
                onclick=\"AreYouSure(this)\" >Cancel Group</button>
                ";
        echo "<button title=\"Edit this event\" name=\"response\" 
                value=\"edit\" font-size=\"small\">Edit Group</button>";
        echo "</td></tr>";
        echo "<input type=\"hidden\" name=\"gid\" value=\"$gid\">";
        echo '</form>';

        $events = getTableEntries( 'events', 'date,start_time', "gid='$gid'" );
        foreach( $events as $event )
        {
            if( $event[ 'status' ] != 'VALID' )
                continue;

            echo '<tr>';
            echo '<td>';
            echo '<form method="post" action="user_show_events_edit.php">';
            echo arrayToTableHTML( $event, 'events', '', $hide );
            echo "<td colspan=\"2\"><button name=\"response\" title=\"Cancel this event\" 
                    onclick=\"AreYouSure(this)\" >" . $symbCancel . "</button>
                </td>";
            echo '</tr>';

            $eid = $event[ 'eid' ];
            echo "<input type=\"hidden\" name=\"eid\" value=\"$eid\">";
            echo "<input type=\"hidden\" name=\"gid\" value=\"$gid\">";
            echo '</form>';

        }

        echo "</table>";
        echo "<br>";
    }
    echo '</div>';
}

echo goBackToPageLink( "user.php", "Go back" );

?>
