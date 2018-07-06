<?php
#########################################################
# Copyright © 2008 Darrin Yeager                        #
# https://www.dyeager.org/                               #
# Licensed under BSD license.                           #
#   https://www.dyeager.org/downloads/license-bsd.txt    #
# Modified by Martin Meesit for testing purposes        #
#########################################################
define("etKey", "et");
define("ruKey", "ru");

function getPreferredLanguage() {
    if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
        return parsePreferredLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    else
        return parsePreferredLanguage(NULL);
}

function parsePreferredLanguage($http_accept, $deflang = "et") {
    if(isset($http_accept) && strlen($http_accept) > 1)  {
        # Split possible languages into array
        $x = explode(",",$http_accept);
        foreach ($x as $val) {
            #check for q-value and create associative array. No q-value means 1 by rule
            if(preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i",$val,$matches))
                $lang[$matches[1]] = (float)$matches[2];
            else
                $lang[$val] = 1.0;
        }

        #return default language (Estonian or Russian)
        $etQval = 0.0;
        $ruQval = 0.0;

        foreach ($lang as $key => $value) {
            if ($key == "et") {
                $etQval = $value;
            }
            if ($key == "ru") {
                $ruQval = $value;
            }
        }

        $deflang = ($ruQval > $etQval ? ruKey : etKey);
    }
    return strtolower($deflang);
}