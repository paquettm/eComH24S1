# Codeception for Acceptance Testing (Windows) 

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

## The Setup (XAMPP/WINDOWS VERSION)

We start by setting up our project with Codeception: 

### Shortcut to the PHP interpreter
To simplify life, with XAMPP, write a php.bat file in your project folder to invoke php.exe with all the parameters forwarded to the executable from c:\xampp\php. This is done as follows, from the command line, in the c:\xampp\htdocs folder type: 

``` 
c:\xampp\php\php.exe %* 
``` 

The php.exe executable is located in the c:\xampp\php folder.
The %* term will take all parameters from the command-line call to php.bat and forward them to the call to php.exe. 
 
### Get Composer

Get and install composer by following the instructions from the top of the page at https://getcomposer.org/download/ and as in the box below: 
The above instructions will install the composer.phar file to the current folder (c:\xampp\htdocs ideally).
The instructions look like the following, but go grab the up-to-date ones.

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"php composer-setup.phpphp -r "unlink('composer-setup.php');"
```
 
### Shortcut to Composer
To simplify life, in Windows, write a composer.bat file that runs php composer.phar with all remaining parameters after. This is done as follows, from the command line in the project folder: 
 
```
php composer.phar %* 
```
### Add Codeception to your project

To add Codeception to your project development dependencies, in your project base folder run 

```
composer require "codeception/codeception" --dev 
```
 
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

Next, for convenience again, create codecept.bat as a shortcut to the codecept script as follows, at the command line, in your project folder: 
 
```
php vendor/bin/codecept %* 
``` 

### Preparing the Codeception folders

Create the basic codeception testing suite using the bootstrap command 
```
codecept bootstrap 
```
This will create the tests folder and all default configurations. 
 
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
