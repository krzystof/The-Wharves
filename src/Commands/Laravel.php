<?php

namespace Wharf\Commands;

use Wharf\Wharf;
use Dotenv\Dotenv;
use Wharf\Processes\DockerVersion;
use Wharf\Project\DockerComposeYml;
use Wharf\Exceptions\EnvironmentNotReady;
use Wharf\Processes\DockerComposeVersion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Laravel extends Command
{
    protected $name = 'laravel';

    protected $description = 'Set up a docker compose environment for a laravel app.';

    public function handle()
    {
        $this->runCommand('check');

        $this->info('Setting up your Laravel project...');

        $this->runCommand('web');


        /*
         * PHP Container
         */
        // DONE prompt for php version
        // TODO extract to another command

        // update php command:
            // php wharf php
        $output->writeln('<info>Currently using php version <fg=red>'.$project->phpVersion().'</></info>');

        $helper = $this->getHelper('question');
        // would you like to change?
        $question = new ConfirmationQuestion(
            '<question>Would you like to change it?</question>',
            false
        );

        $wantToChange = $helper->ask($input, $output, $question);

        if (! $wantToChange) {
            return;
        }

        $question = new ChoiceQuestion(
            '<question>What version of php would you like?</question>',
            Wharf::supportedPhpVersions(),
            $project->phpVersion()
        );

        $phpVersion = $helper->ask($input, $output, $question);

        $output->writeln('Now using <fg=red>php'.$phpVersion.'</>');

        $project->setPhpVersion($phpVersion);

        $this->runCommand('db');


        // $greetInput = new ArrayInput($arguments);
        // $returnCode = $command->run($greetInput, $output);



        /*
         * AFTER RUN
         */
        // TODO prompt for .gitignoring the created files


        $project->save();
    }
}
