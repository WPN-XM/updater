<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\InstallerRegistries;
use WPNXM\Updater\DownloadFilenames;
use WPNXM\Updater\InstallerRegistry;
use WPNXM\Updater\Registry;
use WPNXM\Updater\Version;
//use WPNXM\Updater\View;

/**
 * Git Commit and Push the installer registries of the next version.
 * This action runs after a button click, after automatically updating components (UpdateComponents).
 */
class GitPushNextVersionRegistries extends ActionBase
{
    public function __invoke()
    {
        $gitpush = filter_input(INPUT_GET, 'gitpush', FILTER_SANITIZE_STRING);

        $msg = 'updated installer registries of "next" version';

        if($gitpush == 'true') {
            self::gitCommitAndPush($msg, true);
            echo 'committed and pushed <br> message: ' . $msg; 
        } elseif($gitpush == 'false') {
            self::gitCommitAndPush($msg, false);    
            echo 'committed, but not pushed <br> message:' . $msg; 
        } else {
            echo 'error: get variable "gitpush" missing';
        }  
    }

    /**
     * Git commits and pushes the latest changes to the
     * "next version" installer registry with specified commit message.
     *
     * @param string Optional Commit Message
     * @param bool If true, do git push, else only git commit.
     */
    public static function gitCommitAndPush($commitMessage = '', $doGitPush = false)
    {
        echo '<pre>';

        // setup path to git
        $git = '"git" ';
        passthru($git . '--version');

        // switch to the git submodule "registry"
        chdir(DATA_DIR . 'registry');
        //echo 'Switched to Registry folder: ' . getcwd() . NL;

        // make sure we are on the "master" branch and not in "detached head" state
        echo NL . 'Switching branch to "master":' . NL;
        passthru($git . 'checkout master');

        echo NL . 'Pulling possible changes:' . NL;
        passthru($git . 'pull');

        //echo NL . 'Staging current changes' . NL;
        //exec("git add .; git add -u .");

        echo NL . 'Committing current changes "' . $commitMessage . '"' . NL;
        passthru($git . 'commit -a -m "' . $commitMessage . '"');
       
        if($doGitPush === true) {
            echo NL . 'Pushing commit(s) to remote server:' . NL;
            passthru($git . 'push');
        } else {
            echo NL . 'You might "git push" now.' . NL;
        }

        echo '</pre>';
    } 

}