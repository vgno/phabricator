<?php

require 'recipe/common.php';

server('development-server', 'phabricator-dev.int.vgnett.no', 22)
    ->path('/services/applications/phabricator-dev/phabricator')
    ->user('erlwienc')
    ->pubKey();

server('production-server', 'phabricator.int.vgnett.no', 22)
    ->path('/services/applications/phabricator/phabricator')
    ->user('erlwienc')
    ->pubKey();

stage('dev', array('development-server'), array('branch' => 'master'), true);
stage('prod', array('production-server'), array('branch' => 'master'), true);

set('repository', 'git@github.com:vgno/phabricator.git');
set('shared_dirs', ['conf']);

task('deploy', [
    'deploy:start',
    'deploy:prepare',
    'deploy:update_code',
    'deploy:symlink',
    'deploy:shared',
    'cleanup',
    'deploy:end'
])->desc('Deploy your project');
