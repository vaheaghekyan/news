<?php
return
[
    'Just now' => 'Just now', // (for the first 30 mins)
    '30 minutes ago' => '{0} minutes ago' ,  //(from min 31 - 59)
    '1 hour ago' => '{0} hour ago', //(1 hr to 1:59)
    '2 hours ago' => '{0} hours ago', //(2 hrs to 2:59)
    '3 hours ago' => '{0} hours ago', //(3 hrs to 3:59)
    'Today' => 'Today', //(for anything over 4 hours and below 6 hours)
    'Earlier today' => 'Earlier today', //(over 6 hours to 24 hours)

    '1 day ago' => '{0} day ago', //(anything past 24 hours)
    '2 days ago' => '{0} days ago', //(24 - 48 hours)
    '3 days ago' => '{0} days ago', //(49 - 72 hours)
    'This week' => 'This week', //( anything over 3 days old to 7 days)

    'Last week' => 'Last week', //(1 week old)
    '2 weeks ago' =>  '{0} weeks ago',// (2 weeks old)
    '3 weeks ago' => '{0} weeks ago', //(3 weeks old)

    'This month' => 'This month', //(4 weeks old)

    '1 month ago' => '{0} month ago', //(5 weeks - 7 weeks old)
    '2 months ago' =>'{0} months ago', //(8 weeks old - 11 weeks old)
    '3 months ago' => '{0} months ago', //(12 weeks old)
];
?>