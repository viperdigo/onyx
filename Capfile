load 'deploy'
# Uncomment if you are using Rails' asset pipeline
# load 'deploy/assets'
require 'capifony_symfony2'

after "deploy:finalize_update" do
  run "sudo chown -R www-data:www-data #{latest_release}/#{cache_path}"
  run "sudo chown -R www-data:www-data #{latest_release}/#{log_path}"
  run "sudo chmod -R 777 #{latest_release}/#{log_path}"
  run "sudo chmod -R 777 #{latest_release}/#{cache_path}"
end

load 'app/config/deploy/config' # remove this line to skip loading any of the default tasks