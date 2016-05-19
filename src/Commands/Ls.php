<?php

namespace Wharf\Commands;

use Wharf\Containers\Image;
use Symfony\Component\Console\Input\InputArgument;

class Ls extends Command
{
    protected $name              = 'ls';
    protected $description       = 'List all available containers and versions.';
    protected $optionalArguments = ['service' => 'List images for a specific service.'];

    public function configure()
    {
        $this->addArgument('service', InputArgument::OPTIONAL, 'List images for a service', 'all');

        parent::configure();
    }

    public function handle()
    {
        $availableServices = Image::all()->filter(function ($availableService) {
            return $this->argument('service') === 'all' || $availableService === $this->argument('service');
        });

        $availableServices->each(function ($images, $service) {
            $this->lineBreak();
            $this->comment(sprintf('Available for "%s"', $service));

            collect($images)->each(function ($tags, $image) {
                $this->write(sprintf('%s: ', str_replace('wharf/', '', $image)));

                collect($tags)->each(function ($tag) {
                    $this->write(sprintf(' - %s', $tag));
                });
            });
        });
    }
}
