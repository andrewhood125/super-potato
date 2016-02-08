@servers(['web' => 'deployer@45.55.2.134'])

@task('deploy', ['on' => 'web'])
    cd super-potato
    git fetch --all
    git reset --hard origin/master
    ./artisan route:cache
    composer install
    ./artisan migrate --force
    npm install
    ./node_modules/.bin/bower install
    ./node_modules/.bin/gulp
@endtask
