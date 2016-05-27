## Wharf... is Coming Soon.

### 1 Change a service IN PROGRESS

To change the version of a service, you can use the --to option.
```
wharf php --to=70
wharf mysql --to=57
```

If you want to change the software, use the --switch option.
```
wharf db --switch=postgres
```

### 2 Validate it works on a fresh laravel project:
php70
- mysql57
- mysql56
- mysql55
php56
- mysql57
- mysql56
- mysql55
php55
- mysql57
- mysql56
- mysql55

### 3 Handle the --fast flag

### Handle env file
if .env exists, load it. If not, prompt. When checking a container configuration, check for the .env file. if not, prompt.

### Beta soon...

Wharf is just a php command line app that will set up a docker-compose.yml file in a sec using images in docker hub's Wharf namespace. In fact, it is a wrapper around a wrapper around a... you know. I just wanted a quick way to build my dev environment, without looking in older projects to find out how I did it last time.

Wharf aim to provide a fast way to set up a development environment using docker and docker-compose. By using a docker-compose.yml in each project, we can set up a configuration that will be isolated (software, versions, other goodies...) from one project to another. Makes it also pretty easy to work in a team and to share the same configuration.

### What is left to do for the beta?
I need to write doc.
I need to record a couple of gif.

### What next for v1?
I need to add some goodies commands.
I need to add reddis, and some others.
Fix nginx stopping very slow...
