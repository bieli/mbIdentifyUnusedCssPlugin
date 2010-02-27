<?php

if ( false === class_exists('sfException') )
{
    class sfException extends Exception
    {
        
    }
}

class IdentifyUnusedCssException extends sfException
{
    
}

