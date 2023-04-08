
# ASPIRE

## PROJECT BUILD UP
### Requirements
- Docker
- Makefile (if your OS is Windows, please install Makefile to run the build up script)

### Build up action
1. Run command to build up system:
    ```
    make init-project
    ```

    If everything is ok, we can use system normally (details of api using in the next section).

    Notes: for details of this command, please refer this file: `./environment-setup/makefiles/init-project.mk`
    
2. Troubleshooting
    - For windows: remove all sudo in the `./environment-setup/makefiles/init-project.mk` before run the `make init-project` command
    
    - Nginx timeout
        + phenomena: in `./environment-setup/nginx/log/error.log` has timeout exception, but when routing to index.html (self-create in public folder) is ok - without any problems
        + reason: Phpstorm is listening for debugging, so php was caught --> turn off debug listening
        
    - Allowed memory size of xxx bytes exhausted (tried to allocate xxx bytes) <br>
        Permission denied in storage folder --> sudo chmod 777 -R storage

## How to use the app
### Run api by postman
- Postman collection file: `./zdoc/ASPIRE.postman_collection.json`
- Postman environment variable: `base_url=http://localhost:8085/api/v1`

Provided three user for testing the api (1 admin and 2 customers).

### Execute test
1. ssh to artisan container
   ```
   make artisan-sh
   ```
2. Run the test
   ```
   php artisan test
   ```

### Additional
- how to run composer
  ```
  make composer
  ```

## Core code structure
- environment-setup folder <br>
   Provided all docker setup, include: docker-compose, dockerfile, makefile commands, nginx config, php xdebug config
- app\Modules\auth folder  <br>
    Provided all authenticating method
- app\Modules\Loan folder <br>
    All our challenge logic here
- tests folder <br>
    Provided all Feature and Unit test

## Remarks
   - For convenient, I configured QUEUE_CONNECTION=sync, however you can change it to redis (if needed and remember to run queue:work) 

