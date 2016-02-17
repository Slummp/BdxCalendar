<?php
// Librairie du parser d'iCal
require 'class.iCalReader.php';

setlocale(LC_ALL, 'fr_FR');
$ical = new ical('http://127.0.0.1/BdxCalendar/calendar.php?semester=IN601&group=2&filters=J1IN6014,J1IN6016,J1INPM01,N1MA6W31&anglais=450&groupBD=1&groupAlgo=0&groupAS=4');

$nb_events = $ical->event_count;

$events = $ical->events();

if(!isset($_GET['semaine']))
    $_GET['semaine'] = 0;

$current_date = strtotime('+' . $_GET['semaine'] . ' week', time());
$current_day = strtotime('last monday', strtotime('+3 day', $current_date));
$timestamp_ics = strftime('%Y%m%d', $current_day);

$last_day = strftime('%Y%m%d', strtotime('+4 days', $current_day));

$i = 0;

while(intval(substr($events[$i]['DTSTART'], 0, -8)) < intval($timestamp_ics))
{
    unset($events[$i]);
    $i++;
}

$i = $nb_events - 1;

while(intval(substr($events[$i]['DTSTART'], 0, -8)) > intval($last_day))
{
    unset($events[$i]);
    $i--;
}

$days = array();

foreach($events as $event)
{

}

$events = array_values($events);

echo strftime('Semaine %V', $current_date);
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- Métas-datas -->
        <meta charset="UTF-8">

        <!-- Favicon -->
        <link rel="shortcut icon" href="images/favicon.png" />

        <!-- CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/style.css" />

        <!-- Titre -->
        <title>Emploi du temps - Université Bordeaux - Licence Informatique</title>
    </head>

    <body>
        <h1>
            <?php echo strftime('Semaine %V', $current_day) . '<br>';
            /* equivalent ->

            <?= strftime(); ?>

            */
            ?>
        </h1>

        <nav>
            <ul class="pager">
                <li class="previous">
                    <a href="<?php echo $_SERVER['PHP_SELF'] . "?semaine=" . ($_GET['semaine'] - 1); ?>">
                        <span aria-hidden="true">&larr;</span>
                        Semaine précédente
                    </a>
                </li>
                <li class="next">
                    <a href="<?php echo $_SERVER['PHP_SELF'] . "?semaine=" . ($_GET['semaine'] + 1); ?>">
                        Semaine suivante
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="schedule">
            <div class="hours">
                <span class="week_day"></span>

                <?php
                foreach(range(8, 20) as $hour)
                {
                    ?>
                    <div class="hour">
                        <?php echo $hour; ?>h00
                    </div>
                    <?php
                }
                ?>
            </div>

            <?php
            foreach(range(0, 4) as $day)
            {
                ?>
                <div class="day">
                    <span class="week_day<?php if(time() >= $current_day && time() < ($current_day + 86400)) echo " current_day"; ?>">
                        <?php echo strftime('%A', $current_day) . " (" . strftime('%d/%m/%Y', $current_day) . ")"; ?>
                    </span>

                    <div class="event" style="flex: 48">
                        <span class="event_content">test 1</span>
                    </div>
                    <div class="event" style="flex: 73">
                        <span class="event_content">test 2</span>
                    </div>
                    <div class="event" style="flex: 35">
                        <span class="event_content">test 3</span>
                    </div>
                </div>
                <?php
                $current_day = strtotime('+1 day', $current_day);
            }
            ?>
        </div>
    </body>
</html>
