<?php

include_once 'methods.php';
include_once 'tohtml.php';
include_once 'database.php';
include_once 'check_access_permissions.php';
include_once './calendar/NCBSCalendar.php';

// We come here from google-calendar 
// When we come here from ./authenticate_gcalendar.php page, the GOOGLE API 
// sends us a GET response. Use this token to process all other queries.

// Find event in list of events but comparing summary.
function findEvent( $events, $googleEvent )
{
    $found = false;
    foreach( $events as $e )
    {
        // Database event compared with google event summary.
        //echo "<pre>Comparing " . $e[ 'calendar_event_id' ] . " and " . 
        //$googleEvent['id'] . "</pre><br/>" ;

        if( $e[ 'calendar_event_id' ] == $googleEvent[ 'id' ] )
        {
            $found = true;
            break;
        }
    }

    return $found;
}


function synchronize_google_calendar( )
{
    $conf = getConf( );
    $calendar = new NCBSCalendar( $conf[ 'google calendar']['calendar_id'] );
    $everythingWentOk = true;

    echo alertUser( "Synchronizing google calendar ..." );

    // Get the list of public events.
    $publicEvents = getPublicEvents( 'today', 'VALID', 14 );
    $total = count( $publicEvents );

    // Update all public events first.
    echo printInfo( "Putting local update to google calendar " );
    for ($i = 0; $i < $total; $i++) 
    {
        $event = $publicEvents[ $i ];
        try {
            if( $calendar->exists( $event ) )
                $gevent = $calendar->updateEvent( $event );
            else 
                $gevent = $calendar->addNewEvent( $event );
        } catch ( Exception $e ) {
            echo printWarning( "Failed to add or update event: " . $e->getMessage( ) );
        }

    }

    // Now get all events from google calendar and if some of them are not 
    // in database, remove them if they are not available locally. This 
    // means some events have been deleted locally, they should be deleted 
    // from calendar as well.
    $eventsOnGoogleCalendar = $calendar->getEvents( $from = 'today' );
    $total = count( $eventsOnGoogleCalendar );
    $i = 0;

    // Make sure you get the list of events here as well. Because if a new 
    // events was added before, it was assigned a event id in hippo 
    // database.
    $publicEvents = getPublicEvents( 'today', 'VALID', 14 );
    foreach( $eventsOnGoogleCalendar as $event )
    {
        if( findEvent( $publicEvents, $event ) )
            continue;           // We are good.
        else
        {
            echo printInfo( "Deleting event: " . $event[ 'summary' ] . 
                " because this event is not found in local database " );
            echo "</br>";
            $calendar->deleteEvent( $event );
            ob_flush(); flush( );
        }

    }
}

?>

