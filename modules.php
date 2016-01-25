<?php
require 'class.iCalReader.php';

$url = 'http://www.hackjack.info/et/' . $_POST["semester"] . '_A/ical';

if(get_headers($url)[0] != 'HTTP/1.1 200 OK')
{
    echo 'error';
}
else
{
    $ical = new ical($url);

    $modules= array();

    $sub = array("Cours", "cours", "/", "TD", "td", "TP", "tp", "Machine");

    foreach($ical->events() as $event)
        $modules[substr($event["SUMMARY"], -9, -1)] = ltrim(str_replace($sub, "", substr($event["SUMMARY"], 0, -11)));

    $modules = array_unique($modules);
    ksort($modules);

    $modulesCheckboxes = '';

    foreach($modules as $key => $module)
    {
        $modulesCheckboxes .= ' <div class="checkbox">
                                    <label for="checkboxes-' . $key . '">
                                        <input name="modules[]" id="checkboxes-' . $key . '" value="' . $key . '" type="checkbox">
                                        ' . $module . '
                                    </label>
                                </div>';
    }

    echo $modulesCheckboxes;
}
?>