<?php

/**
 * SQL Statement
 */

abstract class SQLStatement
{
    const Unknown = 'UNKNOWN';
    const Select = 'SELECT';
    const Insert = 'INSERT';
    const Update = 'UPDATE';
    const Delete = 'DELETE';
    const AddColumn = 'ADDCOLUMN';
    const DropColumn = 'DROPCOLUMN';
    const CreateTable = 'CREATETABLE';
    const DropTable = 'DROPTABLE';
}