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

define("xlsxFile", realpath(__DIR__ . '/../../var/xlsx/CompositeRefCerts.xlsx'));

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
        $this->outFileName = 'Report_' . $ts . '.' . $this->getFileExtension();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function invoke(Request $request)
    {
        $user1 = (object)$request->request->get('user');
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

        date_default_timezone_set('America/Los_Angeles');
        $shName = 'Updated ' . date('Y-M-d');

        switch ($this->uri) {
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
            case 'ruc':
                $this->outFileName = "RefUpgradeCandidates.$u.$this->outFileName";
                $results = $this->dw->getRefUpgradeCandidates($userKey, $limit);
                break;
            case 'urr':
                $this->outFileName = "UnregisteredRefs.$u.$this->outFileName";
                $results = $this->dw->getUnregisteredRefs($userKey, $limit);
                break;
            // @codeCoverageIgnoreStart
            case 'rsh':
                $this->outFileName = "MissingSafeHaven.$u.$this->outFileName";
                $results = $this->dw->getSafeHavenRefs($userKey, $limit);
                break;
            case 'rcdc':
                $this->outFileName = "MissingConcussionRefs.$u.$this->outFileName";
                $results = $this->dw->getConcussionRefs($userKey, $limit);
                break;
            case 'rsca':
                $this->outFileName = "MissingSuddenCardiacArrest.$u.$this->outFileName";
                $results = $this->dw->SuddenCardiacArrestRefs($userKey, $limit);
                break;
            case 'rss':
                $this->outFileName = "MissingSafeSport.$u.$this->outFileName";
                $results = $this->dw->getSafeSportRefs($userKey, $limit);
                break;
            case 'rssx':
                $this->outFileName = "SafeSportExpiration.$u.$this->outFileName";
                $results = $this->dw->getSafeSportExpirationRefs($userKey, $limit);
                break;
            case 'rls':
                $this->outFileName = "MissingLiveScan.$u.$this->outFileName";
                $results = $this->dw->getLiveScanRefs($userKey, $limit);
                break;
            case 'rxr':
                $this->outFileName = "ExpiredRiskStatus.$u.$this->outFileName";
                $results = $this->dw->getExpiredRiskRefs($userKey, $limit);
                break;
            case 'nra':
                if ($user1->admin) {
                    $this->outFileName = "NationalRefAssessors.$u.$this->outFileName";
                    $results = $this->dw->getRefNationalAssessors();
                } else {
                    $results = null;
                }
                break;
            // @codeCoverageIgnoreEnd
            case 'xra':
                if ($user1->admin) {
                    $this->outFileName = "RefAssessors.1.Report." . $this->getFileExtension();
                    $results = $this->dw->getRefAssessorsReport();
                } else {
                    $results = null;
                }
                break;
            case 'xri':
                if ($user1->admin) {
                    $this->outFileName = "RefInstructors.1.Report." . $this->getFileExtension();
                    $results = $this->dw->getRefInstructorsReport();
                } else {
                    $results = null;
                }
                break;
            case 'xrie':
                if ($user1->admin) {
                    $this->outFileName = "RefInstructorEvaluators.1.Report." . $this->getFileExtension();
                    $results = $this->dw->getRefInstructorEvaluatorsReport();
                } else {
                    $results = null;
                }
                break;
            case 'xnra':
                if ($user1->admin) {
                    $this->outFileName = "NationalRefAssessors.1.Report." . $this->getFileExtension();
                    $results = $this->dw->getRefNationalAssessorsReport();
                } else {
                    $results = null;
                }
                break;
            case 'crct':
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
            if ($user1->section && $this->uri == 'crct') {
                // @codeCoverageIgnoreStart
                if ($_SERVER['APP_ENV'] === 'dev') {
                    $this->generateExport($content, $results, $shName);
                    file_put_contents(realpath(xlsxFile), $this->export($content));
                }
                // @codeCoverageIgnoreEnd

                try {
                    $response = new BinaryFileResponse(realpath(xlsxFile));
                    // @codeCoverageIgnoreStart
                } catch (Exception $e) {
                    $response = new Response();

                    $this->generateExport($content, array(), $shName);
                    $response->setContent($this->export($content));

                }
                // @codeCoverageIgnoreEnd
            } else {
                $response = new Response();
                $this->generateExport($content, $results, $shName);
                $response->setContent($this->export($content));
            }
        }
        $response->headers->set('Content-Type', $this->contentType);
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $this->outFileName);
        $response->headers->set('Set-Cookie', 'fileDownload=true; path=/');

        return $response;
    }

    /**
     * @param $content
     * @param array $certs
     * @param string $shName
     * @return void
     */
    private function generateExport(&$content, array $certs, string $shName): void
    {
        // @codeCoverageIgnoreStart
        if (empty($certs)) {
            $content[$shName]['data'] = array('There were no records found for this report.');
            $content[$shName]['options'] = [];
            return;
        }
        // @codeCoverageIgnoreEnd

        //set the header labels
            $rec = (array)$certs[0];

            $labels = [];
            foreach ($rec as $hdr => $val) {
                $labels[] = $hdr;
            }
            if ($this->uri == 'crct') {
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
                            case 'First_Name':
                            case 'Last_Name':
                            case 'City':
                                $value = ucwords(strtolower($value));
                                break;
                            case 'Email':
                                $value = strtolower($value);
                                break;
                            case 'Safe_Haven_Date':
                            case 'Concussion_Awareness_Date':
                            case 'Sudden_Cardiac_Arrest_Date':
                                $trainingComplete = $trainingComplete && !empty($value);
                                break;
                            case 'SafeSport_Date':
                                $is_18 = $this->is_18($cert['DOB']);
                                if ($is_18)
                                    $trainingComplete = $trainingComplete && !empty($value);
                                break;
                            case 'LiveScan_Date':
                                break;
                            case 'RiskStatus':
                                $is_18 = $this->is_18($cert['DOB']);
                                $trainingComplete =  $trainingComplete && ($value == 'Green' or (!$is_18 && $value == 'Blue'));
                                break;
                        }

                        $row[] = $value;
                    }
                    if ($this->uri == 'crct') {
                        $row[] = $trainingComplete ? 'COMPLETE' : '';
                    }
                }

                $data[] = $row;
            }

        if (!empty($data)) {
            $content[$shName]['data'] = $data;
            $content[$shName]['options']['freezePane'] = 'A2';
            $content[$shName]['options']['horizontalAlignment'] = ['B1:Z' => 'left'];
            $content[$shName]['options']['selectRange'] = 'A2';

        }

    }

    /**
     * @param string $dob
     * @return bool
     */
    private function is_18(string $dob):bool {
        return date_diff(date_create($dob), date_create())->y > 17;
    }

    /**
     * @param string $dob
     * @return float
     */
    private function age(string $dob):float {
        return date_diff(date_create($dob), date_create())->y;
    }

}
