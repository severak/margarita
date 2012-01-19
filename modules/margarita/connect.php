<?php

rpd::$db->query("ATTACH DATABASE '".dirname(__FILE__)."/db/szeged.db3' AS margarita");
rpd::$db->db_attached[] = 'margarita';