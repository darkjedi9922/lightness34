# Lightness 3.4 Framework

This project is a PHP framework for site building.

The framework aims on strong component and code structuring.

## Implemented Mechanisms

1. **Routing.** It automatically finds out the page to view.
2. **Views.** Views are used to show visible parts of a site. One view is only one markup file. It can be HTML or PHP files.
3. **Database access.** It gives convenient access to MySQL database.
4. **Actions**. Handle forms and validate them.
5. **Error Handlers**. Handle errors of any type from notices to fatal in one way that can be different to different error types.
6. **Macroses**. Do something when you recieve trigger GET parameters.
7. **Rules**. Validates action, route and other data using chains of rule validators.
8. **Cash**. Should not use cash values in the frame classes because these values make testing and architecture of that classes more difficult.

## Getting Started

### Prerequisties

* Apache 2
* MySQL
* PHP 7

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

### Running the tests

[PHPUnit Framework](https://phpunit.de) is used for testing.

```bash
# Go to the project's tests directory.
cd tests

# Run all the tests using PHPUnit.
phpunit --bootstrap tests/__bootstrap.php tests
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
Copyright (c) 2019 Jed Dark
