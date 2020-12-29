<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController2
{
    private $super;

    /**
     * AdminController constructor
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);

        $sa = $this->session->get('superadmin');
        $this->super = is_bool($sa) ? $sa : false;
    }

    /**
     * @Route("/admin", name="admin" )
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized() && !$this->super) {
            return $this->redirectToRoute('reports');
        }

        $this->logStamp($request);

        $request->query->set('user', (array)$this->user);
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
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function handler(Request $request): ?string
    {
        $this->user = $request->get('user');
        $this->msg[] = '';

        $_POST = $request->request->all();
        foreach (array_keys($_POST) as $key) {
            switch ($key) {
                case 'btnAddUser':
                    return $this->btnAddUser($_POST);

                case 'btnUpdate':
                    return $this->btnUpdate($_POST);

                case 'btnExportLog':
                    return 'ExportLog';

                case 'btnLogItem':
                    if (!empty($_POST['logNote'])) {
                        $msg = $this->user['name'] . ' note: ' . $_POST['logNote'];
                        $this->dw->logInfo('CRS', $msg);
                    }
                    return 'LogItem';

                case 'btnDone':
                    return 'Done';
            }

        }

        return null;

    }

    /**
     * @return null
     * @throws Exception
     */
    public function renderPage()
    {
        $adminPath = $this->generateUrl('admin');

        $content = array(
            'admin' => $this->user['admin'],
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
     * @throws Exception
     */
    protected function renderContent(): array
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

    /**
     * @param array $post
     * @return string
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function btnAddUser(array $post): string
    {
        $userName = $post['userName'];
        $pw = $post['newPassword'];

        $this->msg['add'] = '';
        if (empty($pw)) {
            $this->msg['add'] .= "<br>Password may not be blank.";
            $this->msgStyle['add'] = "color:#FF0000";
        }

        if (empty($userName)) {
            $this->msg['add'] .= "User name may not be blank.";
            $this->msgStyle['add'] = "color:#FF0000";
        }

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
            $this->dw->logInfo('CRS', $this->user['name'] . ": New user " . $userData['name'] . " added");
            $this->msg['add'] = "$userName has been enabled.";
            $this->msgStyle['add'] = "color:#000000";
        } else {
            $this->msg['add'] = "User already exists. Update the password below.";
            $this->msgStyle['add'] = "color:#FF0000";
        }

        return 'AddUser';

    }

    /**
     * @param array $post
     * @return string
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function btnUpdate(array $post): string
    {
        $userName = $post['selectAssignor'];
        $pw = $post['passwordInput'];

        if (!empty($pw)) {

            $user = $this->dw->getUserByName($userName);

            $userData = array(
                'name' => $user->name,
                'enabled' => $user->enabled,
            );

            $userData['hash'] = password_hash($pw, PASSWORD_BCRYPT);

            $this->dw->setUser($userData);

            $this->msg['update'] = "$userName password has been updated.";
            $this->dw->logInfo('CRS', $this->user['name'] . ": " . $this->msg['update']);

            $this->msgStyle['update'] = "color:#000000";
        } else {
            $this->msg['update'] = "Password may not be blank.";
            $this->msgStyle['update'] = "color:#FF0000";
        }

        return 'Update';

    }
}
