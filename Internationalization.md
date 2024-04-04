# Internationalisation and Localisation of an MVC Web Application in a Docker container
```
+-------------------+
|     HTML File     |
|                   |
|  <p><?=__('Hello')?>  |
|     <?=__('World')?>  |
+-------------------+
            |
            | Calls __() function
            |
+-------------------+
|     i18n.php      |
|                   |
| <?php             |
| ...
| $t = new \Symfony\Component\Translation\Translator($locale);
| $t->addLoader('mo', new \Symfony\Component\Translation\Loader\MoFileLoader());
| $t->addResource('mo',"messages.mo", $locale);
| function __($str) |
| {                 |
|   global $t;      |
|   return $t->trans($str);
| }                 |
+-------------------+
            |
            | Loads translations from
            |
+-------------------+
|   messages.mo     |
|   (Translation File) |
|                   |
|  msgid "Hello"    |
|  msgstr "Bonjour" |
|                   |
|  msgid "World"    |
|  msgstr "Monde"   |
+-------------------+
```


## Start your Docker Container

Ensure that your Docker container is running and pointing to your project code.
For example, with no container called myXampp or using ports 22/80, at the command line run:
```
docker run --name myXampp -p 22:22 -p 80:80 -d -v YOUR_PROJECT_LOCATION:/opt/lampp/htdocs tomsik68/xampp
```
For example, if your project Web root folder is `C:\MyProject` then the code should be 
```
docker run --name myXampp -p 22:22 -p 80:80 -d -v C:\MyProject:/opt/lampp/htdocs tomsik68/xampp
```

## Start a Terminal to your Container
You can use the terminal from Docker Desktop for your container to run the `bash` command or run the terminal from the Windows command line as follows:
```
docker exec -it myXampp bash
```
This runs the bash terminal program and gives you access at the command line.

## Software Setup

We have a few tools to setup and configurations to make before we can dive into Internationalisation and Localisation.

To internationalise our Web applications means that we get these applications ready to accept localisations.
Localisations are translations of our Web applications to apply the local language and formats for Date, Currency, etc.
Localisation is providing a translation of language and formats for the application.

There are many ways to proceed for this, but one of the most widespread utility to provide these services is gettext.
Gettext popular because it loads language definitions in memory and can serve up results to web clients based on their language settings, with minimal code added to our applications.
Sadly however, gettext is hard to get working... so we will use the Translation component of Symfony, while keeping the same level of simplicity as gettext in our views.

More specifically, 
- Strings in our views will be output by a function instead of directly. More specifically, each string is output by the `__()` (two underscores) function within a php output block `<?= ?>` as follows `<?=__('string')?>`.
- These strings will then get extracted by the xgettext tool and placed in a localisation template.
- Code to select the language is run at each request.

The process of localisation will use the localisation template and translation files will be placed in a strict folder structure.

### Fixing xgettext in the docker container 

Xgettext is a tool which we will use to extract translatable strings from our Web application views.

This tool requires libgomp1 and for some reason this package is not part of the docker image.
To fix this, run  
```
apt update 
apt install libgomp1
```

### Shortcut to xgettext 

Create a file named `xgettext` in your project folder using the command prompt as follows: 

```
nano /opt/lampp/htdocs/xgettext
```
and add the following contents
```
/opt/lampp/bin/xgettext "$@" 
```
Make the script executable:
```
chmod +x /opt/lampp/htdocs/xgettext
```
Fix the environment by adding . to the PATH:
```
export PATH=$PATH:.
```

Test the script by navigating to the htdocs folder (with `cd /opt/lampp/htdocs` if that is not already done) and running the command as follows: 
```
xgettext --version
```
Your output should look like this:
```
xgettext (GNU gettext-tools) 0.19.8.1
Copyright (C) 1995-1998, 2000-2016 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <http://gnu.org/licenses/gpl.html>
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.
Written by Ulrich Drepper.
```
	
The command `xgettext -a *.php` can get all strings from the php files in the current folder.
But to get all files in subfolders you need to use find. 

### Extraction Process

To extract all strings from all

Create the file `/opt/lampp/htdocs/extract' which will use `find` to search for all php files and run xgettext on each one.
The file should have the following content:
```
touch messages.po
find ./app/views -type f -name "*.php" -exec xgettext --keyword=__ -j {} \;
mv messages.po messages.pot
```
This will do the following:
1. create the messages.po file for output
2. find all php files within the views folder and its subfolders
3. process each of these files and find all strings enclosed within the `__()` function.
4. rename messages.po to messages.pot

Make the extract script executable
```
chmod +x extract
```

## Internationalisation (i18n) 

Internationalisation is a long word starting with `i`, ending with `n`, and counting 18 letters in between.
Hence the abbreviation i18n.
It is the process of taking an application and making it compatible with localisations.
Localisations are different translations of your Web applications. 

### Internationalise your view strings 

For all views, convert the files to output strings from PHP with `<?=__('')?>` around all strings excluding the HTML tags containing these strings. For example: 
```
<html> 
<head><title>Hello World!</title></head> 
<body> 
<h1>Hello World!</h1> 
<p>I like cats.</p> 
</body> 
</html> 
```
becomes 
```
<html> 
<head><title><?= __('Hello World!') ?></title></head> 
<body> 
<h1><?= __('Hello World!') ?></h1> 
<p><?= __('I like cats.') ?></p></body> 
</html> 
```

### Internationalise your other strings 

If you have strings output directly in your controllers, or in helper functions, don’t forget about them. For example, 
```
echo 'The current date is ', strftime('%V,%A,%G,%Y', $date1->getTimestamp()); 
```
should become 
```
echo __('The current date is '), strftime(__('%V,%A,%G,%Y'), $date1->getTimestamp()); 
```
This way, we internationalise the text and also the date formats. 

### Extract all Strings to Adapt 

From the `/opt/lampp/htdocs` folder, run the `extract` script that we built earlier to create the `messages.pot` file with all strings from all views.
As a reminder, the script contains the following commands:
```
touch messages.po
find ./app/views -type f -name "*.php" -exec xgettext --keyword=__ -j {} \;
mv messages.po messages.pot
```
The first command creates an empty file.
Then we run the find command to run xgettext on all php files in our views.
Then rename the file to .pot to make it a “template”. 

### Folder Structure for Resource Files 

Build a folder structure from the project base folder (where `.htaccess` and the `index.php` entry point are located), htdocs for example, as follows: 
```
htdocs 
 └locales 
   └en 
     └LC_MESSAGES 
```
In this tree structure, en is one of the locales.
There will be one branch per locale, for example, the following structures are for the `fr` locale and the `es` locale:
```
htdocs 
 └locales 
   └fr 
     └LC_MESSAGES 
```
and
```
htdocs 
 └locales 
   └es 
     └LC_MESSAGES 
```

### Loading the Resource Files 

We have not created the resource files yet, this will be the process of localisation (l10n), which we will see in the next section.

To load existing resource files, we will make use of the Symfony Translation component.
In our project folder, we will add the Symfony Translation component by running
```
composer require symfony/translation
```
This will add all the packages that are required to handle the translation files.
Next, we will include the composer autoload files to our Web application code by adding the following line in the `app/core/init.php` file, just below the session_start(); instruction:
```
require_once('vendor/autoload.php');
require('app/core/i18n.php');
```
The second inclusion is for the next file we will write, `app/core/i18n.php`, which will load the language resources as required.
The file goes as follows:
```
<?php
//use statements to simplify code below
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\MoFileLoader;
//list the localisations that have been built
$supportedLocales = ['fr', 'en'];
//get the requested locale to use here we default to 'fr'
$locale = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'fr';
//ensure the locale is supported
if(!in_array($locale, $supportedLocales))
	$locale = 'fr';
//save the setting to a cookie
setcookie('lang',$locale,0,'/');
//initialise a translator object for the locale
$t = new Translator($locale);
//add a loader for .mo files
$t->addLoader('mo', new MoFileLoader());
//grab the .mo file resource from the folders previously built
$t->addResource('mo',"./locales/$locale/LC_MESSAGES/messages.mo", $locale);

//define a helper function to load translations from the translator
//that remains unobstrusive for writing in views
function __($message){
	global $t;
	return $t->trans($message);
}
```

## Localisation (l10n) 

Localisation is the process of adapting the application for different locales. This means we modify the language, and also the way some data is displayed, e.g., dates. 

We will now create translations starting from the template file messages.pot. 

### Tool setup: msginit and msgfmt

The `msginit` tool is built to prepare .po translation files ready to accept new translations.

Create the `/opt/lampp/htdocs/msgfmt` script with the following contents:
```
/opt/lampp/bin/msginit "$@"
```
Make it executable:
```
chmod +x /opt/lampp/htdocs/msginit
```

The `msgfmt` tool is built to build .mo binary translation files from .po translations files.

Create the `/opt/lampp/htdocs/msgfmt` script with the following contents:
```
/opt/lampp/bin/msgfmt "$@"
```
Make it executable:
```
chmod +x /opt/lampp/htdocs/msgfmt
```

### Initialise the translation

Make sure that you have the folders needed for your new localisation.
Here we will build the english localisation:
```
cd /opt/lampp/htdocs/locales
mkdir en
cd en
mkdir LC_MESSAGES
```
Go back to `/opt/lampp/htdocs`.
```
cd /opt/lampp/htdocs
```
Run the utility for english:
```
msginit --input messages.pot --locale=en --output=locales/en/LC_MESSAGES/messages.po
```

### Edit the .po file:
```
nano locales/en/LC_MESSAGES/messages.po
```

Your objective is to complete all the `msgid/msgstr` pairs such that there is actual text associated to each message id string.
For English, a pair may look as follows:
```
#: app/views/User/login.php:11 app/views/User/registration.php:12
#: app/views/User/update.php:12
msgid "Username:"
msgstr "Username:"
```
For French it may look as follows:
```
#: app/views/User/login.php:11 app/views/User/registration.php:12
#: app/views/User/update.php:12
msgid "Username:"
msgstr "Nom d'utilisateur:"
```

### Convert the .po to .mo (binary)
To make the file easily read by the application, it is best to convert it to the .mo binary format as follows:
```
msgfmt -o locales/en/LC_MESSAGES/messages.mo locales/en/LC_MESSAGES/messages.po
```

### Run the application with the localisation 

For the `en` localisation that we just created, you should be able to run it by adding `?lang=en` to any URL accessing the Web application. 
For the `fr` localisation, you should be able to run it by adding `?lang=fr` to any URL accessing the Web application. 
