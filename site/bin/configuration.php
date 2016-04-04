<?php
class JConfig {
	// Daemon config
	public $max_memory_limit = '16M';
	public $application_pid_file = '/var/www/floks74.ru/tmp/ws.pid';
	public $author_name = 'Saity74 LLC';
	public $author_email = 'info@saity74.ru';
	public $application_name = 'WSDaemon';
	public $application_description = 'WebSocket Daemon';
	// App config
	public $dbtype = 'mysqli';
	public $host = 'localhost:3306';
	public $user = 'root';
	public $password = 'root';
	public $db = 'sites_floks74.ru';
	public $dbprefix = 'j_';
	public $secret = 'rMeY8XRTxd4O9L3m';
	public $error_reporting = 'development';
	public $tmp_path = '/var/www/floks74.ru/tmp/';
	public $log_path = '/var/www/floks74.ru/logs/';
	public $mailer = 'smtp';
	public $mailfrom = 'admin@example.com';
	public $fromname = 'floks74.ru';
	public $sendmail = '/usr/bin/env catchmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';
	public $debug = '0';
	public $smtpsecure = 'none';
	public $smtpport = '1025';
	public $redis_persist = '1';
	public $redis_server_host = 'localhost';
	public $redis_server_port = '6379';
	public $redis_server_auth = '';
	public $redis_server_db = '0';
	public $mailonline = '1';
	public $massmailoff = '0';
}