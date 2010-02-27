<?php

class SimpleVerboseSupport
{
    const VERBOSE_PREFIX = ' >> ';

    private $verboseMode = false;

    private $debugMode = false;

    private $verboseLogs = array();


    public function setVerbose($i_VerboseMode = true)
    {
        $this->verboseMode = $i_VerboseMode;

        return true;
    }

    public function setDebug($i_DebugMode)
    {
        $this->debugMode = $i_DebugMode;

        return true;
    }

    public function addLog($i_Log, $i_Level = 'info')
    {
        if ( true === $this->debugMode )
        {
            $this->verboseLogs[] = $i_Log;
        }

        if ( true === $this->verboseMode )
        {
            echo self::VERBOSE_PREFIX . '{' . $i_Level . '} ' . $i_Log . "\n";
        }

        return true;
    }

    public function getAllLogs()
    {
        $o_Resuls = array();

        $o_Resuls = $this->verboseLogs;

        return $o_Resuls;
    }

    public function clearAllLogs()
    {
        $this->verboseLogs =array();

        return true;
    }
}

