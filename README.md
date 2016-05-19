## Wharf... is Coming Soon.

### Handle env file
if .env exists, load it. If not, prompt. When checking a container configuration, check for the .env file. if not, prompt.

### Beta soon...

Wharf is just a php command line app that will set up a docker-compose.yml file in a sec using images in docker hub's Wharf namespace. In fact, it is a wrapper around a wrapper around a... you know. I just wanted a quick way to build my dev environment, without looking in older projects to find out how I did it last time.

Wharf aim to provide a fast way to set up a development environment using docker and docker-compose. By using a docker-compose.yml in each project, we can set up a configuration that will be isolated (software, versions, other goodies...) from one project to another. Makes it also pretty easy to work in a team and to share the same configuration.

### What is left to do for the beta?

I need a command to list all available images.
I need to start building all the images in docker hub, and update Wharf's containers settings in the same time.
I need to write doc.
I need to record a couple of gif.
I need to do a static website for the doc.

### What next for v1?

I need to add some goodies commands.
I need to add reddis, and some others.

### Thinking about

A ruby dev env
