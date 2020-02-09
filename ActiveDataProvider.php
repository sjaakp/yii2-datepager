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

use yii\data\ActiveDataProvider as YiiActiveDataProvider;

class ActiveDataProvider extends YiiActiveDataProvider {
    use _DateTrait;

    /**
     * @var string date format string for the database
     */
    public $sqlDateFormat = 'Y-m-d';

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function init()  {
        if (is_null($this->beginDate)) $this->beginDate = $this->query->min($this->dateAttribute);
        else $this->head = true;
        if (is_null($this->endDate)) $this->endDate = $this->query->max($this->dateAttribute);
        else $this->tail = true;
        $this->initTrait();
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function prepareModels()    {

        $attribute = $this->dateAttribute;
        $active = $this->getActive();

        $this->query->orderBy($attribute);
        if ($active[0] != $this->beginDate)
            $this->query->andWhere(['>=', $attribute, $active[0]->format($this->sqlDateFormat)]);
        if ($active[0] != $this->endLimit)
            $this->query->andWhere(['<', $attribute, $active[1]->format($this->sqlDateFormat)]);
        return parent::prepareModels();
    }
}
