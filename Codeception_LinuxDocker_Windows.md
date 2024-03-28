# Codeception for Acceptance Testing (with Linux Docker Container Running in Windows) 

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

### Ensuring Correct Script execution

We must ensure that the path contains the current folder to allow all scripts to run correctly.
Check if your path contains `.` by running
```
echo $PATH
```
If it does not, run the following to append `.` to the end of the path: 
```
export PATH=$PATH:.
```

### Shortcut to the PHP interpreter
To simplify life, write a `php` file in your project folder to invoke the php executable with all the parameters forwarded to the php executable.
This is done by adding a file called `php` to the `/opt/lampp/htdocs` folder, for example with the nano editor using the command `nano /opt/lampp/htdocs/php`, and then adding the following contents:
``` 
#!/bin/bash

/opt/lampp/bin/php "$@"
``` 
If you use nano as your text editor, make your changes, then press CTRL-O + ENTER to save the changed to your opened file and press CTRL-X to exit.

Then, we must make the script executable with the following Linux command:
```
chmod +x php
```
**Note it is important to name the file exactly `/opt/lampp/htdocs/php` with no extension for the next steps to work as written.**
The `"$@"` term will take all parameters from the command-line call to the `php` script and forward them to the call to the `php` executable.
The php executable is located in the `/opt/lampp/bin` folder in the Docker image we use in this example;
if your `php` executable is located elsewhere, use its full path to replace that of the example.

At this point, it is important to note that the file MUST be created using the Linux container CLI since Windows will not save characters the same way, resulting in errors stating that the php executable is not found.
This error is likely caused by incompatible definitions of carriage returns between Windows and Linux, but this is not verified (let me know in an issue).

### Get Composer

Get and install composer by following the instructions from the top of the page at `https://getcomposer.org/download/` which may be identical to those in the box below (date: March 27, 2024).
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
The instructions will install the `composer.phar` file to the current folder (`/opt/lampp/htdocs` which was mapped to your project folder).
 
### Shortcut to Composer
We now write another shortcut file named `composer` in the `/opt/lampp/htdocs` folder, full path `/opt/lampp/htdocs/composer`, to run `php composer.phar`, forwarding all call parameters.
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

To add Codeception to your project development dependencies we use composer.
From the CLI, while located in your project base folder, run 
```
composer require "codeception/codeception" --dev 
```
If you ever get an error such as `bash: YOUR_COMMAND: command not found` where YOUR_COMMAND can be any command, then run the PATH export command shown above.

**You should see a new folder appear: vendor.**
If it was already there, then some other subfolders probably appeared and, especially notice the `composer.json` file should now contain mention of Codeception in a dev section, similar (but maybe different) to the following: 
```
{ 
    "require-dev": { 
        "codeception/codeception": "^5.0" 
    } 
} 
``` 

### Shortcut to Codeception

Next, we create a script file named `codecept` in the `/opt/lampp/htdocs/` directory, maybe with the command `nano /opt/lampp/htdocs/codecept`, as a shortcut to the codecept script, containing: 
```
#!/bin/bash

./php vendor/bin/codecept "$@" 
```
and make it executable with
```
chmod +x codecept
```

### Preparing the Codeception folders

We create the basic codeception testing suite using the codeception `bootstrap` command as follows: 
```
codecept bootstrap 
```
This will create the `tests` folder and all default configurations.
You may be told that modules are added and asked if you wish to run `composer update`.
Answer `y` for yes.

If you get any error, it is likely because codeception was not able to access the correct files because your path environment viariable does not contain `.`.
Now you have to delete the tests and vendor folders, composer.json, composer.lock, and codeception.yml with the following commands:
```
rm -rf tests
rm -rf vendor
rm composer.lock
rm composer.json
rm codeception.yml
```
This time, add `.` to the path for real, as follows:
```
export PATH=$PATH:.
```
Re-do section `Add Codeception to your project` and restart this section.

### Configure Codeception

We now configure how our tests are run by default by modifying the `Acceptance.suite.yml` file in the `tests` subfolder. 
Specifically, change the value of url form `localhost/myapp` to `localhost` as in the following example:
```
# Codeception Acceptance Test Suite Configuration
#
# Perform tests in a browser by either emulating one using PhpBrowser, or in a real browser using WebDriver.
# If you need both WebDriver and PhpBrowser tests, create a separate suite for each.

actor: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: http://localhost/
# Add Codeception\Step\Retry trait to AcceptanceTester to enable retries
step_decorators:
    - Codeception\Step\ConditionalAssertion
    - Codeception\Step\TryTo
    - Codeception\Step\Retry       
```
We are assuming here that our project runs direct from the localhost URL and that there are no virtual servers.

## Building Test Suites 

We now explore writing test specifications using the Gherkin language.
Specifically, we write feature files which contain descriptions and test scenarios.

The Gherkin language uses natural language with the addition of only a few simple keywords, allowing stakeholders to partake in the feature writing process.
Moreover, upon parsing of these feature files, Codeception generates function stubs which you simply complete to have functional test code. 

### Generate Feature Files
We generate a feature file, as an example, by running the following command:
```
codecept g:feature Acceptance google
```
Here we are asking codeception (`codecept`) to generate (`g:`) a `feature` in the `Acceptance` test folder with the name `google`.
Codeception will add the file named `google.feature` in the `tests/Acceptance` subfolder. 
Open this file, e.g.:
```
nano tests/Acceptance/google.feature
```

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
Paste the snippets in the `tests/Support/AcceptanceTester.php` file within the `class AcceptanceTester` definition. 

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
Notice above that we have changed parameter names and that if you only copy and pasted method contents, you should see corresponding errors in the next step.

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
