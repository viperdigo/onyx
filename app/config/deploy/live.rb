role :app, "onyx.com", :primary => true
role :web, "onyx.com"

set :deploy_to, "/var/www/html/#{application}/live"
set :user, "root"
set :keep_releases,   3