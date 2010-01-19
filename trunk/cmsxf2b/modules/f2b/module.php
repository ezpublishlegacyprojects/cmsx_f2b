<?php
$Module = array( "name" => "F2b" );

$ViewList = array();

$ViewList["confirmardados"] = array(
    "script" => "confirmardados.php",
    'ui_context' => 'edit',
    "default_navigation_part" => 'ezshopnavigationpart',
    'single_post_actions' => array( 'StoreButton' => 'Store',
                                    'CancelButton' => 'Cancel'
                                    )
    );
?>