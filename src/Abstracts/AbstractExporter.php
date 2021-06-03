<?php

namespace App\Abstracts;

use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;

/*
    // Sample array of data to publish
    $arrayData = array(
        array(NULL, 2010, 2011, 2012),   //heading labels
        array('Q1',   12,   15,   21),
        array('Q2',   56,   73,   86),
        array('Q3',   52,   61,   69),
        array('Q4',   30,   32,    0),
    );
*/

abstract class AbstractExporter
{
    private $format;
    private $objPHPExcel;

    public $fileExtension;
    public $contentType;

    protected $ws;

    public function __construct($format)
    {
        $this->format = $format;
        $this->objPHPExcel = new PhpSpreadsheet\Spreadsheet();

        switch ($format) {
            case 'xls':
                $this->fileExtension = 'xlsx';
                $this->contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            //case 'pdf':
            //    $this->fileExtension = "pdf";
            //    $this->contentType = "application/pdf";
        }
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @param $content
     * @return null|string
     */
    public function export($content)
    {
        switch ($this->format) {
            case 'xls':
                return $this->exportXLSX($content);
            default:
                return null;
        }
    }
    //public function exportPdf($content, $padlen = 18)
    //{
    //    $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
    //    $rendererLibrary = 'domPDF0.6.0beta3';
    //    $rendererLibraryPath = dirname(__FILE__). 'libs/classes/dompdf' . $rendererLibrary;
    //
    //    $this->writeWorksheet($content);
    //
    //    $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'PDF');
    //
    //    ob_start();
    //    $objWriter->save('php://output'); // Instead of file name
    //
    //    return ob_get_clean();
    //
    //}
//    public function exportCSV($content)
//    {
//
//        //for csv type, only export first sheet
//        $content = array_values($content);
//
//        $this->writeWorksheet($content[0]);
//
//        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'CSV');
//
//        ob_start();
//        $objWriter->save('php://output'); // Instead of file name
//
//        return ob_get_clean();
//
//    }

    public function is_asso($a)
    {
        foreach (array_keys($a) as $key) {
            if (!is_int($key)) {
                return true;
            }
        }

        return false;
    }

    public function exportXLSX($content, $sheetName = 'Sheet')
    {
        $xl = $this->objPHPExcel;

        //check for sheet names as keys
        $isAssoc = $this->is_asso($content);

        // ensure unique sheetname
        foreach ($content as $shName => $data) {
            if ($isAssoc) {
                $sheetName = $shName;
            }

            $xl->createSheet();
            $xl->setActiveSheetIndex($xl->getSheetCount() - 1);

            $this->writeWorksheet($data, $sheetName);
        }

        //remove first sheet -- is blank
        $xl->removeSheetByIndex(0);

        //write to application output buffer
        $objWriter = PhpSpreadsheet\IOFactory::createWriter($xl, 'Xlsx');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name

        return ob_get_clean();

    }

    private function pregMatch($rng)
    {
        preg_match('/(.+[a-zA-Z])/', $rng, $matches);

        return strtoupper($matches[0]);
    }

    public function writeWorksheet($content, $shName = "Sheet")
    {
        //check for data
        if (!isset($content['data'])) {
            return null;
        }

        //get data
        $data = $content['data'];
        //get options (if any)
        $options = $content['options'] ?? null;

        //select active sheet
        $this->ws = $this->objPHPExcel->getActiveSheet();

        //load data into sheet
        $this->ws->fromArray($data, null, 'A1');

        //auto-size columns
        foreach (range('A', $this->ws->getHighestDataColumn()) as $col) {
            $this->ws->getColumnDimension($col)->setAutoSize(true);
        }

        //apply options

        // Hide sheet columns.
        $this->hideCols($options);

        //freeze pane
        $this->freezePane($options);

        // date format

        // ['options']['style'] = array('M:M'=>'yyyy-mm-dd');
        $this->setStyle($options);

        //horizontal alignment
        //$options['horizontalAlignment'] = ['WS'=>'left'];
        $this->setAlignment($options);

        //protect cells
        //reference: http://stackoverflow.com/questions/20543937/disable-few-cells-in-phpexcel
        //$options['protect'] = array('pw' => '2016NG', 'range' => array('A:D'));
        $this->protectCells($options);

        //select Range
        //$options['selectRange'] = 'A2:A2';
        $this->selectRange($options);

        //ensure sheet name is unique
        $inc = 1;
        $name = $shName;
        while (!is_null($this->objPHPExcel->getSheetByName($name))) {
            $name = $shName.$inc;
            $inc += 1;
        }

        //$shName = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);

        //Excel limit sheet names to 31 characters
        if (strlen($shName) > 31) {
            $shName = substr($name, -31);
        }

        //name the sheet
        $this->ws->setTitle($shName);

        return;

    }

    protected function hideCols(array $hideCols)
    {
        //$options['hideCols'] = true | false;
        if (isset($hideCols['hideCols'])) {
            $cols = $$hideCols['hideCols'];
            foreach ($cols as $col) {
                $this->ws->getColumnDimension($col)->setVisible(false);
            }
        }
    }

    protected function freezePane(array $freezePane)
    {
        //$options['freezePane'] = 'A2';
        if (isset($freezePane['freezePane'])) {
            $this->ws->freezePane($freezePane['freezePane']);
        }
    }

    protected function setStyle(array $style)
    {
        if (isset($style['style'])) {
            foreach ($style['style'] as $rng => $format) {
                if ($rng == 'WS') {
                    $rng = $this->ws->calculateWorksheetDimension();
                } else {
                    $rowCount = $this->ws->getHighestRow();
                    $rng = $this->pregMatch($rng);
                    $rng .= $rowCount;
                }

                $this->ws->getStyle($rng)
                    ->getNumberFormat()
                    ->setFormatCode($format);
            }
        }
    }

    protected function setAlignment(array $align)
    {
        if (isset($align['horizontalAlignment'])) {
            foreach ($align['horizontalAlignment'] as $rng => $format) {
                if ($rng == 'WS') {
                    $rng = $this->ws->calculateWorksheetDimension();
                } else {
                    $rowCount = $this->ws->getHighestRow();
                    $rng = $this->pregMatch($rng);
                    $rng .= $rowCount;
                }

                switch ($format) {
                    case 'center':
                        $ha = Alignment::HORIZONTAL_CENTER;
                        break;
                    case 'justify':
                        $ha = Alignment::HORIZONTAL_JUSTIFY;
                        break;
                    case 'left':
                        $ha = Alignment::HORIZONTAL_LEFT;
                        break;
                    case 'right':
                        $ha = Alignment::HORIZONTAL_RIGHT;
                        break;
                    default:
                        $ha = Alignment::HORIZONTAL_GENERAL;
                }
                $this->ws->getStyle($rng)->getAlignment()->setHorizontal($ha);

            }
        }
    }

    protected function protectCells(array $protect)
    {
        if (isset($protect['protection']['pw']) and isset($protect['protection']['unlocked'])) {
//            $pw = $options['protection']['pw'];
            $range = $protect['protection']['unlocked'];

            //turn protection on
            $this->ws->getProtection()->setSheet(true);

            //now unprotect requested range
            foreach ($range as $cells) {
                $this->ws->getStyle($cells)->getProtection()->setLocked(
                    Protection::PROTECTION_UNPROTECTED
                );
            }
        }
    }

    protected function selectRange(array $range)
    {
        if (isset($range['selectRange'])) {
            $this->ws->setSelectedCells($range['selectRange']);
        }
    }
}

