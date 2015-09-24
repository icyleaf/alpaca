# alpaca (ONLY for Kohana v3.0.x)

> Project is **NOT** maintain, Thanks for support!

A swift, lightweight forum system (development)

> It is unstable and still developing, so we are not recommend to deploy it in your project.

## Requirements

* [PHP](http://php.net) 5.2.8+
* [Mysql](http://mysql.com) 5.0+
* [Kohana](http://github.com/kohana/kohana) v3.0.x
* Kohana Modules: [Database](http://github.com/kohana/database), [ORM](http://github.com/kohana/orm), [Auth](http://github.com/icyleaf/alpaca/tree/master/modules/auth/), [Pagination](http://github.com/kohana/pagination), [Gravatar](http://github.com/icyleaf/gravatar/) and [Twig](http://github.com/icyleaf/twig/).

## Installation

### App Intaller (*Recommend*)

	curl -s https://gist.github.com/raw/771385/download_alpaca.sh | sh

> This script just helps you to download and initial structure, you also following steps beginning `Setup 2` below.

### Manual Setup

Step 1: Download Alpaca!

Using your console, to get it from git execute the following command in the root of your development environment:

 * Using Git 1.6.5 or newer version:

	`$ git clone --recursive git://github.com/icyleaf/alpaca.git`

 * Using older git verions:

	`$ git clone git://github.com/icyleaf/alpaca.git`

Next, add whatever submodules alpaca need, they must be initialized and update (go to `alpaca` path):
	
	$ cd alpaca
	$ git submodule update --init

One more thing, alpaca require `twig-php` template engine. Then, go to path `core/modules/twig`, it also must be initialized and update:

	$ cd core/modules/twig
	$ git submodule update --init
	
That's all there is to it.

Step 2: Cownfiguration of Database

Edit `application/config/database.php` with the correct information.

> `$development` variable is development environment in local.

> `$production` variable is production environment online.

Step 3: Import SQL

Run the SQL found in `dump/install.sql`.

Step 4: Configuration of Alpaca

Open `core/bootstrap.php` and make the following changes:

* Set the default [timezone](http://php.net/timezones) for your application

Make sure the `cache` and `logs` directories are world writable with `chmod {cache,logs} 0777`

> Depending on your platform, the installation's subdirs may have lost their permissions thanks to zip extraction. Chmod them all to 755 by running `find . -type d -exec chmod 0755 {} \;` from the root of your Alpaca installation.

**Never need to set `base_url`, it could be detected from your browser.**

Step 5: Configuration of Forum

Open `core/application/config/alpaca.php` and make whatever your need to change, but ONLY make the `project` property to renain for upgrade.

## Start your journey!

Now Browse to `yourdomain.com` and you should see the **Home Page**.

> By default, the first registered user has Administrator privilege.
