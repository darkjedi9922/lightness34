# Lightness 3.4 Framework

This project is a PHP framework for site building.

The framework aims on strong component and code structuring. The first purpose is simplifying of creating page routes without configurations.

## Implemented Features

### Mechanisms

1. **Routing.** The framework provides and uses classes for parsing URLs, getting their parameters, data from HTTP-requests and for setting parameters of HTTP-responses.
2. **Views.** Views are used to show visible parts of a site. They include pages, blocks, widgets, layouts. The page routes match file hierarchy of the page view directory and support human-friendly URL.
3. **Database.** Work with MySQL and execution of SQL-queries. There is a simple implementation of ORM for work with separate records as objects. Also it includes a simple query builder.
4. **Actions**. Actions let developers handle forms and other CRUD requests which refers to creation, editing and deleting providing an abstract class to their step-by-step implementation.
5. **Error Handlers**. Setup the single error handler which cat catch and handle all types of PHP errors with user error handlers. The mechanism includes showing of error page views.
6. **Events**. The app and framework classes can have events on which it is possible to subscribe and handle from any part of the app. The algorithm which handles HTTP-requests is based on the events.
7. **Configuration.** Gives an opportunity to use different config types which can be interchanged using configs only by a name without an extension. The default built-in types are JSON and PHP.
8. **Authorization.** User authorization with opportunity of login remembering based on using *cookies*. Also the mechanism lets developers define their own implementations.
9. **Access Control.** Definition of user rights splitting them by modules and opportunity of implementation above the rights own checks with own logic. The mechanism is based on the best of RBAC and ABAC.
10. **Cash**. Single definition of values which have time- or memory-consuming initialization. Fifferent types of cash storages can be applied to the values. There is a static type but developers can create their own.
11. **Drivers**. The mechanism of drivers define implementation of abstract functional in order to interchange it at the global level. Also the drivers support decoration at runtime.

### Additional

1. **Reading Trackers**. They allow to attach state and progress tracking of website content reading for every user.
2. **Pagination**. Splitting lists by page numbers. The paginators are views so it is possible to setup their view with logic.
3. **JSON for Frontend.** Generation JSON which can be included to HTML and JavaScript in order to send data from PHP to JavaScript without API requests.
4. **Cookie and Sessions.** There are utilites for work with cookie and sessions of clients. Additionally, cookies is updated without page reloading.
5. **Logging.** All errors are written in a log in details including the call stack. The records can be logged splitting by levels.
6. **Semaphores.** Crossplatform implementation of semaphores for access synchronization of parallel processes with special blocking flags.
7. **Units.** Utilites for work with units including conversion and finding the most human-readable value.
8. **Daemons**. Implementation of event handlers that are like daemon processes in operating systems which are run regularly in set time interval.

### Built-in User Modules

1. **Users.** Adding, editing users. Viewing profile lists. The module includes controlling their genders and groups.
2. **Messages.** Users can talk to each other in dialogs. The module gives opportunities to control dialog lists and messages.
3. **Articles.** Adding, editing, removing articles, viewing all articles and also lists of the new for a user articles.
4. **Comments.** Adding comments to module materials. The module is implemented as submodule so it can be attached to any module.
5. **Statistics.** Dinamically plugs in collecting of main mechanisms work statistics and provides API for getting it.
6. **Admin Panel.** Viewing information about modules, their data including application statistics and also configuring them.

## Getting Started

### Prerequisties

* Apache 2
* MySQL
* PHP >= 7.2

### Installation

Clone the git project and create a new virtual host like you usually do. 

**Apache confguration** for a site on this framework:

```apache
<VirtualHost *:80>
    ServerName lightness34.local
    ServerAdmin webmaster@localhost
    DocumentRoot "/path/to/lightness34"
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory "/path/to/lightness34">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**If you want to use a database** in the project you need to create a file `config/db.json` like below to configure the database object.

```json
{
    "host": "localhost",
    "username": "root",
    "password": "",
    "dbname": "lightness"
}
```

*This file is ignored by default because developers can have their own database configurations on their local machines that is different between each other. It is easier to ignore this file at all.*

#### Installation of default modules

1. Import database backup `_dev/database/lightness34.sql`.
2. Using [NPM](https://www.npmjs.com) ([Node](https://nodejs.org) Package Manager) build style and javascript assets:

```bash
cd public
npm install # download and install packages

# build (any of two commands)
npm run webpack # debug build
npm run build # release build
```

### Default modules

By defaut the module has one **root user** with login `Admin` and password `0000`.

The same password `0000` is used to login into the **admin panel**.

### Running the tests

[PHPUnit Framework](https://phpunit.de) is used for testing. The configuration for running the tests is stored in `phpunit.xml`.

```bash
cd lightness34 # Go to the project's directory.
phpunit tests # Run all the tests using PHPUnit.
```

## Versioning

The version looks like **3.4.major.minor.patch** and is based on [Semantic Versioning](https://semver.org).

**3.4.** is a **constant version**. 3 times the framework (early it was a site engine) was being completely rewritting from start and it very differs from its predecessors. 4 times it was being almost completely rewritting on the one base version 3.

The part **major.minor.patch**:

* MAJOR version is incremented when there is made incompatible API changes,
* MINOR version is incremented when there is added functionality in a backwards-compatible manner
* PATCH version is incremented when there is made backwards-compatible bug fixes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.  
Copyright (c) 2019-2020 Jed Dark
