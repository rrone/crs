<?php

namespace App\Action\Export;

use App\Action\AbstractExporter;
use App\Action\DataWarehouse;
use Slim\Http\Response;
use Slim\Http\Request;
use Illuminate\Support\Collection;
use DateTime;
use DateTimeZone;

class ExportXl extends AbstractExporter
{
    /* @var DataWarehouse */
    private $dw;

    private $outFileName;
    private $user;
    private $baseURL;

    public function __construct(DataWarehouse $dataWarehouse)
    {
        parent::__construct('xls');

        $this->dw = $dataWarehouse;
        $utc = $this->dw->getUpdateTimestamp();
        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('America/Los_Angeles'));
        $ts = $ts->format('Ymd_His');
        $this->outFileName = 'Report_'.$ts.'.'.$this->getFileExtension();
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getAttribute('user');
        $this->baseURL = $request->getAttribute('baseURL');

        if ($this->user->admin) {
            $userKey = '%%';
        } else {
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

        $uri = str_replace('/', '', $uri);

        $user = explode(' ', $this->user->name);
        $u = strtoupper(str_replace('/', '', end($user)));

        switch ($uri) {
            case 'hrc':
                $this->outFileName = "HighestRefCerts.$u.$this->outFileName";
                $results = $this->dw->getHighestRefCerts($userKey, $limit);
                break;
            case 'ra':
                $this->outFileName = "RefAssessors.$u.$this->outFileName";
                $results = $this->dw->getRefAssessors($userKey, $limit);
                break;
            case 'ri':
                $this->outFileName = "RefInstructors.$u.$this->outFileName";
                $results = $this->dw->getRefInstructors($userKey, $limit);
                break;
            case 'rie':
                $this->outFileName = "RefInstructorEvaluators.$u.$this->outFileName";
                $results = $this->dw->getRefInstructorEvaluators($userKey, $limit);
                break;
            case 'nocerts':
                $this->outFileName = "RefsWithNoBSCerts.$u.$this->outFileName";
                $results = $this->dw->getRefsWithNoBSCerts($userKey, $limit);
                break;
            case 'ruc':
                $this->outFileName = "RefUpgradeCandidates.$u.$this->outFileName";
                $results = $this->dw->getRefUpgradeCandidates($userKey, $limit);
                break;
            case 'urr':
                $this->outFileName = "UnregisteredRefs.$u.$this->outFileName";
                $results = $this->dw->getUnregisteredRefs($userKey, $limit);
                break;
            case 'rcdc':
                $this->outFileName = "ConcussionRefs.$u.$this->outFileName";
                $results = $this->dw->getRefsConcussion($userKey, $limit);
                break;
            case 'rsh':
                $this->outFileName = "SafeHavenRefs.$u.$this->outFileName";
                $results = $this->dw->getSafeHavenRefs($userKey, $limit);
                break;
            case 'nra':
                if ($this->user->admin) {
                    $this->outFileName = "NationalRefAssessors.$u.$this->outFileName";
                    $results = $this->dw->getRefNationalAssessors($userKey, $limit);
                } else {
                    $results = null;
                }
                break;
            case 'bshca':
                $this->outFileName = "CompositeRefCerts.$u.$this->outFileName";
                $results = $this->dw->getCompositeRefCerts($userKey, $limit);
                break;
            default:
                $results = null;
        }

        $content = null;

        if (is_null($results)) {
            return $response->withRedirect($this->baseURL);
        } else {
            $this->generateExport($content, $results);
        }
        // generate the response
        $response = $response->withHeader('Content-Type', $this->contentType);
        $response = $response->withHeader('Content-Disposition', 'attachment; filename='.$this->outFileName);
        $response = $response->withHeader('Set-Cookie', 'fileDownload=true; path=/');

        $body = $response->getBody();

        $body->write($this->export($content));

        return $response;
    }

    private function generateExport(&$content, Collection $certs)
    {
        if (is_null($certs) or $certs->isEmpty()) {
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
                                break;
                            case 'CertDate':
                                $value = date_format(date_create($value), 'm/d/Y');
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

//    private function tableHeaders($uri)
//    {
//        if (is_null($uri)) {
//            return null;
//        }
//
//        $tableName = null;
//
//        switch ($uri) {
//            case 'hrc':
//                $tableName = 'tmp_hrc';
//                break;
//            case 'ra':
//                $tableName = 'tmp_ra';
//                break;
//            case 'ri':
//                $tableName = 'tmp_ri';
//                break;
//            case 'rie':
//                $tableName = 'tmp_rie';
//                break;
//            case 'nocerts':
//                $tableName = 'tmp_nocerts';
//                break;
//            case 'ruc':
//                $tableName = 'tmp_ruc';
//                break;
//            case 'urr':
//                $tableName = 'tmp_urr';
//                break;
//            case 'rcdc':
//                $tableName = 'tmp_rcdc';
//                break;
//            case 'rsh':
//                $tableName = 'tmp_rsh';
//                break;
//            case 'nra':
//                if($this->user->admin) {
//                    $tableName = 'tmp_nra';
//                } else {
//                    $results = null;
//                }
//                break;
//            default:
//                $results = null;
//        }
//
//        $content = [];
//
//        $results = $this->dw->getTableHeaders($tableName);
//
//        //set the header labels
//        if (!empty($results)) {
//            $rec = (array)$results[0];
//
//            $labels = [];
//            foreach ($rec as $hdr => $val) {
//                $labels[] = $val;
//            }
//
//            $content = array($labels);
//        }
//
//        return $content;
//    }

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