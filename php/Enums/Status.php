<?php

/**
 * Status
 */

abstract class Status
{
    public const Success = 'success';
    public const Information = 'information';
    public const Warning = 'warning';
    public const Error = 'error';

    /**
     * GetSumStatus
     */
    public static function getSumStatus($main, $current)
    {
        switch ($main) {
            case Status::Success:
                return $current;
            case Status::Warning:
                if ($current == Status::Error) {
                    return Status::Error;
                } else {
                    return Status::Warning;
                }
            case Status::Error:
                return Status::Error;
        }
    }
}
