<?php

namespace App\Action\Export;

use App\Action\AbstractExporter;
use App\Action\DataWarehouse;
use Slim\Http\Response;
use Slim\Http\Request;

class ExportXl extends AbstractExporter
{
    /* @var DataWarehouse */
    private $dw;

    private $outFileName;
    private $user;

    public function __construct(DataWarehouse $dataWarehouse)
    {
        parent::__construct('xls');

        $this->dw = $dataWarehouse;
        $this->outFileName = 'Report_'.date('Ymd_His').'.'.$this->getFileExtension();
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getAttribute('user');

        // generate the response
        $response = $response->withHeader('Content-Type', $this->contentType);
        $response = $response->withHeader('Content-Disposition', 'attachment; filename='.$this->outFileName);

        $content = null;

        $this->generateHighestCertification($content);

        /** @noinspection PhpUndefinedMethodInspection */
        $body = $response->getBody();
        /** @noinspection PhpUndefinedMethodInspection */
        $body->write($this->export($content));

        return $response;
    }

    private function generateHighestCertification(&$content)
    {
        $data = [];

        $certs = $this->dw->getHighestRefCerts();

        //set the header labels
        if (!empty($certs)) {
            $rec = (array)$certs[0];

            $labels = [];
            foreach ($rec as $hdr => $val) {
                $labels[] = $hdr;
            }

            $data = array($labels);

            //set the data : 1 record in each row
            foreach ($certs as $cert) {
                $row = [];
                if (!empty($cert)) {
                    foreach ($cert as $ref) {
                        $row[] = $ref;
                    }
                }

                $data[] = $row;
            }

        }
        if (!empty($data)) {
            $content['report']['data'] = $data;
            $content['report']['options']['freezePane'] = 'A2';
            $content['report']['options']['horizontalAlignment'] = ['B1:Z' => 'left'];
        }

        return $content;
    }

    static function sortOnRep($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        if ($a[0] == 'Assignor') {
            return -1;
        }

        if ($b[0] == 'Assignor') {
            return 1;
        }

        return ($a[0] < $b[0]) ? -1 : 1;
    }

}