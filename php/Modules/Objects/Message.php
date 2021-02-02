<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */

/**
 * Message
 */
abstract class Message
{
    public static function Create($funcionName, $status, $message)
    {
        return [
            $funcionName => [
                'Status' => $status,
                'Messagee' => $message
            ]
        ];
    }
}
