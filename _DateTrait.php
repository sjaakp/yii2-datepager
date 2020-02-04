<?php
/**
 * sjaakp/yii2-datepager
 * ----------
 * Date pager for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2020
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-wordcount
 * https://sjaakpriester.nl
 */

namespace sjaakp\datepager;

use Yii;
use yii\web\Request;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

trait _DateTrait {
    /**
     * @var string - name of the model attribute the date pager works with.
     * This must be set.
     */
    public $dateAttribute;

    /**
     * @var string
     * Name of the date pagination parameter. Not much reason to change this (unless you have a conflict
     *      with another widget).
     */
    public $dateParam = 'date';

    /**
     * @var string | \DateInterval
     * @link https://www.php.net/manual/en/dateinterval.construct.php
     * @link https://en.wikipedia.org/wiki/ISO_8601#Durations
     */
    public $interval = 'P1Y';

    /**
     * @var string | \DateTimeInterface
     */
    public $beginDate;
    public $endDate;

    protected $_active;

    /**
     * @throws InvalidConfigException
     * @throws \Exception
     */
    protected function initTrait()  {
        if (! $this->dateAttribute) {
            throw new InvalidConfigException('_DateTrait::dateAttribute must be set.');
        }
        $this->interval = new \DateInterval($this->interval);
    }

    /**
     * @return array - begin and end of the active page
     * @throws \Exception
     */
    protected function getActive()
    {
        /* @var $this BaseDataProvider */
        if ($this->_active === null) {
            $name = $this->id ? $this->id . '-' . $this->dateParam : $this->dateParam;
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];

            $begin = isset($params[$name]) && is_scalar($params[$name])
                ? new \DateTimeImmutable(substr($params[$name] . '-01-01', 0, 10)) : $this->beginDate;
            $end = $begin->add($this->interval);
            $this->_active = [$begin, $end];
        }
        return $this->_active;
    }

    /**
     * @param $page \DateTimeInterface
     * @return string
     */
    public function createUrl($page)    {
        /* @var $this BaseDataProvider */
        $request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];

        // don't copy query parameter from 'normal' pagination
        $suppress = $this->id ? $this->id . '-page' : 'page';
        if (isset($params[$suppress])) unset($params[$suppress]);

        $params[0] = Yii::$app->controller->getRoute();
        $format = $this->interval->m ? ($this->interval->d ? 'Y-m-d' : 'Y-m') : 'Y';
        $params[$this->dateParam] = $page->format($format);
        $urlManager = Yii::$app->getUrlManager();
        return $urlManager->createUrl($params);
    }

    /**
     * @param $date \DateTimeImmutable
     * @return mixed
     */
    protected function normalizeDate($date, $ceil = false)
    {
        $roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $im = $this->interval->m;
        $d = 1;
        if ($im) {
            $id = $this->interval->d;
            if ($id)    {
                $step = intdiv($date->format('j') - 1, $id);
                $d = $step * $id + 1;
            }
            $step = intdiv($date->format('n') - 1, $im);
            $m = $roman[$step * $im];
        }
        else $m = 'I';
        $date = $date->modify("$m $d 00:00:00");
        if ($ceil) $date = $date->add($this->interval);
        return $date;
    }
}
