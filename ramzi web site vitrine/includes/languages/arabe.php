<?php
function lang($phrase)
{
    static $lang = array(
        'MSG' => 'مرحبا'
    );

    return $lang[$phrase];
}
