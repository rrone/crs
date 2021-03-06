<?php

namespace App\Services;

use App\Abstracts\AbstractExporter;
use App\Repository\DataWarehouse;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use DateTime;
use DateTimeZone;
use Exception;

define("xlsxFile", realpath(__DIR__.'/../../var/xlsx/CompositeRefCerts.xlsx'));

class ExportXl extends AbstractExporter
{
    /* @var DataWarehouse */
    private DataWarehouse $dw;

    private string $outFileName;

    private string $uri;

    /**
     * ExportXl constructor.
     * @param DataWarehouse $dataWarehouse
     * @throws Exception
     */
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

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function invoke(Request $request)
    {
        $user1 = (object) $request->request->get('user');
        $baseURL = $request->request->get('baseURL');

        if ($user1->admin) {
            $userKey = '%%';
        } else {
            $key = explode(' ', $user1->name);
            $userKey = end($key);
            $userKey = $userKey == '10' ? '1' : $userKey;
        }

        $this->uri = $request->attributes->get('_route');

        $limit = $request->query->get('limit');
        $limit = is_null($limit) ? $this->dw->bigLimit() : $limit;

        $this->uri = str_replace('/', '', $this->uri);

        $user = explode(' ', $user1->name);
        $u = strtoupper(str_replace('/', '', end($user)));

        switch ($this->uri) {
            // @codeCoverageIgnoreStart
            case 'hrc':
                $this->outFileName = "HighestRefCerts.$u.$this->outFileName";
                $results = $this->dw->getHighestRefCerts($userKey, $limit);
                break;
            // @codeCoverageIgnoreEnd
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
            // @codeCoverageIgnoreStart
            case 'nocerts':
                $this->outFileName = "RefsWithNoBSCerts.$u.$this->outFileName";
                $results = $this->dw->getRefsWithNoBSCerts($userKey, $limit);
                break;
            // @codeCoverageIgnoreEnd
            case 'ruc':
                $this->outFileName = "RefUpgradeCandidates.$u.$this->outFileName";
                $results = $this->dw->getRefUpgradeCandidates($userKey, $limit);
                break;
            case 'urr':
                $this->outFileName = "UnregisteredRefs.$u.$this->outFileName";
                $results = $this->dw->getUnregisteredRefs($userKey, $limit);
                break;
            // @codeCoverageIgnoreStart
            case 'rcdc':
                $this->outFileName = "ConcussionRefs.$u.$this->outFileName";
                $results = $this->dw->getRefsConcussion($userKey, $limit);
                break;
            case 'rsh':
                $this->outFileName = "SafeHavenRefs.$u.$this->outFileName";
                $results = $this->dw->getSafeHavenRefs($userKey, $limit);
                break;
            // @codeCoverageIgnoreEnd
            case 'nra':
                if ($user1->admin) {
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
            // @codeCoverageIgnoreStart
            default:
                $results = null;
            // @codeCoverageIgnoreEnd
        }

        // generate the response
        if (is_null($results)) {
            return new RedirectResponse($baseURL);
        } else {
            $content = null;
            if ($user1->section && $this->uri == 'bshca') {
                // @codeCoverageIgnoreStart
                if ($_SERVER['APP_ENV'] === 'dev') {
                    $this->generateExport($content, $results);
                    file_put_contents(realpath(xlsxFile), $this->export($content));
                }
                // @codeCoverageIgnoreEnd

                try{
                    $response = new BinaryFileResponse(realpath(xlsxFile));
                    // @codeCoverageIgnoreStart
                } catch (Exception $e) {
                    $response = new Response();

                    $this->generateExport($content, array());
                    $response->setContent($this->export($content));

                }
                // @codeCoverageIgnoreEnd
            } else {
                $response = new Response();
                $this->generateExport($content, $results);
                $response->setContent($this->export($content));
            }
        }
        $response->headers->set('Content-Type', $this->contentType);
        $response->headers->set('Content-Disposition', 'attachment; filename='.$this->outFileName);
        $response->headers->set('Set-Cookie', 'fileDownload=true; path=/');

        return $response;
    }

    /**
     * @param $content
     * @param array $certs
     * @return void
     */
    private function generateExport(&$content, array $certs): void
    {
        // @codeCoverageIgnoreStart
        if (is_null($certs) or empty($certs)) {
            $content['report']['data'] = array('There was an error generating your report.');
            return;
        }
        // @codeCoverageIgnoreEnd

        $data = [];

        //set the header labels
        if (!empty($certs)) {
            $rec = (array)$certs[0];

            $labels = [];
            foreach ($rec as $hdr => $val) {
                switch ($hdr) {
                    case 'Address':
                        break;
                    default:
                        $labels[] = $hdr;
                }
            }
            if ($this->uri == 'bshca') {
                $labels[] = 'Health & Safety';
            }

            $data = array($labels);

            //set the data : 1 record in each row
            foreach ($certs as $cert) {
                $row = [];
                $trainingComplete = true;
                if (!empty($cert)) {
                    foreach ($cert as $key => $value) {
                        switch ($key) {
                            case 'Name':
                            case 'First Name':
                            case 'Last Name':
                            case 'City':
                                $value = ucwords(strtolower($value));
                                break;
                            case 'Email':
                                $value = strtolower($value);
                                break;
                            case 'shCertDate':
                            case 'cdcCertDate':
                            case 'scaCertDate':
                                $trainingComplete = $trainingComplete && !is_null($value);
                        }

                        if ($key !== 'Address') {
                            $row[] = $value;
                        }
                    }
                    if ($this->uri == 'bshca') {
                        $row[] = $trainingComplete ? 'COMPLETE' : '';
                    }
                }
                $data[] = $row;
            }
        }
        if (!empty($data)) {

            $content['report']['data'] = $data;
            $content['report']['options']['freezePane'] = 'A2';
            $content['report']['options']['horizontalAlignment'] = ['B1:Z' => 'left'];
            $content['report']['options']['selectRange'] = 'A2';

        }

    }

}
