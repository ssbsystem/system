<?php

/**
 * Path
 */

abstract class Path
{
    /** Data **/
    /* json */
    const VersionsData_json = 'Data/JSON/VersionsData.json';

    /** Enums **/
    const SQLStatement_php  = 'Enums/SQLStatement.php';
    const Status_php  = 'Enums/Status.php';
    const Session_php  = 'Enums/Session.php';
    const IndexingType_php  = 'Enums/IndexingType.php';

    /** Interfaces **/
    const GeneralClass_php = 'Interfaces/GeneralClass.php';

    /** Modules **/
    /* Objects */
    const Query_php = 'Modules/Objects/Query.php';
    const Message_php = 'Modules/Objects/Message.php';

    //SQL
    const SQLTable_php = 'Modules/Objects/SQL/SQLTable.php';

    //Statements
    const Select_php = 'Modules/Objects/Statements/Select.php';
    const Insert_php = 'Modules/Objects/Statements/Insert.php';


    /** Connectors **/
    const Connect_php  = 'Modules/Connect.php';
}
