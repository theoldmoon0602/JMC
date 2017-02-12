
echo 'CREATE TABLE users(id integer primary key, username text unique, password text, handicap real);
CREATE TABLE submits(id integer primary key, problem_id integer, user_id integer, score real, created_at real, input text);' | sqlite3 database.db

sudo chown `whoami`:www-data -R .
sudo chmod 0750 -R .
