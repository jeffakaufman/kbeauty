# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'kbeauty'
set :repo_url, "git@github.com:jeffakaufman/#{fetch(:application)}"

ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
# set :deploy_to, '/var/www/my_app_name'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# set :log_level, :debug

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
# set :linked_files, fetch(:linked_files, []).push('config/database.yml', 'config/secrets.yml')

# Default value for linked_dirs is []
# set :linked_dirs, fetch(:linked_dirs, []).push('log', 'tmp/pids', 'tmp/cache', 'tmp/sockets', 'vendor/bundle', 'public/system')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

namespace :deploy do

  desc "Create media symlink"
  task :media_symlink do 
    on roles(:app) do
      if fetch(:stage).match(/prod./)
        puts "creating media symlink to '/mnt/file1/export/media'"
        execute "ln -nfs /mnt/file1/export/media #{current_path}/media"
      else
        puts "creating media symlink to '#{shared_path}/media'"
        execute "ln -nfs #{shared_path}/media #{current_path}/media"
      end
      execute "ln -nfs #{shared_path}/config/app/etc/config.xml #{current_path}/app/etc/config.xml"
      execute "ln -nfs #{shared_path}/config/app/etc/local.xml #{current_path}/app/etc/local.xml"
    end
  end

end
after 'deploy', 'deploy:media_symlink'
