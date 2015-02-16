<?php

require 'recipe/common.php';

server('development-server', 'phabricator-dev.int.vgnett.no', 22)
    ->path('/services/applications/phabricator-dev/phabricator')
    ->user('erlwienc')
    ->pubKey();

stage('dev', array('development-server'), array('branch' => 'master'), true);

set('repository', 'git@github.com:vgno/phabricator.git');

task('deploy', [
    'deploy:start',
    'deploy:prepare',
    'deploy:update_code',
    'deploy:symlink',
    'cleanup',
    'deploy:end'
])->desc('Deploy your project');
