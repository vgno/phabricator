<?php

require 'recipe/composer.php';

server('development-server', 'phabricator-dev.int.vgnett.no', 22)
    ->path('/services/apache/phabricator-dev/phabricator-new')
    ->user('erlwienc')
    ->pubKey();

stage('dev', array('development-server'), array('branch' => 'master'), true);

set('repository', 'git@github.com:vgno/phabricator.git');
