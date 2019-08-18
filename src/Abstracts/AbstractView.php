<?php

namespace App\Abstracts;

use Symfony\Component\HttpFoundation\Response;

use App\Repository\DataWarehouse;

use DateTime;
use DateTimeZone;

abstract class AbstractView
{
    /* @var DataWarehouse */
    protected $dw;

    //view variables
    protected $user;
    protected $event;

    //view variables
    protected $page_title;
    protected $dates;
    protected $location;
    protected $msg;
    protected $msgStyle;
    protected $menu;

    protected $uri;


    public function __construct(DataWarehouse $dataWarehouse)
    {
        $this->dw = $dataWarehouse;

        $this->page_title = "Section 1: Certification Reporting System";
    }

    protected function getUpdateTimestamp()
    {
        $utc = $this->dw->getUpdateTimestamp();

        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('America/Los_Angeles'));

        return $ts->format('Y-m-d H:i T');
    }

}