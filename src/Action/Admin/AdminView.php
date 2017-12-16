<?php
namespace App\Action\Admin;

use App\Action\AbstractView;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\DataWarehouse;

class AdminView extends AbstractView
{
    public function __construct(Container $container, DataWarehouse $repository)
    {
        parent::__construct($container, $repository);

        $this->dw = $repository;
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getAttribute('user');

        if ($request->isPost()) {
            $_POST = $request->getParsedBody();

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

    public function render(Response &$response)
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
