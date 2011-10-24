<?php
// Runs as a cron every 24 hours at midnight
// purging the giving MySQL table
setInterval(function(){
    
    $db = mysql_connect('localhost', 'root', '');
    mysql_query('DELETE FROM my_tmp_table WHERE `rm` = 1', $db);
    mysql_close($db);
    
}, 86400, null, '24 Hour MySQL Purge', null, '24:00');