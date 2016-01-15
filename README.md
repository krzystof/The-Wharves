# Docker + Laravel
This repo contains my Dockerfiles used to build a dev environment for Laravel, and a docker-compose.yml file to drop in the root of a project.

## Common aliases
Run Composer commands
alias dcomposer="dc run --rm composer"

Run Artisan commands
alias dart="dc run --rm fpm php artisan"

Run Behat
alias dbehat="dc run --rm behat"
