<?php

rpd::$db->query("ATTACH DATABASE '".dirname(__FILE__)."/db/testovaci.sqlite' AS margarita");
rpd::$db->db_attached[] = 'margarita';