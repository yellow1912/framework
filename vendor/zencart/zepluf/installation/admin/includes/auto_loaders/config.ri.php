<?php

require(__DIR__.'/../../../zepluf/app/bootstrap.php');

$autoLoadConfig[200][] = array(
    'autoType' => 'require',
    'loadFile' => $container->getParameter('kernel.root_dir') . '/plugins/riPlugin/init_includes.php'
);