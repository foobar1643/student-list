# Student List
Simple CRUD application, designed using MVC architectural pattern and written using mostly Flat PHP (without any frameworks).

### Requirements
1. [PHP] >= 5.4
2. Web server with URL rewrite support
3. [PostgreSQL] database
4. [Composer dependency manager]

### Used technologies
1. [Twitter Bootstrap]
2. [Pimple Dependency Injection container]
3. [FIGCookies] by [Dflydev]
4. PSR-4 autoloader implementation by Composer
5. [PHPUnit testing framework]
6. Favicon (and other icons) is from [Glyphicons free] pack


### Installation
1. Clone the repository using `git clone https://github.com/foobar1643/student-list.git` command.
2. Install application dependencies using `composer install` command.
3. Set `public` directory as a document root on your web server.
4. Configure URL rewriting on your web server [as described here].
5. Set your database credentials in `db.ini` configuration file.
6. Import `database.sql` in your PostgreSQL database.

### License
This application is licensed under the MIT license. For more information see [License file].

[License file]: <https://github.com/foobar1643/student-list/blob/master/LICENSE.md>
[as described here]: <https://github.com/foobar1643/student-list/blob/development/ROUTING.md>
[PHPUnit testing framework]: <https://phpunit.de/>
[Dflydev]: <https://github.com/dflydev>
[FIGCookies]: <https://github.com/dflydev/dflydev-fig-cookies>
[Pimple Dependency Injection container]: <http://pimple.sensiolabs.org/>
[Composer dependency manager]: <https://getcomposer.org/>
[PostgreSQL]: <https://www.postgresql.org/>
[PHP]: <https://secure.php.net/>
[Twitter Bootstrap]: <https://getbootstrap.com/>
[Glyphicons free]: <http://glyphicons.com/>