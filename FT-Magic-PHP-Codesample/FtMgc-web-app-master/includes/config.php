<?php

// Set the folder that you'd like data to be saved to.
define('DATA_FOLDER', 'user_data'); // Default value = 'user_data'

// Update Feeds
$updateFeed = array(
    'release' => array(
        'feed' => 'https://api.github.com/repos/ftmgc/ftmgc-web-app/branches/master',

    ),
    'beta' => array(

        'zip' => 'https://github.com/ftmgc/ftmgc-web-app/archive/beta.zip',
    ),
    'nightly' => array(
        'feed' => 'https://api.github.com/repos/ftmgc/ftmgc-web-app/branches/nightly',
        'zip' => 'https://github.com/ftmgc/ftmgc-web-app/archive/nightly.zip',
    ),
);