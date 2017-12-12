<?php
/**
 * Created by PhpStorm.
 * User: rick
 * Date: 11/6/16
 * Time: 8:59 AM
 */

namespace App\Action\Logon;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractView;
use App\Action\DataWarehouse;

class LogonView extends AbstractView
{
    public function __construct(Container $container, DataWarehouse $repository)
    {
        parent::__construct($container, $repository);

        $this->dw = $repository;
    }

    public function handler(Request $request, Response $response)
    {
        parent::handler($request, $response);

        if ($request->isPost()) {
            $_POST = $request->getParsedBody();

            $userName = isset($_POST['user']) ? $_POST['user'] : null;
            $user = $this->dw->getUserByName($userName);
            $_SESSION['user'] = $user;

            // try user pass
            $pass = isset($_POST['passwd']) ? $_POST['passwd'] : null;
            $hash = isset($user) ? $user->hash : null;
            $authed = password_verify($pass, $hash);

            if ($authed) {
                $_SESSION['authed'] = true;
                $this->msg = null;
            } else {
                //try master password
                $user = $this->dw->getUserByName('Admin');
                $hash = isset($user) ? $user->hash : null;
                $authed = password_verify($pass, $hash);

                if ($authed) {
                    $_SESSION['authed'] = true;
                    $_SESSION['admin'] = true;
                    $this->msg = null;
                } else {
                    $_SESSION['authed'] = false;
                    $_SESSION['admin'] = false;
                    $this->msg = 'Unrecognized password for ' . $_POST['user'];
                }
            }
        }

        $this->dw->sessionWrite();

        return null;
    }

    public function render(Response &$response)
    {
        $content = array(
            'content' => $this->renderView(),
            'users' => $this->getBaseURL('logon'),
            'message' => $this->msg,
        );

        $this->view->render($response, 'logon.html.twig', $content);

        return $response;
    }

    protected function renderView()
    {
        $html = null;

        $users = $this->dw->getAllUsers();

        $logonPath = $this->getBaseURL('logon');

        if (count($users) > 0) {
            $html .= <<<EOD
                      <form name="form1" method="post" action="$logonPath">
        <div class="center">
			<table>
				<tr>
					<td width="50%"><div class="right">Report Admin: </div></td>
					<td width="50%"><select id="user" class="form-control left-margin" name="user">
EOD;

            $html .= $this->selectedUsers($users);

            $html .= <<<EOD
                			            </select></td>
				</tr>

				<tr>
					<td width="50%"><div class="right">Password: </div></td>
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

    public function selectedUsers($users)
    {
        $options = null;

        foreach ($users as $user) {
            if($user->enabled) {
                $options .= "<option>$user->name</option>\n";
            }
        }

        return $options;
    }
}