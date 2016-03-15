# Wharf

## TODO

## Package in development. Come back later!
The plan is to support Linux and Mac, heavily inspired and helped by Inviqa's dock-cli package. Check it out if you don't have Docker on your machine!

Build a development environment for your PHP app in a blink.

### TODO
#### Commands
- [ ] `wharf laravel` will initialise a dev environment for an existing laravel app. All the containers will be created with values from your env file. If it is not present, it will use some default settings.
- [ ] `wharf init` will check that you have the required docker set up, then it will prompt for informations about your project.
When this is done, just run `docker-compose up` and you are good to go.
- [ ] `wharf php` to change the php version
- [ ] `wharf db` to change the database system

#### Containers
- [ ] php 5.4, 5.5, 5.6, 7.0
- [ ] db mysql, mariadb, postresql, sqlite, ???

#### Containers to Ansible
