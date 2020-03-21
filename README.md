# Lightness 3.4 Framework

This project is a PHP framework for site building.

The framework aims on strong component and code structuring. The first purpose is simplifying of creating page routes without configurations.

## Implemented Mechanisms

1. **Routing.** It automatically finds out the page to view.
2. **Views.** Views are used to show visible parts of a site. One view is only one markup file. It can be HTML or PHP files.
3. **Database access.** It gives convenient access to MySQL database.
4. **Actions**. Handle forms and validate them.
5. **Error Handlers**. Handle errors of any type from notices to fatal in one way that can be different to different error types.
6. **Events and Macros**. Emit and handle events with macros.
7. **Reading trackers**. They allow to track reading state or progress any objects for users.
8. **Cash**. Pre-defined variables that are created when they are used first time. Should not use cash values in the frame classes because these values make testing and architecture of that classes more difficult.
9. **Dynamic pages**. It allows to form dynamic routes sort of `page/non-existence-page/another-non-existence-page`. In this example `page` is a existing page that is really present as viewfile, and next parts of the url are *virtual* pages. Their names can be accessed from viewfile of dynamic page. That feature can be used, for example, to form routes like `article/my-first-article` where `my-first-article` is a name of the article that will be loaded from database in `article` viewfile.
10. **Lists**. Convenient way to make lists of Identities.
11. **Pagination.** The lists mechanism includes `Pager`'s that allows to split contents of lists by page numbers.
12. **Authorization.** The way to authorize users.
13. **Modules**. They allows to create parts of a site based on the same stuctures. Also modules are an important part of **user rights mechanism** (*see below*).
14. **User/Group rights.** Gives a convenient way to define group rights. The groups are part of users, so there are also user rights that can have additional checks on the defined user. 

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
