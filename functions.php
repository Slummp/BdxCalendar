<?php
function in_arrays($needles, $haystack)
{
    foreach ((array) $needles as $needle)
    {
        if (in_array($needle, $haystack) === true)
        {
            return true;
        }
    }

    return false;
}
?>
