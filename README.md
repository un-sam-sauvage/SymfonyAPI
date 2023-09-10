# API
## Installation
Start by cloning the project inside the directory of your choice, <br />
you can then open a terminal window in that directory, <br />
you can then load examples data as in the fixtures with `php bin/console doctrine:fixtures:load` <br />

## making your first query
To make your first query, you can use the documentation made with <a href="https://github.com/nelmio/NelmioApiDocBundle">nemlio</a> <br />
you can found the page once you started the server and the database at the address : http://localhost:8000/api/doc <br />

WARNING : before making your first query, you will need to generate some certificate because the api is protected by JWT.
To do that, do the following in your terminal : `php bin/console lexik:jwt:generate-keypair`
Once the command is finished, you can try using the API. If it is not working with an SSL error : try regenerating the keypair with the flag : `overwrite` so `php bin/console lexik:jwt:generate-keypair -overwrite`
