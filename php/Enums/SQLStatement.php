<?php

/**
 * SQL Statement
 */

abstract class SQLStatement
{
    const Select = 0;
    const Insert = 1;
    const Update = 2;
    const Delete = 3;
    const AddColumn = 4;
    const DropColumn = 5;
    const CreateTable = 6;
    const DropTable = 7;
}