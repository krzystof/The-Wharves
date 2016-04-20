<?php

namespace Wharf\Commands;

use Wharf\Project;
use Wharf\Commands\Exceptions\CommandAborted;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    protected $next;

    public function configure()
    {
        $this->setName($this->name)
             ->setDescription($this->description);

        if (isset($this->optionFlags)) {
            foreach ($this->optionFlags as $name => $description) {
                $this->addOption(
                    $name,
                    null,
                    InputOption::VALUE_NONE,
                    $description
                );
            }
        }
    }

    public function initialize()
    {
        $this->questionHelper = $this->getHelper('question');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->project = isset($this->project)
                       ? $this->project
                       : Project::onDirectory(getcwd());
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->handle();
    }

    abstract public function handle();

    protected function info($message)
    {
        $this->output->writeln([sprintf('<info>%s</info>', $message)]);
    }

    protected function comment($message)
    {
        $this->output->writeln([sprintf('<comment>%s</comment>', $message)]);
    }

    protected function error($message)
    {
        $this->output->writeln([sprintf('%s<error>%s</error>', "\n", $message)]);
    }

    protected function confirm($question, $default = false)
    {
        $question = new ConfirmationQuestion(
            sprintf(
                '<question>%s</question>%s ',
                $question,
                $this->getDefault($default)
            ),
            $default === 'yes' ? true : false
        );

        return $this->ask($question);
    }

    protected function choose($question, $choices, $default)
    {
        $question = new ChoiceQuestion('<question>'.$question.'</question>', $choices, strtolower($default));

        return $this->ask($question);
    }

    protected function prompt($question, $default = false)
    {
        $question = new Question(
            sprintf('<question>%s</question>%s', $question, $this->getDefault($default)),
            $default
        );

        return $this->ask($question);
    }

    protected function getDefault($value)
    {
        return $value ? sprintf(' <fg=yellow>(default = %s)</> ', $value) : '';
    }

    protected function ask($question)
    {
        $this->lineBreak();

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    protected function abort($message = 'Command aborted.')
    {
        throw new CommandAborted($message);
    }

    protected function runCommandOnProject($name)
    {
        $command = $this->getApplication()->find($name);

        $command->onProject($this->project);

        try {
            $command->run($this->input, $this->output);
        } catch (CommandAborted $exception) {
            // do nothing
        }

        return $this;
    }

    private function onProject($project)
    {
        $this->project = $project;
    }

    protected function lineBreak()
    {
        $this->output->writeln("\n");
    }

    protected function displayCurrentContainerAndConfirmUpdate()
    {
        $this->displayCurrentContainer();

        if (! $this->confirm('Do you want to update this configuration?', 'no')) {
            return $this->abort();
        }

        return $this;
    }

    protected function displayCurrentContainer()
    {
        $this->container->displayTo($this->output);

        return $this;
    }

    protected function saveProject()
    {
        $this->project->save($this->container);
    }

    protected function displayCurrentContainerImage()
    {
        $this->info(sprintf(
            'Use %s %s',
            $this->container->image()->name(),
            $this->container->image()->tag()
        ));
    }

    protected function sourceEnvironment()
    {
        $this->comment('Reading info from your .env file...');

        $this->container->environmentFrom($this->project->envFile());
    }

    protected function setImageIfNotSame($image)
    {
        if ($this->container->image()->notSameAs($image)) {
            $this->container->image($image);

            $this->displayCurrentContainerImage();
        }

        return $this;
    }

    protected function checkContainerInvalidOptions()
    {
        $this->container->eachInvalidOptions(function ($default, $option) {
            $value = $this->prompt(
                sprintf('The setting "%s" is required, please enter a value:', $option),
                $default
            );

            $this->container->configure([$option => $value]);
        });

        return $this;
    }
}
