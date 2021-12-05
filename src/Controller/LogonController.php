<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Abstracts\AbstractController2;

use Symfony\Component\Routing\Annotation\Route;

class LogonController extends AbstractController2
{
    private string $url;

    /**
     * LogonController constructor.
     * @param RequestStack $requestStack
     * @throws Exception
     */
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);
    }

    /**
     * @Route("/", name="logon")
     * @Route("/logon", name="/")
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function index()
    {
        $this->request->query->set('url', $this->generateUrl('logon'));

        $this->invoke($this->request);

        if ($this->isAuthorized()) {
            $this->logStamp($this->request);

            return $this->redirectToRoute('reports');
        }

        return $this->render('logon.html.twig', $this->renderPage());
    }

    public function invoke(Request $request)
    {

        $this->url = $request->query->get('url');

        if ($request->isMethod('post')) {
            $userName = $request->request->get('user');
            $pass = $request->request->get('passwd');

            $user = $this->dw->getUserByName($userName);
            $this->user = $user;
            $session = $this->request->getSession();
            $session->set('user', $user);
            $session->set('superadmin', false);

            // try user pass
            $hash = isset($user) ? $user->hash : null;
            $authed = password_verify($pass, $hash);

            if ($authed) {
                $session->set('authed', true);
                $this->msg[0] = '';
            } else {
                //try master password
                $user = $this->dw->getUserByName('Super Admin');
                $hash = isset($user) ? $user->hash : null;
                $authed = password_verify($pass, $hash);

                if ($authed) {
                    $session->set('authed', true);
                    $session->set('admin', true);
                    $session->set('superadmin', true);
                    $this->msg[0] = '';
                } else {
                    $session->set('authed', false);
                    $session->set('admin', false);
                    $this->msg[0] = 'Unrecognized password for '.$userName;
                }
            }
        }

        return null;
    }

    public function renderPage(): array
    {

        $content = array(
            'content' => $this->renderContent(),
            'users' => $this->url,
            'message' => $this->msg[0],
            'admin' => $this->user->admin ?? false,
        );

        return array_merge($content, $this->getBaseContent());
    }

    protected function renderContent(): ?string
    {
        $html = null;

        $users = $this->dw->getAllUsers();
        $logonPath = $this->generateUrl('logon');

        if (!is_null($users) > 0) {
            $html .= <<<EOD
                      <form name="form1" method="post" action="$logonPath">
        <div class="center">
			<table>
				<tr>
					<td style="width: 50%"><div class="right">Report Admin: </div></td>
					<td style="width: 50%"><select id="user" class="form-control left-margin" name="user">
EOD;

            $html .= $this->selectedUsers($users);

            $html .= <<<EOD
                			            </select></td>
				</tr>

				<tr>
					<td style="width: 50%"><div class="right">Password: </div></td>
					<td><input class="form-control" type="password" name="passwd"></td>
				</tr>
			</table>
			<p>
            <input type="submit" class="btn btn-primary btn-xs active" name="Submit" value="Logon">
			</p>
        </div>
      </form>
EOD;
        }

        return $html;
    }

    public function selectedUsers($users): ?string
    {
        $options = null;

        foreach ($users as $user) {
            $user = (object)$user;
            if ($user->enabled) {
                $options .= "<option>$user->name</option>\n";
            }
        }

        return $options;
    }


}
