# alpaca

A swift, lightweight forum system (development)

> It is unstable and still developing, so we are not recommend to deploy it in your project.

## Requirements

* PHP 5.2+
* Mysql 5.0+
* [Kohana](http://github.com/kohana/kohana)
* Kohana Modules: [Database](http://github.com/kohana/database), [ORM](http://github.com/kohana/orm), [Auth](http://github.com/icyleaf/alpaca/tree/master/modules/auth/), [Pagination](http://github.com/kohana/pagination), [Gravatar](http://github.com/icyleaf/gravatar/) and [Twig](http://github.com/jonathangeiger/kohana-twig/). (**they all include Alpaca**)

## Installation

Step 1: Download Alpaca!

Using your console, to get it from git execute the following command in the root of your development environment:

	$ git clone git://github.com/icyleaf/alpaca.git

And watch the git magic...

Of course you can always download the code from the [github project](http://github.com/icyleaf/alpaca) as an archive.

Step 2: Initial Structure

Next, add whatever submodules alpaca need, they must be initialized and update:

	$ git submodule update --init

That's all there is to it.

Step 3: Configuration of Database

Edit `application/config/database.php` with the correct information.

> `$development` variable is development environment in local.

> `$production` variable is production environment online.

Step 4: Import SQL

Run the SQL found in `dump/install.sql`.

Step 5: Configuration of Alpaca

Open `core/bootstrap.php` and make the following changes:

* Set the default [timezone](http://php.net/timezones) for your application

Make sure the `cache` and `logs` directories are world writable with `chmod {cache,logs} 0777`

> Depending on your platform, the installation's subdirs may have lost their permissions thanks to zip extraction. Chmod them all to 755 by running `find . -type d -exec chmod 0755 {} \;` from the root of your Alpaca installation.

**Never need to set `base_url`, it could be detected from your browser.**

Step 6: Configuration of Forum

Open `core/application/config/alpaca.php` and make whatever your need to change, but ONLY make the `project` property to renain for upgrade.

## Start your journey!

Now Browse to `yourdomain.com` and you should see the **Home Page**.

> By default, the first registered user has Administrator privilege.

