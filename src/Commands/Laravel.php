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
        // TODO Execute check requirements command

        $output->writeln([
            '<info>Setting up a new project for a Laravel app...</info>'
        ]);

        $project = Wharf::onCurrentDirectory();

        /*
         * WEB Container
         */
        // TODO detect directory, prompt to confirm directory to serve
        // TODO prompt for hostname
        // TODO create a .wharf and nginx and put nginx conf in it

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

        /*
         * CALL A COMMAND FROM ANOTHER COMMAND
         */
        // $command = $this->getApplication()->find('db');

        // $arguments = array(
        //     'command' => 'demo:greet',
        //     'name'    => 'Fabien',
        //     '--yell'  => true,
        // );

        // $greetInput = new ArrayInput($arguments);
        // $returnCode = $command->run($greetInput, $output);



        /*
         * AFTER RUN
         */
        // TODO prompt for .gitignoring the created files


        $project->save();
    }
}
