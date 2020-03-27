<?php

namespace App\Services;

use App\Repository\DataWarehouse;
use App\Abstracts\AbstractExporter;
use Symfony\Component\HttpFoundation\Response;

class LogExport extends AbstractExporter
{
    /* @var DataWarehouse */
    private $dw;

    private $outFileName;

    public function __construct(DataWarehouse $dataWarehouse)
    {
        parent::__construct('xls');

        $this->dw = $dataWarehouse;

        $this->outFileName = 'Log_'.date('Ymd_His').'.'.$this->getFileExtension();
    }

    public function handler()
    {
        // generate the response
        $response = new Response();
        $response->headers->set('Content-Type', $this->contentType);
        $response->headers->set('Content-Disposition', 'attachment; filename='.$this->outFileName);
        $response->headers->set('Set-Cookie', 'fileDownload=true; path=/');

        $content = null;
        $this->generateAccessLogData($content);
        $response->setContent($this->export($content));

        return $response;
    }

    public function generateAccessLogData(&$content)
    {
        $log = $this->dw->getAccessLog();

        //set the header labels
        $labels = array('Timestamp (UTC)', 'Project Key', 'User', 'Note');
        $data = array($labels);

        //set the data : match in each row
        foreach ($log as $item) {
            $item = (object)$item;
            $msg = explode(':', $item->note);
            if (isset($msg[1])) {
                $user = $msg[0];
                $p = strpos($item->note, ':') + 1;
                $s = trim(substr($item->note, $p));
                $note = $s;
            } else {
                $user = '';
                $note = $item->note;
            }

            $row = array(
                $item->timestamp,
                $item->projectKey,
                $user,
                $note,
            );

            $data[] = $row;
        }

        $content['Access_Log']['data'] = $data;
        $content['Access_Log']['options']['freezePane'] = 'A2';

        return $content;

    }
}
