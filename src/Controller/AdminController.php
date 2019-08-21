<?php
namespace App\Controller;

use App\Abstracts\AbstractController2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

class AdminController extends AbstractController2
{
    /**
     * AdminController constructor.
     */
    public function __construct() {

	    parent::__construct();

    }

    /**
     * @Route("/admin", name="admin")
     * @param Request $request
     * @param Response $response
     * @return RedirectResponse|null
     */
    public function index(Request $request, Response $response)
    {
        if(!$this->isAuthorized() || !$this->user->admin) {
            return $this->redirectToRoute('reports');
        };

        $this->logStamp($request);

        $request = $request->query->set('user', $this->user);

        $response->headers->set('admin', $this->generateUrl('admin'));
        $result = $this->handler($request, $response);

        switch ($result) {
             case 'Done':

                 return $this->redirectToRoute('reports');

            case 'ExportLog':

                return $this->redirectToRoute('logExport');
        }

        return $this->renderPage($response);

    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->get('user');

        if ($request->isMethod('POST')) {
            $_POST = $request->query;

            if (in_array('btnAddUser', array_keys($_POST))) {
                $userName = $_POST['userName'];
                $pw = $_POST['newPassword'];

                if (!empty($userName) && !empty($pw)) {
                    $userData = array(
                        'name' => $userName,
                        'enabled' => true,
                    );

                    $user = $this->dw->getUserByName($userName);

                    if(is_null($user)) {
                        $userData['hash'] = password_hash($pw, PASSWORD_BCRYPT);

                        $this->dw->setUser($userData);

                        $this->msg['add'] = "$userName has been enabled.";
                        $this->msgStyle['add'] = "color:#000000";
                    } elseif (!$this->isRepost($request)) {
                        $this->msg['add'] = "User already exists. Update the password below.";
                        $this->msgStyle['add'] = "color:#FF0000";
                    } else {
                        $this->msg['add'] = null;
                        $this->msgStyle['add'] = null;
                    }
                } else {
                    if (empty($userName)) {
                        $this->msg['add'] = "User name may not be blank.";
                        $this->msgStyle['add'] = "color:#FF0000";
                    }
                    if (empty($pw)) {
                        $this->msg['add'] .= "<br>Password may not be blank.";
                        $this->msgStyle['add'] = "color:#FF0000";
                    }
                }

                return 'AddUser';

            } elseif (in_array('btnUpdate', array_keys($_POST))) {
                $userName = $_POST['selectUser'];
                $pw = $_POST['passwordInput'];

                if (!empty($pw)) {

                    $user = $this->dw->getUserByName($userName);

                    if (is_null($user)) {
                        $userData = array(
                            'name' => $userName,
                            'enabled' => false,
                        );
                    } else {
                        $userData = array(
                            'name' => $user->name,
                            'enabled' => $user->enabled,
                        );
                    }

                    $userData['hash'] = password_hash($pw, PASSWORD_BCRYPT);

                    $this->dw->setUser($userData);

                    $this->msg['update'] = "$userName password has been updated.";
                    $this->msgStyle['update'] = "color:#000000";
                } else {
                    $this->msg['update'] = "Password may not be blank.";
                    $this->msgStyle['update'] = "color:#FF0000";
                }

                return 'Update';

            } elseif (in_array('btnDone', array_keys($_POST))) {

                return 'Done';

            } elseif (in_array('btnExportLog', array_keys($_POST))) {

                $this->msg = null;

                return 'ExportLog';

            } elseif (in_array('btnLogItem', array_keys($_POST))) {

                if (!empty($_POST['logNote'])) {
                    $msg = $this->user->name . ': ' . $_POST['logNote'];
                    $this->dw->logInfo('CRS', $msg);
                }

            } else {
                $this->msg = null;
            }

        }

        return null;

    }

    public function renderPage(Response &$response)
    {
        $adminPath = $response->getHeader('admin')[0];

        $content = array(
            'view' => array(
                'admin' => $this->user->admin,
                'users' => $this->renderUsers(),
                'action' => $adminPath,
                'messageAdd' => $this->msg['add'] ?? '',
                'messageStyleAdd' => $this->msgStyle['add'] ?? '',
                'messageUpdate' => $this->msg['update'] ?? '',
                'messageStyleUpdate' => $this->msgStyle['update'] ?? '',
            )
        );

        $this->view->render($response, 'admin.html.twig', $content);

        return null;
    }

    protected function renderUsers()
    {
        $users = $this->dw->getAllUsers();

        $selectOptions = [];
        foreach ($users as $user) {
            if ($user->name != 'Admin') {
                $selectOptions[] = "$user->name";
            }
        }

        return $selectOptions;
    }

}