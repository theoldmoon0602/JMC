
echo 'CREATE TABLE IF NOT EXISTS users(id integer primary key, username text unique, password text, handicap real);
CREATE TABLE IF NOT EXISTS submits(id integer primary key, problem_id integer, user_id integer, score real, created_at real, input text);' | sqlite3 database.db

sudo chown `whoami`:www-data -R .
sudo chmod 0770 -R .

git update-index --skip-worktree settings.php
