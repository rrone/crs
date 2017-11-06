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

            if (in_array('btnUpdate', array_keys($_POST))) {
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

                    $this->msg = "$userName password has been updated.";
                    $this->msgStyle = "color:#000000";
                } else {
                    $this->msg = "Password may not be blank.";
                    $this->msgStyle = "color:#FF0000";
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
                'message' => $this->msg,
                'messageStyle' => $this->msgStyle,
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
