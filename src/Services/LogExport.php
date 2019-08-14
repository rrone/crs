<?php
namespace App\Services;

use Symfony\Bridge\Twig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DataWarehouse;
use App\Abstracts\AbstractExporter;

class LogExport extends AbstractExporter
{
    /* @var DataWarehouse */
    private $dw;

    private $outFileName;
    private $user;

    public function __construct(DataWarehouse $dataWarehouse)
    {
        parent::__construct('xls');

        $this->dw = $dataWarehouse;

        $this->outFileName = 'Log_' . date('Ymd_His') . '.' . $this->getFileExtension();
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->get('user');

        // generate the response
        $response = $response->headers->set('Content-Type', $this->contentType);
        $response = $response->headers->set('Content-Disposition', 'attachment; filename=' . $this->outFileName);

        $content = null;

        $this->generateAccessLogData($content);

        $body = $response->query;
        $body->write($this->export($content));

        return $response;
    }

    public function generateAccessLogData(&$content)
    {
        $log = $this->dw->getAccessLog();

        //set the header labels
        $labels = array('Timestamp', 'Project Key', 'User', 'Note');
        $data = array($labels);

        //set the data : match in each row
        foreach ($log as $item) {
            $msg = explode(':', $item->note);
            if (isset($msg[1])) {
                $user = $msg[0];
                $note = $msg[1];
            } else {
                $user = '';
                $note = $item->note;
            }

            $row = array(
                $item->timestamp,
                $item->projectKey,
                $user,
                $note
            );

            $data[] = $row;
        }

        $content['Access_Log']['data'] = $data;
        $content['Access_Log']['options']['freezePane'] = 'A2';

        return $content;

    }
}