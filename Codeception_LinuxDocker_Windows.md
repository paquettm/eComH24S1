# Codeception for Acceptance Testing (Linux Docker image in Windows) 

This is an introduction to using Codeception, a testing framework built upon widely used tools such as PHPUnit.
Codeception is the standard for testing (unit, functional, and acceptance testing) of PHP applications. 

Requirements can be expressed as expected behaviour of an application or piece of code.
These behaviours are easily translated as Unit/Functional/Behaviour tests.

In a perfect world, it is easy to imagine that testing is built into the development process and our code is always compliant with the requirements that are verified by these tests.
This is the philosophy behind Behaviour-Driven Development (BDD) and Test-Driven Development (TDD).  

This is an introduction to using Codeception, a testing framework built upon widely used tools such as PHPUnit, within a Test-Driven Development approach.

## Test-Driven Development (TDD) 

Most developers will design and run tests near the end of their projects because they are eager to develop applications that are functional right away.
This approach comes at the price of incomplete test coverage, i.e., not all situations are covered by our tests and bugs slip into our code.
A reversed approach to testing is required to improve our code quality.  

Test design is actually a great way to communicate expectations on software expressed by an end user.
It is ideally done as a first step in a development methodology, within a requirements-gathering phase.

The test-driven development approach asks us to write requirements as tests before writing the code to satisfy requirements.
The process goes as follows: 

- Build a test, run it, and watch it fail (this new test has failed because the code to make this functionality possible does not exist). 
- Write the minimum code to make the test pass: This can be done by hardcoding the expected values to prove that the functions actually get called. 
- Refactor the new code better, simpler, more descriptive as needed, no new logic. This way, the values are not hardcoded anymore. 

Here, we design a test first, and to satisfy this test, we develop code.
The development is driven by the need to pass the test, hence Test-Driven Development.

This process allows us to plan our implementation by taking the time to think about its use before we start coding new logic.

## Acceptance-Testing with Codeception 

The concept of automated testing is simple.
We write code to run processes in our software and to check that the results are as expected.
Then, we make sure that these tests run on an automation, each time we commit a new version of our code to a reposiroty, for instane.

With automated testing frameworks, we write tests in driver classes that use functions to compare the results of our applications against expected results.
Codeception integrates functionality to define test suites into driver classes and, when these are run, reports on the results of the testing process. 

## The Setup (Linux Docker running in Windows VERSION)

Ensure that your Docker engineis running by starting up your instance of Docker Desktop.

### Start your Docker Container

Ensure that your Docker container is running and pointing to your project code.
For example, with no container called myXampp or using ports 22/80, at the command line run:
```
docker run --name myXampp -p 22:22 -p 80:80 -d -v YOUR_PROJECT_LOCATION:/opt/lampp/htdocs tomsik68/xampp
```
For example, if your project Web root folder is `C:\MyProject` then the code should be 
```
docker run --name myXampp -p 22:22 -p 80:80 -d -v C:\MyProject:/opt/lampp/htdocs tomsik68/xampp
```

### Start a Terminal to your Container
You can use the terminal from Docker Desktop for your container to run the `bash` command or run the terminal from the Windows command line as follows:
```
docker exec -it myXampp bash
```
This runs the bash terminal program and gives you access at the command line.

Now we start by setting up our project with Codeception: 

### Shortcut to the PHP interpreter
To simplify life, write a `php` file in your project folder to invoke the php executable with all the parameters forwarded to the php executable.
This is done by adding a file called `php` to the `/opt/lampp/htdocs` folder and with the following contents:
``` 
#!/bin/bash

/opt/lampp/bin/php "$@"
``` 
and then, top make it executable,
```
chmod +x php
```
The php executable is located in the /opt/lmpp/bin folder in our example.
The `"$@"` term will take all parameters from the command-line call to the `php` script and forward them to the call to the `php` executable. 
 
### Get Composer

For convenience and as a temporary measure, we will add . to the path to allow simplified running of scripts.
Run
```
export PATH=$PATH:.
```

Get and install composer by following the instructions from the top of the page at `https://getcomposer.org/download/` which may be identical to those in the box below.
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
The instructions will install the composer.phar file to the current folder (/opt/lampp/htdocs which was mapped to your project folder).
 
### Shortcut to Composer
To simplify life, now write a file named `composer` in the `/opt/lampp/htdocs` folder to run `php composer.phar` with all remaining parameters after.
The file contains: 
```
#!/bin/bash

./php composer.phar "$@"
```
Make the file executable:
```
chmod +x composer
```

### Add Codeception to your project

To add Codeception to your project development dependencies, in your project base folder run 

```
composer require "codeception/codeception" --dev 
```
If you ever get an error such as `bash: YOUR_COMMAND: command not found` where YOUR_COMMAND can be any command, then simply add ./ in front of the command to run it (if it is there in the present working directory (or run the export bit shown above).

You should see a new folder appear: vendor.
If it was already there, then some other subfolders probably appeared and, especially notice the `composer.json` file should now contain mention of Codeception in a dev section, similar (but maybe different) to the following: 
```
{ 
    "require-dev": { 
        "codeception/codeception": "^5.0" 
    } 
} 
``` 

### Shortcut to Codeception

Next, for convenience again, we create a script `/opt/lampp/codecept` as a shortcut to the codecept script as, containing: 
 
```
#!/bin/bash

./php vendor/bin/codecept "$@" 
```
and make it executable with
```
chmod +x codecept
```

### Preparing the Codeception folders

Create the basic codeception testing suite using the bootstrap command 
```
codecept bootstrap 
```
This will create the tests folder and all default configurations.
You may be told that modules are added and asked if you wisht to run `composer update`.
Answer `y` for yes.

If you get any error, it is likely because codeception was not able to access the correct files because of the path.
delete the test folder, composer.json, composer.lock, and the vendor folder with the following commands:
```
rm -rf tests
rm -rf vendor
rm composer.lock
rm composer.json
```
Export . to the path as follows:
```
export PATH = $PATH:.
```
Re-do section `Add Codeception to your project` and restart this section.


### Configuring Codeception

We will now configure the way that our tests are run by default by modifying the acceptance.suite.yml file in the tests folder. We wish specifically to change the main server URL to localhost as in the following example: 

```
# Codeception Test Suite Configuration 
# 
# Suite for acceptance tests. 
# Perform tests in browser using the WebDriver or PhpBrowser. 
# If you need both WebDriver and PHPBrowser tests - create a separate suite. 
 
actor: AcceptanceTester 
modules: 
    enabled: 
        - PhpBrowser: 
            url: http://localhost/ 
step_decorators: ~         
```

## Building Test Suites 

We now explore writing test specification using the Gherkin language to write feature descriptions and test scenarios
The Gherkin language uses natural language with the addition of only a few simple keywords, allowing your clients to partake in the feature writing 
Moreover, upon parsing of these tests, Codeception handles partial generation of the test code. 

### Generate Feature Files
To generate your first feature file example, run the following command
```
codecept g:feature Acceptance google
```
Here we are asking codeception (`codecept`) to generate (`g:`) a `feature` in the `Acceptance` test folder with the name `google`.
If you navigate to the `tests/Acceptance` subfolder, you will find a new file named `google.feature`. 
Open this file.

### Complete Feature Files
Let’s write the feature specification and test scenarios that make up the acceptance tests.
Take the following `google.feature` file as an example: 

```
Feature: google 

  In order to Google terms 
  As a user 
  I need to input search terms on the main google.ca page and get matching results back 
 
  Scenario: try googling "frog" 
    Given I am on "http://www.Google.ca" 
    When I enter "frog" in the search box 
    And click Search 
    Then I see "frog" 
 
  Scenario: try googling "dog" 
    Given I am on "http://www.Google.ca" 
    When I enter "dog" in the search box 
    And click Search 
    Then I see "dog" 
```

In this file above, the `Feature:` section provides documentation on the feature that we wish to develop.
We state the action we want a type of user of the system to take and what we expect. 

Also above, in each `Scenario:`, we describe a successful interaction with the system.
Each `Scenario:` is an acceptance test written in Gherkin with its Given-When-Then format to express different portions of the test:

- Given: these are preconditions, e.g., to be on a page, to have an account, etc. 
- When: these are actions, e.g., typing and clicking, navigating, etc. 
- Then: the requirements making up the expected result, e.g., seeing a result that matches the expectation
- And: this is to add an extra precondition, action, or requirement to the Given-When-Then sections.

### Dry-run
We now dry-run the new feature.
Run
```
codecept run Acceptance google.feature 
```

### Completing Code
There is code missing for the test to be complete.
We want our framework to run the tests from the feature file, but the computer does not understand natural language.
We ask codeception what code snippets we need to add into our test context to be able to run feature file tests:
```
codecept gherkin:snippets Acceptance 
```
You will get output as follows: 
```
Snippets found in: 
  - google.feature 
 Generated Snippets: 
 ----------------------------------------- 
    /** 
     * @Given I am on :arg1 
     */ 
     public function iAmOn($arg1) 
     { 
         throw new \PHPUnit\Framework\IncompleteTestError("Step `I am on :arg1` is not defined"); 
     } 
 
    /** 
     * @When I enter :arg1 in the search box 
     */ 
     public function iEnterInTheSearchBox($arg1) 
     { 
         throw new \PHPUnit\Framework\IncompleteTestError("Step `I enter :arg1 in the search box` is not defined"); 
     } 
 
    /** 
     * @When click Search 
     */ 
     public function clickSearch() 
     { 
         throw new \PHPUnit\Framework\IncompleteTestError("Step `click Search` is not defined"); 
     } 
 
    /** 
     * @Then I see :arg1 
     */ 
     public function iSee($arg1) 
     { 
         throw new \PHPUnit\Framework\IncompleteTestError("Step `I see :arg1` is not defined"); 
     } 
 
 ----------------------------------------- 
 4 snippets proposed 
Copy generated snippets to AcceptanceTester or a specific Gherkin context
```

The above output mentions that there are 4 functions missing from the `AcceptanceTester.php` class file for the framework to know what to do with the sentences written in the Feature file `Scenario:` sections.
Codeception provides the function definitions to be completed to ensure proper test functionality.

### Generating The Functions
Copy the code snippets from the screen output (In Windows by selecting them and pressing the ENTER key).
Paste the snippets in the `tests/_support/AcceptanceTester.php` file within the `class AcceptanceTester` definition. 

Replace the function bodies with logic that will provide meaning to the sentences that we put in the test scenarios.
In our example: 
```
    /** 
     * @Given I am on :arg1 
     */ 
     public function iAmOn($url) 
     { 
         $this->amOnPage($url); //make the browser go on a URL 
     } 
 
    /** 
     * @When I enter :arg1 in the search box 
     */ 
     public function iEnterInTheSearchBox($term) 
     { 
         $this->fillField('q', $term);//write the term in the box 
     } 
 
    /** 
     * @When click Search 
     */ 
     public function clickSearch() 
     { 
        $this->click('Google Search'); 
     } 

    /**
     * @Then I see :arg1 
     */ 
     public function iSee($arg1) 
     { 
         $this->see($arg1);//assert that you can see the string 
     }
```

### Running a Test
Your first test is complete.
To run exclusively the Googling feature test, run 
```
codecept run Acceptance google.feature 
```
To run all acceptance tests, run the following.
```
codecept run Acceptance 
```

Looking at the output, you should now see that all tests have passed. 

### Building More Tests 

Codeception can be used to build and run unit tests, functional tests, and acceptance tests.
You can build your tests directly as code, but this will remove your methodological advantage of integrating your testing as part of an agile methodology.  

Note that you will be able to integrate PHPUnit tests as unit tests if you wish.  

Refer to [the Codeception referene](https://codeception.com/docs/reference/Commands) to learn more about commands to use.

## Conclusion 

You have all the skills needed to write full test suites for you application. 

## Exercise 

Write one full feature and at least two scenarios that define a feature and acceptance tests for a part of your completed assignment. Go through the steps to dry run and generate the code, then complete the code and see your test pass. 

Write one full feature and at least two scenarios that define a feature and acceptance tests for a part of your Term Project. Go through the steps to dry run and generate the code, then complete the code and see your test fail. 

Try to write the minimum code needed to make your automated tests pass.
