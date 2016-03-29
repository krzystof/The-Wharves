<?php

namespace Wharf\Commands;

use Wharf\Project;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    public function configure()
    {
        $this->setName($this->name)
             ->setDescription($this->description);
    }

    public function initialize($value='')
    {
        $this->project = Project::onCurrentDirectory();

        $this->questionHelper = $this->getHelper('question');
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
        $this->output->writeln([
            sprintf('<info>%s</info>', $message)
        ]);
    }

    protected function comment($message)
    {
        $this->output->writeln([
            sprintf('<comment>%s</comment>', $message)
        ]);
    }

    protected function error($message)
    {
        $this->output->writeln([
            sprintf('%s<error>%s</error>', "\n", $message)
        ]);
    }

    protected function confirm($question, $default = false)
    {
        $question = new ConfirmationQuestion('<question>'.$question.'</question>', $default);

        return $this->ask($question);
    }

    protected function choose($question, $choices, $default)
    {
        $question = new ChoiceQuestion('<question>'.$question.'</question>', $choices, strtolower($default));

        return $this->ask($question);
    }

    protected function prompt($question)
    {
        $question = new Question('<question>'.$question.'</question>');

        return $this->ask($question);
    }

    protected function ask($question)
    {
        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    protected function abort($message)
    {
        $this->comment("\n".$message."\n");
        exit(1);
    }

    protected function runCommand($name)
    {
        $command = $this->getApplication()->find($name);

        $command->run($this->input, $this->output);
    }

    protected function displayInfo($subject)
    {
        $this->lineBreak();
        $this->info(sprintf('About: <fg=yellow>%s</>', $subject->name()));
        $this->lineBreak();

        foreach ($subject->infos() as $label => $detail) {
            $this->info(sprintf('%s:%s%s', $label, "\t\t", $detail));
        }

        $this->lineBreak();
    }

    protected function lineBreak()
    {
        $this->output->writeln("\n");
    }
}
