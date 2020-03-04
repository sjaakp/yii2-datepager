<?php
/**
 * sjaakp/yii2-datepager
 * ----------
 * Date pager for Yii2 framework
 * Version 1.1.0
 * Copyright (c) 2020
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-datepager
 * https://sjaakpriester.nl
 */

namespace sjaakp\datepager;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

class DatePager extends Widget {

    /**
     * @var ActiveDataProvider that this pager is associated with.
     * This property must be set.
     */
    public $dataProvider;

    /**
     * @var int
     */
    public $maxButtonCount = 10;

    /**
     * @var null | string | callable
     *  - null: DatePager guesses based on interval
     *  - string: date format according to yii\i18n\Formatter::$dateFormat
     *  - callable: function($dateTimeInterface, $thisDatePager) returning string
     */
    public $labelFormat;

    /**
     * @var array HTML attributes for the pager container tag.
     * Default makes pager look good with Bootstrap.
     */
    public $options = ['class' => 'pagination flex-wrap'];

    /**
     * @var array HTML attributes for the button in a pager container tag.
     * Default makes pager look good with Bootstrap 4 (and doesn't hurt with Bootstrap 3).
     */
    public $buttonOptions = ['class' => 'page-item'];

    /**
     * @var array HTML attributes for the link in a pager container tag.
     * Default makes pager look good with Bootstrap 4 (and doesn't hurt with Bootstrap 3).
     */
    public $linkOptions = ['class' => 'page-link'];

    /**
     * @var string CSS class for the active (currently selected) date button.
     */
    public $activePageCssClass = 'active';

    /**
     * @var string CSS class for the disabled date buttons.
     */
    public $disabledPageCssClass = 'disabled';

    /**
     * @var bool|string Text label for the "next" date button. Will not be HTML-encoded.
     * If this property is false, the "next" date button will not be displayed.
     */
    public $nextPageLabel = '&raquo;';

    /**
     * @var bool|string Text label for the "previous" date button. Will not be HTML-encoded.
     * If this property is false, the "next" date button will not be displayed.
     */
    public $prevPageLabel = '&laquo;';

    /**
     * @var bool|string Text label for the "first" date button. Will not be HTML-encoded.
     * Default is false, which means the "first" date button will not be displayed.
     */
    public $firstPageLabel = false;

    /**
     * @var bool|string Text label for the "last" date button. Will not be HTML-encoded.
     * Default is false, which means the "last" date button will not be displayed.
     */
    public $lastPageLabel = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()  {
        if (! $this->dataProvider) {
            throw new InvalidConfigException('DatePager::dataProvider must be set.');
        }
        if (! in_array(_DateTrait::class, class_uses($this->dataProvider)))
        {
            throw new InvalidConfigException('DatePager::dataProvider is not a datapager provider.');
        }
        if (is_null($this->labelFormat))    {
            $int = $this->dataProvider->interval;
            $this->labelFormat = $int->d == 0
                ? ($int->m == 0
                    ? 'y'
                    : ($int->m % 3 ? 'y-MM' : 'y QQQ')
                )
                : 'short';
        }
    }

    /**
     * @return string|void
     * @throws InvalidConfigException
     */
    public function run()   {
        $dp = $this->dataProvider;
        $buttons = [];

        /* @var $active \DateTimeImmutable */
        $active = $dp->active[0];
        $begin = $active;
        $end = $active;
        $beginLimit = $dp->beginDate;
        $endLimit = $dp->endDate;

        $buttonCount = $this->maxButtonCount;

        while ($buttonCount > 0)    {
            if ($dp->isLeftOf($end, $endLimit))   {
                $end = $end->add($dp->interval);
                $buttonCount--;
                if ($buttonCount == 0) break;
            }
            else    {
                if ($dp->isEqualOrLeftOf($begin, $beginLimit)) break;
            }
            if ($dp->isRightOf($begin, $beginLimit))   {
                $begin = $begin->sub($dp->interval);
                $buttonCount--;
                if ($buttonCount == 0) break;
            }
        }

        while ($dp->isEqualOrLeftOf($begin, $end))    {
            $buttons[] = $this->renderPageButton($begin, $begin == $active);
            $begin = $begin->add($dp->interval);
        }

        if (count($buttons))    {
            if ($this->prevPageLabel)  {
                $prev = $this->renderPageButton($active->sub($dp->interval), false, $this->prevPageLabel, $dp->isEqualOrLeftOf($active, $beginLimit));
                array_unshift($buttons, $prev);
            }
            if ($this->firstPageLabel)  {
                $first = $this->renderPageButton($beginLimit, false, $this->firstPageLabel, $dp->isEqualOrLeftOf($active, $beginLimit));
                array_unshift($buttons, $first);
            }
            if ($this->nextPageLabel) {
                $next = $this->renderPageButton($active->add($dp->interval), false, $this->nextPageLabel, $dp->isEqualOrRightOf($active, $endLimit));
                $buttons[] = $next;
            }
            if ($this->lastPageLabel)  {
                $last = $this->renderPageButton($endLimit, false, $this->lastPageLabel, $dp->isEqualOrRightOf($active, $endLimit));
                $buttons[] = $last;
            }
        }

        echo Html::tag('ul', implode("\n", $buttons), $this->options);
    }

    /**
     * @param $page \DateTimeInterface
     * @param $isActive bool
     * @return string
     * @throws InvalidConfigException
     */
    protected function renderPageButton($page, $isActive, $label = null, $isDisabled = false)
    {
        if (is_null($label)) $label = $this->getDateLabel($page);

        $options = $this->buttonOptions;
        if ($isActive) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($isDisabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
        }
        $link = $isDisabled ? Html::tag('span', $label, $this->linkOptions) : Html::a($label, $this->dataProvider->createUrl($page), $this->linkOptions);
        return Html::tag('li', $link, $options);
    }

    /**
     * @param $page \DateTimeInterface
     * @return string
     * @throws InvalidConfigException
     */
    public function getDateLabel($page) {
        if (is_callable($this->labelFormat)) return call_user_func($this->labelFormat, $page, $this);
        $r = \Yii::$app->formatter->asDate($page, $this->labelFormat);
        $d = $this->dataProvider;
        if ($d->head && $page == $d->beginDate) $r = '... ' . $r;
        if ($d->tail && $page == $d->endDate) $r = $r . ' ...';
        return $r;
    }
}
