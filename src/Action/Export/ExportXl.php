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
        if($this->user->admin) {
            $userKey = '%%';
        }else{
            $key = explode(' ', $this->user->name);
            $userKey = end($key);
            $userKey = $userKey == '10' ? '1' : $userKey;
        }

        $uri = $request->getUri()->getPath();

        $params = $request->getParams();
        if (!is_null($params)) {
            $params = array_keys($params);
            $limit = isset($params[0]) ? (integer)$params[0] : null;
            $limit = $limit == 0 ? null : $limit;
        } else {
            $limit = null;
        }

        switch (str_replace('/', '', $uri)) {
            case 'hrc':
                $results = $this->dw->getHighestRefCerts($userKey, $limit);
                break;
            case 'ra':
                $results = $this->dw->getRefAssessors($userKey, $limit);
                break;
            case 'ri':
                $results = $this->dw->getRefInstructors($userKey, $limit);
                break;
            case 'rie':
                $results = $this->dw->getRefInstructorEvaluators($userKey, $limit);
                break;
            case 'nocerts':
                $results = $this->dw->getRefsWithNoBSCerts($userKey, $limit);
                break;
            case 'nra':
                $results = $this->dw->getRefNationalAssessors($userKey, $limit);
                break;
            default:
                $results = null;
        }

        $content = null;
        $this->generateExport($content, $results);

        if (is_null($results)) {
            return $response->withRedirect($this->getBaseURL('logon'));
        }
        // generate the response
        $response = $response->withHeader('Content-Type', $this->contentType);
        $response = $response->withHeader('Content-Disposition', 'attachment; filename='.$this->outFileName);

        /** @noinspection PhpUndefinedMethodInspection */
        $body = $response->getBody();
        /** @noinspection PhpUndefinedMethodInspection */
        $body->write($this->export($content));

        return $response;
    }

    private function generateExport(&$content, $certs)
    {
        if (is_null($certs)) {
            return null;
        }

        $data = [];

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
                    foreach ($cert as $key => $value) {
                        switch ($key) {
                            case 'Name':
                            case 'First Name':
                            case 'Last Name':
                            case 'Address':
                            case 'City':
                            $value = ucwords(strtolower($value));
                                break;
                            case 'Email':
                                $value = strtolower($value);
                        }

                        $row[] = $value;
                    }
                }

                $data[] = $row;
            }
        }
        if (!empty($data)) {
            $content['report']['data'] = $data;
            $content['report']['options']['freezePane'] = 'A2';
            $content['report']['options']['horizontalAlignment'] = ['B1:Z' => 'left'];
        } else {
            return null;
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