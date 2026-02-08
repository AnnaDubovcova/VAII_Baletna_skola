<?php

namespace App\Controllers;

use App\Configuration;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

use Framework\DB\Connection;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends AppController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            $logged = $this->app->getAuthenticator()->login($request->value('email'), $request->value('password'));
            if ($logged) {
                $currentUser = $this->app->getAuthenticator()->getUser();

                if ($currentUser->isAdmin()) {
                    return $this->redirect($this->url('admin.index'));
                }

                return $this->redirect($this->url('osoba.index'));
            }


        }

        $message = $logged === false ? 'Nespravny email alebo heslo.' : null;
        return $this->html(compact("message"));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->html();
    }

    public function authorize(\Framework\Http\Request $request, string $action): bool
    {
        // ak je user prihlásený, nech sa nedostane na login/register
        if ($this->user->isLoggedIn() && in_array($action, ['login', 'register'], true)) {
            return false; // FW by mal dať 403 alebo redirect podľa tvojej logiky
        }
        return parent::authorize($request, $action);
    }


    public function register(Request $request): Response
    {
        if ($this->user->isLoggedIn()) {
            // prihlásený user nemá čo registrovať nový účet
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        $errors = [];
        $email = '';
        $password = '';
        $password2 = '';

        if ($request->isPost()) {
            $email = trim((string)$request->value('email'));
            $password = (string)$request->value('password');
            $password2 = (string)$request->value('password2');

            // --- VALIDÁCIA ---
            if ($email === '' || mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Zadaj platný email (max 150 znakov).';
            }

            if ($password === '' || mb_strlen($password) < 8) {
                $errors['password'] = 'Heslo musí mať aspoň 8 znakov.';
            }

            if ($password2 === '' || $password2 !== $password) {
                $errors['password2'] = 'Heslá sa nezhodujú.';
            }

            // --- UNIQUE EMAIL ---
            if (!isset($errors['email'])) {
                $con = Connection::getInstance();
                $stmt = $con->prepare('SELECT 1 FROM pouzivatel WHERE email = :e LIMIT 1');
                $stmt->execute(['e' => $email]);
                if ($stmt->fetchColumn()) {
                    $errors['email'] = 'Tento email je už zaregistrovaný.';
                }
            }

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $con = Connection::getInstance();
                $stmt = $con->prepare(
                    'INSERT INTO pouzivatel (email, password_hash, rola) VALUES (:e, :h, :r)'
                );
                $stmt->execute([
                    'e' => $email,
                    'h' => $hash,
                    'r' => 'user',
                ]);

                // po registrácii -> login
                return $this->redirect($this->url('auth.login'));
            }
        }

        return $this->html([
            'errors' => $errors,
            'email' => $email,
        ]);
    }


}
