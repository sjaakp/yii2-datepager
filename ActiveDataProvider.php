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
     * @var string format string for the database
     */
    public $sqlDateFormat = 'Y-m-d';

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function init()  {
        $this->initTrait();
        $this->beginDate = $this->normalizeDate(new \DateTimeImmutable($this->query->min($this->dateAttribute)));
        $this->endDate = $this->normalizeDate(new \DateTimeImmutable($this->query->max($this->dateAttribute)), true);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function prepareModels()    {

        $attribute = $this->dateAttribute;
        $active = $this->getActive();

        $this->query->orderBy($attribute)
            ->andWhere(['between', $attribute, $active[0]->format($this->sqlDateFormat), $active[1]->format($this->sqlDateFormat)]);
        return parent::prepareModels();
    }
}
