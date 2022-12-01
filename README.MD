1) run in terminal 'docker-compose up'
2) connect to db (find configuration in .env file)
3) run in terminal 'docker exec -it <name app container> bash' 
4) run 'php bin/console doctrine:migrations:migrate' to create tables 
5) run 'php bin/console doctrine:fixtures:load' to load fake data to table
enjoy :) 