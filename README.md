## Requirements
* PHP >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Composer
* Node.js 8+
* NPM

## Run the code
1. Build the app
```
composer update
npm install
npm run production
```

2. Run the server
Go to the project folder and run the following command
```
php -S localhost:8000 -t public
```

3. View the project in the browser
Open any web browsers with the following URL: http://localhost:8000/

Note: you can use a different port if the port 8000 is taken by another application.

## Contribute to this project
The PHP code follows the code style guidelines of PSR-2.
Use PHP CodeSniffer to test the files if they are following the standards.
```
./vendor/bin/phpcs --standard=PSR-2 THE_FILE_PATH
```

In order to test the project using PHP Unit:
```
./vendor/bin/phpunit
```

In order to build the web app:
```
composer update
npm install
npm run production
```

## Structure of this project
* The CSS files, the JS files and the images are found under "resources/assets/"
* The views are found under "resources/views/"
* The TicTacToe library is found in "app/Libraries/TicTacToe.php"
* The test files is found under "tests/"
* The API routes file is found under "routes/web.php"

## TODO
* Use Laravel Mix with the Javascript dependencies (React, ReactDOM, Babel)
* Use StandardJS with ESLint to set a code style guideline for the React code
* Save in LocalStorage the details about the session
* Security: implement PHP sessions to prevent the user from tempering the board on each HTTP request
* Security: setup HTTPS and generate a SSL certificate using Certbot
* Security: add a middleware to handle HSTS

## Attributions
* Icon: copyright of QuizAnswers (http://www.quizanswers.com) -- Brain Games icon pack
