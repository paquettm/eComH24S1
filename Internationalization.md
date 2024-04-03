# Internationalisation and Localisation of an MVC Web Application in a Docker container

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
Gettext is so popular because it loads language definitions in memory and can serve up results to web clients based on their language settings, with minimal code added to our applications.

More specifically, 
- Strings in our views will be output by a gettext function instead of directly. This will imply that each string is output from the `_()` function within a php output block `<?= ?>` as follows `<?=_('string')?>`.
- These strings will then get extracted by a tool, xgettext, and placed in a localisation template.
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
find ./app/views -type f -name "*.php" -exec xgettext -j {} \;
mv messages.po messages.pot
```
Make it executable
```
chmod +x extract
```

## Internationalisation (i18n) 

Internationalisation is a long word starting with I, followed by 18 letters and then n, hence it is abbreviated i18n. It is the process of taking an application and making it compatible with localisations. Localisations are different translations of your Web applications. 

### Internationalise your view strings 

For all views, convert the files to output strings from PHP with _("") around all strings excluding the HTML tags containing these strings. For example: 
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
<head><title><?= _("Hello World!") ?></title></head> 
<body> 
<h1><?= _("Hello World!") ?></h1> 
<p><?= _("I like cats.") ?></p></body> 
</html> 
```

### Internationalise your other strings 

If you have strings output directly in your controllers, or in helper functions, don’t forget about them. For example, 
```
echo “The current date is ”, strftime("%V,%A,%G,%Y", $date1->getTimestamp()); 
```
should become 
```
echo _(“The current date is ”), strftime(_("%V,%A,%G,%Y"), $date1->getTimestamp()); 
```
This way, we internationalise the text and also the date formats. 

### Extract all Strings to Adapt 

From the `/opt/lampp/htdocs` folder, run the `extract` script that we built earlier to create the `messages.pot` file with all strings from all views.
As a reminder, the script contains the following commands:
```
touch messages.po
find ./app/views -type f -name "*.php" -exec xgettext -j {} \;
mv messages.po messages.pot
```
The first command creates an empty file.
Then we run the find command to run gettext on all php files in our views.
Then rename the file to .pot to make it a “template”. 

### Folder Structure for Resource Files 

Build a folder structure from the project base folder (where .htaccess and the index.php entry point are located), htdocs for example, as follows: 
```
htdocs 
 └locale 
   └en 
     └LC_MESSAGES 
```
This is the way to work under Windows because Windows only accepts working with its installed locales.
Here we assume the installed locale is “en” and we will show you later how to determine the installed locale on your Windows computer. 

### Determining which Language to Use 

You may be using a different base language on your computer and therefore may need to change the folder name “en” above. To determine this, proceed as follows: 

In php.ini, activate intl extension and restart Apache. 

Run the PHP code "echo Locale::getDefault();" to see which base localisation is used on your computer. 

If the result is not “en” or “en_xx” where xx could be anything, then change the path above to the part of the locale before the underscore. E.g., if your result is fr_CA, then rename the “en” folder to “fr”. 

### Loading the Resource Files 

We have not created the resource files yet, this will be the process of localisation (l10n). 

To load existing resource files for your language, add the following code at the starting point of your application. This could be an initialization script that includes all resources, for example the init.php file in our homemade framework (read the comments for explanations): 

```
//to accept languages from the querystring as follows: mysite.com?lang=fr_CA 
if(isset($_GET['lang'])){ //if there is a language choice in the querystring 
	$lang = $_GET['lang'];//use this language 
	setcookie("lang",$lang, 0, '/'); //set a cookie for the entire domain 
}else 
	$lang=(isset($_COOKIE["lang"])?$_COOKIE["lang"]:'en'); //from cookie or default 
//extract the root language from the complete locale to use with strftime 
$rootlang = preg_split('/_/', $lang); 
$rootlang = (is_array($rootlang)?$rootlang[0]:$rootlang); 

setlocale(LC_ALL, $rootlang.".UTF8");//which locale to use. .UTF8 is to ensure proper encoding of output 
bindtextdomain($lang, "locale"); //pointing to the locale folder for the language of choice 
textdomain($lang); //what is the file name to find translations 
```

## Localisation (l10n) 

Localisation is the process of adapting the application for different locales. This means we modify the language, and also the way some data is displayed, e.g., dates. 

We will now create translations starting from the template file messages.pot. 

Use Poedit to open the new messages.pot file 

Open Poedit, click “Browse files” and open the messages.pot file produced in an earlier step. If you have a red window on top, set the data and save it else click create new translation. Let’s use French (Canada) for our example. 

Select French(Canada) 

You will be presented with a list of strings extracted from your application in the top pane, and you will need to provide translations for all of these that need to change for this language/locale. For all strings in the top pane: 

Select the string to translate in the source text pane on top 

Confirm the source text in the middle pane 

Provide the translation in the bottom pane or clock on the right choice in the right pane... but these given options will run out. 

Once you are done, save the fr_CA.po file to the LC_MESSAGES folder that we created earlier. 

Run the application with the localisation 

For the fr_CA localisation that we just created, you should be able to run it by adding ?lang=fr_CA to any URL accessing the Web application. 
