<?php
// Runs as a cron every 24 hours at midnight sending an
// email telling you the server is working
setInterval(function(){
    mail(
        'your@email.com',
        'Server is up!',
        'Your server is working! YAY!'
    );
}, 86400, null, '24 Hour Mail Service', null, '24:00');