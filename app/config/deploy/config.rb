depend :remote, :command, :git

set :stages,           [ "live", "staging" ]
set :default_stage,    "staging"
set :stage_dir,        "app/config/deploy"
require                "capistrano/ext/multistage"

# user set on each stage file
set :application,     "onyx"
set :scm,             :git
set :repository,      "git@bitbucket.org:viperdigo/#{application}.git"
set :deploy_via,      :remote_cache

set :use_sudo,        false


set :app_path, "app"

set :shared_files,        [ "app/config/parameters.yml" ]
set :shared_children,     [ app_path + "/logs" ]
set :use_composer,        true
set :interactive_mode,    false
set :dump_assetic_assets, true
set :composer_options,    "--verbose --prefer-dist --optimize-autoloader --prefer-source"
set :copy_vendors,        true

# shared files set by stage file (live.rb and staging.rb)
#set :public_children, []

set :model_manager, "doctrine"

default_run_options[:pty]   = true
ssh_options[:forward_agent] = true

logger.level = Logger::MAX_LEVEL

namespace :worldplay do

    desc "Create Release folder"
    task :create_release, :except => { :no_release => true } do
        logger.important ">>> If not found release's  folder, create the folder"
        try_sudo "releasesDir=/var/www/html/#{application}/#{stage}/releases; if [ ! -d $releasesDir ]; then mkdir -p $releasesDir; fi;"
    end

    desc "Update parameters.yml with sed"
    task :update_parameters do
        capifony_pretty_print "--> Updating parameters file"
        # run "sed -f /etc/deploy/parameters.yml #{current_release}/app/config/parameters.yml > #{latest_release}/app/config/parameters.yml"
        capifony_puts_ok
    end
end

namespace :deploy do

    task :restart do
        run "sudo service apache2 restart"
        run "sudo service php5-fpm restart"
    end
end

before  "deploy",  "worldplay:create_release"
after  "deploy:update", "deploy:cleanup"

# Run migrations before warming the cache
before "symfony:cache:warmup", "symfony:doctrine:migrations:migrate"

after 'deploy:rollback:revision', 'symfony:cache:clear'
after 'deploy:rollback:revision', 'symfony:assetic:dump'
after 'deploy:rollback:revision', 'deploy:restart'
