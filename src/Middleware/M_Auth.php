<?php

declare(strict_types=1);

namespace MmanagerPOS\Middleware;

use Pimple\Container;

class M_Auth
{
    public $type;
    private $cnt;
    private $session;
    private $user_name;
    private $user_password;

    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct(Container $cnt, $entity=null, $type=null, $user_name = null, $user_password=null)
    {
        $this->cnt = $cnt;
        $this->session = $cnt['session'];

        $this->user_name = $user_name;
        $this->user_password = $user_password;

        if ($type == 'logout') {
            $this->doLogout();
        }
        elseif ($type == 'login') {
            $this->dologinWithPostData($entity);
        } else {
            $this->isUserLoggedIn($entity);
        }
    }

    /**
     * log in with post data
     */
    public function dologinWithPostData($entity)
    {
        // check login form contents
        if ( !isset($this->user_name)) {
            $this->errors[] = "Username field was empty.";
        } elseif ( !isset($this->user_password)) {
            $this->errors[] = "Password field was empty.";
        } elseif (isset($this->user_name) && isset($this->user_password)) {

            // create a database connection, using the constants from config/db.php (which we loaded in index.php)
            $this->db_connection = new \mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));

            // change character set to utf8 and check it
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }

            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) {

                // escape the POST stuff
                $this->user_name = $this->db_connection->real_escape_string($this->user_name);
                $sql = "";

                switch ($entity) {
                    case 'user':
                        $sql = "SELECT user_name, user_email, user_password_hash
                                FROM users
                                WHERE user_name = '" . $this->user_name . "' OR user_email = '" . $this->user_name . "';";
                        break;
                    case 'client':
                        $sql = "SELECT name_company, client_email, client_password
                                FROM oc_clients
                                WHERE name_company = '" . $this->user_name . "' OR client_email = '" . $this->user_name . "';";
                        break;
                }
                $result_of_login_check = $this->db_connection->query($sql);

                // if this user exists
                if ($result_of_login_check->num_rows == 1) {

                    // get result row (as an object)
                    $result_row = $result_of_login_check->fetch_object();

                    // using PHP 5.5's password_verify() function to check if the provided password fits
                    // the hash of that user's password
                    switch ($entity) {
                        case 'user':
                            if (password_verify($this->user_password, $result_row->user_password_hash)) {

                                // write user data into PHP SESSION (a file on your server)
                                $this->session->set('user_name', $result_row->user_name);
                                $this->session->set('user_email', $result_row->user_email);
                                $this->session->set('user_login_status', 1);
                                $this->session->set('user_domain', 'admin');
                            } else {
                                $this->errors[] = "Wrong password. Try again.";
                            }
                            break;
                        case 'client':
                            if (password_verify($this->user_password, $result_row->client_password)) {

                                // write user data into PHP SESSION (a file on your server)
                                $this->session->set('user_name', $result_row->name_company);
                                $this->session->set('user_email', $result_row->client_email);
                                $this->session->set('user_login_status', 1);
                                $this->session->set('user_domain', 'client');

                            } else {
                                $this->errors[] = "Wrong password. Try again.";
                            }
                            break;
                    }
                } else {
                    $this->errors[] = "This user does not exist.";
                }
            } else {
                $this->errors[] = "Database connection problem.";
            }
        }
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
        // delete the session of the user
        $session = array();
        session_destroy();
        // return a little feeedback message
        $this->messages[] = "You have been logged out.";
    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        if ($this->session->exists('user_login_status') AND $this->session->user_login_status == 1) {
            return true;
        }
        // default return
        return false;
    }
}
