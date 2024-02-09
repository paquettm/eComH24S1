# eComH24S1
Winter 20204 eCommerce 42-411-VA Section 1 repository

## Commands run

Created my repository

```
git clone https://github.com/paquettm/eComH24S1.git
```

Opened that new eComH24S1 folder to work and place my new Web application.

You call your project the way you want.

```
git add .
```

```
git commit -m "adding instructions"
```

I should normally have to set up my identity on a computer if this was not previously done.

```
git config user.name "Michel Paquette"
```

```
git config user.email "paquettm@vaniercollege.qc.ca"
```

```
git add .
```

```
git commit -m "adding instructions"
```


**** fix the problem where I can't push from the school compputers... are there ports that are blocked? ******


## Running the Docker container to host the Web application

Start Docker Desktop first.

Then run 
```
docker run --name myXampp -p 22:22 -p 80:80 -d -v C:/Users/paquettm/eComH24S1:/opt/lampp/htdocs tomsik68/xampp
```

## Accessing the project

Open a browser and point it to

```
http://localhost
```

## Bootstrap the project

Create a file called `.htaccess`.
```
Options -MultiViews
Options -Indexes

RewriteEngine On

RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
RewriteRule ^()$ index.php?url=$1 [QSA,L]
```
Copy this file exactly, don't add a single character.

## core folder

holds all the framework core functionality that will not be modified by the programmer/user

The App class is there to call the appropriate Controller class and method for the received HTTP request

## Controllers

Controllers contain the application logic that constitutes the glue between the request (after routing) the models and the views.

To be continued...

## Theory session 2

The following themes were adressed:

- Adding a view method to our Person controller
  - $this
  - calling methods in general and the view method
- Refactoring by moving the view method to a Controller superclass
  - calling the view method again
- Introducing views
  - passing data with parameters and using local variables
  - displaying simple data
  - foreach with sequential and associative arrays

## up next

- Routing 
- Autoloading (See php_guide repo)
- Assignment 1: Static website (to be published)
- Calling a partial view from within a view

## TODO

solve git push problem in room D-241? (Works from Office workstation and D-242.)
