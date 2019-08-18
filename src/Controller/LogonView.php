<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Abstracts\AbstractView;
use App\Repository\DataWarehouse;

class LogonView extends AbstractView
{
    private $baseUrl;

    public function __construct(DataWarehouse $repository)
    {
        parent::__construct($repository);

        $this->dw = $repository;
    }

    public function handler(Request $request)
    {
        if ($request->isMethod('post')) {
            $_POST = $request->query;

            $this->baseUrl = $request->query->get('baseUrl');

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

        return null;
    }

    public function render()
    {
        $content = array(
            'content' => $this->renderView(),
            'users' => $this->baseUrl,
            'message' => $this->msg,
            'updated' => $this->getUpdateTimestamp(),
        );

        return $content;
    }

    protected function renderView()
    {
        $html = null;

        $users = $this->dw->getAllUsers();

        $logonPath = $this->baseUrl;

        if (count($users) > 0) {
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