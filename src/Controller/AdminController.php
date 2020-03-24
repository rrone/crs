<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;

@Route::class;

class AdminController extends AbstractController2
{
    private bool $super;

    /**
     * AdminController constructor
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);

        $session = $this->request->getSession();
        $this->super = $session->get('superadmin');
    }

    /**
     * @Route("/admin", name="admin" )
     * @param Request $request
     * @return RedirectResponse|null
     * @throws DBALException
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized() || !$this->user->admin) {
            return $this->redirectToRoute('reports');
        }

        $this->logStamp($request);

        $request->query->set('user', $this->user);
        $response = new Response();
        $response->headers->set('admin', $this->generateUrl('admin'));

        switch ($this->handler($request)) {
            case 'Done':

                return $this->redirectToRoute('reports');

            case 'ExportLog':

                return $this->redirectToRoute('log');
        }

        return $this->renderPage();

    }

    /**
     * @param Request $request
     * @return string|null
     * @throws DBALException
     */
    public function handler(Request $request)
    {
        $this->user = $request->get('user');

        if ($request->isMethod('POST')) {
            $_POST = $request->request->all();

            if (in_array('btnAddUser', array_keys($_POST))) {
                $userName = $_POST['userName'];
                $pw = $_POST['newPassword'];

                if (!empty($userName) && !empty($pw)) {
                    $userData = array(
                        'name' => $userName,
                        'enabled' => true,
                    );

                    $this->msg['add'] = null;
                    $this->msgStyle['add'] = null;
                    $user = $this->dw->getUserByName($userName);

                    if (is_null($user)) {
                        $userData['hash'] = password_hash($pw, PASSWORD_BCRYPT);

                        $this->dw->setUser($userData);

                        $this->msg['add'] = "$userName has been enabled.";
                        $this->msgStyle['add'] = "color:#000000";
                    } else {
                        $this->msg['add'] = "User already exists. Update the password below.";
                        $this->msgStyle['add'] = "color:#FF0000";
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
                    $msg = $this->user->name.' note: '.$_POST['logNote'];
                    $this->dw->logInfo('CRS', $msg);
                }

                return 'LogItem';

            } else {

                $this->msg = null;
            }

        }

        return null;

    }

    /**
     * @return null
     */
    public function renderPage()
    {
        $adminPath = $this->generateUrl('admin');

        $content = array(
            'admin' => $this->user->admin,
            'users' => $this->renderContent(),
            'action' => $adminPath,
            'messageAdd' => $this->msg['add'] ?? '',
            'messageStyleAdd' => $this->msgStyle['add'] ?? '',
            'messageUpdate' => $this->msg['update'] ?? '',
            'messageStyleUpdate' => $this->msgStyle['update'] ?? '',
        );

        $content = array_merge($content, $this->getBaseContent());

        return $this->render('admin.html.twig', $content);
    }

    /**
     * @return array
     */
    protected function renderContent()
    {
        $users = $this->dw->getAllUsers();

        $selectOptions = [];
        foreach ($users as $user) {
            $user = (object)$user;
            if ($this->super) {
                $selectOptions[] = "$user->name";
            }
        }

        return $selectOptions;
    }

}
