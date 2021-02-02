<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */
require_once(Path::GeneralClass_php);
require_once(Path::Message_php);
require_once(Path::Query_php);
require_once(Path::SQLStatement_php);

/**
 * Version Manager
 */
class VersionManager implements GeneralClass
{
    const dataOfVersionsPath = Path::VersionsData_json;
    const currentVersion = '1.0.1';
    const targetVersion = '1.0.2';

    private $response = [];
    private $versionsData = [];

    function __construct()
    {
        //nothing
    }

    function run()
    {
        $this->loadJSON(self::dataOfVersionsPath);
        $this->updateSystem(
            $this->versionsData,
            self::currentVersion,
            self::targetVersion
        );
    }

    function loadJSON(string $dataPath)
    {
        $string = file_get_contents($dataPath);
        if ($string === false) {
            $this->response['loadJSON'][] = 'Version data reading failed!';
            return;
        }

        $data = json_decode($string, true);
        if ($data === null) {
            $this->response['loadJSON'][] = 'Version data deccoding failed!';
            return;
        }

        $this->response[] = Message::Create(
            'loadJSON',
            Status::Success,
            'Success json loading!'
        );

        $this->versionsData = $data;
        return;
    }

    /**
     * Update system
     */
    function updateSystem($versionsData, $currentVersion, $targetVersion)
    {
        $currentVersionFound = false;
        $isUpdate = false;

        foreach ($versionsData as $version => $versionData) {
            if ($currentVersion == $version) {
                $currentVersionFound = true;
                $isUpdate = true;
                continue;
            }

            if ($currentVersionFound) {
                $finalSql = "";

                foreach ($versionData['SQL'] as $sqlSnippet) {
                    $finalSql .= $sqlSnippet;
                }

                $query = new Query();
                $query->setSql($finalSql, SQLStatement::Select, []);
                $query->run();
                //print_r($query->getData());

                if ($query->getStatus() == Status::Success) {
                    $this->response[] = Message::Create(
                        'updateSystem',
                        Status::Success,
                        "Success version update: $version"
                    );
                }
            }

            if ($targetVersion == $version) {
                break;
            }
        }

        if (!$isUpdate) {
            $this->response = Message::Create(
                'updateSystem',
                Status::Success,
                "There is no any update!"
            );
        }
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of versionData
     */
    public function getVersionData($version)
    {
        return $this->versionsData[$version];
    }
}
